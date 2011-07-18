<?php

/* 
 * Do not change anything in here unless you know what you're doing!
 * To configure phpLogin for an installation, edit config.phplogin.php.
 * 
 * --Kris
 */
require( "config.secure.phplogin.php" );
require_once( $phplogin_sql_server . ".phplogin.class.php" );
require_once( "check.phplogin.class.php" );
require_once( "register.phplogin.class.php" );
require_once( "session.phplogin.class.php" );
require_once( "user.phplogin.class.php" );
