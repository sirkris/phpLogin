<?php

class phplogin_register
{
	function create_user( $username, $password, $email, $adminbypass = FALSE )
	{
		require( "config.phplogin.php" );
		require( "check.phplogin.class.php" );
		
		if ( $pwenc = self::encrypt_password( $password, $passhash ) == FALSE )
		{
			return array( "SUCCESS" => FALSE, "ERROR" => "The password encryption algorithm is not supported by the server!  Please report this to the webmaster ASAP!" );
		}
		
		$check = new phplogin_check();
		
		if ( $check->user_exists( array( "username", "email" ), array( $username, $email ), SQL_OR ) == TRUE )
		{
			return array( "SUCCESS" => FALSE, "ERROR" => "Username and/or email is already registered!" );
		}
		
		if ( $adminbypass == FALSE )
		{
			/* Disallow deceptive and malicious usernames.  --Kris */
			if ( $check->valid_username( $username ) == FALSE )
			{
				return array( "SUCCESS" => FALSE, "ERROR" => "That username is not allowed!  Please choose another." );
			}
			
			/* Validate the email address format.  --Kris */
			if ( $check->valid_email( $email ) == FALSE )
			{
				return array( "SUCCESS" => FALSE, "ERROR" => "Email address is not valid!" );
			}
			
			/* Validate the email hostname.  --Kris */
			if ( $requiredns == TRUE )
			{
				if ( $check->email_dns_exists( $email ) == FALSE )
				{
					return array( "SUCCESS" => FALSE, "ERROR" => "The domain for the email address could not be verified!" );
				}
			}
		}
		
		$sql = new phplogin_sql();
		
		return ( $sql->query( "insert into phplogin_users ( username, password, pwhash, email, registered ) values ( ?, ?, ?, ?, ? )", array( $username, $pwenc, $passhash, $email, time() ), RETURN_AFFECTEDROWS ) > 0 ? array( "SUCCESS" => TRUE ) : array( "SUCCESS" => FALSE, "ERROR" => "Insertion query failed!" ) );
	}
	
	function encrypt_password( $password, $hashtype )
	{
		/* First, make sure the specified protocol is supported by the PHP install.  --Kris */
		switch ( $hashtype )
		{
			default:
				return FALSE;
				break;
			case "md5":
				if ( !function_exists( "md5" ) )
				{
					return FALSE;
				}
				break;
			case "sha1":
				if ( !function_exists( "sha1" ) )
				{
					return FALSE;
				}
				break;
			case "sha256":
			case "sha512":
				if ( !function_exists( "hash" ) 
					|| in_array( $hashtype, hash_algos() ) == FALSE )
				{
					return FALSE;
				}
				break;
		}
		
		/* Return the encrypted password and we're done!  --Kris */
		switch ( $hashtype )
		{
			default:
				die( "ERROR!  Sanity check passed for encrypt_password but functionality for $passhash protocol not added!" );
				break;
			case "md5":
				return md5( $password );
				break;
			case "sha1":
				return sha1( $password );
				break;
			case "sha256":
			case "sha512":
				return hash( $hashtype, $password );
				break;
		}
	}
}
