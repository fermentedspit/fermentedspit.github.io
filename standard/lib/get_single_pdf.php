<?php
/**
 * RackForms Form Library Files - get_single_pdf.php
 * @version 1.3
 * @author RackForms
 * @category Database
 * @copyright 2008-2011 nicSoft
 * @name get_single_pdf.php
 * 
 * ------------------
 * get_single_pdf.php
 * ------------------
 * 
 * 
 * Used in Builder File Calls as:
 * 
 * Display PDF Inline In Browser:
 * lib/get_single_pdf.php?id=#{id}&action=&display-method=inline
 * 
 * Display Download Prompt For PDF
 * lib/get_single_pdf.php?id=#{id}&action=&display-method=download
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
 * lib/get_single_pdf.php?id=#{id}&action=name
 * 
 * 
 */
ini_set('display_errors', 0); // Change to 1 to display all error messages.
ini_set('error_reporting', E_ALL);

$debug = 0;

$fb_file_id = isset($_GET['id']) ? $_GET['id'] : -1;

$action = isset($_GET['action']) ? $_GET['action'] : '';

$display_method = isset($_GET['display-method']) ? $_GET['display-method'] : '';

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
			$sql_file = "SELECT file_name, file_mime, file_data FROM fb_files WHERE file_id = ?";

			if($pass){
				
				$params = array($fb_file_id);
				$result_file = $dbh_file->pdo_procedure_params($debug, $sql_file, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
				
				if($result_file[0][$get] != ''){

					// download pdf
					if(isset($_GET['display-method']) && $_GET['display-method'] == 'download'){
						header('Content-Description: File Transfer');
						header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
						header('Pragma: public');
						header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
						header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
						// force download dialog
						if (strpos(php_sapi_name(), 'cgi') === false) {
							header('Content-Type: application/force-download');
							header('Content-Type: application/octet-stream', false);
							header('Content-Type: application/download', false);
							header('Content-Type: application/pdf', false);
						} else {
							header('Content-Type: application/pdf');
						}
						// use the Content-Disposition header to supply a recommended filename
						header('Content-Disposition: attachment; filename="'.$result_file[0]['file_name'].'";');
						header('Content-Transfer-Encoding: binary');
						// output data
						echo $result_file[0][$get];
						
					} else { // default - display pdf inline
						header('Content-Type: application/pdf');
						header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
						header('Pragma: public');
						header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
						header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
						header('Content-Disposition: inline; filename="'.$result_file[0]['file_name'].'";');
						echo $result_file[0][$get];
					}

				}
			}
	}

}
?>