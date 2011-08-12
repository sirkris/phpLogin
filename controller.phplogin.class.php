<?php

class phplogin_controller
{
	function __construct()
	{
		require( "config.phplogin.php" );
		
		$args = func_get_args();
		
		if ( empty( $args ) || !isset( $args[0] ) )
		{
			$this->template = "400";  //Bad request.  --Kris
			return;
		}
		
		$template = new phplogin_templates();
		if ( $template->exists( $args[0] ) )
		{
			$this->tempalte = $args[0];
		}
		else
		{
			$this->template = "404";  //File not found.  --Kris
		}
	}
}
