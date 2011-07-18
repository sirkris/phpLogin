<?php

class phplogin_user
{
	public function __construct()
	{
		require( "config.phplogin.php" );
		
		$session = new phplogin_session();
		$session->start();
		
		/* Check for useable session data.  If not there or corrupted, clear and initialize.  --Kris */
		if ( !isset( $_SESSION["phplogin_userid"] ) 
			|| !isset( $_SESSION["phplogin_timeoutmin"] ) 
			|| !isset( $_SESSION["phplogin_sessiontimeout"] ) 
			|| !isset( session_id() ) 
			|| !is_numeric( $_SESSION["phplogin_userid"] ) 
			|| $_SESSION["phplogin_userid"] <= 0 )
		{
			$this->clear_session();
			
			/* Check the database to rebuild session data, if possible.  --Kris */
			if ( isset( session_id() ) )
			{
				$this->populate_session();
			}
		}
		else if ( time() >= $_SESSION["phplogin_sessiontimeout"] )
		{
			$this->populate_session();
		}
		else
		{
			$this->update_lastaction();
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
		if ( !isset( session_id() ) )
		{
			return FALSE;
		}
		
		require( "config.phplogin.php" );
		
		$sql = new phplogin_sql();
		
		return $sql->query( "select $select from phplogin_users where phpsessid = ?", array( $sql->addescape( session_id() ) ) );
	}
	
	function clear_session()
	{
		/* Don't use session_destroy or session_unset because host script might have unrelated session data already.  --Kris */
		foreach ( $_SESSION as $skey => $ignore )
		{
			if ( strcmp( substr( $skey, 0, 9 ), "phplogin_" ) == 0 )
			{
				unset( $_SESSION[$skey] )
			}
		}
	}
	
	function populate_session()
	{
		require( "config.phplogin.php" );
		
		$this->update_lastaction();
		
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
		
		if ( $phplogin_ping_db == TRUE && isset( $_SESSION["phplogin_userid"] ) )
		{
			$sql = new phplogin_sql();
			
			$sql->query( "update phplogin_users set lastaction = ? where phpsessid = ?", array( strval( time() ), $sql->addescape( session_id() ) ), RETURN_NULL );
		}
	}
}
