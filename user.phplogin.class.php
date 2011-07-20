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
	
	function load_data( $select = "userid, username, email, registered, loggedon, loggedonsince, lastaction, timeoutmin, status" )
	{
		$session_id = session_id();
		
		if ( !isset( $session_id ) )
		{
			return FALSE;
		}
		
		require( "config.phplogin.php" );
		
		$sql = new phplogin_sql();
		
		return $sql->query( "select $select from phplogin_users where phpsessid = ?", array( $sql->addescape( $session_id ) ) );
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
		
		/* If user has been logged-out, banned, etc; clear the session data.  --Kris */
		if ( $_SESSION["userdata"]["status"] <= 0 || $_SESSION["userdata"]["loggedon"] != 1 )
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
}
