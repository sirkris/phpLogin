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
	function setvars()
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
			case 400:
				$this->tempaltevars["getvars"] = $_GET;
				break;
			case 404:
				$this->templatevars["templatefile"] = $template->filename( $this->template );
				break;
		}
		
		return TRUE;
	}
}
