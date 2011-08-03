<?php

require( "config.phplogin.php" );

$user = new phplogin_user();

/* If not authorized, throw an error.  --Kris */
if ( !isset( $user->userdata ) || !isset( $user->userdata["status"] ) || !isset( $user->userid ) || $user->userdata["status"] < 2 )
{
	die( "You are not authorized to view this page." );
}

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Manage Users" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_manageuserscontainerdiv"><?php

/* Load all users below the editor's status level.  --Kris */
$victimdata = array();
$victimdata = phplogin_user::load_data( "status < " . $user->userdata["status"] );

/* Initialize the variables in case there's no match.  --Kris */
foreach ( $victimdata[0] as $var => $ignore )
{
	$phplogin_templates->set( $var, NULL );
}

$userslist = "<select name=\"phplogin_userid\" id=\"phplogin_userid\" onChange=\"document.phplogin_pickuser.submit();\">\r\n";
$userslist .= "<option value=\"\">-- Select a User --</option>\r\n";

foreach ( $victimdata as $vkey => $victim )
{
	if ( isset( $_GET["phplogin_userid"] ) && $_GET["phplogin_userid"] == $victim["userid"] )
	{
		$sel = " selected=\"selected\"";
		
		/* This will make sure all columns retrieved from the database are available to the template designer.  --Kris */
		foreach ( $victim as $var => $val )
		{
			$phplogin_templates->set( $var, $val );
		}
		
		$selkey = $vkey;
	}
	else
	{
		$sel = NULL;
	}
	
	$userslist .= "<option value=\"" . $victim["userid"] . "\"" . $sel . ">" . $victim["username"] . "</option>\r\n";
}
$userslist .= "</select>\r\n";

$phplogin_templates->set( "action", "manage_users.backend.phplogin.php" );
$phplogin_templates->set( "action2", "#" );
$phplogin_templates->set( "submit", "Save Changes" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );
$phplogin_templates->set( "userslist", $userslist );

if ( $user->userdata["status"] < 4 )
{
	$phplogin_templates->set( "readonly", " readonly=\"readonly\"" );
}
else
{
	$phplogin_templates->set( "readonly", NULL );
}

if ( isset( $selkey ) )
{
	$phplogin_templates->set( "status", $phplogin_statuslevels[$victimdata[$selkey]["status"]] );
	$phplogin_templates->set( "registered", ( $victimdata[$selkey]["registered"] > 0 ? date( "F jS, Y", $victimdata[$selkey]["registered"] ) : "N/A" ) );
	$phplogin_templates->set( "loggedonsince", ( $victimdata[$selkey]["loggedonsince"] > 0 ? date( "F jS, Y @ h:i:s A (T)", $victimdata[$selkey]["loggedonsince"] ) : "N/A" ) );
	$phplogin_templates->set( "lastaction", ( $victimdata[$selkey]["lastaction"] > 0 ? date( "F jS, Y @ h:i:s A (T)", $victimdata[$selkey]["lastaction"] ) : "N/A" ) );
	$phplogin_templates->set( "loggedon", ( $victimdata[$selkey]["loggedon"] == 1 ? "Yes" : "No" ) );
}

$phplogin_templates->display( "manage_users" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
