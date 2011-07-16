<?php

class phplogin_sql
{
	function init()
	{
		require( "config.phplogin.php" );
		
		$link = mysql_connect( $sql_host, $sql_user, $sql_pass ) or die( "Unable to connect to MySQL : " . mysql_error() );
		
		mysql_select_db( $sql_db, $link ) or die( "Unable to connect to database : " . mysql_error() );
		
		return $link;
	}
	
	function query( $link, $query, $params = array(), $returntype = 1 )
	{
		/* Prepared statements aren't supported, so let's just cheat and convert them.  --Kris */
		if ( !empty( $params ) )
		{
			if ( substr_count( $query, "?" ) != count( $params ) )
			{
				die( "(php_mysql Conversion Error):  Number of variables doesn't match number of parameters in prepared statement." );
			}
			
			$parseloop = 0;
			$newquery = NULL;
			foreach ( $params as $pkey => $val )
			{
				for ( $parseloop = $parseloop; $parseloop < strlen( $query ); $parseloop ++ )
				{
					if ( strcmp( $query{$parseloop}, "?" ) == 0 )
					{
						if ( is_numeric( $val ) )
						{
							$newquery .= $val;
						}
						else
						{
							$newquery .= "'" . $val . "'";
						}
						
						$parseloop++;
						break;
					}
					else
					{
						$newquery .= $query{$parseloop};
					}
				}
			}
			
			for ( $parseloop = $parseloop; $parseloop < strlen( $query ); $parseloop++ )
			{
				$newquery .= $query{$parseloop};
			}
			
			$query = $newquery;
		}
		
		$result = mysql_query( $query ) or die( "Query '$query' failed : " . mysql_error() );
		
		switch ( $returntype )
		{
			default:
			case 0:
				$returnvar = NULL;
				break;
			case 1:
				$returnvar = self::fetch( $result );
				break;
			case 2:
				$returnvar = mysql_affected_rows();
				break;
			case 3:
				$returnvar = mysql_num_rows( $result );
				break;
			case 4:
				$returnvar = $result;
				break;
		}
		
		return $returnvar;
	}
	
	function fetch( $result )
	{
		$resdata = array();
		while ( $line = mysql_fetch_array( $result, MYSQL_ASSOC ) )
		{
			$tmp = array();
			$sqloop = 0;
			foreach ( $line as $col_value )
			{
				$fieldname = mysql_field_name( $result, $sqloop );
				if ( strcmp( intval( $col_value ), $col_value ) == 0 )
				{
					/* To mimic the mysqli behavior as closely as possible.  --Kris */
					$tmp[$fieldname] = (int) $col_value;
				}
				else
				{
					$tmp[$fieldname] = $col_value;
				}
				$sqloop++;
			}
			
			$resdata[] = $tmp;
		}
		
		return $resdata;
	}
	
	function close( $link )
	{
		return mysql_close( $link );
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
