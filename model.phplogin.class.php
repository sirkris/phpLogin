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
	
	/* Load a specific user.  --Kris */
	public static function get_userdata_by_userid( $userid )
	{
		require( "config.phplogin.php" );
		
		return phplogin_user::load_data( "userid = ?", array( $userid ) );
	}
	
	/* Load all users below the current user's status level (available to moderators and above only).  --Kris */
	public static function get_inferior_users()
	{
		require( "config.phplogin.php" );
		
		$user = self::get_userdata();
		
		if ( !isset( $user->userdata ) || !isset( $user->userdata["status"] ) || !isset( $user->userid ) || $user->userdata["status"] < 2 )
		{
			return array( "Success" => FALSE, "Reason" => "Access denied." );
		}
		
		return phplogin_user::load_data( "status < " . $user->userdata["status"] );
	}
	
	/* Load all active users.  Could slow things down if your database is large, so use sparingly!  --Kris */
	public static function get_users( $include_inactive = FALSE )
	{
		require( "config.phplogin.php" );
		
		if ( $include_inactive == TRUE )
		{
			return phplogin_user::load_data( "*" );
		}
		else
		{
			return phplogin_user::load_data( "status >= 1" );
		}
	}
	
	/* Get the dropdown users list for manage_users template.  --Kris */
	public static function get_manage_users_userlist( $selected_userid = NULL )
	{
		require( "config.phplogin.php" );
		
		$victimdata = array();
		$victimdata = self::get_inferior_users();
		
		$userslist = NULL;
		foreach ( $victimdata as $vkey => $victim )
		{
			if ( $selected_userid != NULL && $selected_userid == $victim["userid"] )
			{
				$sel = " selected=\"selected\"";
			}
			else
			{
				$sel = NULL;
			}
			
			$userslist .= "<option value=\"" . $victim["userid"] . "\"" . $sel . ">" . $victim["username"] . "</option>\r\n";
		}
		
		return $userslist;
	}
	
	/* Determine if user is logged-in.  --Kris */
	public static function is_loggedon()
	{
		require( "config.phplogin.php" );
		
		$user = self::get_userdata();
		
		return ( isset( $user->userdata ) && isset( $user->userdata["status"] ) && isset( $user->userid ) && $user->userdata["status"] >= 1 ? TRUE : FALSE );
	}
	
	/* Determine if user outranks the victim (user must be moderator or above).  --Kris */
	public static function is_superior( $selected_userid )
	{
		require( "config.phplogin.php" );
		
		$user = self::get_userdata();
		
		$victimdata = array();
		$victimdata = self::get_userdata_by_userid( $selected_userid );
		
		return ( $user->userdata["status"] > $victimdata[0]["status"] && $user->userdata["status"] >= 2 ? TRUE : FALSE );
	}
}
