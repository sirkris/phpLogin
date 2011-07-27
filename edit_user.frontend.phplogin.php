<?php

require( "config.phplogin.php" );

$user = new phplogin_user();

/* If the userid is invalid/banned/etc, redirect to view_profile and let that script handle it.  --Kris */
if ( !isset( $user->userdata ) || !isset( $user->userdata["status"] ) || !isset( $user->userid ) || $user->userdata["status"] < 1 )
{
	header( "Location: view_profile.frontend.phplogin.php?userid=" . $user->userid . "&errmsg=Unable to edit user!" );
	die();
}

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Edit Profile" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_editusercontainerdiv"><?php

$phplogin_templates->set( "action", "edit_user.backend.phplogin.php" );
$phplogin_templates->set( "submit", "Save Changes" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );
$phplogin_templates->set( "username", $user->userdata["username"] );
$phplogin_templates->set( "email", $user->userdata["email"] );

$phplogin_templates->display( "edit_user" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
