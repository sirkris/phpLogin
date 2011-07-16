<?php

/* Database settings.  --Kris */
$sql_server = "mysqli";  // Currently supported:  mysql, mysqli

$sql_host = "127.0.0.1";
$sql_user = "kris";
$sql_db = "phplogin";

/* Email validation settings.  --Kris */
$requiredns = TRUE;

/* Password encryption method.  --Kris */
// Note - You can safely change this without deprecating any existing stored passwords.  --Kris
$passhash = "sha512";  // Currently supported:  md5, sha1, sha256, sha512

/* Do not modify or remove this!  --Kris */
require( "config.init.phplogin.php" );
