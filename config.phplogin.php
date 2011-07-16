<?php

/* Database settings.  --Kris */
$phplogin_sql_server = "mysqli";  // Currently supported:  mysql, mysqli

$phplogin_sql_host = "127.0.0.1";
$phplogin_sql_user = "kris";
$phplogin_sql_db = "phplogin";

/* Email validation settings.  --Kris */
$phplogin_requiredns = TRUE;

/* Password encryption method.  --Kris */
// Note - You can safely change this without deprecating any existing stored passwords.  --Kris
$phplogin_passhash = "sha512";  // Currently supported:  md5, sha1, sha256, sha512

/*
 * If enabled, this feature will cross-reference session data against the database on every 
 * user action.  This has the advantage of potentially allowing other users to see whether 
 * or not a user is idle/afk/etc, which can be very useful for gaming and social networking 
 * sites.  The disadvantage, however, is that this will create an additional load (albeit 
 * a tiny one) on the server, potentially causing increased lag.  If you're trying to 
 * maximize performance, it is recommended that you disable this feature.
 */
$phplogin_ping_db = TRUE;

/* Do not modify or remove this!  --Kris */
require( "config.init.phplogin.php" );
