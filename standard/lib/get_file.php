<?php
/**
 * RackForms Form Library Files - get_file.php
 * @version 1.3
 * @author RackForms
 * @category Database
 * @copyright 2008-2011 nicSoft
 * @name get_file.php
 * 
 * ------------------
 * get_file.php
 * ------------------
 * 
 * Returns(echo's) raw file data from a database to feed a call such as:
 * <img src="lib/get_file.php?id=<?php echo $res['id'];?>" />
 * 
 * Used in Builder File Calls as:
 * 
 * ALL FILES:
 * lib/get_file.php?id=#{id}
 * 
 * -----------------
 * How to use:
 * -----------------
 * 
 * IMPORTANT!
 * This file only reads database configuration details from the jobs config.php file.
 * Thus, if you move this job to another server with different database login details,
 * you MUST update the config.php file to match!
 * 
 * When you export a form RackForms includes this file in the lib directory with your form.
 * This file calls your RackForms database looking for three fields:
 * file_name file_mime, file_date
 * 
 * The call is based on the presence of a relational field: entry_id
 * That is, the file is related to another table in your database who's PK or other identifier
 * is the value of the entry_id field.
 * 
 * For example, you could use this SQL+ code in a form that submits an file:
 * INSERT INTO fb_files (file_data, file_thumb, file_name, file_mime, file_size, entry_id) VALUES (?,?,?,?,?,?)
 * With these paramters:
 * file, file_thumb, file_name, file_mime, file_size, ${ret_val}
 * 
 * Where ${ret_val} is the lastInsertID of a prior database INSERT.
 * 
 * Should the database call return an file, a header is sent with the mime type of your file, 
 * and the file is returned.
 * 
 * This function can also return meta information about your file.
 * To do so, in addition to the id, call the file with an action parameter, 
 * with the aciton name being the field you want:
 * lib/get_file.php?id=#{id}&action=name
 * 
 * Please see below for full database create code (though as of Build 646 this table is already installed by default)
 * 
 */
ini_set('display_errors', 0); // Change to 1 to display all error messages.
ini_set('error_reporting', E_ALL);

$fb_file_id = isset($_GET['id']) ? $_GET['id'] : -1;

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Build 695 - Use Inline Connection Details By Default
// These values come from the jobs Repeater element.
$connector = isset($_GET['connector']) ? $_GET['connector'] : 'inline';

if($fb_file_id != -1){
	
	// get connection data
	if($connector == 'inline'){
		$db_host = '#HOST';
		$db_type = '#TYPE';
		$db_user = '#USER';
		$db_pass = '#PASS';
		$db_catalog = '#CATALOG';
		$mysql_socket = '#SOCKET';
		$mysql_port = '#PORT';
		$dbdsn = '#DBDSN';
		$dbconnector = '#DBCONNECTOR';
		
		if(file_exists($dbconnector)){
			include $dbconnector;
		}
	}
	
	include '../Database.php';
	$dbh_file = new Database();

	switch($action){
		case 'name' :
			$sql_file = "SELECT file_name FROM fb_files WHERE entry_id = ?";
			$params = array($fb_file_id);
			$result_file = $dbh_file->pdo_procedure_params($debug, $sql_file, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_file[0]['file_name'] != ''){
				echo $result_file[0]['file_name'];
			}
			exit(0);
			break;
		case 'mime' :
			$sql_file = "SELECT file_mime FROM fb_files WHERE entry_id = ?";
			$params = array($fb_file_id);
			$result_file = $dbh_file->pdo_procedure_params($debug, $sql_file, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_file[0]['file_mime'] != ''){
				echo $result_file[0]['file_mime'];
			}
			exit(0);
			break;
		default :
			$pass = 1;
			$get = 'file_data';
			$sql_file = "SELECT file_name, file_mime, file_data FROM fb_files WHERE entry_id = ?";

			if($pass){
				$params = array($fb_file_id);
				$result_file = $dbh_file->pdo_procedure_params($debug, $sql_file, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
				
				if($result_file[0][$get] != ''){
					// file
					header("Content-Disposition: attachment; filename=\"{$result_file[0]['file_name']}\"");
					echo $result_file[0][$get];
				}
			}
	}

}
/**
 * MySQL Database create code
 * You can always customize this code, but if you simply want to get up and running
 * copy and paste this SQL create code into your DB admin tool.
 * NOTE: This table is already installed with RackForms!
 */

/*

CREATE TABLE fb_files(
  file_id INT (11) NOT NULL AUTO_INCREMENT,
  entry_id INT (11) DEFAULT NULL,
  file_caption LONGTEXT DEFAULT NULL,
  file_name VARCHAR (100) DEFAULT NULL,
  file_mime VARCHAR (40) DEFAULT NULL,
  file_size INT (11) DEFAULT NULL,
  file_data LONGBLOB DEFAULT NULL,
  file_thumb LONGBLOB DEFAULT NULL,
  PRIMARY KEY (file_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 30720
CHARACTER SET utf8
COLLATE utf8_general_ci;

*/
?>