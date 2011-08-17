<?php

require( "config.phplogin.php" );

if ( !isset( $_POST ) || empty( $_POST ) )
{
	$controller = new phplogin_controller( $_GET );
}
else
{
	$controller = new phplogin_controller( $_POST );
}

$controller->send_view();
