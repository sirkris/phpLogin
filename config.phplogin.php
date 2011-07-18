<?php

/* Database settings.  --Kris */
$phplogin_sql_server = "mysqli";  // Currently supported:  mysql, mysqli

$phplogin_sql_host = "127.0.0.1";
$phplogin_sql_user = "kris";
$phplogin_sql_db = "phplogin";

/* Email validation settings.  --Kris */
$phplogin_requiredns = TRUE;

/* If enabled, new users will receive a confirmation email with a link required to activate their account (does not apply to Superuser).  --Kris */
$phplogin_activation_email = TRUE;

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
 * 
 * --Kris
 */
$phplogin_ping_db = TRUE;

/* How many consecutive failed login attempts on a user before image verification required (needs phpMeow or CAPTCHA).  --Kris */
$phplogin_failures_verif_threshold = 5;

/* How many consecutive failed login attempts on a user before that session is blocked from logging in.  --Kris */
$phplogin_failures_ban_threshold = 100;

/* If a session and/or IP is banned from logging in, the ban will expire in this many seconds.  --Kris */
$phplogin_failures_ban_timeout = pow( 60, 2 ) * 24;

/* If enabled, ban and verification thresholds will be cross-referenced against user's IP address.  Use with caution!  --Kris */
$phplogin_failures_use_ip = FALSE;

/* If the login wasn't referred by another page (i.e. the user directly hit login.phplogin.php), direct to this page on successful login.  --Kris */
$phplogin_default_redirect = "success.phplogin.php";

/* Number of seconds before session data must be repopulated from database.  Lower value = better security & slower performance.  --Kris */
$phplogin_session_repopulate = 300;

/*
 * If enabled, Moderators, Admins, and the Superuser will be redirected to the moderator/admin control panel upon successful 
 * login instead of the login referral page (if applicable).  This can be useful if the site you integrated phpLogin with 
 * doesn't contain a link to the admin control panel.  It is recommended you use this only if integrating an admin panel link 
 * into the site would not be practical.
 * 
 * --Kris
 */
$phplogin_admin_redirect = FALSE;

/* Do not modify or remove this!  --Kris */
require( "config.init.phplogin.php" );
