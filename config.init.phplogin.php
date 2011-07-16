<?php

/* 
 * Do not change anything in here unless you know what you're doing!
 * To configure phpLogin for an installation, edit config.phplogin.php.
 * 
 * --Kris
 */
require( "config.secure.phplogin.php" );
require_once( $phplogin_sql_server . ".phplogin.class.php" );

define( "RETURN_NULL", 0 );
define( "RETURN_RESULTS", 1 );  // Default.
define( "RETURN_AFFECTEDROWS", 2 );
define( "RETURN_NUMROWS", 3 );
define( "RETURN_OBJECT", 4 );

define( "SQL_AND", TRUE );
define( "SQL_OR", FALSE );
