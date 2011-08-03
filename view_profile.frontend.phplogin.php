<?php

if ( !isset( $_GET["phplogin_userid"] ) || !is_numeric( $_GET["phplogin_userid"] ) )
{
	die( "You must specify a phplogin_userid!" );
}

require( "config.phplogin.php" );

$user = new phplogin_user();

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "View Profile" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_viewprofilecontainerdiv"><?php

$victimdata = array();
$victimdata = phplogin_user::load_data( "userid = " . $_GET["phplogin_userid"] );

if ( empty( $victimdata ) || $victimdata == FALSE )
{
	die( "That user doesn't exist!" );
}

$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );

/* If user is logged-in, provide a contact link.  --Kris */
if ( isset( $user->userdata ) && isset( $user->userdata["status"] ) && isset( $user->userid ) && $user->userdata["status"] >= 1 )
{
	$phplogin_templates->set( "contact", "[ <a href=\"contact.frontend.phplogin.php?phplogin_userid=" . $victimdata[0]["userid"] . "\">Send Message</a> ]" );
}
else
{
	$phplogin_templates->set( "contact", NULL );
}

/* Provide a convenient edit link if the user has the appropriate authorization.  --Kris */
if ( isset( $user->userdata ) && isset( $user->userdata["status"] ) && isset( $user->userid ) && $user->userdata["status"] > $victim["status"] 
	&& $user->userdata["status"] >= 2 )
{
	$phplogin_templates->set( "editlink", "[ <a href=\"manage_users.frontend.phplogin.php?phplogin_userid=" . $victimdata[0]["userid"] . "\">Edit User</a> ]" );
}
else
{
	$phplogin_templates->set( "editlink", NULL );
}

$phplogin_templates->set( "status", $phplogin_statuslevels[$victimdata[0]["status"]] );
$phplogin_templates->set( "usersince", ( $victimdata[0]["registered"] > 0 ? date( "F jS, Y", $victimdata[0]["registered"] ) : "N/A" ) );
$phplogin_templates->set( "loggedonsince", ( $victimdata[0]["loggedonsince"] > 0 ? date( "F jS, Y @ h:i:s A (T)", $victimdata[0]["loggedonsince"] ) : "N/A" ) );
$phplogin_templates->set( "loggedon", ( $victimdata[0]["loggedon"] == 1 ? "Yes" : "No" ) );
$phplogin_templates->set( "username", $victimdata[0]["username"] );

$phplogin_templates->display( "view_profile" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
