<?php

class phplogin_controller
{
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
	
	
}
