<?php

/**
 * Form Page Safety Features
 */

// Can we show this form?
if($active != 1){
	echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\">{$active_message}<br/><br/></div>";
	exit(0);
}

// Host names from where the form is authorized to be posted from: 
$authHosts = $domain_list;

// die($_SERVER['SERVER_NAME']); // Uncomment this to see your server name

// Where have we been posted from?
// Trouble getting a form to read your domain? Please check:
// http://www.php.net/manual/en/reserved.variables.server.php
if(!isset($_SERVER['SERVER_NAME'])){
	die('Error: Illegal Access.'); // No direct access to the process page without first using another page on the site.
} else {
	$from = $_SERVER['SERVER_NAME'];
}

// Make sure the form was posted from an approved host name.
if(is_array($authHosts) && $authHosts[0] != ''){
	if(!in_array($from, $authHosts)){
		// The default action if this form is accessed outside of the scope you choose is to die. However
		// You can comment out these lines and set your own behavior, such as a database entry, header as in below, or anything else.
		echo 'This action is not authorized!';
		//die();
		header("HTTP/1.0 403 Forbidden");
		exit;    
	}
}

// For basic web-forms, we don't care about anything other than requests from a browser:
if(!isset($_SERVER['HTTP_USER_AGENT'])){
   die("Forbidden - You are not authorized to view this page");
}

if(!$_SERVER['REQUEST_METHOD'] == "post" || !$_SERVER['REQUEST_METHOD'] == "get"){
   die("Forbidden - You are not authorized to view this page"); 
}

// Build 882 - Persist Original Query String.

$query_string = "";

if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {

	$query_string = '?' . htmlspecialchars($_SERVER['QUERY_STRING']);

}

/**
 * If we have an ip limit set, we need to check this user against the RackForms Entry Database.
 * If the limit has been hit, we kill execution and display a message.
 * 
 * Build 755 - Added null value for dbdsn. 
 */
if(isset($ip_limit) && $ip_limit != 0){
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	
	// include our database files
	@include_once "{$_SESSION['MAX_PATH']}Database.php";
	
	// create the db object
	$dbh = new Database();
	
	$now = time();
	
	// Create the query
	$sql = "SELECT job_id, COUNT(DISTINCT ts) AS counted, MAX(ts) as time_stamp FROM fb_job_entries WHERE remote_ip = ? AND ts + ? > ? AND job_id = ? GROUP BY job_id";
	$params = array((string)$remote_ip, (double)$delta, (int)$now, (int)$job_id);
	$result = $dbh->pdo_procedure_params($debug, $sql, $db_host, $db_type, $mysql_socket, $mysql_port, '', $db_user, $db_pass, $db_catalog, $params, 0);

	if($result[0]['counted'] >= $ip_limit){
		// This user cannot enter with this ip any more
		echo "<div style=\"font-family:Tahoma, Geneva, sans-serif; font-size:12px;\">{$ip_limit_message}<br/><br/></div>";
		exit(0);
	}
}

// Build 695 - Login Logic
// Build 863 - Persistent login logic support.
// Build 882 - Pass original query string.

if($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login']->login == 1){
	
	if($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login']->persistent_login == 1){
	
		// Login persists across this browser submission for this specific form job (by job id).
		
		if(!isset($_SESSION['fb_basic_login_authenticated_persistent'])){
			// show login form
			header("Location: security/login/index.php{$query_string}");
			exit;
		}
		
		if(!isset($_SESSION['fb_basic_login_authenticated_persistent']["{$fb_login->persistent_login_job_id}"])){
			// show login form
			header("Location: security/login/index.php{$query_string}");
			exit;
		}

	} else if ($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login']->persistent_login == 2) {
	
		// Login persists across this browser submission for all forms.
		
		if(!isset($_SESSION['fb_basic_login_authenticated_persistent'])){
			// show login form
			header("Location: security/login/index.php{$query_string}");
			exit;
		}

	} else {
		
		// Standard logic, one per form submission.
		
		if(!isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_authenticated'])){
			// show login form
			header("Location: security/login/index.php{$query_string}");
			exit;
		}
		
	}

}

?>