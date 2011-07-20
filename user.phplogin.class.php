<?php

class phplogin_user
{
	public function __construct()
	{
		require( "config.phplogin.php" );
		
		$session = new phplogin_session();
		$session->start();
		
		$session_id = session_id();
		
		/* Check for useable session data.  If not there or corrupted, clear and initialize.  --Kris */
		if ( !isset( $_SESSION["phplogin_userid"] ) 
			|| !isset( $_SESSION["phplogin_timeoutmin"] ) 
			|| !isset( $_SESSION["phplogin_sessiontimeout"] ) 
			|| !isset( $session_id ) 
			|| !is_numeric( $_SESSION["phplogin_userid"] ) 
			|| $_SESSION["phplogin_userid"] <= 0 )
		{
			$this->clear_session();
			
			/* Check the database to rebuild session data, if possible.  --Kris */
			if ( isset( $session_id ) )
			{
				$this->populate_session();
			}
		}
		else if ( time() >= $_SESSION["phplogin_sessiontimeout"] )
		{
			$this->populate_session();
		}
		
		/* Populate common data from session for convenience.  --Kris */
		if ( isset( $_SESSION["phplogin_userid"] ) )
		{
			$this->userid = $_SESSION["phplogin_userid"];
		}
		
		if ( isset( $_SESSION["phplogin_userdata"] ) )
		{
			$this->userdata = $_SESSION["phplogin_userdata"];
		}
	}
	
	function load_data( $cond = "phpsessid", $params = array(), $sel = "userid, username, email, registered, loggedon, loggedonsince, lastaction, timeoutmin, status" )
	{
		$session_id = session_id();
		
		if ( !isset( $session_id ) )
		{
			return FALSE;
		}
		
		require( "config.phplogin.php" );
		
		$sql = new phplogin_sql();
		
		if ( strcmp( $wherecond, "phpsessid" ) == 0 )
		{
			$query = "select $sel from phplogin_users where phpsessid = ?";
			$params = array( $sql->addescape( $session_id ) );
		}
		else
		{
			$query = "select $sel from phplogin_users where $cond";
		}
		
		return $sql->query( $query, $params );
	}
	
	function clear_session()
	{
		/* Don't use session_destroy or session_unset because host script might have unrelated session data already.  --Kris */
		foreach ( $_SESSION as $skey => $ignore )
		{
			if ( strcmp( substr( $skey, 0, 9 ), "phplogin_" ) == 0 )
			{
				unset( $_SESSION[$skey] );
			}
		}
	}
	
	function populate_session()
	{
		require( "config.phplogin.php" );
		
		$_SESSION["phplogin_userdata"] = $this->load_data();
		
		$_SESSION["phplogin_userdata"] = $_SESSION["phplogin_userdata"][0];
		
		/* If user has been logged-out, banned, etc; clear the session data.  --Kris */
		if ( $_SESSION["phplogin_userdata"]["status"] <= 0 || $_SESSION["phplogin_userdata"]["loggedon"] != 1 )
		{
			$this->clear_session();
			return;
		}
		
		foreach ( $_SESSION["phplogin_userdata"] as $sukey => $suval )
		{
			$_SESSION["phplogin_" . $sukey] = $suval;
		}
		
		$_SESSION["phplogin_sessiontimeout"] = time() + $phplogin_session_repopulate();
	}
	
	/* Update the last action timestamp in the database (if enabled).  --Kris */
	function update_lastaction()
	{
		require( "config.phplogin.php" );
		
		$session_id = session_id();
		
		$_SESSION["phplogin_lastaction"] = time();
		
		if ( $phplogin_ping_db == TRUE && isset( $session_id ) )
		{
			$sql = new phplogin_sql();
			
			$sql->query( "update phplogin_users set lastaction = ? where phpsessid = ?", array( strval( time() ), $sql->addescape( $session_id ) ), PHPLOGIN_SQL_RETURN_NULL );
		}
	}
	
