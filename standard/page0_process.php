<?php
//--
// PHP Form Process Script - Generated: October 17, 2018
//--


// Start our main session

if(!session_id()) { session_start(); }

$error = 0; $valError = "";

// Form Page Submit Security
$domain_list = explode(',',"");
$ip_limit = 0;
$ip_limit_message = <<<EOT
Sorry, form entry limit reached.
EOT;
$delta = 3155692600;
$db_key = 2;
$job_id = 2;
$active = 1;
$active_message = <<<EOT
Sorry, this form is currently disabled.
EOT;

include_once 'security/secure_submit.php';
include_once 'lib/utility.php';

$_SESSION['entry_key_demo'] = isset($_SESSION['entry_key_demo']) ? $_SESSION['entry_key_demo'] : md5(time() + rand(10000, 1000000));

$form_persistance = 0;
	
// Collect Optional GeoData
$items = array("geo_lat", "geo_long", "geo_accuracy", "geo_altitude", "geo_heading", "geo_speed", "geo_timestamp");

if(is_array($items) && count($items) != 0){

	foreach($items as $item){
	
		// always reset our tmp value
		unset($tmp);
	
		// check for and create dynamic element
		if(isset($_POST["{$item}"]) && $_POST["{$item}"] != '') {
			$tmp = isset($_POST["{$item}"]) ? $_POST["{$item}"] : 0;
			$_SESSION["{$item}"] = $tmp;
			if(isset($_SESSION["{$item}_is"])) { $_SESSION["{$item}_is"] = 0; }
			if(isset($_SESSION["{$item}_processed"])) { $_SESSION["{$item}_processed"] = true; }
		} else {
			$_SESSION["{$item}"] = null;
		}
		$tmp = isset($_SESSION["{$item}"]) ? $_SESSION["{$item}"] : '';
		$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["{$item}"] = $tmp;
		$_SESSION['qs-entities']["{$_SESSION['entry_key_demo']}"]["{$item}"] = htmlentities($tmp);
		$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["Dynamic - {$item}"] = $tmp;
		$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$item}"] = $item;
		$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$item}_pageTitle"] = "Form Page";
	
	}
	
}
		
// ------------------------------------
// PROCESS FORM FIELD
// email - text
// ------------------------------------

if(isset($_POST['email']) && $_POST['email'] != '') {
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$_SESSION['email'] = $email;
	if(!preg_match("/^[A-Za-z0-9._%+-]+@(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,6}$/", $email)) {
		$error = 1;
		$valError .= 'email is not a valid email address! <br/>';
	}
		
} else {

	$in_bucket = 0;
	$rf_field_type = "text";
	
	// Is Field Validation Method Set to: Field Only Validates When Visible?
	
	// Checkbox Fields Must Validate Using JS.
	
	if($rf_field_type != "checkbox"){
	
		// If we only validate when visible, pass if field not submitted.
		
		if(1 != 1 && $in_bucket == false){
			if(!isset($_POST['email'])) {
				$error = '1'; 
				$valError .= 'email is required.<br/>';
			} else {
				$_SESSION['email'] = null;
			}
		}
						
	}
}
if(isset($_SESSION['email_is'])) { $_SESSION['email_is'] = 0; }

	if(isset($_SESSION['email_processed'])) { $_SESSION['email_processed'] = true; }$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['email'] = $email;
$_SESSION['qs-entities']["{$_SESSION['entry_key_demo']}"]['email'] = is_array($email) ? htmlentities(implode(':', $email)) : htmlentities($email);		
		
// Label Processing
		
$label = "email";
		
if(isset($_POST['email_dyn_label']) && $_POST['email_dyn_label'] != ""){
	$label = filter_input(INPUT_POST, 'email_dyn_label', FILTER_SANITIZE_STRING);
}
		
$label = addslashes($label);
		
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$label}"] = $email;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['email'] = $label;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['email_pageTitle'] = "Form Page";
// ------------------------------------
// PROCESS FORM FIELD
// f_Name - text
// ------------------------------------

if(isset($_POST['f_Name']) && $_POST['f_Name'] != '') {
	$f_Name = isset($_POST['f_Name']) ? $_POST['f_Name'] : '';
	$_SESSION['f_Name'] = $f_Name;
		
} else {

	$in_bucket = 0;
	$rf_field_type = "text";
	
	// Is Field Validation Method Set to: Field Only Validates When Visible?
	
	// Checkbox Fields Must Validate Using JS.
	
	if($rf_field_type != "checkbox"){
	
		// If we only validate when visible, pass if field not submitted.
		
		if(1 != 1 && $in_bucket == false){
			if(!isset($_POST['f_Name'])) {
				$error = '1'; 
				$valError .= 'First Name is required.<br/>';
			} else {
				$_SESSION['f_Name'] = null;
			}
		}
						
	}
}
if(isset($_SESSION['f_Name_is'])) { $_SESSION['f_Name_is'] = 0; }

	if(isset($_SESSION['f_Name_processed'])) { $_SESSION['f_Name_processed'] = true; }$f_Name = isset($_SESSION['f_Name']) ? $_SESSION['f_Name'] : '';
$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['f_Name'] = $f_Name;
$_SESSION['qs-entities']["{$_SESSION['entry_key_demo']}"]['f_Name'] = is_array($f_Name) ? htmlentities(implode(':', $f_Name)) : htmlentities($f_Name);		
		
