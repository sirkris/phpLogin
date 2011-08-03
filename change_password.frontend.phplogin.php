<?php

require( "config.phplogin.php" );

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Change Password" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_changepasswordcontainerdiv"><?php

$phplogin_templates->set( "action", "change_password.backend.phplogin.php" );
$phplogin_templates->set( "submit", "Change Password" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );

if ( isset( $_GET["code"] ) )
{
	$oldpasswordcaption = NULL;
	$oldpasswordfield = NULL;
}
else
{
	$oldpasswordcaption = "<div class=\"phplogin_changepasswordrowdiv\">\r\nOld Password:\r\n</div>\r\n<br />\r\n";
	$oldpasswordfield = "<div class=\"phplogin_changepasswordrowdiv\">\r\n<input type=\"password\" name=\"phplogin_oldpassword\" id=\"phplogin_oldpassword\" />\r\n</div>\r\n<br />\r\n";
}

$phplogin_templates->set( "oldpasswordcaption", $oldpasswordcaption );
$phplogin_templates->set( "oldpasswordfield", $oldpasswordfield );

$phplogin_templates->display( "change_password" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
