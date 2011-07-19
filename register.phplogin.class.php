<?php

class phplogin_register
{
	function create_user( $username, $password, $email, $adminbypass = FALSE )
	{
		require( "config.phplogin.php" );
		
		if ( $pwenc = phplogin_encryption::encrypt_string( $password, $passhash ) == FALSE )
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
}
