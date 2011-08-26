<?php

/* This dispatch table defines what model functions can be called from form POST data.  Keys matched automatically for convenience.  --Kris */
$phplogin_dispatch = array
			( 
				"login", 
				"logout", 
				"register" 
			);

$phplogin_dispatch = array_combine( $phplogin_dispatch, $phplogin_dispatch );
