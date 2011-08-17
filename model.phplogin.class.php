<?php

/* Everything here is static, so no instantiation required.  --Kris */
class phplogin_model
{
	/* Return data for contact template.  --Kris */
	public static function get_contactdata( $userid )
	{
		require( "config.phplogin.php" );
		
		$cond = "userid = ? AND status >= 1";
		$params = array( $userid );
		$sel = "username, email";
		
		$userdata = array();
		$userdata = phplogin_user::load_data( $cond, $params, $sel );
		
		if ( empty( $userdata ) )
		{
			$userdata = array( "Success" => FALSE, "Reason" => "Userid did not return valid user data!" );
		}
		else
		{
			$userdata["Success"] = TRUE;
		}
		
		return $userdata;
	}
	
	/* Return initialized user data.  --Kris */
	public static function get_userdata()
	{
		require( "config.phplogin.php" );
		
		$user = new phplogin_user();
		
		if ( !isset( $user->userdata ) || !isset( $user->userdata["status"] ) || !isset( $user->userid ) || $user->userdata["status"] < 1 )
		{
			$user->error = TRUE;
		}
		
		return $user;
	}
}
