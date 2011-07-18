<?php

define( "RETURN_NULL", 0 );
define( "RETURN_RESULTS", 1 );  // Default.
define( "RETURN_AFFECTEDROWS", 2 );
define( "RETURN_NUMROWS", 3 );
define( "RETURN_OBJECT", 4 );

define( "SQL_AND", TRUE );
define( "SQL_OR", FALSE );

class phplogin_sql
{
	function __construct()
	{
		require( "config.phplogin.php" );
		
		$this->db = new mysqli( $phplogin_sql_host, $phplogin_sql_user, $phplogin_sql_pass, $phplogin_sql_db );
	}
	
	function query( $query, $params = array(), $returntype = 1 )
	{
		$stmt = $this->db->prepare( $query ) or die( "Error preparing SQL statement" );
		
		$args = array();
		$args[0] = NULL;
		foreach ( $params as $var => $val )
		{
			if ( is_numeric( $val ) )
			{
				$vartype = "i";
			}
			else
			{
				$vartype = "s";
			}
			
			$$var = $val;
			
			$args[0] .= $vartype;
			$args[] = $$var;
		}
		
		if ( !empty( $params ) )
		{
			call_user_func_array( array( $stmt, "bind_param" ), self::sanitize( $args ) );
		}
		
		$stmt->execute();
		
		switch ( $returntype )
		{
			default:
			case 0:
				$returnval = NULL;
				break;
			case 1:
				$returnval = self::fetch( $stmt );
				break;
			case 2:
				$returnval = $stmt->affected_rows;
				break;
			case 3:
				$stmt->store_result();
				$returnval = $stmt->num_rows();
				break;
			case 4:
				$returnval = $stmt;
				break;
		}
		
		$stmt->close();
		
		return $returnval;
	}
	
	/* Original function by fabio at kidopi dot com dot br, posted at http://www.php.net/manual/en/mysqli-stmt.bind-param.php.  --Kris */
	function sanitize( $arr )
	{
		if ( strnatcmp( phpversion(), "5.3" ) >= 0 )
		{
			$arr2 = array();
			
			foreach ( $arr as $arrkey => $arrval )
			{
				$arr2[$key] =& $arr[$key];
			}
			
			return $arr2;
		}
		else
		{
			return $arr;
		}
	}
	
	/* Original function by nieprzeklinaj at gmail dot com, posted at http://www.php.net/manual/en/mysqli-stmt.bind-result.php.  --Kris */
	function fetch( $result )
	{   
		$array = array();
		
		if ( $result instanceof mysqli_stmt )
		{
			$result->store_result();
			   
			$variables = array();
			$data = array();
			$meta = $result->result_metadata();
			   
			while ( $field = $meta->fetch_field() )
			{
				$variables[] = &$data[$field->name]; // pass by reference
			}
			   
			call_user_func_array( array( $result, "bind_result" ), $variables );
			   
			$i = 0;
			while( $result->fetch() )
			{
				$array[$i] = array();
				foreach ( $data as $k => $v )
				{
					$array[$i][$k] = $v;
				}
				
				$i++;
				
				// don't know why, but when I tried $array[] = $data, I got the same one result in all rows
			}
		}
		else if( $result instanceof mysqli_result )
		{
			while ( $row = $result->fetch_assoc() )
			{
				$array[] = $row;
			}
		}
			   
		return $array;
	}
	
	function close()
	{
		return $this->db->close();
	}
	
	function build_where_clause( $columns = array(), $values = array(), $and = TRUE )
	{
		if ( ( empty( $columns ) && empty( $values ) ) 
			|| count( $columns ) != count( $values ) )
		{
			return FALSE;
		}
		
		$andor = ( $and == TRUE ? "AND" : "OR" );
		
		$where = NULL;
		foreach ( $columns as $ckey => $col )
		{
			if ( $where != NULL )
			{
				$where .= " $andor ";
			}
			
			$where .= "$col = ?";
		}
		
		return array( 0 => $where, 1 => $values );
	}
}