// Label Processing
		
$label = "First Name";
		
if(isset($_POST['f_Name_dyn_label']) && $_POST['f_Name_dyn_label'] != ""){
	$label = filter_input(INPUT_POST, 'f_Name_dyn_label', FILTER_SANITIZE_STRING);
}
		
$label = addslashes($label);
		
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$label}"] = $f_Name;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['f_Name'] = $label;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['f_Name_pageTitle'] = "Form Page";
// ------------------------------------
// PROCESS FORM FIELD
// l_Name - text
// ------------------------------------

if(isset($_POST['l_Name']) && $_POST['l_Name'] != '') {
	$l_Name = isset($_POST['l_Name']) ? $_POST['l_Name'] : '';
	$_SESSION['l_Name'] = $l_Name;
		
} else {

	$in_bucket = 0;
	$rf_field_type = "text";
	
	// Is Field Validation Method Set to: Field Only Validates When Visible?
	
	// Checkbox Fields Must Validate Using JS.
	
	if($rf_field_type != "checkbox"){
	
		// If we only validate when visible, pass if field not submitted.
		
		if(1 != 1 && $in_bucket == false){
			if(!isset($_POST['l_Name'])) {
				$error = '1'; 
				$valError .= 'Last Name is required.<br/>';
			} else {
				$_SESSION['l_Name'] = null;
			}
		}
						
	}
}
if(isset($_SESSION['l_Name_is'])) { $_SESSION['l_Name_is'] = 0; }

	if(isset($_SESSION['l_Name_processed'])) { $_SESSION['l_Name_processed'] = true; }$l_Name = isset($_SESSION['l_Name']) ? $_SESSION['l_Name'] : '';
$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['l_Name'] = $l_Name;
$_SESSION['qs-entities']["{$_SESSION['entry_key_demo']}"]['l_Name'] = is_array($l_Name) ? htmlentities(implode(':', $l_Name)) : htmlentities($l_Name);		
		
// Label Processing
		
$label = "Last Name";
		
if(isset($_POST['l_Name_dyn_label']) && $_POST['l_Name_dyn_label'] != ""){
	$label = filter_input(INPUT_POST, 'l_Name_dyn_label', FILTER_SANITIZE_STRING);
}
		
$label = addslashes($label);
		
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$label}"] = $l_Name;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['l_Name'] = $label;
$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]['l_Name_pageTitle'] = "Form Page";

// Dynamic Element Processing - Check for existance of our process session element
if(isset($_SESSION['fb_dynamic_elements'])){

	// Create Master Array, Used For Easy Insert Actions With SQL+
	if(!isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["fb_dynamic_elements"]))
		$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["fb_dynamic_elements"] = array();
		
	// Merge Arrays
	$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["fb_dynamic_elements"] = array_merge($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["fb_dynamic_elements"], $_SESSION['fb_dynamic_elements']);

	$items = $_SESSION['fb_dynamic_elements'];
	
	if(is_array($items) && count($items) != 0){
	
		foreach($items as $item){
		
			// always reset our tmp value
			unset($tmp);
		
			// check for and create dynamic element
			if(isset($_POST["{$item}"]) && $_POST["{$item}"] != '') {
				$tmp = isset($_POST["{$item}"]) ? $_POST["{$item}"] : 0;
				$_SESSION["{$item}"] = $tmp;
				if(isset($_SESSION["{$item}_is"])) { $_SESSION["{$item}_is"] = 0; }
				if(isset($_SESSION["{$item}_processed"])) { $_SESSION["{$item}_processed"] = true; }
			} else {
				$_SESSION["{$item}"] = null;
			}
			$tmp = isset($_SESSION["{$item}"]) ? $_SESSION["{$item}"] : '';
			$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["{$item}"] = $tmp;
			$_SESSION['qs-entities']["{$_SESSION['entry_key_demo']}"]["{$item}"] = htmlentities($tmp);
			$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["Dynamic - {$item}"] = $tmp;
			$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$item}"] = $item;
			$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$item}_pageTitle"] = "Form Page";
		
		}
		
	}

}

// unset the session element
unset($_SESSION['fb_dynamic_elements']);


// Form Persistance - Mode|Database Key|Resume Mode|Success Page
savePersistantValues(0, $db_key, 0, 'page1.php');
		


$sid_url = "";

if(defined('SID'))
	$sid_url = (strlen(SID) ? ('?' . htmlspecialchars(SID)) : '');

if($error){
	
	// remove pages from valid list. 
	if(isset($_SESSION['pages']['page1.php'])) { 
		unset($_SESSION['pages']['page1.php']);
		unset($_SESSION['pages-passed']["{$_SESSION['entry_key_demo']}"]['page0.php']);
	} 
	
	$_SESSION["e_message"] = $valError;
	header("Location: {$_SESSION['MAX_PATH_PROC']}page0.php{$sid_url}");
	return;
} else {
	$_SESSION['pages']['page1.php'] = 'pass';
	
	$_SESSION['pages-passed']["{$_SESSION['entry_key_demo']}"]['page0.php'] = 'pass';
	
	// conditional route code
	
	
	// custom route code
	
	
	// default action
	header("Location: {$_SESSION['MAX_PATH_PROC']}page1.php{$sid_url}");
	return;
}

?>