<?php

require( "config.phplogin.php" );

$controller = new phplogin_controller( $_GET );

$controller->send_view();
