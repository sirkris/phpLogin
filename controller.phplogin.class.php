<?php

class phplogin_controller
{
	/* Validate the argument and set the template to be used.  --Kris */
	function __construct()
	{
		require( "config.phplogin.php" );
		
		$args = func_get_args();
		
		if ( empty( $args ) || !isset( $args[0] ) || ( !isset( $args[0]["phplogin_template"] ) && !isset( $args[0]["phplogin_formid"] ) ) )
		{
			$this->template = "400";  //Bad request.  --Kris
			
			$template = new phplogin_templates();
		}
		else
		{
			foreach ( $args as $argarr )
			{
				foreach ( $argarr as $key => $value )
				{
					$this->$key = $value;
				}
			}
			
			/* If we're dealing with a form submission, query the model then route to the appropriate template.  --Kris */
			if ( isset( $args[0]["phplogin_formid"] ) )
			{
				//TODO
			}
			else
			{
				$arg = $args[0]["phplogin_template"];
			}
			
			$template = new phplogin_templates();
			if ( $template->exists( $arg ) )
			{
				$this->template = $arg;
			}
			else
			{
				$this->template = "404";  //File not found.  --Kris
			}
		}
		
		$this->set_vars();
	}
	
	/* Determine the vars to be set for each template.  --Kris */
	function set_vars()
	{
		require( "config.phplogin.php" );
		
		if ( !isset( $this->template ) )
		{
			return FALSE;
		}
		
		$template = new phplogin_templates();
		
		$this->templatevars = array();
		switch ( $this->template )
		{
			default:
				return FALSE;
				break;
			case "400";
				$this->tempaltevars["getvars"] = $_GET;
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : "An error occurred generating the template." );
				break;
			case "403";
				$this->tempaltevars["getvars"] = $_GET;
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : "Access denied." );
				break;
			case "404":
				$this->templatevars["templatefile"] = $template->filename( $this->template );
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : "The requested resource could not be found." );
				break;
			case "change_password":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Change Password";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				if ( phplogin_model::is_loggedon() == FALSE )
				{
					$this->template = "403";
					$this->set_vars();
					return FALSE;
				}
				break;
			case "contact":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Send Message";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				if ( phplogin_model::is_loggedon() == FALSE )
				{
					$this->template = "403";
					$this->set_vars();
					return FALSE;
				}
				else
				{
					$userdata = phplogin_model::get_contactdata( $this->phplogin_userid );
					if ( $userdata["Success"] == TRUE )
					{
						$this->templatevars["username"] = $userdata[0]["username"];
					}
					else
					{
						$this->template = "400";
						$this->set_vars();
						return FALSE;
					}
				}
				break;
			case "edit_user":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Save Changes";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				if ( phplogin_model::is_loggedon() == FALSE )
				{
					$this->template = "403";
					$this->set_vars();
					return FALSE;
				}
				else
				{
					$this->templatevars["username"] = $phplogin_user->userdata["username"];
					$this->templatevars["email"] = $phplogin_user->userdata["email"];
				}
				break;
			case "login":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Login";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				break;
			case "manage_users":
				$this->templatevars["action"] = "#";
				$this->templatevars["action2"] = "#";
				$this->templatevars["submit"] = "Save Changes";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				if ( phplogin_model::is_loggedon() == FALSE || phplogin_model::get_status() < 2 
					|| ( isset( $this->phplogin_userid ) && is_superior( $this->phplogin_userid ) == FALSE ) )
				{
					$this->template = "403";
					$this->set_vars();
					return FALSE;
				}
				if ( isset( $this->phplogin_userid ) )
				{
					$this->templatevars["userslist"] = phplogin_model::get_manage_users_userslist( $this->phplogin_userid );
					$victimdata = phplogin_model::get_userdata_by_userid( $this->phplogin_userid );
					foreach ( $victimdata[0] as $key => $value )
					{
						$this->templatevars[$key] = $value;
					}
				}
				else
				{
					$this->templatevars["userslist"] = phplogin_model::get_manage_users_userlist();
				}
				break;
			case "register":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Submit Registration";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				break;
			case "reset_password":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Send Reset Link";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				break;
			case "view_profile":
				if ( !isset( $this->phplogin_userid ) || !is_numeric( $this->phplogin_userid ) )
				{
					$this->template = "400";
					$this->set_vars();
					return FALSE;
				}
				$victimdata = phplogin_model::get_userdata_by_userid( $this->phplogin_userid );
				if ( !is_array( $victimdata ) || empty( $victimdata ) )
				{
					$this->template = "400";
					$this->set_vars();
					return FALSE;
				}
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				foreach ( $victimdata[0] as $key => $value )
				{
					$this->templatevars[$key] = $value;
				}
				if ( phplogin_model::is_loggedon() )
				{
					$this->templatevars["contact"] = "[ <a href=\"#\" onClick=\"phplogin_loadTemplate( 'contact', 'phplogin_userid=" . $victimdata[0]["userid"] . "' );\">Send Message</a> ]";
				}
				else
				{
					$this->templatevars["contact"] = NULL;
				}
				if ( phplogin_model::is_superior( $victimdata[0]["userid"] ) )
				{
					$this->templatevars["editlink"] = "[ <a href=\"#\" onClick=\"phplogin_loadTemplate( 'manage_users', 'phplogin_userid=" . $victimdata[0]["userid"] . "' );\">Edit User</a> ]";
				}
				else
				{
					$this->templatevars["editlink"] = NULL;
				}
				$this->templatevars["status"] = $phplogin_statuslevels[$victimdata[0]["status"]];
				$this->templatevars["usersince"] = ( $victimdata[0]["registered"] > 0 ? date( "F jS, Y", $victimdata[0]["registered"] ) : "N/A" );
				$this->templatevars["loggedonsince"] = ( $victimdata[0]["loggedonsince"] > 0 ? date( "F jS, Y @ h:i:s A (T)", $victimdata[0]["loggedonsince"] ) : "N/A" );
				$this->templatevars["loggedon"] = ( $victimdata[0]["loggedon"] == 1 ? "Yes" : "No" );
				break;
		}
		
		return TRUE;
	}
	
	/* Send the header HTML for stand-alone pages.  --Kris */
	function send_header()
	{
		require( "config.phplogin.php" );
		
		$phplogin_templates = new phplogin_templates();
		
		$phplogin_templates->set( "title", ucwords( str_replace( "_", " ", $this->template ) ) );
		$phplogin_templates->display( "header" );
		$phplogin_templates->clear();
	}
	
	/* Send the footer HTML for stand-alone pages.  --Kris */
	function send_footer()
	{
		require( "config.phplogin.php" );
		
		$phplogin_templates = new phplogin_templates();
		
		$phplogin_templates->display( "footer" );
		$phplogin_templates->clear();
	}
	
	/* The primary controller function that passes to the view.  --Kris */
	function send_view( $skipheader = TRUE, $skipfooter = TRUE )
	{
		require( "config.phplogin.php" );
		
		if ( $skipheader == FALSE )
		{
			$this->send_header();
		}
		
		$phplogin_templates = new phplogin_templates();
		
		if ( isset( $this->templatevars ) && is_array( $this->templatevars ) )
		{
			foreach ( $this->templatevars as $var => $val )
			{
				$phplogin_templates->set( $var, $val );
			}
		}
		
		$phplogin_templates->display( $this->template );
		$phplogin_templates->clear();
		
		if ( $skipfooter == FALSE )
		{
			$this->send_footer();
		}
	}
}
