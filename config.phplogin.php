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

/* Do not modify or remove this!  --Kris */
require( "config.init.phplogin.php" );
