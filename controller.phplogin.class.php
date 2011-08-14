<?php

class phplogin_controller
{
	/* Validate the argument and set the template to be used.  --Kris */
	function __construct()
	{
		require( "config.phplogin.php" );
		
		$args = func_get_args();
		
		if ( empty( $args ) || !isset( $args[0] ) || !isset( $args[0]["phplogin_template"] ) )
		{
			$this->template = "400";  //Bad request.  --Kris
			return;
		}
		
		$arg = $args[0]["phplogin_template"];
		
		$template = new phplogin_templates();
		if ( $template->exists( $arg ) )
		{
			$this->tempalte = $arg;
		}
		else
		{
			$this->template = "404";  //File not found.  --Kris
		}
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
				break;
			case "404":
				$this->templatevars["templatefile"] = $template->filename( $this->template );
				break;
			case "change_password":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Change Password";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				break;
			// TODO - "contact" (requires model)
			// TODO - "edit_user" (requires model)
			case "login":
				$this->templatevars["action"] = "#";
				$this->templatevars["submit"] = "Login";
				$this->templatevars["errmsg"] = ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL );
				break;
			// TODO - "manage_users" (requires model)
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
			// TODO - "view_profile" (requires model)
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
}
 