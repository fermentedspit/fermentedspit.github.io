<?php

/**
 * reCAPTCHA security module
 * Sign up for a free account at: http://recaptcha.net/whyrecaptcha.html
 * 
 * INSTRUCTIONS FOR reCAPTCHA
 * Be sure to generate a Global Key when creating your reCAPTCHA account if you plan to use the reCAPTCHA system on multiple domains!
 * After you sign up you will recieve two 'keys'. Input those values below inbetween the tick marks for each variable.
 * 
 * You can also leave these variables blank, but that means you will need to fill out the Public and Private key values for each form.
 */
$publickey = '';
$privatekey = '';


/**
 * Form Submit Safety Features
 */

// Can we process this form?
if($active != 1){
	die($active_message);
}

// First, make sure the form was posted from a browser.
// For basic web-forms, we don't care about anything other than requests from a browser:
  
if(!isset($_SERVER['HTTP_USER_AGENT'])){
   die("Forbidden - You are not authorized to view this page");
}

if(!$_SERVER['REQUEST_METHOD'] == "post" || !$_SERVER['REQUEST_METHOD'] == "get"){
   die("Forbidden - You are not authorized to view this page");
}

// Host names from where the form is authorized to be posted from: 
$authHosts = $domain_list;

// Where have we been posted from?
if(!isset($_SERVER['SERVER_NAME'])){
	die('Error: Illegal Access.'); // No direct access to the process page without first using another page on the site.
} else {
	$from = $_SERVER['SERVER_NAME'];
}

// Test to see if the $fromArray used www to get here.
$wwwUsed = strpos($from, "www.");

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

/**
 * If we have an ip limit set, we need to check this user against the RackForms Entry Database.
 * If the limit has been hit, we kill execution and display a message.
 * 
 * Build 755 - Added default value for $dbdsn
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

// Attempt to defend against header injections:
$badStrings = array("Content-Type:",
                     "MIME-Version:",
                     "Content-Transfer-Encoding:",
                     "bcc:",
                     "cc:");

// Loop through each value and test if it contains
// one of the $badStrings:
if($_SERVER['REQUEST_METHOD'] == 'get'){
	foreach($_GET as $k => $v){
	   foreach($badStrings as $v2){
	       if(strpos($v, $v2) !== false){
	           //logBadRequest();
	           header("HTTP/1.0 403 Forbidden");
	               exit;
	       }
	   }
	}
}
if($_SERVER['REQUEST_METHOD'] == 'post'){
	foreach($_POST as $k => $v){
	   foreach($badStrings as $v2){
	       if(strpos($v, $v2) !== false){
	           //logBadRequest();
	           header("HTTP/1.0 403 Forbidden");
	               exit;
	       }
	   }
	}
}
?>