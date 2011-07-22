<?php

class phplogin_templates
{
	public function __construct()
	{
		require( "config.phplogin.php" );
		
		/* If the templates directory is not present/readable, terminate with an error.  --Kris */
		if ( !file_exists( $phplogin_templates_dir ) 
			|| !is_dir( $phplogin_templates_dir ) 
			|| !is_readable( $phplogin_templates_dir ) )
		{
			die( "<b>FATAL ERROR:  The templates directory '$phplogin_templates_dir' does not exist or is not readable!</b>" );
		}
	}
	
	/* Display the specified template (loads $phplogin_templates_dir/$args[0].template.phplogin.html).  --Kris */
	function display( $template )
	{
		require( "config.phplogin.php" );
		
		$templatefile = $phplogin_templates_dir . $template . ".template.phplogin.html";
		
		if ( !file_exists( $templatefile ) 
			|| !is_file( $templatefile ) 
			|| !is_readable( $templatefile ) )
		{
			return array( "Success" => FALSE, "Reason" => "Template file '$templatefile' does not exist or is not readable!" );
		}
		
		$filedata = $this->load( $templatefile ) or return array( "Success" => FALSE, "Reason" => "Error loading template $template for read!" );
		$filedata = $this->parse( $filedata ) or return array( "Success" => FALSE, "Reason" => "Error parsing template $template!" );
		
		print $filedata;
		
		return array( "Success" => TRUE );
	}
	
	/* Load the template file.  --Kris */
	function load( $templatefile )
	{
		return file_get_contents( $templatefile );
	}
	
	/* Parse the template file.  See template_syntax.phplogin.txt for specifications.  --Kris */
	function parse( $filedata )
	{
		if ( !is_array( $this->customvars ) )
		{
			return FALSE;
		}
		
		if ( empty( $this->customvars ) )
		{
			return $filedata;
		}
		
		
	}
}