	/* Login the user.  --Kris */
	function login( $username, $password )
	{
		require( "config.phplogin.php" );
		
		$session = new phplogin_session();
		$session->start();
		
		$session_id = session_id();
		
		/* Sessions are required.  --Kris */
		if ( !isset( $session_id ) || $session_id == NULL )
		{
			return array( "Success" => FALSE, "Reason" => "Unable to retrieve session_id!" );
		}
		
		if ( phplogin_authenticate::login( $username, $password ) == FALSE )
		{
			return array( "Success" => FALSE, "Reason" => "Incorrect username and/or password!" );
		}
		
		$sql = new phplogin_sql();
		
		$query = "update phplogin_users set loggedon = 1, phpsessid = ?, loggedonsince = ?, lastaction = ?";
		$query .= " where username = ?";
		$affrows = $sql->query( $query, array( $sql->addescape( $session_id ), time(), time(), $sql->addescape( $username ) ), PHPLOGIN_SQL_RETURN_AFFECTEDROWS );
		
		/* Just in case there's a problem communicating with the database.  --Kris */
		if ( $affrows == 0 )
		{
			return array( "Success" => FALSE, "Reason" => "Unknown database error!" );
		}
		
		$this->populate_session();
		
		return array( "Success" => TRUE );
	}
	
	/* Logout the user.  Returns boolean success status.  --Kris */
	function logout()
	{
		require( "config.phplogin.php" );
		
		$session_id = session_id();
		
		if ( !isset( $session_id ) )
		{
			return FALSE;
		}
		
		$sql = new phplogin_sql();
		
		$affrows = $sql->query( "update phplogin_users set loggedon = 0, loggedonsince = '0', phpsessid = '' where phpsessid = ?", array( $sql->addescape( $session_id ) ), PHPLOGIN_SQL_RETURN_AFFECTEDROWS );
		
		return ( $affrows == 1 ? TRUE : FALSE );
	}
	
	/* Send an email with a reset password link.  --Kris */
	function forgot_password( $info )
	{
		require( "config.phplogin.php" );
		
		/* Username or email address is accepted.  Nothing is reset unless/until the emailed link is clicked and the subsequent form submitted.  --Kris */
		if ( strstr( $info, "@" ) !== FALSE )
		{
			$check = array( "email", "username" );
		}
		else
		{
			$check = array( "username", "email" );
		}
		
		/* Attempt to retrieve the data.  --Kris */
		$res = array();
		foreach ( $check as $field )
		{
			$res = $this->load_data( "$field = ?", array( $info ) );
			
			if ( !empty( $res ) )
			{
				break;
			}
		}
		
		if ( empty( $res ) )
		{
			return array( "Success" => FALSE, "Reason" => "No user account found matching the provided criteria!" );
		}
		
		/* If the user is currently logged-in, then obviously this is not a legitimate query.  --Kris */
		if ( $res[0]["loggedon"] == 1 
			&& $res[0]["lastaction"] + ($res[0]["timeoutmin"] * 60) < time() )
		{
			return array( "Success" => FALSE, "Reason" => "This user account is still logged-in!  If it is yours, please wait until the login times-out then try again." );
		}
		
		/* Make sure the user is allowed to login.  --Kris */
		if ( phplogin_authenticate::has_privilege( PHPLOGIN_STATUS_ACTIVE ) == FALSE )
		{
			return array( "Success" => FALSE, "Reason" => "This user account is currently locked." );
		}
		
		/* All the sanity checks passed.  Populate phpsessid with the confirmation code and send the email.  --Kris */
		$code = phplogin_session::generate_sid();
		
		$subject = "Change Your phpLogin Password";
		$body = "You are receiving this email because somebody (IP:  " . $_SERVER["REMOTE_ADDR"] . ") submitted a request to ";
		$body .= "change your password.  If this was not you, please disregard this email.\r\n\r\n";
		$body .= "To change your password, click the following link:\r\n\r\n";
		$body .= "http://" . $_SERVER["SERVER_NAME"] . substr( $_SERVER["PHP_SELF"], 0, strrpos( $_SERVER["PHP_SELF"], "/" ) + 1 );
		$body .= "reset_password.phplogin.php?code=" . $code;
		
		$mailresult = mail( $res[0]["email"], $subject, $body, 
			// Email header
			"From: " . $phplogin_mailfrom . "\r\n" 
			. "Reply-To: " . $phplogin_replyto . "\r\n" 
			. "Content-type: text/plain\r\n" 
			. "X-mailer: PHP/" . phpversion() );
		
		if ( !$mailresult )
		{
			return array( "Success" => FALSE, "Reason" => "Unable to send email due to an unknown system error!  Please contact the webmaster." );
		}
		
		return array( "Success" => TRUE );
	}
}
