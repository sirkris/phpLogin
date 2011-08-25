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

function phplogin_updateView( url, method, elementid )
{
	if ( url == "NULL" )
	{
		document.getElementById( elementid ).innerHTML = "";
		return;
	}
	
	var http = phplogin_getHTTPObject();
	
	http.open( method, url, true );
	http.onreadystatechange = function()
	{
		if ( http.readyState == 4 )
		{
			document.getElementById( elementid ).innerHTML = http.responseText;
		}
	}
	http.send( null );
}

function loadTemplate()
{
	var baseurl = "view.phplogin.php?phplogin_template=";
	
	phplogin_updateView( baseurl + document.templateform.s_template.value, "GET", "phplogin_viewerdiv" );
}
