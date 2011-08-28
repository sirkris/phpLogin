var phplogin_dragging = false;
var phplogin_dragging_element;
var phplogin_dragging_element_xpos_start = 0;
var phplogin_dragging_element_ypos_start = 0;
var phplogin_dragging_mouse_xpos_start = 0;
var phplogin_dragging_mouse_ypos_start = 0;

function phplogin_getHTTPObject()
{
	if ( typeof XMLHttpRequest != 'undefined' )
	{
		return new XMLHttpRequest();
	}
	else
	{
		try
		{
			return new ActiveXObject( "Msxml2.XMLHTTP" );
		}
		catch ( e )
		{
			try
			{
				return new ActiveXObject( "Microsoft.XMLHTTP" );
			}
			catch( e )
			{
				return false;
			}
		}
	}
}

function phplogin_updateView( url, method, elementId, postData )
{
	if ( url == "NULL" )
	{
		document.getElementById( elementId ).innerHTML = "";
		phplogin_lightenpage();
		return;
	}
	else
	{
		phplogin_darkenpage();
	}
	
	var http = phplogin_getHTTPObject();
	
	http.open( method, url, true );
	http.setRequestHeader( "User-Agent", "XMLHTTP/1.0" );
	if ( postData != '' )
	{
		http.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
	}
	http.onreadystatechange = function()
	{
		if ( http.readyState == 4 )
		{
			document.getElementById( elementId ).innerHTML = http.responseText;
		}
	}
	http.send( postData );
}

function phplogin_loadTemplate( template, params )
{
	var baseurl = "view.phplogin.php?phplogin_template=";
	
	if ( template == '' )
	{
		try
		{
			template = document.getElementById( "phplogin_template" ).value;
		}
		catch ( e )
		{
			return false;
		}
	}
	
	if ( params != '' )
	{
		template += "&" + params;
	}
	
	phplogin_updateView( baseurl + template, "GET", "phplogin_viewerdiv", '' );
	
	return true;
}

function phplogin_sendForm( form )
{
	var postData = '';
	
	for ( i = 0; i < form.elements.length; i++ )
	{
		if ( postData != '' )
		{
			postData += "&";
		}
		
		postData += form.elements[i].name + "=" + encodeURIComponent( form.elements[i].value );
	}
	
	if ( postData != '' )
	{
		postData += "&";
	}
	
	postData += "phplogin_formid=" + encodeURIComponent( form.id );
	
	/* The controller will route the request based on the phplogin_formid that was sent.  --Kris */
	phplogin_updateView( "view.phplogin.php", "POST", "phplogin_viewerdiv", postData );
	
	return true;
}

function phplogin_sendEmptyDispatch( formid )
{
	return phplogin_updateView( "view.phplogin.php", "POST", "phplogin_viewerdiv", "phplogin_emptydispatch=1&phplogin_formid=" + formid );
}

function phplogin_refreshPage( delay )
{
	setTimeout( "location.reload( true );", delay );  // In milliseconds.  --Kris
}

function phplogin_darkenpage()
{
	document.getElementById( "phplogin_darkenbackground" ).style.zIndex = 9998;
	document.getElementById( "phplogin_darkenbackground" ).style.visibility = "visible";
}

function phplogin_lightenpage()
{
	document.getElementById( "phplogin_darkenbackground" ).style.zIndex = -9998;
	document.getElementById( "phplogin_darkenbackground" ).style.visibility = "hidden";
}

function phplogin_closeViewerFromBg()
{
	/* Hackish little trick to see if we should also do a refresh.  --Kris */
	if ( document.getElementById( "phplogin_viewerdiv" ).innerHTML.indexOf( "phplogin_refreshPage" ) !== -1 )
	{
		phplogin_refreshPage( 50 );
	}
	
	phplogin_updateView( 'NULL', '', 'phplogin_viewerdiv' );
}

/* Functions for clicking and dragging the window div.  Basically useless but awesome nonetheless.  --Kris */
function phplogin_mouse_xpos( event )
{
	/*if ( event.offsetX )
	{
		return event.offsetX;
	}
	else
	{
		return event.pageX;
	}*/
	
	return event.clientX;
}

function phplogin_mouse_ypos( event )
{
	/*if ( event.offsetY )
	{
		return event.offsetY;
	}
	else
	{
		return event.pageY;
	}*/
	
	return event.clientY;
}

function phplogin_mouseDown( event, element )
{
	phplogin_dragging = true;
	phplogin_dragging_element = element;
	document.getElementById( element ).style.cursor = "move";
	phplogin_dragging_mouse_xpos_start = phplogin_mouse_xpos( event );
	phplogin_dragging_mouse_ypos_start = phplogin_mouse_ypos( event );
	
	// TODO - This is due to us not knowing the pixel coords of the viewer div.  I think JQuery has solved this.  Will investigate later.  --Kris
	phplogin_dragging_element_xpos_start = phplogin_dragging_mouse_xpos_start;
	phplogin_dragging_element_ypos_start = phplogin_dragging_mouse_ypos_start;
	
	document.getElementById( element ).style.left = phplogin_dragging_element_xpos_start + "px";
	document.getElementById( element ).style.top = phplogin_dragging_element_ypos_start + "px";
}

function phplogin_mouseMove( event )
{
	if ( phplogin_dragging == true )
	{
		var xdiff = phplogin_mouse_xpos( event ) - phplogin_dragging_mouse_xpos_start;
		var ydiff = phplogin_mouse_ypos( event ) - phplogin_dragging_mouse_ypos_start;
		
		document.getElementById( phplogin_dragging_element ).style.left = (phplogin_dragging_element_xpos_start + xdiff) + "px";
		document.getElementById( phplogin_dragging_element ).style.top = (phplogin_dragging_element_ypos_start + ydiff) + "px";
		
		return false;
	}
}

function phplogin_mouseUp()
{
	if ( phplogin_dragging == true )
	{
		phplogin_dragging = false;
		
		document.getElementById( phplogin_dragging_element ).style.cursor = "auto";
		
		var xdiff = phplogin_mouse_xpos( event ) - phplogin_dragging_mouse_xpos_start;
		var ydiff = phplogin_mouse_ypos( event ) - phplogin_dragging_mouse_ypos_start;
		
		document.getElementById( phplogin_dragging_element ).style.left = (phplogin_dragging_element_xpos_start + xdiff) + "px";
		document.getElementById( phplogin_dragging_element ).style.top = (phplogin_dragging_element_ypos_start + ydiff) + "px";
	}
}
