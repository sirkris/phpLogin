<?php

require( "config.phplogin.php" );

$phplogin_templates = new phplogin_templates();

$phplogin_templates->set( "title", "Register" );
$phplogin_templates->display( "header" );
$phplogin_templates->clear();

?><div class="phplogin_registercontainerdiv"><?php

$phplogin_templates->set( "action", "register.backend.phplogin.php" );
$phplogin_templates->set( "submit", "Submit Registration" );
$phplogin_templates->set( "errmsg", ( isset( $_POST["phplogin_errmsg"] ) ? $_POST["phplogin_errmsg"] : NULL ) );

/* If you want to dynamically add content to the registration form, use these.  --Kris */
$phplogin_templates->set( "addedcaptions", NULL );
$phplogin_templates->set( "addedinputs", NULL );

$phplogin_templates->display( "register" );

?></div><?php

$phplogin_templates->clear();
$phplogin_templates->display( "footer" );
