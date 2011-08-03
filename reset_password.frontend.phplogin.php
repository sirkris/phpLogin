<?php

require( "config.phplogin.php" );

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Reset Password" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_resetpasswordcontainerdiv"><?php

$phplogin_templates->set( "action", "reset_password.backend.phplogin.php" );
$phplogin_templates->set( "submit", "Send Reset Link" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );

$phplogin_templates->display( "reset_password" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
