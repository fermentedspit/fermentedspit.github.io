<?php
/**
 * RackForms Form Library Files - get_single_file_token.php
 * @version 1.1
 * @author RackForms
 * @category Database
 * @copyright 2007-2014 nicSoft
 * @name get_single_file_token.php
 * 
 * ------------------
 * get_single_file_token.php
 * ------------------
 * 
 * -----------------
 * How to use:
 * -----------------
 * This file is used internally by RackForms and should not be called directly from user scripts.
 * 
 * -------------------
 * Rationale
 * -------------------
 * This file allows RackForms to implement the *Q_ token.
 */
ini_set('display_errors', 0); // Change to 1 to display all error messages.
ini_set('error_reporting', E_ALL);

$debug = 0; // set to 1 to see debug messages.

if(!session_id())
	session_start();
	
// get main id for this item
$getid = isset($_GET['id']) ? $_GET['id'] : -1;

// if no id, stop execution
if($getid == -1)
	die;

// token
$token = isset($_GET['token']) ? $_GET['token'] : -1;

// if no token, stop execution
if($token == "")
	die;

// capture variables from session element 'packets' created in the template page
$column_name = isset($_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_column_name']) ? $_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_column_name'] : -1;

// additional field vars
$table_name = isset($_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_table_name']) ? $_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_table_name'] : -1;
$id_column_name = isset($_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_id_column_name']) ? $_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_id_column_name'] : -1;
$id_column_value = isset($_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_id_column_value']) ? $_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_id_column_value'] : -1;
$file_name = isset($_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_id_column_value']) ? $_SESSION['file_packet']["{$getid}"]["{$token}"]['Q_token_file_name_column_value'] : "File Download";


// Use Inline Connection Details By Default
// These values come from the jobs Repeater element.
$connector = isset($_GET['connector']) ? $_GET['connector'] : 'inline';

if($id_column_value != -1){
	
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
	$dbh_img = new Database();

	if($table_name != -1 && $id_column_value != -1){
		$pass = 1;
	} else {
		$pass = 0;
	}

	$get = $column_name;
	$sql_img = "SELECT {$column_name} FROM {$table_name} WHERE {$id_column_name} = ?";
	

	if($pass){
		$params = array($id_column_value);
		$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);	
	}
	
	// set default mime
	$mime = "image/jpeg";
	
	// get file info
	if(version_compare ( PHP_VERSION, "5.3.0", ">=" )){
		$finfo = new finfo(FILEINFO_MIME);
		$file_info = $finfo->buffer($result_img[0][$get]);
		
		$mime = explode(';', $file_info);
		$mime = $mime[0];
	}
	
	

	// Echo File
	header("Content-type: {$mime}");
	
	if($mime == "application/pdf"){
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream', false);
		header('Content-Type: application/download', false);
		header('Content-Type: application/pdf', false);
	}
	
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	
	echo $result_img[0][$get];
	


}
?>