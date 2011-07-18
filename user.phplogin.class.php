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
			/* Don't use session_destroy or session_unset because host script might have unrelated session data already.  --Kris */
			foreach ( $_SESSION as $skey => $ignore )
			{
				if ( strcmp( substr( $skey, 0, 9 ), "phplogin_" ) == 0 )
				{
					unset( $_SESSION[$skey] )
				}
			}
			
			/* Check the database to rebuild session data, if possible.  --Kris */
			if ( isset( session_id() ) )
			{
				// TODO
			}
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
}
