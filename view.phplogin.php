<?php

require( "config.phplogin.php" );

$vars = array();
foreach ( $_GET as $getkey => $getval )
{
	$vars[$getkey] = $getval;
}

foreach ( $_POST as $postkey => $postval )
{
	$vars[$postkey] = $postval;
}

$controller = new phplogin_controller( $vars );

$controller->send_view();
