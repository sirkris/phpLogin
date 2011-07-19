<?php

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
		
		$rows = $sql->query( "select userid from phplogin_users where username = ? AND password = ?", array( $sql->addescape( $username ), $sql->addescape( $pwenc ) ), RETURN_NUMROWS );
		
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
			|| self::allowed() == FALSE )
		{
			$user->logout()
			return FALSE;
		}
		
		$user->update_lastaction();
		
		return TRUE;
	}
	
	/* Determine if the user is allowed to login (status > 0).  --Kris */
	function allowed()
	{
		
	}
	
	/* Determine if the user has moderator privileges.  --Kris */
	function is_moderator()
	{
		
	}
	
	/* Determine if the user has admin privileges.  --Kris */
	function is_admin()
	{
		
	}
	
	/* Determine if the user has superuser privileges.  --Kris */
	function is_superuser()
	{
		
	}
	
	/* Retrieve the correct password encryption method for the user.  --Kris */
	function get_passhash( $username )
	{
		require( "config.phpmeow.php" );
		
		$sql = new phpmeow_sql();
		
		return $sql->query( "select pwhash from phplogin_users where username = ?", $sql->addescape( $username ) );
	}
}
