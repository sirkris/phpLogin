<?php

/* 
 * Do not change anything in here unless you know what you're doing!
 * To configure phpLogin for an installation, edit config.phplogin.php.
 * 
 * --Kris
 */
require( "config.secure.phplogin.php" );
require_once( $phplogin_sql_server . ".phplogin.class.php" );
require_once( "authenticate.phplogin.class.php" );
require_once( "check.phplogin.class.php" );
require_once( "controller.phplogin.class.php" );
require_once( "encryption.phplogin.class.php" );
require_once( "model.phplogin.class.php" );
require_once( "register.phplogin.class.php" );
require_once( "session.phplogin.class.php" );
require_once( "templates.phplogin.class.php" );
require_once( "user.phplogin.class.php" );

if ( strcmp( substr( $phplogin_templates_dir, strlen( $phplogin_templates_dir ) - 1, 1 ), "/" ) )
{
	$phplogin_templates_dir .= "/";
}

/* The user status levels.  See status_codes.phplogin.txt for details.  --Kris */
$phplogin_statuslevels = array();
$phplogin_statuslevels[-2] = "RESERVED";
$phplogin_statuslevels[-1] = "BANNED";
$phplogin_statuslevels[0] = "PENDING";
$phplogin_statuslevels[1] = "ACTIVE";
$phplogin_statuslevels[2] = "MODERATOR";
$phplogin_statuslevels[3] = "ADMIN";
$phplogin_statuslevels[4] = "SUPERUSER";
