<?php

require( "config.phplogin.php" );

/* If the userid is invalid/banned/etc, redirect to view_profile and let that script handle it.  --Kris */
$cond = "userid = ? AND status >= 1";
$params = array( $_GET["phplogin_userid"] );
$sel = "username, email";

$userdata = array();
$userdata = phplogin_user::load_data( $cond, $params, $sel );

if ( empty( $userdata ) )
{
	header( "Location: view_profile.frontend.phplogin.php?userid=" . $_GET["phplogin_userid"] . "&errmsg=Unable to contact user!" );
	die();
}

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Send a Message" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_contactcontainerdiv"><?php

$phplogin_templates->set( "action", "contact.backend.phplogin.php" );
$phplogin_templates->set( "submit", "Send Message" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );
$phplogin_templates->set( "username", $username );

$phplogin_templates->display( "contact" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
