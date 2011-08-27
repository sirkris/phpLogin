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
		return;
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
