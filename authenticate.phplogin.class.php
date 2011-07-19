<?php

define( "PHPLOGIN_STATUS_RESERVED", -2 );
define( "PHPLOGIN_STATUS_BANNED", -1 );
define( "PHPLOGIN_STATUS_PENDING", 0 );
define( "PHPLOGIN_STATUS_ACTIVE", 1 );
define( "PHPLOGIN_STATUS_MODERATOR", 2 );
define( "PHPLOGIN_STATUS_ADMIN", 3 );
define( "PHPLOGIN_STATUS_SUPERUSER", 4 );

class phplogin_authenticate
{
	/* Validate username/password.  --Kris */
	function login( $username, $password )
	{
		require( "config.phplogin.php" );
		
		$sql = new phpmeow_sql();
		$res = $sql->get_passhash( $username );
		
		$pwhash = $res["pwhash"];
		
		$pwenc = phplogin_encryption::encrypt_string( $password, $pwhash );
		
		$rows = $sql->query( "select userid from phplogin_users where username = ? AND password = ?", array( $sql->addescape( $username ), $sql->addescape( $pwenc ) ), PHPLOGIN_SQL_RETURN_NUMROWS );
		
		return ( $rows == 1 ? TRUE : FALSE );
	}
	
	/* Determine if session data indicates a logged-in user.  --Kris */
	function session()
	{
		require( "config.phplogin.php" );
		
		$session_id = session_id();
		
		$user = new user();
		
		if ( !isset( $user->userdata ) || !isset( $_SESSION["phplogin_userdata"] ) 
			|| !isset( $_SESSION["phplogin_timeoutmin"] ) 
			|| !isset( $_SESSION["phplogin_sessiontimeout"] ) 
			|| !isset( $session_id ) 
			|| !is_numeric( $_SESSION["phplogin_userid"] ) 
			|| $_SESSION["phplogin_userid"] <= 0 )
		{
			$user->clear_session();
			return FALSE;
		}
		
		if ( $_SESSION["phplogin_loggedon"] != 1 )
		{
			return FALSE;
		}
		
		if ( $_SESSION["phplogin_lastaction"] + ($_SESSION["phplogin_timeoutmin"] * 60) <= time() 
			|| self::has_privilege( PHPLOGIN_STATUS_ACTIVE ) == FALSE )
		{
			$user->logout();
			return FALSE;
		}
		
		$user->update_lastaction();
		
		return TRUE;
	}
	
	/* Determine if user meets or exceeds a specified access privilege (shouldn't be used for status <= 0).  --Kris */
	function has_privilege( $privilege )
	{
		require( "config.phplogin.php" );
		
		$session = new phplogin_session();
		$session->start();
		
		$session_id = session_id();
		
		if ( !isset( $session_id ) )
		{
			return FALSE;
		}
		
		return ( $_SESSION["phplogin_status"] >= $privilege ? TRUE : FALSE );
	}
	
	/* Retrieve the correct password encryption method for the user.  --Kris */
	function get_passhash( $username )
	{
		require( "config.phpmeow.php" );
		
		$sql = new phpmeow_sql();
		
		return $sql->query( "select pwhash from phplogin_users where username = ?", array( $sql->addescape( $username ) ) );
	}
}
