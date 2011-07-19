<?php

class phplogin_check
{
	function user_exists( $columns = array(), $values = array(), $and = TRUE )
	{
		require( "config.phplogin.php" );
		
		$sql = new phplogin_sql();
		$db = $sql->init();
		
		if ( !( $where = $sql->build_where_clause( $columns, $values, $and ) ) )
		{
			return FALSE;
		}
		
		return ( $sql->query( $db, "select userid from phplogin_users where " . $where[0], $where[1], RETURN_NUMROWS ) > 0 ? TRUE : FALSE );
	}
	
	function valid_email( $email )
	{
		return filter_var( $email, FILTER_VALIDATE_EMAIL );
	}
	
	function email_dns_exists( $email )
	{
		/* Modified from function posted by reportingsjr at gmail dot com at http://php.net/manual/en/function.checkdnsrr.php.  --Kris */
		if ( preg_match('/^\w[-.\w]*@(\w[-._\w]*\.[a-zA-Z]{2,}.*)$/', $email, $matches ) 
			&& function_exists( "checkdnsrr" ) )
		{
			return ( checkdnsrr( $matches[1] . ".", "MX" ) == FALSE && checkdnsrr( $matches[1] . ".", "MX" ) == FALSE ? FALSE : TRUE );
		}
		else
		{
			return self::valid_email( $email );
		}
	}
	
	function valid_username( $username )
	{
		$invalid = array();
		$invalid[] = "admin";
		$invalid[] = "administrator";
		$invalid[] = "webmaster";
		$invalid[] = "moderator";
		$invalid[] = "owner";
		$invalid[] = "root";
		$invalid[] = "sysadmin";
		$invalid[] = "sysop";
		$invalid[] = "system";
		$invalid[] = "guest";
		$invalid[] = "anonymous";
		
		if ( in_array( strtolower( $username ), $invalid ) )
		{
			return FALSE;
		}
		
		// TODO - Add more conditions here.  --Kris
		
		return TRUE;
	}
}
