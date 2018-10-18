<?php

/*****************************************************
 RackForms 2
 Copyright 2008-2011 nicSoft
 
 License available at http://www.rackforms.com/license.php
 *****************************************************/

// Build 770
if(isset($db_type) && $db_type == 'mongodb'){
	
} else {

	// if we have no catalog (database) set we need to include the default config file from RackForms installation. 
	if(!isset($db_catalog) || $db_catalog == ''){ 
		// ...and only if not using an ODBC DSN
		if(!isset($dbdsn) || $dbdsn == ""){
			@include 'config.php';
		}
	}
	
}

if(!class_exists('Database')){
	
	class Database {
		
		public function write_log($content, $sql = "") {
				
			if(defined('RF_LOG_DATABASE_ERRORS') && RF_LOG_DATABASE_ERRORS == true) {
				
				$ts = date('c');
				
				@file_put_contents ( 'db_error_log.log', $ts . ' - ' . $content . "\n" . $sql . "\n", FILE_APPEND);
		
			}
			
			if(defined('RF_LOG_DATABASE_ERRORS_EMAIL') && RF_LOG_DATABASE_ERRORS_EMAIL != "") {
				
				mail(RF_LOG_DATABASE_ERRORS_EMAIL, "NOTICE - RackForms Database Error Logged.", $content);
				
			}
		
		}

		/**
		 * Creates a new PDOConnection object using connection vars
		 * Return the connection for use with a PDOStatement
		 *
		 * @return PDOConnection
		 */
		public function get_pdo_instance($debug = 0, $db_type, $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass){
			try{
				
				// Build 844 - Make socket optional.
				if($mysql_socket == ""){
					$dsn = "mysql:host=".$db_host.";dbname=".$db_catalog.";port=".$mysql_port;
				} else {
					$dsn = "mysql:host=".$db_host.";unix_socket=".$mysql_socket.";dbname=".$db_catalog;
				}
				
				// Build 704 - Set UTF-8 - Check CONST first as: http://bugs.php.net/bug.php?id=47224
				if(defined('PDO::MYSQL_ATTR_INIT_COMMAND')){
					$dbh = new PDO($dsn, $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				} else {
					$dbh = new PDO($dsn, $db_user, $db_pass);
				}
				
				$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			} catch (PDOException $e) {
				
				self::write_log($e->getMessage(), $db_catalog);
				
				// Build 575
				switch($debug){
					case 0 :
						echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
						exit(0);
						break;
					case 1 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$e->getMessage()}<br/><br/>";
						echo $debug_message;
						exit(0);
						break;
					case 2 :
						$help_message = '<br/>This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$e->getMessage()}<br/><br/>";
						echo $debug_message;
						echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
						exit(0);
						break;
				}
			}
			return $dbh;
		}
		
		public function get_mysqli_instance($debug = 0, $db_type, $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass){
			
			if($mysql_port == "")
				$mysql_port = null;
			
			$dbh = new mysqli($db_host, $db_user, $db_pass, $db_catalog, $mysql_port);
			
			// Build 704
			$dbh->set_charset("utf8");
			
			if ($dbh->connect_error) {
				
				self::write_log($dbh->connect_error, $db_catalog);
				
				switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$sql_message = '(' . $dbh->connect_errno . ') ' . $dbh->connect_error;
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$sql_message}<br/><br/>";
							echo $debug_message;
							exit(0);
							break;
						case 2 :
							$help_message = '<br/>This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$sql_message = '(' . $dbh->connect_errno . ') ' . $dbh->connect_error;
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$sql_message}<br/><br/>";
							echo $debug_message;
							exit(0);
							break;
					}
				}

			// return database connection object
			return $dbh;
		}
		
		public function get_mssql_instance($debug, $db_host, $db_catalog, $db_user, $db_pass){
			$conn = sqlsrv_connect($db_host, array( 
					'UID' => $db_user,
					'PWD' => $db_pass,
					'Database' => $db_catalog
				));
			if (!$conn) {
				
				self::write_log("SQL Server - Failed To Connect.", $db_catalog);
				
				switch($debug){
					case 0 :
						echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
						exit(0);
						break;
					case 1 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre>' . print_r(sqlsrv_errors(), true) . '</pre>';
						exit(0);
						break;
					case 2 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre>' . print_r(sqlsrv_errors(), true) . '</pre>';
						exit(0);
						break;
				}
			} else {
				return $conn;
			}
			
		}
		
		// http://php.net/manual/en/function.odbc-connect.php
		// http://www.php.net/manual/en/function.odbc-error.php
		
		// Be aware of 32 vs. 64-bit Datasources!
		// http://vijirajkumar.blogspot.com/2009/11/32-bit-driver-installation-for-64bit.html
		// http://support.microsoft.com/kb/942976
		// As your instance of PHP may be 32-bit, though on a 64-bit system, you need:
		// %systemdrive%\Windows\SysWoW64\Odbcad32.exe
		// The 64-bit version (I'm not making this up!) is in the 32-bit folder:
		// %systemdrive%\Windows\System32
		// This is a problem because by default, the verison that loads via Start > Administrative Tools > Datasources is the 64-bit version.
		// If you run 32-bit PHP, the two will not connect.
		public function get_odbc_instance($debug, $dbdsn, $db_user, $db_pass){
			
			$conn = odbc_connect($dbdsn, $db_user, $db_pass);
			
			if (!$conn) {
				
				self::write_log("ODBC - Failed To Connect.", $dbdsn);
				
				switch($debug){
					case 0 :
						echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
						exit(0);
						break;
					case 1 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre>' . print_r(odbc_errormsg (), true) . '</pre>';
						print '<pre>ODBC STATE: ' . print_r(odbc_error (), true) . '</pre>';
						exit(0);
						break;
					case 2 :
						$help_message = '<br/>This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre>' . print_r(odbc_errormsg (), true) . '</pre>';
						print '<pre>ODBC STATE: ' . print_r(odbc_error (), true) . '</pre>';
						exit(0);
						break;
				}
			} else {
				return $conn;
			}
			
		}
		
		
		/**
		 * Create Connection To PostgreSQL
		 *
		 * @param int $debug
		 * @param string $db_type
		 * @param string $mysql_socket
		 * @param string $mysql_port
		 * @param string $db_host
		 * @param string $db_catalog
		 * @param string $db_user
		 * @param string $db_pass
		 * @return $dbh
		 * 
		 * @since 714
		 * 
		 * Documentation: 
		 * http://www.php.net/manual/en/pgsql.examples-basic.php
		 * http://www.pgadmin.org/docs/dev/connect-error.html
		 */
		public function get_postgresql_instance($debug = 0, $db_type, $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass){
			
			// http://www.php.net/manual/en/function.pg-connect.php
			if($mysql_port != '') { $port = "port={$mysql_port}"; } else { $port = ""; }
			
			$dbh = pg_connect("host={$db_host} dbname={$db_catalog} user={$db_user} password={$db_pass} {$port} connect_timeout=15");
			
			if (!$dbh) {
				
				self::write_log(pg_last_error($dbh), $db_catalog);
				
				switch($debug){
					case 0 :
						echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
						exit(0);
						break;
					case 1 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$sql_message = '(' . '' . ') ' . pg_last_error($dbh);
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$sql_message}<br/><br/>";
						echo $debug_message;
						exit(0);
						break;
					case 2 :
						$help_message = '<br/>This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$sql_message = '(' . '' . ') ' . pg_last_error($dbh);
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$sql_message}<br/><br/>";
						echo $debug_message;
						exit(0);
						break;
				}
				
			}
				
			// http://www.php.net/manual/en/function.pg-set-client-encoding.php
			pg_set_client_encoding($dbh, "UTF8");

			// return database connection object
			return $dbh;
			
		}
		
		public function get_mongo_instance($debug = 0, $db_type = '', $socket, $db_port, $db_host, $db_catalog, $db_user, $db_pass){
			
			// http://docs.mongodb.org/manual/reference/connection-string/#connection-string-uri-format
			
			// [username:password@]host1[:port1][,host2[:port2],...[,hostN[:portN]]][/[database][?options]]
			
			if($db_port != '')
				$db_port = ":{$db_port}";
				
			if($db_host != '')
				$db_host = "@{$db_host}";
				
			if($db_catalog != '')
				$db_catalog = '/' . $db_catalog;
			
			// create dsn
			$dsn = "";
			
			if($db_user != '' && $db_pass != ''){
				$dsn = "mongodb://{$db_user}:{$db_pass}{$db_host}{$db_port}";
			} else {
				if($db_host != ''){
					$dsn = "mongodb://{$db_host}{$db_port}";
				}
			}
			
			// connect
			try {
				
				if($dsn == ''){
					$m = new MongoClient(); // cannot include blank string in constructor
				} else {
					$m = new MongoClient($dsn);
				}
				
				return $m;
				
			} catch(MongoConnectionException $e) {
				
				self::write_log($e->getMessage, $db_catalog);
				
				// MongoConnectionException Methods:
				// ( [0] => __construct [1] => getMessage [2] => getCode [3] => getFile [4] => getLine [5] => getTrace [6] => getPrevious [7] => getTraceAsString [8] => __toString ) 
				
				switch($debug){
					case 0 :
						echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
						exit(0);
						break;
					case 1 :
						$help_message = '<br/>This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre> MongoDB Error Code: ' . $e->getCode . '</pre>';
						print '<h2>Error Message</h2><pre>' . $e->getMessage . '</pre>';
						exit(0);
						break;
					case 2 :
						$help_message = '<br/>This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
						$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/>{$help_message}<br/><br/><b>Error Message:";
						print $debug_message;
						print '<pre> MongoDB Error Code' . $e->getCode . '</pre>';
						print '<h2>Error Message</h2><pre>' . $e->getMessage . '</pre>';
						print '<h2>Stack Trace</h2><pre>' . print_r($e->getTrace) . '</pre>';
						exit(0);
						break;
				}
			}
			
		}
	
		/**
		 * Perform an inline SQL Query, that is, one where we define the connection details on the fly. 
		 * We do not use/pass paramters for these queries. 
		 * 
		 * Used in:
		 * - Checkbox, Radio, and Select SQL Queries.
		 *
		 * @param int $debug
		 * @param string $sql
		 * @param string $db_host
		 * @param string $db_type
		 * @param string $mysql_socket
		 * @param string $mysql_port
		 * @param string $dbdsn
		 * @param string $db_user
		 * @param string $db_pass
		 * @param string $db_catalog
		 * @param string $return_true
		 * @return variable
		 */
		public function inline_pdo_query($debug, $sql, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $return_true = 0){
			
			// Build 710										
			$db_type = explode('_', $db_type);	
			
			if($db_type[0] == 'odbc'){
				
				$conn = $this->get_odbc_instance($debug, $dbdsn, $db_user, $db_pass);
				
				if(substr($sql, 0, 5) == 'call '){
					$sql = '{' . $sql . '}'; // mssql procedures via ODBC needs these braces
				}
				
				// EXECUTE :: http://www.php.net/manual/en/function.odbc-exec.php
				$result = odbc_exec($conn, $sql);
				
				if(!$result){
					
					self::write_log("ODBC Query Failed.", $sql);
					
					if($debug == 1 || $debug == 2){
						print '<pre>Database Execute Error:<br/><br/>' . print_r(odbc_errormsg(), true) . '</pre>';
						print '<pre>ODBC Code: <br/><br/>' . print_r(odbc_error(), true) . '</pre>';
						exit;
					} else {
						echo "Error in page.\n";
						exit;
					}
				}
				
				// PROCESS RESULTS
								
				$row_tmp = array();
				$row = array();
				
				while($rows = odbc_fetch_array($result)) {
					$row_tmp[] = $rows;
				}
				
				// create double index array to match all other drivers (ODBC has no native FETCH_BOTH)
				foreach($row_tmp as $idx=>$r){
					foreach($r as $idx_j=>$value){
						$row[$idx]["{$idx_j}"] = $value;
						$row[$idx][] = $value;
					}
				}
				
				odbc_free_result($result);
				
				odbc_close($conn);
				
				if($result && $return_true){
					return true;
				}
				
				return array(true, $row);

				
			} // odbc
			
			if($db_type[0] == 'mssql'){
				$conn = $this->get_mssql_instance($debug, $db_host, $db_catalog, $db_user, $db_pass);
				
				// mssql needs to have at least one parameter for queries, mysql doesn't
				if($db_type == 'mssql'){
					$val = 1;
					$params = array((int)$val);
					//$sql = '{' . $sql . '}'; // mssql needs these braces for stored procedures
					$stmt = sqlsrv_query($conn, $sql, $params);
				} else {
					$stmt = sqlsrv_query($conn, $sql);
				}
				
				if($stmt === false ){
					
					$results = print_r(sqlsrv_errors(), true);
					self::write_log($results, $sql);
					
					if($debug == 1 || $debug == 2){
						print '<pre>Database Error:<br/><br/>' . print_r(sqlsrv_errors(), true) . '</pre>';
						exit;
					} else {
						echo "Error in page.\n";
					}
				}
				
				// return without results
				if($stmt && $return_true){
					return true;
				}
				// return with results
				$row = array();
				while( $rows = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_BOTH)) {
					$row[] = $rows;
				}
				// clean up
				sqlsrv_free_stmt($stmt);
				sqlsrv_close($conn);
				return array(true, $row);
			}
			
			if($db_type[0] == 'mysql'){
	
				$dbh = $this->get_pdo_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				try {
					$result = $dbh->query($sql);
				} catch(PDOException $e) {
					if ($dbh->errorCode () != '00000') {
						
						self::write_log($e->getMessage(), $sql);
						
						switch($debug){
							case 0 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
								exit(0);
								break;
							case 1 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '</div>';
								exit(0);
							case 2 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '<b>Error Info: </b>' . implode ( ': ', $sth->errorInfo () ) . '<br/><br/>';
								echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
								echo '</div>';
								exit(0);
						}
					}
				}
				
				// no results
				if($result && $return_true){
					return true;
				}
				$row = $result->fetchAll(PDO::FETCH_BOTH);
				return array(true, $row);
			}
			
			if($db_type[0] == 'mysqli'){
				
				$dbh = $this->get_mysqli_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				// prepare statement
				$result = $dbh->real_query($sql);
				
				// check for errors
				if(!$result){
					
					self::write_log($dbh->error, $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo '</div>';
							break;
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo '<b>Error Info: </b>' . $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo "<b>SQL State:</b><pre>"; $dbh->sqlstate; echo '</pre><br/><br/></div>';
							echo '</div>';
							exit(0);
					}
					
				} // !result
				
				// return true
				if($result && $return_true){
					return true;
				}
				
				$rows = array();
				
				
				if($result !== false){
					
					if ($dbh->field_count) {
						
						// check for errors
					    if($dbh->error != ''){
					    	$error = $dbh->error;
					    }
					    
						// check for errno
					    if($dbh->errno != 0){
					    	$errno = $dbh->errno;
					    }
					    
						// Create a MYSQLI_BOTH array
						// http://us2.php.net/manual/en/mysqli-result.fetch-all.php
						// When Mysqlnd becomes more standarized mysqli_fetch_all() will be a cleaner solution
						
					    $result = $dbh->store_result();
					    
					    $field_cnt = $dbh->field_count;
					    
					    $colnames = array();
					    
					    while($colinfo = $result->fetch_field()){
					    	array_push($colnames, $colinfo->name);
					    }
					    
					    $index = 0;
					    
						while($row = $result->fetch_row()){
							
							for($i = 0; $i < $field_cnt; $i++){
								$rows[$index][$colnames[$i]] = $row[$i];
								$rows[$index][$i] = $row[$i];
							}
							
							// new row index
							$index++;
		
					    }
					    
					    // free result
					    $result->free_result();
					    
					    // close connection
					    $dbh->close();
					    
					    // reset object
					    $dbh = null;
					    
					}
					
				}
				
				// will be empty array for non select/show/describe calls
				return array(true, $rows);
			
			} // mysqli
			
			
			if($db_type[0] == 'postgresql'){
				
				$dbh = $this->get_postgresql_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);

				// execute statement
				$result = pg_query($sql);
				
				if(!$result){
					
					self::write_log(pg_last_error(), $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							$error_message = pg_last_error();
							echo $error_message . '<br/><br/>';
							echo '</div>';
							exit(0);
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							$error_message = pg_last_error();
							echo $error_message . '<br/><br/>';
							echo '</div>';
							exit(0);
					}
					
				}

				
				// Do not process results sets, as we do not have any via the $return_true var
				// $return_true is always set to 1 for SQL+ calls, as they are always inserts
				if($return_true) {
					return true;
				} else {
					$row = array();
					if($result) {
						
						while ($line = pg_fetch_array($result, null, PGSQL_BOTH)) {

						    $row[] = $line;

						}

						if(count($row) == 0){
							return false;
						} else {
							return array(true, $row);
						}
					}
				}
				
			} // if postgresql
			
		}

		
		/**
		 * Perform SQL Query
		 *
		 * @param int $debug
		 * @param string $sql
		 * @param string $db_host
		 * @param string $db_type
		 * @param string $mysql_socket
		 * @param string $mysql_port
		 * @param string $dsn
		 * @param string $db_user
		 * @param string $db_pass
		 * @param string $db_catalog
		 * @param array $params
		 * @param int $return_true
		 * @param int $return_last_insert_id
		 * @param int $fetch_mode
		 * @return array
		 * 
		 * Build 671 - Added $pass_nulls so Builder Updates do not fail on paging and other operations where we pass empty values.
		 * Build 700 - Added $builder_query To Allow MSSQL Queries To Use Null search logic. Defaults to False, but true on main Builder queries.
		 * Build 710 - Added ODBC Support
		 * Build 714 - Added PostgreSQL Support
		 * Build 753 - Added $ambiguous_culumn_name_warning Reference For Query Module Calls.
		 * Build 770 - Added $query_mongo_query_mode, 0 1 to say if it's a query or insert.
		 * Build 780 - Added $close_connection to support multiple temp table queries. 
		 */
		public function pdo_procedure_params($debug,
												$sql,
												$db_host,
												$db_type,
												$mysql_socket,
												$mysql_port,
												$dbdsn,
												$db_user,
												$db_pass,
												$db_catalog,
												$params = array(),
												$return_true = 0,
												$return_last_insert_id = 0,
												$fetch_mode = 4,
												$pass_nulls = true,
												$builder_query = false,
												&$ambiguous_culumn_name_warning = false, /* reference */
												$query_mongo_query_mode = 0,
												$mongo_collection = '',
												$close_connection = false){

			// Build 710										
			$db_type = explode('_', $db_type);		
									
			
			/////////////////////////////////////////////////
			//////////////ODBC
			/////////////////////////////////////////////////
			
			if($db_type[0] == 'odbc'){
				
				// JET Programming Resources:
				// http://msdn.microsoft.com/en-us/library/aa140011%28v=office.10%29.aspx
				// http://technet.microsoft.com/en-us/library/cc966377.aspx
				
				// Possible Enhancements:
				// http://jet.sourceforge.net/javadoc/jet/sql/RowSetDynaClass.html
				
				$use_prepared = true;
				
				// Build 778 - Bug Fix.
				if(isset($db_type[1]) && $db_type[1] == 'access'){
					$use_prepared = false;
				}
				
				$conn = $this->get_odbc_instance($debug, $dbdsn, $db_user, $db_pass);
				
				if(substr($sql, 0, 5) == 'call '){
					$sql = '{' . $sql . '}'; // mssql procedures via ODBC needs these braces
				} else {
					// Not supported for Access, so skip if so...
					if((int)$return_last_insert_id == 1 && $db_type[1] != 'access'){
						$sql .= "; select @@identity as id";
					}
				}
				
				// Add Default Param Values When Emptry String Passed (similar to MySQL $pass_nulls).
				$i = 0; // itterator for parameters
				foreach ($params as $idx=>$value){
					
					// Build 669 - Pass (null) for blank values
					if($value == '' || $value == "\r"){
						
						// Build 671 - We only pass nulls when allowed
						if($builder_query){ 
							$params[$idx] = null;
						}
						
					}
					$i++;
				}
				
				// PREPARE :: http://www.php.net/manual/en/function.odbc-prepare.php
				if($use_prepared){
					
					$stmt = odbc_prepare($conn, $sql);
					
					if($stmt === false ){
						
						$results = print_r(odbc_errormsg(), true);
						self::write_log($results, $sql);
						
						if($debug == 1 || $debug == 2){
							print '<pre>Database Prepare Error:<br/><br/>' . print_r(odbc_errormsg(), true) . '</pre>';
							print '<pre>ODBC Code: <br/><br/>' . print_r(odbc_error(), true) . '</pre>';
							exit;
						} else {
							echo "Error in page.\n";
							exit;
						}
					}
					
				} else {

					// manually replace(bind) parameters for access et al.
					if(substr_count($sql, '?') == count($params)){
						
						// replace & sanitize markers
						foreach($params as $idx=>$p){
							
							$pos = strpos($sql, '?');
	
							// sanitize
							$p = str_replace("'", "\'", $p);
							$p = str_replace("--", "", $p);
							
							// type 
							if(is_numeric($p)){
								$p = $p;
							} else if(is_string($p)){
								$p = "'{$p}'";						
							}
							
							$sql = substr_replace($sql, $p, $pos, 1);
							
						}
						
						//die($sql); // for debugging transformed sql
						
					} else {
						if($debug == 1 || $debug == 2){
							print '<pre>Database Execute Error:<br/><br/>Bound Parameters Do Not Match</pre>';
							exit;
						} else {
							echo "Error in page.\n";
							exit;
						}
					}
					
				} // use prepared
					
					
				// EXECUTE :: http://www.php.net/manual/en/function.odbc-execute.php
				
				if($use_prepared){
					
					$result = odbc_execute($stmt, $params);
					
				} else {
					
					$result = odbc_exec($conn, $sql);
					
				}
				
				
				if(!$result){
					
					$results = print_r(odbc_errormsg(), true);
					self::write_log($results, $sql);
					
					if($debug == 1 || $debug == 2){
						print '<pre>Database Execute Error:<br/><br/>' . print_r(odbc_errormsg(), true) . '</pre>';
						print '<pre>ODBC Code: <br/><br/>' . print_r(odbc_error(), true) . '</pre>';
						exit;
					} else {
						echo "Error in page.\n";
						exit;
					}
				}
				
				
				// PROCESS RESULTS
				
				// return last inserted row id
				if((int)$return_last_insert_id == 1){
					
					if($db_type[1] == 'access'){
						
						// run new query, as access cannot return multiple result sets (or run multiple queries)
						$sql = 'select @@identity as id';
						$result = odbc_exec($conn, $sql);
						
						while(odbc_fetch_row($result)){
							return(odbc_result($result, 'id'));
						}
						
						// TODO: Implement for other drivers...
						
					} else {
						//odbc_next_result($stmt);
						//odbc_fetch_row($stmt);
						//$insert_id = sqlsrv_get_field($stmt, 0);
						//return $insert_id;
					}
					
					//TODO: Implement Affected Rows.
					// http://php.net/manual/en/function.odbc-num-rows.php
				}
				
				$row_tmp = array();
				
				$row = array();
				
				if($use_prepared){
					
					while($rows = odbc_fetch_array($stmt)) {
						$row_tmp[] = $rows;
					}
				
					// create double index array to match all other drivers (ODBC has no native FETCH_BOTH)
					foreach($row_tmp as $idx=>$r){
						foreach($r as $idx_j=>$value){
							$row[$idx]["{$idx_j}"] = $value;
							$row[$idx][] = $value;
						}
					}
					
				} else { // non-prepared
					
					$num_fields = odbc_num_fields($result);
					
					$row_count = 0;
					
					while(odbc_fetch_row($result)){
						for($i = 1; $i <= $num_fields; $i++){ // fields start at 1, just to be confusing : )
							$field_name = odbc_field_name($result, $i);
							$field_value = odbc_result($result, $i);
							$row[$row_count]["{$field_name}"] = $field_value;
							$row[$row_count][] = $field_value;
						}
						$row_count++;
					}
						
				}
				
				if(!$use_prepared){
					odbc_free_result($result); // only used with odbc_exec()
				}
				
				// Build 780
				if(!isset($close_connection) || $close_connection == "")
					$close_connection = false;
				
				if($close_connection)
					odbc_close($conn);
				
				if($use_prepared){
					
					if($stmt && $return_true){
						return true;
					}
					
				} else {
					
					if($result && $return_true){
						return true;
					}
					
				}
				
				if(count($row) == 0){
					return false;
				} else {
					return $row;
				}

				
			} // odbc
			
			
			
			/////////////////////////////////////////////////
			//////////////MSSQL
			/////////////////////////////////////////////////
			
			// Documentation
			// http://sqlsrvphp.codeplex.com/	
			// http://msdn.microsoft.com/en-us/library/cc296183.aspx
												
			if($db_type[0] == 'mssql'){
				
				$conn = $this->get_mssql_instance($debug, $db_host, $db_catalog, $db_user, $db_pass);
				
				if(substr($sql, 0, 5) == 'call '){
					$sql = '{' . $sql . '}'; // mssql procedures via ODBC needs these braces
				} else {
					// Build 648 - get last insert_id() by appending sql to query
					if((int)$return_last_insert_id == 1){
						$sql .= "; select scope_identity() as id";
					}
				}
				
				
				// Build 700 - Add Default Param Values When Emptry String Passed (similar to MySQL $pass_nulls).
				$i = 0; // itterator for parameters
				foreach ($params as $idx=>$value){
					// Build 669 - Pass (null) for blank values
					if(is_string($value) && $value == '' || $value == "\r"){
						// Build 671 - We only pass nulls when allowed
						// If this is a builder query, we crash on conversion errors if field is not set to NULL
						// Build 841 - NULLS when $pass_nulls
						if($builder_query || $pass_nulls){ 
							$params[$idx] = null;
						}
					}
					$i++;
				}
				
				
				$integer_indexed_array = array();
				
				// Build 773 - Make sure indexes are always integers, as the version 3 driver breaks without this requirement being met.
				foreach ($params as $idx=>$value){
					$integer_indexed_array[] = $value;
				}
				
				/* Disable warnings as errors behavior. Needed for Proc's that use PRINT statement. */
				
				sqlsrv_configure("WarningsReturnAsErrors", 0);
				
				
				$stmt = sqlsrv_query($conn, $sql, $integer_indexed_array);
				
				if($stmt === false ){
					
					$results = print_r(sqlsrv_errors(), true);
					self::write_log($results, $sql);
					
					if($debug == 1 || $debug == 2){
						print '<pre>Database Error:<br/><br/>' . print_r(sqlsrv_errors(), true) . '</pre>';
						exit;
					} else {
						echo "Error in page.\n";
						exit;
					}
				}
				
				// Build 648 - We now support lastinsert_id for MSSQL
				if((int)$return_last_insert_id == 1){
					
					sqlsrv_next_result($stmt);
					sqlsrv_fetch($stmt);
					$insert_id = sqlsrv_get_field($stmt, 0);
					
					// Build 880 - if 0, atempt to return affected rows.
					// http://php.net/manual/en/function.sqlsrv-rows-affected.php
					
					if($insert_id == 0) {
						
						$rows_affected = sqlsrv_rows_affected($stmt);
						
						if($rows_affected === false) {
							
							self::write_log($results, $sql);
							
							if($debug == 1 || $debug == 2){
								print '<pre>Database Affected Rows Error:<br/><br/>' . print_r(sqlsrv_errors(), true) . '</pre>';
								exit;
							} else {
								echo "Error in page.\n";
								exit;
							}
							
						} elseif($rows_affected == -1) {
							
							return -1;
							
						} else {
							
							return $rows_affected;
							
						}
						
						
					} else {
						
						return $insert_id;
						
					}
					
					
				}
				
				$row = array();
				
				// Build 695 - Changed To SQLSRV_FETCH_BOTH To Match MySQL Fetch Mode.
				while( $rows = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_BOTH)) {
					$row[] = $rows;
				}
				
				sqlsrv_free_stmt($stmt);
				
				// Build 780
				if(!isset($close_connection) || $close_connection == "")
					$close_connection = false;
				
				if($close_connection)
					sqlsrv_close($conn);

				if($stmt && $return_true){
					return true;
				}
				if(count($row) == 0){
					return false;
				} else {
					return $row;
				}
				
			} // if mssql
			
			
			/////////////////////////////////////////////////
			//////////////PDO
			/////////////////////////////////////////////////
			
			if($db_type[0] == 'mysql'){
				
				// Build 714 - $db_type is now an array, so we must pass the 0 element as it's used in the dsn string.
				$dbh = $this->get_pdo_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				// Build 655 - Non PDO users will not have PDO:: in default argument call, we set here instead.
				$fetch_mode = PDO::FETCH_BOTH;
			
				try{
					$sth = $dbh->prepare ($sql);
				} catch(PDOException $e){
					
					self::write_log($e->getMessage(), $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							$help_message = 'This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>=Database Error Page=</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$e->getMessage()}<br/><br/>";
							echo $debug_message;
							exit(0);
							break;
						case 2 :
							$help_message = 'This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>=Database Error Page=</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$e->getMessage()}<br/><br/>";
							echo $debug_message;
							echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
							exit(0);
							break;
					}
					//print "Error!: " . $e->getTrace() . $e->getTraceAsString() . "<br/>";
				}
				if ($dbh->errorCode () != '00000') {
					
					self::write_log($e->getMessage(), $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							echo $e->getMessage() . '<br/><br/>';
							echo implode ( ': ', $dbh->errorInfo ());
							echo '</div>';
							exit(0);
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							echo $e->getMessage() . '<br/><br/>';
							echo implode ( ': ', $dbh->errorInfo ());
							echo '</div>';
							exit(0);
					}
				}
				$i = 1; // itterator for parameters
				foreach ($params as $value){ //$key=>$value for mysqli with parameter names
					
				$type = PDO::PARAM_STR; // default to string
				
				if(is_string($value)){
					$type = PDO::PARAM_STR;
				}
				if(is_double($value)){
					$type = PDO::PARAM_INT;
				}
				if(is_int($value)){
					$type = PDO::PARAM_INT;
				}
				// Build 669 - Pass (null) for blank values
				// Build 866 - Added is_string check, as 0's evaluate to $value == '' for strings.
				if(is_string($value) && $value == ''){
					$type = PDO::PARAM_NULL;
					// Build 671 - We only pass nulls when allowed
					if($pass_nulls){ 
						$value = null;
					}
				}
					

					$sth->bindValue ($i, $value, $type);

					$i++;
				}
				// execute statement
				try{
					$result = $sth->execute();
				} catch(PDOException $e){
					
					self::write_log($e->getMessage(), $sql);
					
					// database object errors
					if ($dbh->errorCode () != '00000') {
						switch($debug){
							case 0 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
								exit(0);
								break;
							case 1 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '</div>';
								exit(0);
							case 2 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '<b>Error Info: </b>' . implode ( ': ', $sth->errorInfo () ) . '<br/><br/>';
								echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
								echo '</div>';
								exit(0);
						}
					}
					// statement errors
					if ($sth->errorCode () != '00000') {
						
						self::write_log($e->getMessage(), $sql);
						
						switch($debug){
							case 0 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
								exit(0);
								break;
							case 1 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '</div>';
								exit(0);
							case 2 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
								echo $e->getMessage() . '<br/><br/>';
								echo '<b>Error Info: </b>' . implode ( ': ', $sth->errorInfo () ) . '<br/><br/>';
								echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
								echo '</div>';
								exit(0);
						}
					}
					
				}
				
				// Return last insert id
				if($return_last_insert_id){
					
					// Build 880 - if 0, return affected rows.
					// http://php.net/manual/en/pdostatement.rowcount.php
					
					if($dbh->lastInsertId() == 0) {
						
						return $sth->rowCount();
						
					} else {
						
						return $dbh->lastInsertId();
						
					}
					
				}
				
				// Do not process results sets, as we do not have any via the $return_true var
				// $return_true is always set to 1 for SQL+ calls, as they are always inserts
				if($return_true) {
					return true;
				} else {
					$row = array();
					if($result) {
						try{
							// Build 640 - Allow custom fetch mode as passed in to the main function
							$row = $sth->fetchAll($fetch_mode);
						} catch (PDOException $e) {
							
							self::write_log($e->getMessage(), $sql);
							
							switch($debug){
								case 0 :
									// no error for null result sets
									break;
								case 1 :
									echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page (325)</b><br/><br/>";
									echo $e->getMessage() . '<br/><br/>';
									echo '</div>';
									exit(0);
								case 2 :
									echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page (325)</b><br/><br/>";
									echo $e->getMessage() . '<br/><br/>';
									echo '<b>Error Info: </b>' . implode ( ': ', $sth->errorInfo () ) . '<br/><br/>';
									echo "<b>Error Trace:</b><pre>"; print_r($e->getTrace()); echo '</pre><br/><br/></div>';
									echo '</div>';
									exit(0);
							}
							
						}
						if(count($row) == 0){
							return false;
						} else {
							return $row;
						}
					}
				}
			} // if mysql
			
			
			
			/////////////////////////////////////////////////
			//////////////MYSQLI
			/////////////////////////////////////////////////
			
			if($db_type[0] == 'mysqli'){
				
				// Build 688 - Disable MySQL Warnings.
				// http://www.php.net/manual/en/function.mysqli-report.php
				mysqli_report(MYSQLI_REPORT_OFF);
				
				// get instance
				$dbh = $this->get_mysqli_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				// prepare statement
				$stmt = $dbh->prepare($sql);
				
				// check for statement errors
				if(!$stmt){
					
					self::write_log($dbh->error, $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo '</div>';
							exit(0);
							break;
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo '<b>Error Info: </b>' . $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo "<b>SQL State:</b><pre>"; $dbh->sqlstate; echo '</pre><br/><br/></div>';
							echo '</div>';
							exit(0);
							break;
					}
					
				} 
				
				// add param values
				$i = 0; // itterator for parameters
				$types = '';
				foreach ($params as $idx=>$value){
					$type = 's'; // default to string
					if(is_string($value)){
						$type = 's';
					}
					if(is_double($value)){
						$type = 'd';
					}
					if(is_int($value)){
						$type = 'i';
					}
					// Build 669 - Pass (null) for blank values
					// Build 866 - Added is_string check, as 0's evaluate to $value == '' for strings.
					if(is_string($value) && $value == ''){
						$type = 's';
						// Build 671 - We only pass nulls when allowed
						if($pass_nulls){ 
							$params[$idx] = null;
						}
					}
					$types .= $type;
					$i++;
				}
				
				
				// bind params
				$param = array();
				foreach($params as $value){
					array_push($param, $value);
					//$param[] = $value;
				}
				
				// Must pass paramters as references, the code below creates variables at run time to allow for this
			    $bind_names[] = $types;
			    for ($i=0; $i<count($param);$i++) {
			    	$bind_name = 'bind' . $i;
			      	// create a variable out of a named item
			      	$$bind_name = $param[$i];
			      	$bind_names[] = &$$bind_name;
			    }
			    
			    // only process if we have params to avoid warning messages
			    if(count($params) != 0){
			    	
			    	if(!call_user_func_array(array($stmt,'bind_param'),$bind_names)){
			    		
			    		// Build 825
			    		if(isset($debug) && $debug != 0){
			    			
			    			self::write_log("Database Parameter Bind Error For Statement: {$sql}", $db_catalog);
			    			
			    			echo '<pre>Database Parameter Bind Error. This usually means we have to many or too few parametsrs in our prepared statement.';
			    			echo '<br><br>We want to make sure the number of question marks in the \'SQL Code\' text area';
			    			echo '<br>matches the number of variables in the \'Variables\' text area.';
			    			echo '</pre>';
			    			
			    		}
			    		
			    		
			    	}
			    	
			    }
		    
				// Build 724 - check for errors on execute
                $success = $stmt->execute();
                
                if(!$success){
                	
                	self::write_log($dbh->error, $sql);
                	
	                switch($debug){
	                    case 0 :
		                    echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
		                    exit(0);
		                    break;
	                    case 1 :
		                    echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
		                    echo $dbh->errno . ": " . $dbh->error . '<br/><br/>';
		                    echo '</div>';
		                    exit(0);
		                    break;
	                    case 2 :
		                    echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
		                    echo '<b>Error Info: </b>' . $dbh->errno . ": " . $dbh->error . '<br/><br/>';
		                    echo "<b>SQL State:</b><pre>"; $dbh->sqlstate; echo '</pre><br/><br/></div>';
		                    echo '</div>';
		            		exit(0);
		        	}
                }
			    
				
				// needed for images (seems to slow down debugger a lot) find a more efficient way?
				$stmt->store_result();
				
				// check for execute errors
				if(!$stmt){
					
					self::write_log($dbh->error, $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo '</div>';
							exit(0);
							break;
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
							echo '<b>Error Info: </b>' . $dbh->errno . ": " . $dbh->error . '<br/><br/>';
							echo "<b>SQL State:</b><pre>"; $dbh->sqlstate; echo '</pre><br/><br/></div>';
							echo '</div>';
							exit(0);
					}
					
				} 
				
				
				// Return last_insert_id
				if($return_last_insert_id){
					
					// Build 880 - If 0, return affected rows.
					// http://php.net/manual/en/mysqli.affected-rows.php
					
					if($dbh->insert_id == 0) {
						
						return $dbh->affected_rows;
						
					} else {
						
						return $dbh->insert_id;
						
					}
					
					
				}
				
				// get meta fields to build result rows
				$result = $stmt->result_metadata();
				
				$results = array(); // Build 735 - Moved above create call.
				
				$ambiguous_culumn_name_warning = false;
				
				// If we have a result that means our query returned rows, if not, close and return
				// $stmt->result_metadata() returns false if no rows present
				if($result){
				
			        $fields = array();
			        
			        while ($field = $result->fetch_field()) { 
			            
			        	$name = $field->name; 
			            
			            // Build 753 - does this field key already exist?
			            if(array_key_exists($name, $fields)){
			            	
			            	$ambiguous_culumn_name_warning = true;
			            	
			            	// if so, we must index the column name to avoid creating ambiguous (duplicate) column names.
			            	
			            	$done = false;
			            	$index = 1;
			            	
			            	while(!$done){
			            		
			            		$new_name = $name . $index;
			            		
			            		if(!in_array($new_name, $fields)){
			            			$fields[$new_name] = &$$new_name;
			            			$done = true;
			            		} else {
			            			$index++;
			            		}
			            	}           	

			            } else{
			            
			            	// standard insert
			            	$fields[$name] = &$$name; 
			            	
			            }
			        } 
			        
			        array_unshift($fields, $stmt); 
			        
			        // Build 753 - We now check this call for errors.
			        $bind = call_user_func_array('mysqli_stmt_bind_result', $fields); 
			        
			        // this error usually means the number of bind variables doesn't match number of fields in prepared statement
			        if($bind == false){
			        	
			        	self::write_log($dbh->error, $sql);
			        	
			        	// Only supported in PHP 5.2 or higher.
				        if (version_compare ( PHP_VERSION, "5.2.0", ">=" )) {
							$error = error_get_last();
						} else {
							$error['message'] = 'The number of bind variables doesn\'t match number of fields in prepared statement';
						}

				        switch($debug){
							case 0 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
								exit(0);
								break;
							case 1 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
								echo $error['message'] . '<br/><br/>';
								echo '</div>';
								exit(0);
								break;
							case 2 :
								echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page.</b><br/><br/>";
								echo '<b>Error Info: </b>' . $error['message'] . " " . $dbh->error . '<br/><br/>';
								echo "<b>SQL State (usualyl blank for these errors):</b><pre>"; $dbh->sqlstate; echo '</pre><br/><br/></div>';
								echo '</div>';
								exit(0);
						}
			        }
			
			        array_shift($fields); 
			        
			        $index = 0;
			        
			        while ($stmt->fetch()) { 
			            $temp = array(); 
			            foreach($fields as $key => $val) {
			            	
			            	// key based
			            	$temp[$key] = $val;
			            	
			            	// index based
			            	$temp[$index] = $val;
			            	
			            	$index++;
			            }
			            // each result row gets new index
			            $index = 0;
			            array_push($results, $temp); 
			        } 
			        
				} // if result
		        
		        
				// Build 780
				if(!isset($close_connection) || $close_connection == "")
					$close_connection = false;
				
				// Always free result and close the statement, but connection can remain open if specified.
				$stmt->free_result();
				$stmt->close();
				
				if($close_connection){
					$dbh->close();
					
					// reset object
					$dbh = null;
				}
			

				if($return_true){
					return array(true);		
				}
		        
				
				if(count($results) == 0){
					return false;
				} else {
					return $results;
				}
			
			} // if mysqli
			
			
			
			/////////////////////////////////////////////////
			//////////////POSTGRESQL
			/////////////////////////////////////////////////
			
			// Documentation:
			// http://php.net/manual/en/book.pgsql.php
			// http://pointbeing.net/weblog/2008/03/mysql-versus-postgresql-adding-an-auto-increment-column-to-a-table.html
			
			if($db_type[0] == 'postgresql'){
				
				$dbh = $this->get_postgresql_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				$fetch_mode = PGSQL_ASSOC;
						
				
				// Replace RackForms Question Marks With: $1, $2 [...]
				
				$param_ct = substr_count($sql, '?');
	
				$ct = 1;
				
				for($i = 0; $i < $param_ct; $i++){
					$start = strpos($sql, '?');
					$sql = substr_replace($sql, '$'.$ct, $start, 1);
					$ct++;
				}
				

				// http://www.php.net/manual/en/function.pg-prepare.php
				if(!pg_prepare($dbh, "", $sql)){
					
					self::write_log(pg_last_error(), $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							$help_message = 'This message appears because you have $error=1; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$error_message = pg_last_error();
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>=Database Error Page=</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$error_message}<br/><br/>";
							echo $debug_message;
							exit(0);
							break;
						case 2 :
							$help_message = 'This message appears because you have $error=2; set in your processing page.<br/>Be sure to set this back to $error=0; in a production environment.';
							$error_message = pg_last_error();
							$debug_message = "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>=Database Error Page=</b><br/>{$help_message}<br/><br/><b>Error Message: </b>{$error_message}<br/><br/>";
							echo $debug_message;
							exit(0);
							break;
					}
				}
				

				
				// http://www.php.net/manual/en/function.pg-execute.php
				$result = pg_execute($dbh, "", $params);
				
				if(!$result){
					
					self::write_log(pg_last_error(), $sql);
					
					switch($debug){
						case 0 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Application Error Page.</b><br/><br/><b>Form Creators --  You can enable debugging for this item in the 'SQL Debug Mode' select item.</b><br/><br/></div>";
							exit(0);
							break;
						case 1 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							$error_message = pg_last_error();
							echo $error_message . '<br/><br/>';
							echo '</div>';
							exit(0);
						case 2 :
							echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\"><b>Database Error Page</b><br/><br/>";
							$error_message = pg_last_error();
							echo $error_message . '<br/><br/>';
							echo '</div>';
							exit(0);
					}
					
				}
				
				
				// Build 780
				if(!isset($close_connection) || $close_connection == "")
					$close_connection = false;
				
				if($close_connection){
					pg_close($dbh);
				}

				
				// Return Last Increment Value (table must have sequence defined)
				// http://php.net/manual/en/function.pg-last-oid.php
				// http://www.postgresql.org/docs/8.4/interactive/functions-sequence.html
				if((int)$return_last_insert_id == 1){
					
					$sql = "select LASTVAL() as id";
					
					$result_last = pg_query($sql);
					$row = pg_fetch_array($result_last);
					
					// Build 880 - If 0, return affected rows.
					// http://php.net/manual/en/function.pg-affected-rows.php
					
					if($row['id'] == 0) {
						
						return pg_affected_rows($result);
						
					} else {
						
						return $row['id'];
						
					}
					
								
				
				}
				
				// Do not process results sets, as we do not have any via the $return_true var
				// $return_true is always set to 1 for SQL+ calls, as they are always inserts
				if($return_true) {
					return true;
				} else {
					$row = array();
					if($result) {
						
						while ($line = pg_fetch_array($result, null, PGSQL_BOTH)) {

						    $row[] = $line;

						}
						
						// http://www.php.net/manual/en/function.pg-free-result.php
						pg_free_result($result);

						if(count($row) == 0){
							return false;
						} else {
							return $row;
						}
					}
				}
				
			} // if postgresql
			
			
			
			/////////////////////////////////////////////////
			//////////////MONGO
			/////////////////////////////////////////////////
			
			if($db_type[0] == 'mongodb'){
				
				$e_message = ''; 
				
				// map names
				$query = $sql;
				
				
				$m = $this->get_mongo_instance($debug, $db_type[0], $mysql_socket, $mysql_port, $db_host, $db_catalog, $db_user, $db_pass);
				
				// Parse Query
				$collection = $mongo_collection;
				
				// if no collection or query, return
				if($collection == '')
					$e_message .= 'You must provide a collection name.';
					
					
				// Process any errors so far...
				if($e_message != ''){
					
					echo $e_message;
					exit(0);
					
				}
				
				
				// Select Database
				// http://www.php.net/manual/en/mongoclient.selectdb.php
				try {
					$db = $m->selectDB($db_catalog);	
				} catch(Exception $e){
					$e_message .= $e->getMessage();
				}
				
				// Select Collection
				// http://www.php.net/manual/en/mongodb.selectcollection.php
				try {
					$collection = $db->selectCollection($collection);	
				} catch(Exception $e){
					$e_message .= $e->getMessage();
				}
				
				switch((int)$query_mongo_query_mode){
					
					case 0 :
				
						// Query Collection
						// http://www.php.net/manual/en/mongocollection.find.php
						
						if($query == ""){
							
							$cursor = $collection->find();
							
						} else {
							
							$cursor = $collection->find($query);
							
						}
						
						
						if($return_true) {
							
							return true;
						
						} else {
							
							$row = array();
							
							if($cursor) {
								
								// http://www.php.net/manual/en/class.mongocursor.php
								
								// iterate through the results
								foreach ($cursor as $document) {
									$row[] = $document;
								}
		
								if(count($row) == 0){
									return false;
								} else {
									return array($row, $cursor);
								}
							}
						}
						break;
					
					case 1 :
						
						// Write to Collection
						// http://www.php.net/manual/en/mongocollection.insert.php

						$collection->insert($query);
						
						// http://stackoverflow.com/questions/4525556/mongodb-php-get-id-of-new-document
						if((int)$return_last_insert_id == 1){
							
							return $query['_id'];					
						
						}
						break;
						
					case 2 :
						
						// Update One Collection Record
						// http://www.php.net/manual/en/mongocollection.update.php
						
						$collection->update($query, $params, array("multiple" => false));
						
						break;
						
					case 3 :
						
						// Update All Collection Records
						// http://www.php.net/manual/en/mongocollection.update.php
						
						$collection->update($query, $params, array("multiple" => true));
						
						break;
						
					case 4 :
						
						// Remove One Collection Record
						// http://php.net/manual/en/mongocollection.remove.php
						
						$collection->remove($query, array("multiple" => false));
						
						break;
						
						
					case 5 :
						
						// Remove All Matching Collection Records
						// http://php.net/manual/en/mongocollection.remove.php
						
						$collection->remove($query, array("multiple" => true));
						
						break;
						
							
					case 6 :
					
						// Drop Entire Collection
						// http://www.php.net/manual/en/mongocollection.drop.php
						
						$collection->drop();
						
					
						break;
						
				} // action switch
				
				
			}


			
		} // function wrapper
		
		
		/**
		 * Query SQL Meta Fields
		 * This is used for Builder when we click the token chooser link.
		 * 
		 * Build 695 - Added Support For SQLSRV Extension.
		 * Build 705 - Translate Database Type Values.
		 * Build 710 - Added member support for Utility_Methods.php - However, this also means the caller MUST include that file! 
		 * 
		 * Build 740 - Fetch 'flags' data for Primary Key.
		 * The trick here is we create a comma delimited list and then use index based on a split() operation in JS code.
		 * Thus, if we just append this guy to the list we should not cause a problem for existing code.
		 * 
		 * Each vendor presents the data in specific ways. 
		 * 
		 * For the fb_demo table the id columns gets us:
		 * 
		 * PDO -- http://php.net/manual/en/pdostatement.getcolumnmeta.php (experimental extension!)
		 * a call to getColumnMeta() shows:
		 * flags[]
		 * 	[0] not_null
		 * 	[1] primary_key
		 * 
		 * However, if no flags are present the array is empty. It will also contain other info, like blob for blob fields.
		 * 
		 * 
		 * 
		 * MySQLi -- http://php.net/manual/en/mysqli-result.fetch-fields.php
		 * a call to: $metadata->fetch_fields(); shows:
		 * flags 49667
		 * 
		 * http://php.net/manual/en/mysqli-result.fetch-fields.php
		 * Which tells us: An integer representing the bit-flags for the field.
		 * 
		 * NOT_NULL_FLAG = 1                                                                             
	     * PRI_KEY_FLAG = 2                                                                              
	     * UNIQUE_KEY_FLAG = 4                                                                           
	     * BLOB_FLAG = 16                                                                                
	     * UNSIGNED_FLAG = 32                                                                            
	     * ZEROFILL_FLAG = 64                                                                            
	     * BINARY_FLAG = 128                                                                             
	     * ENUM_FLAG = 256                                                                               
	     * AUTO_INCREMENT_FLAG = 512                                                                     
	     * TIMESTAMP_FLAG = 1024                                                                         
	     * SET_FLAG = 2048                                                                               
	     * NUM_FLAG = 32768                                                                              
	     * PART_KEY_FLAG = 16384                                                                         
	     * GROUP_FLAG = 32768                                                                            
	     * UNIQUE_FLAG = 65536
		 * 
		 * $meta = $mysqli_result_object->fetch_field();
	     * if ($meta->flags & 4) {
	     * 	echo 'Unique key flag is set';
	  	 * } 
	  	 * 
	  	 * If no flags are set we return a 0
	  	 * 
	  	 * 
	  	 * 
	  	 * MSSQL -- http://msdn.microsoft.com/en-us/library/cc296197.aspx
	  	 * a call to: sqlsrv_field_metadata() shows:
	  	 * Nothing we need.
	  	 * 
	  	 * 
	  	 * 
	  	 * PostgreSQL -- http://bytes.com/topic/postgresql/answers/174998-how-do-i-get-primary-key
	  	 * Not worth it for now, as I would need to rewrite the query.
	  	 * 
	  	 * Thus, in the final outline for Build 740 we support this data call for MySQLi and PDO only. 
		 * 
		 * @param string $db_type
		 * @param string $host
		 * @param string $database
		 * @param string $db_user
		 * @param string $db_pass
		 * @param string $socket
		 * @param string $port
		 * @param string $dbdsn
		 * @param string $sql
		 * @return array
		 */
		public static function mysql_meta_query($debug,
												$sql,
												$host,
												$db_type,
												$socket,
												$port,
												$dbdsn,
												$db_user,
												$db_pass,
												$database){
			
			// Build 695 - Support For MSSQL
			
			/*	Handy query for checking out data types.
			 	SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION,
	       		COLUMN_DEFAULT, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,
	       		NUMERIC_PRECISION, NUMERIC_PRECISION_RADIX, NUMERIC_SCALE,
	       		DATETIME_PRECISION
				FROM rackforms.INFORMATION_SCHEMA.COLUMNS
			 */
			
			
			// Build 751 - Check to see that at very minimum our db_type is valid and supported. 
			$supported_types = array('mysql', 'mysqli' , 'mssql', 'odbc' , 'odbc_access', 'postgresql');
			
			if(!in_array($db_type, $supported_types)){
				$message =  'SQL ERROR: '; // error prefix to trigger js error.
				$message .= "\n\nFor your reference, the current value of the offending \$db_type is: " . $db_type;
				echo $message;
				exit;
			}
			
	
			// switch based on database type
			if($db_type == 'mysql'){ // pdo mysql
			
				if($host == ''){
					$dbh = self::get_pdo_instance();
				} else {
					// Note the first bit of text: normally we use the constant value from the config file,
					// but as we do this dynamically, we put the raw value in.
					// However, when we add PDO_MSSQL support, we'll want to make this cleaner.
					$dsn = "mysql:unix_socket=".$socket.";host=".$host.";dbname=".$database. ";port=".$port;
					try{
						$dbh = new PDO($dsn, $db_user, $db_pass);
					} catch (PDOException $e) {
						print('SQL ERROR: ' . $e->getMessage());
						// We can show a full trace, but I'll hide this for now, as it's not very useful.
						//print "\n\nFull Debug Trace:\n" . $e->getTrace() . $e->getTraceAsString() . "<br/><br/>";
						exit;
					}
				}
				
				// perform query
				try {
					$sth = $dbh->prepare($sql);
				} catch (PDOException $e){
					
					self::write_log($e->getMessage(), $sql);
					
					die('SQL ERROR: ' . $e->getMessage());
					
				}
				
				// catch errors - 590
				if ($dbh->errorCode () != '00000') {
					
					self::write_log($e->getMessage(), $sql);
					
					die('SQL ERROR: ' . implode ( ': ', $dbh->errorInfo () ));
					
				}
	
				$sth->execute() or die('SQL ERROR: ' .implode(': ', $sth->errorInfo()));
	
				// catch errors - 590
				if ($dbh->errorCode () != '00000') {
					
					self::write_log($e->getMessage(), $sql);
					
					die('SQL ERROR: ' . implode ( ': ', $dbh->errorInfo () ));
					return array(1, $e->getMessage());
					
				}
				
				if(!$sth->columnCount()){
					die('SQL ERROR: ' . 'Your PDO Driver Doesn\'t support columnCount(), or your query doesn\'t contain any fields.');
				}
				
				$cols = $sth->columnCount();
				
				// try to get column meta
				if(!$sth->getColumnMeta(0)){
					die('SQL ERROR: ' . 'Your PDO Driver Doesn\'t support getColumnMeta(), or your query doesn\'t contain any fields.');
				}
				
				$vars = array();
				for($i = 0; $i < $cols; $i++){
					$metadata = $sth->getColumnMeta($i);
					
					// Build 740 - Append PK Data If Present.
					if(is_array($metadata['flags']) && in_array('primary_key', $metadata['flags'])){
						$pk = 'true';
					} else {
						$pk = 'false';
					}
					
					// the tiny field in fb_demo doesn't return a native_type which causes an error.
					if(!isset($metadata['native_type'])){
						$metadata['native_type'] = 'LONG';
					}
					
					$vars[] = $metadata['name'] . ',' . $metadata['native_type'] . ',' . $pk;
				}
				
				return array(1, $vars);
				
			}
			
			// Build 695 - We used to lump both mysqls together, we now have to break this out, as MSSQL means 
			// we may not have the right one.
			if($db_type == 'mysqli'){ // mysqli
				
				// get instance - no connection details provided, so we just use the ones in app/config.php
				if($host == ''){
					$dbh = Database::get_mysqli_instance();
				} else {
	
					// Build 657 - Handled errors gracefully, also actually use passed dynamic values now (the if/else branch directly above). 
					
					// We set our empty function to catch errors
					//set_error_handler("mysqli_error_bypass"); // not used yet.
					
					try{
						$dbh = new mysqli($host, $db_user, $db_pass, $database);
					}catch(Exception $e){
						
					}
					
					// Restore default PHP error handler
					restore_error_handler();
					
					// This check (using a function and then function(s) in the error mesaage) is PHP < 5.2.9 compatible
					// This is because $mysqli->connect_error was broken until that point.
					if (mysqli_connect_error()) {
						$conn = false;
						$e_message = 'SQL ERROR: Could not connect to the MySQL Database.';
						$e_message .= "\n\nPlease double check your Username, Password, Host, and if need be, MySQL Socket Path settings.";
						$e_message .= "\nIf using a DB Connector File, please be sure to check that as well.";
						$e_message .= "\n\nAlso be sure the Database has been created, and the database user has permission to access that database.";
						
						$e_message .= "\n\n\nActual SQL Debug Message:\n\n" . mysqli_connect_errno() . ') ' . mysqli_connect_error();
						echo $e_message;
						exit;
					}
					
				}
				
				// prepare statement
				$stmt = $dbh->prepare($sql);
				
				// check for statement errors
				if($dbh->errno <> 0){
					die('SQL ERROR: ' . $dbh->errno . ": " . $dbh->error);
				}
				
				// get query meta data
				$metadata = $stmt->result_metadata();
				
				$cols = $metadata->field_count;
				
				$vars = array();
				
				// http://www.php.net/manual/en/mysqli-stmt.result-metadata.php
				
				// show any meta errors
				if(!$metadata->fetch_fields()){
					die('SQL ERROR: ' . 'Your MySQL driver doesn\'t support result_metadata(), or your query doesn\'t contain any fields.');
				}
				
				$fields = $metadata->fetch_fields();
				
				foreach($fields as $field){
					
					// Build 740 - Append PK Data If Present.
					if($field->flags != 0 && $field->flags & 2){
						$pk = 'true';
					} else {
						$pk = 'false';
					}
					
					array_push($vars, $field->name . ',' . $field->type . ',' . $pk);
				}
	
				return array(1, $vars);
	
			} // database mysqli
			
			// Build 710 - We need to get eiether or...
			if($db_type == 'mssql'){
				if($host == ''){
					$conn = self::get_mssql_instance();
				} else {
					$conn = sqlsrv_connect($host, array( 
						'UID' => $db_user,
						'PWD' => $db_pass,
						'Database' => $database
					));
				}
				
				if (!$conn) {
					print 'SQL ERROR: ' . print_r(sqlsrv_errors(), true) . '</pre>';
				}
		
				/* Prepare the statement. */
				$stmt = sqlsrv_prepare( $conn, $sql);
				
				$vars = array();
				
				// http://msdn.microsoft.com/en-us/library/cc296197.aspx
				foreach( sqlsrv_field_metadata( $stmt) as $metadata)
				{
					$pk = 'false';
					$vars[] = $metadata['Name'] . ',' . $metadata['Type'] . ',' . $pk;
				}
				sqlsrv_free_stmt($stmt);
				sqlsrv_close($conn);
				
				return array(1, $vars);
				
			}
	
			// Build 710
			if($db_type == 'odbc' || $db_type == 'odbc_access'){ // ms access
				
				$conn = odbc_connect($dbdsn, $db_user, $db_pass);
				
				$result = odbc_exec($conn, $sql);
				
				$num_fields = odbc_num_fields($result);
						
				$row_count = 0;
				
				$vars = array();
				
				for($i = 1; $i <= $num_fields; $i++){ // fields start at 1, just to be confusing : )
					
					$pk = 'false';
					$vars[] = odbc_field_name($result, $i) . ',' . odbc_field_type($result, $i) . ',' . $pk;
					
				}
				
				return array(1, $vars);
				
				//die('SQL ERROR: ' . 'Your MySQL driver doesn\'t support result_metadata(), or your query doesn\'t contain any fields.');
				
			}
			
			
			
			// Build 714
			if($db_type == 'postgresql'){
				
				// http://www.php.net/manual/en/function.pg-connect.php
				if($port != '') { $port = "port={$port}"; } else { $port = ""; }
				
				$dbh = pg_connect("host={$host} dbname={$database} user={$db_user} password={$db_pass} {$port} connect_timeout=15");
				
				if (!$dbh) {
					
					$e_message = 'SQL ERROR: Could not connect to the PostgreSQL Database.';
					$e_message .= "\n\nPlease double check your Username, Password, And Host Values.";
					$e_message .= "\nIf using a DB Connector File, please be sure to check that as well.";
					$e_message .= "\n\nAlso be sure the Database has been created, and the database user has permission to access that database.";
					
					$e_message .= "\n\n\nActual SQL Debug Message:\n\n" .pg_last_error() . ') ';
					
					echo $e_message;
					exit;
					
				}
				
				// http://www.php.net/manual/en/function.pg-set-client-encoding.php
				pg_set_client_encoding($dbh, "UTF8");
				
				
				// http://www.php.net/manual/en/function.pg-execute.php
				$result = pg_query($dbh, $sql);
				
				if(!$result){
					
					$e_message = 'SQL ERROR: Error Executing Statment.';
					$e_message .= "\n\nPlease double check your Username, Password, And Host Values.";
					$e_message .= "\nIf using a DB Connector File, please be sure to check that as well.";
					$e_message .= "\n\nAlso be sure the Database has been created, and the database user has permission to access that database.";
					
					$e_message .= "\n\n\nActual SQL Debug Message:\n\n" .pg_last_error() . ') ';
					
					echo $e_message;
					exit;
					
				}
				
				// process metadata
				
				$num_fields = pg_num_fields($result);
				
				$vars = array();
				
				for($i = 0; $i < $num_fields; $i++){ // fields start at 0
					
					$pk = 'false';
					$vars[] = pg_field_name($result, $i) . ',' . pg_field_type($result, $i) . ',' . $pk;
					
				}
				
				return array(1, $vars);
				
			} // if postgresql
			
		} // end function
		
	}
	
	
}

?>