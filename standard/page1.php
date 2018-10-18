<?php 
//--
// RackForms v1.0 PHP Form Submit Script - Generated: October 17, 2018
//--
if(function_exists('ini_set')){
	ini_set('display_errors', 0); // Change to 1 to display all error messages.
	ini_set('error_reporting', E_ALL);
}
	
// Start our main session

if(!session_id()) { session_start(); }

$error = 0; $valError = "";

// Form Page Submit Security
$domain_list = explode(',',"");

// Form Page Security
$ip_limit = 0;
$job_id = '2';
$job_name = "demo";
$ip_limit_message = <<<EOT
Sorry, form entry limit reached.
EOT;
$ip_limit_duration = 0;
$delta = 3155692600;

$active = 1;
$active_message = <<<EOT
Sorry, this form is currently disabled.
EOT;

// Global Timestamps
$timestamp = time();
$datetime = date('Y-m-d  H:i:s', time());

// Visitor IP
$remote_ip = $_SERVER['REMOTE_ADDR'];

if(!isset($_SESSION['MAX_PATH']))
	$_SESSION['MAX_PATH'] = "";

include_once "{$_SESSION['MAX_PATH']}security/secure_submit.php";
include_once "{$_SESSION['MAX_PATH']}lib/utility.php";

// Build 693 - We now include database code by default.
if(file_exists("{$_SESSION['MAX_PATH']}Database.php")){
	@include_once "{$_SESSION['MAX_PATH']}Database.php";
}

// Required Pages Logic.
$required_pages = array('page0.php');

// check pages
foreach($required_pages as $rp){
	if($_SESSION['pages-passed']["{$_SESSION['entry_key_demo']}"]["{$rp}"] != 'pass'){
	
$message = <<<EOD
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Submission Error Page</title>
<style type="text/css">
a { 
text-decoration:none;	
}
</style>			
</head>
<body>
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
				
<div style="padding:15px; background-color:#f8f8f8; border-radius:10px; border:solid 1px #e2e2e2; font-family: 'Open Sans', sans-serif;">
	
	<p style="font-size:14pt;">Submission Notice: All required pages must be completed before we can submit this form.</p>

	<span style="font-size:9pt;"><strong>Form users:</strong> Please <a href="{$rp}">click here</a> to try the form submission again.</span>
				
	<br/><br/>
				
	<span style="font-size:9pt;"><strong>Form creators:</strong> Please be sure to check the: "Page Not Required / Allow Direct Access?" box under: "Form Properties" for EVERY page that can be skipped or is not required for the ENTIRE job.</span>
	<p style="font-size:9pt;"><span>For this job, that means starting with the page: <strong>{$rp}</strong></span></p>
	<span style="font-size:9pt;">Please see <a href="https://www.rackforms.com/documentation/rackforms/page-elements/sortable.php#FormPropertiesDirectAccess" target="_blank">this link</a> for more details.</span>
</div>
</body>
</html>
EOD;

		echo $message; die();
	}
}
		

if(isset($_SESSION['qs']) && isset($_SESSION['entry_key_demo'])) {

	// Build 689 - Always Called.
	clearPersistantValues(0, $job_id);
    
    // Build 805
    if(1 == 0){
        init_stats($job_id, "page1.php", 'confirm');
    }

	// Utility Function Clean up this session's inline data -- remove all field keys, then the qs and qs-label, followed by entry_key and page data
	function clear_fb_session(){
	
		// Build 866 - Remove Any ACI Transaction Data
		if(isset($_SESSION['aci-level1']))
			unset($_SESSION['aci-level1']); 
				
		// Build 770 - Remove Any Stripe Transaction Data
		if(isset($_SESSION['stripe']))
			unset($_SESSION['stripe']); 

		// Remove all singleton session data fields (selected items etc)
		$named_sesison_vars = array_keys($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]);
		foreach($named_sesison_vars as $var){
			// fields
			unset($_SESSION["{$var}"]);
			// isset
			unset($_SESSION["{$var}_is"]);
			// _processed - Build 712
			unset($_SESSION["{$var}_processed"]);
		}
	
		if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"])){
			foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"] as $key=>$value){
				unset($_SESSION[$key]);
			}
		}
		
		// Build 764
		if(isset($_SESSION['fb_ecomm_demo'])){
			unset($_SESSION['fb_ecomm_demo']);
		}
		
		if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['signatures'])){
			unset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['signatures']);
		}
		
		unset($_SESSION['pages']);
		unset($_SESSION['pages-passed']["{$_SESSION['entry_key_demo']}"]);
		
		clean_output_location('tmp');
		clean_output_location('lib/jquery-upload/server/php/files/' . $_SESSION['entry_key_demo']); // Buld 860
		
		if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]); }
		if(isset($_SESSION['qs-entity']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs-entity']["{$_SESSION['entry_key_demo']}"]); }
		if(isset($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]); }
		if(isset($_SESSION['entry_key_demo'])) {unset($_SESSION['entry_key_demo']); }

		if(isset($_SESSION['fielded_data_array'])) { unset($_SESSION['fielded_data_array']); }
		
		// Build 757 - unset main indentifiers, which is trnasformed at build time to specific element for this form.
		unset($_SESSION['entry_key_demo']);
				
		// Build 836
		if(isset($_SESSION['fb_entry_id_auto']))
			unset($_SESSION['fb_entry_id_auto']);
		
	}
	// call session clear code




// common to all e-mail methods
if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]) && $_SESSION['qs']["{$_SESSION['entry_key_demo']}"] != ''){		

/**
 * RackForms PHPMail e-mail Processing v.1.3 11/05/12
 **/


// check for and replace array based variables
$vars = array();
foreach($vars as $var){
	if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"][$var])){
		if(is_array($_SESSION['qs']["{$_SESSION['entry_key_demo']}"][$var])){
			$field_items = '';
			foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"][$var] as $key=>$v){
				if($key != 0){ $field_items .= ' : '; }
				$field_items .= $v;
			}
			$_SESSION['qs']["{$_SESSION['entry_key_demo']}"][$var] = $field_items;
		}
	}
}


$datagrid_items = array();
$datagrid_items_raw = array();
$datagrid_items_displayed = array();

if(!isset($datagrid_html))
	$datagrid_grid_code = '';

if(!isset($datagrid_items_processed) && count($datagrid_items) != 0){
	
	$datagrid_grid_code = create_datagrid_email_table($datagrid_items,
													  $_SESSION['qs']["{$_SESSION['entry_key_demo']}"], 
													  $_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]);
	
	// flag so we only process once per page.
	$datagrid_items_processed = true;
	
}

// Debug Calls.
// print_r($datagrid_grid_code);
// echo $datagrid_grid_code['grid_name'][0]; // [grid_name] = The name of the grid, [0-4] index to show each of the styled output options.

$datagrid_format_type = 4; // for datagrid processing
$datagrid_format_insert_start = "</table>";
$datagrid_format_insert_end = '<table class="result-table" width="100%" border="0" cellpadding="3">';

$ignore_empty_fields = 0;

// Outlook, TABLE based Format.

$style = <<<EOF
<style type="text/css">
.wrapper { padding:7px; } 
div.wrapper:nth-child(even) { background-color:#efefef }
	
table
{
    color:#000000;
    border-collapse:collapse;
    font:14px Arial, Helvetica, sans-serif;
    padding:0px;
}
 
th
{
    color:#000000;
    font-weight:bold;
    padding:0px;
    text-align:center;
    vertical-align:top;
}
 
tr
{
    font-weight:normal;
}
 
td
{
    border-bottom-style: solid;
    border-width:1px;
    border-color:#f2f2f2;
    padding:10px;
    text-align:left;
    vertical-align:top;
}
 		
.name { color:#999999; font-family:Arial, Gadget, sans-serif; font-weight: bold; width:60%; } 
.value { color:#529BCC; font-family:Arial, Helvetica, sans-serif; font-weight: bold; }
 		
.clear { clear:both; height:2px; }

.datagrid-wrapper { font-family:Arial, Gadget, sans-serif; margin:30px 0px 30px 0px; border-top:10px solid #529BCC; border-bottom: 10px solid #529BCC; }
 		
.datagrid-label-row { color:#666; width:20%; font-weight:bold; } 
.datagrid-label-header { color:#999; width:20%; font-weight:bold; } 
.datagrid-row-item { color:#529BCC; width:20%; font-weight:bold; }
</style>
EOF;
 		
// Replace all newlines with RACKFORMS_CLB until final composition.
$style = str_replace("\r\n", 'RACKFORMS_CLB', $style);
$style = str_replace("\n", 'RACKFORMS_CLB', $style);

$message = <<<EOM

EOM;
		
$message .= "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />RACKFORMS_CLB<title>Section 8</title>{$style}</head><body>RACKFORMS_CLB";
$message .= '<table class="result-table" width="100%" border="0" cellpadding="3">';
	foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"] as $key=>$val){
	
						
		// Is This A Bucket Field?
		if(substr_count($key, 'fb-bucket-repeater-') != 0) {
				
			$bucket_data = array();
			$debug_bucket_data = false;
			$bucket_were_processing = '';
				
			$bucket_were_processing_array = explode('fb-bucket-repeater-', $key);

			if(isset($bucket_were_processing_array[1]) && $bucket_were_processing_array[1] != ''){
				$bucket_were_processing = $bucket_were_processing_array[1];
			}
			
			if($bucket_were_processing == '') {
				continue;	
			}
		
			// Do we have bucket data to process?
			if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb-valid-bucket-names'])){
		
				// If so, loop over the existing buckets.
				foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb-valid-bucket-names'] as $bucket_name) {
		
					// If the bucket we're looping through is the same as the one we've got data for, proceed.
					if($bucket_were_processing == $bucket_name) {
							
						// Loop over each field in this bucket...
						foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['bucket-repeater']["{$bucket_name}"] as $bucket_group) {
		
							// Take the bucket name and loop over the fields array
							foreach($bucket_group as $bucket_field_name){
		
								$bucket_data["{$bucket_field_name}"] = array();
								$bucket_data["{$bucket_field_name}"]['value'] = $_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["{$bucket_field_name}"];
								$bucket_data["{$bucket_field_name}"]['label'] = $_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$bucket_field_name}"];
				
								if($debug_bucket_data) {
									echo '<br/>Bucket Field Name: ' . $bucket_field_name;
									echo '<br/>Bucket Field Value: ' . $_SESSION['qs']["{$_SESSION['entry_key_demo']}"]["{$bucket_field_name}"];
									echo '<br/>Bucket Field Label: ' . $_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]["{$bucket_field_name}"];
								}
		
							}
		
						}
	
					}
	
				}
		
			}
				
			// Debug Bucket Data Captured.
			if($debug_bucket_data) {
				echo '<pre>';
				print_r($bucket_data);
				echo '</pre>';
				die;
			}
			
		
		}
		
	
		if(isset($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"][(string)$key]) &&
			$_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"][(string)$key] != 'FB_NULL' && // do not include specific NULL vals
			substr_count($key, '_ts') == 0 && substr_count($key, '_dt') == 0 // or calendar _dt and _ts
			&& substr_count($key, 'geo_') == 0 && substr_count($key, '-bucket-') == 0){ // or geo_ or bucket items
			
			$label = html_entity_decode($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"][(string)$key]);

						// grid processing
			$test_key = explode('_', $key);
			
			// if we've already shown this one, skip
			if(in_array($test_key[0], $datagrid_items_displayed))
				continue;
			
			// if we haven't processed, show proper grid item
			if(in_array($test_key[0], $datagrid_items_raw)){
				$message .= $datagrid_format_insert_start;
				$message .= $datagrid_grid_code["{$test_key[0]}"][$datagrid_format_type];
				$message .= $datagrid_format_insert_end;
				
				for($i = 0; $i < count($datagrid_items_raw); $i++){
					if($datagrid_items_raw[$i] == $test_key[0])
						break;
				}
				
				$datagrid_items_displayed[] = $test_key[0];

				continue;
			}
			
			$message .= '<tr>';
			if(is_array($val)){
				if($ignore_empty_fields == 1 && count($val) == 0){
					continue;
				} else {
					$message .= 'RACKFORMS_CLB<td valign="top" class="name">' . $label . '</td><td valign="top" class="value">';
					foreach($val as $idx=>$v){
						if($idx != 0) { $message .= ' : '; }
						$message .= htmlentities($v);
					}
					$message .= '</td></tr>';
				}
			} else {
				if($ignore_empty_fields == 1 && $val == ''){
					continue;
				} else {
					$message .= 'RACKFORMS_CLB<td valign="top" class="name">' . $label . '</td><td valign="top" class="value">' . htmlentities($val) . '</td></tr>';
				}
			}
		}
		
		// Display Processed Bucket Items
		if(isset($bucket_data) && count($bucket_data) != 0 && substr_count($key, 'fb-bucket-repeater') == 0) {
			
			// have we processed the last child element this bucket repeates over?
			
			reset($bucket_data);
			$last_field = end($bucket_data);
			$last_field = key($bucket_data);
			reset($bucket_data);
			
			$last_field_array = explode('-bucket-', $last_field);
			
			$last_field = isset($last_field_array[0]) ? $last_field_array[0] : '';
			
			$has_processed = false;
			
			foreach($_SESSION['qs']["{$_SESSION['entry_key_unit-bucket-repeater']}"] as $k => $v) {
				if($k == $key) {
					break;
				}
				if($k == $last_field) {
					$has_processed = true;
				}
			}
		
			if($has_processed) {
				
				foreach($bucket_data as $d) {
						
					$message .= 'RACKFORMS_CLB<td valign="top" class="name">' . $d['label'] . '</td><td valign="top" class="value">' . htmlentities($d['value']) . '</td></tr>';
										
				}
					
				unset($bucket_data);
				
			}

		} // Display Processed Bucket Items.		
		
	}
	$message .= '</table>';
								
	$message .= <<<EOM

EOM;
		
	$message .= '</body></html>';
	//die(str_replace("RACKFORMS_CLB", "\n", $message)); // Uncomment this line to dump raw table HTML strait to your browser for debugging.
		
//
// process email section
//


//die($message); // uncomment this line to view the raw source of your message.

if(0) { // if debug
	echo "openssl.cafile: ", ini_get('openssl.cafile'), "\n";
	echo "curl.cainfo: ", ini_get('curl.cainfo'), "\n\n";
	
	echo "openssl_get_cert_locations";
	print_r(openssl_get_cert_locations());
	echo "\n\n";
}

require_once 'phpmailer/PHPMailerAutoload.php';

$mail             = new PHPMailer();

$body             = $message;
$body             = str_replace("\\",'',$body);
$body             = str_replace("\r\n",'<br/>',$body);
$body             = str_replace("\n",'<br/>',$body);

$body             = str_replace("RACKFORMS_CLB", "\n",$body);

//die($body); // uncomment this line to view the final email content.

$mail->IsSMTP(); // telling the class to use SMTP

$mail->IsHTML(true); // telling the class to use html

$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->Host       = "ocean.mxroute.com:465"; // sets the SMTP server - needs a port number to work!
$mail->SMTPSecure = "ssl";					// sets the connection prefix "", "ssl" or "tls"

// Require Properly Configurd Certificates (optional).


$mail->SMTPOptions = array(
	'ssl' => array(
			'verify_peer' => true,
			'verify_peer_name' => true,
			'allow_self_signed' => true
	)
);
		

$mail->Username   = "boss@danielhagwood.com"; // SMTP account username
$mail->Password   = "#p7]S[7O)1pm"; // SMTP account password

$mail->SetFrom("boss@danielhagwood.com", "Zero to Mastery");

$mail->Subject    = "Section 8";

$mail->Encoding   = "8bit";

$mail->CharSet    = "utf-8";

$mail->AltBody    = 'To view this message, please use an HTML compatible email viewer!'; // optional

$mail->MsgHTML($body);

// to (address) elements
$to = "";
$to = "boss@danielhagwood.com";
$to_array = explode(',', $to);
foreach($to_array as $to_item){
	if(trim($to_item) != ''){
		$mail->AddAddress($to_item);
	}
}

// cc elements
$cc = "";

$cc_array = explode(',', $cc);
foreach($cc_array as $cc_item){
	if(trim($cc_item) != ''){
		$mail->AddCC($cc_item);
	}
}

// bcc elements
$bcc = "";

$bcc_array = explode(',', $bcc);
foreach($bcc_array as $bcc_item){
	if(trim($bcc_item) != ''){
		$mail->AddBCC($bcc_item);
	}
}


// reply-to elements
$replyto = "";

$replyto_array = explode(',', $replyto);
foreach($replyto_array as $replyto_item){
	if(trim($replyto_item) != ''){
		$mail->AddReplyTo($replyto_item);
	}
}

// custom headers
$custom_headers = "";
$custom_headers = ",X-Priority: 3";
$custom_headers_array = explode(',', $custom_headers);
foreach($custom_headers_array as $custom_headers_item){
	if($custom_headers_item != ''){
		$mail->AddCustomHeader($custom_headers_item);
	}
}

//
// Attachments Block
//



// add any signature calls


// add any image calls


// add any static attachments
$rf_filename = "";
$rf_filepath = "";

// Pass directory, get all files within.

if(is_dir($rf_filepath)) {

	foreach (new DirectoryIterator($rf_filepath) as $fileInfo) {
	    if($fileInfo->isDot()) continue;
	    
	    $data = file_get_contents($rf_filepath. $fileInfo->getFilename());
	    
	    // Add file attachment to the message
		$mail->AddStringAttachment($data, $fileInfo->getFilename(), 'base64');

	}

} else {

	if(file_exists($rf_filepath)) {
	
		$data = file_get_contents("");
		
		// get name if set
		if($rf_filename == ''){
		
			if((version_compare(PHP_VERSION, '5.2.0') >= 0)){
				$pathparts = pathinfo($rf_filepath);
				$rf_filename = $pathparts['filename'] . '.' . $pathparts['extension'];
			} else {
				$pathparts = explode(DIRECTORY_SEPARATOR, $rf_filepath);
				$rf_filename = array_pop($pathparts);
			}
			
		}
		
		// Add file attachment to the message
		$mail->AddStringAttachment($data, $rf_filename, 'base64');
	}

}

	

if(!$mail->Send()) {
	$mail_error = "Mailer Error: " . $mail->ErrorInfo;
	$error = 1;
	if(0) { // if debug
  		$valError = "Mail was not sent. Please try again later. <br/>" . $mail_error;
	} else {
		$valError = "Mail was not sent. Please try again later.";
	}
} else { // no error sending message
	if(0) { // if debug
		echo "Additional SMTP Debug Message(s) (may be duplicate messages here) : " . $mail->ErrorInfo;
	}
}

// remove any inline image temp files


// remove any temp image files)


// remove any saved pdf files


// remove any static attachment files





} else {
	$error = 1;
	$valError = 'General Form Error';
} // end simple mail processing


} else { die(); }
?>


<?php

// PDF Rendering Flags.
$PAGE_IS_PDF = false;
$PDF_LIBRARY = "";

?>
		<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="0" >
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Powered By RackForms" />
<meta name="keywords" content="" />
<title>Submission Page</title>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['MAX_PATH']; ?>formpage.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['MAX_PATH']; ?>print.css" media="print" />
<link rel="stylesheet" type="text/css" href="../assets/css/main.css" media="screen" />

<script type="text/javascript" src="<?php echo $_SESSION['MAX_PATH']; ?>lib/utility.js"></script>



<style type="text/css">
body { margin:5px;  font-family: 'Helvetica'; }
html { margin:5px; }
</style>

<style type="text/css">		
/* link colors */
.rackforms-output-div a:link { color:#0D00AE; text-decoration:none; }
.rackforms-output-div a:visited { color:#0D00AE; text-decoration:none; }
.rackforms-output-div a:hover { color:#0D00AE; text-decoration:underline; }
.rackforms-output-div a:active { color:#0D00AE; text-decoration:none; }
</style>		


</head>
<body onload="">
<a name="top" id="top"></a>

<div id="rackforms-output-div" class="rackforms-output-div" style=" font-family: DejaVu Sans, Arial, Helvetica, sans-serif;  background-color: rgba(255,255,255,1);     width:500px;   ">
<?php
$visible = '';
$enabled = '';
?>

<!-- Form Field Start -->
<div id="fb_fld-244586" rf-field="true" class="" style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >

<?php
// Process Array Variables.
if(!isset($array_vars_processed)){ $vars = array(); process_array_variables($vars, ', '); $array_vars_processed = true; }
?>
		<div class="section-head " style="color:#222222; font-size:15pt; font-weight:normal; width:100%;    border-radius:0px;  text-align:left;   padding-left:0px;  padding-right:0px;  padding-top:0px;  padding-bottom:0px;  ">WE'LL BE IN TOUCH WHEN IT'S CURTAINS UP!</div>
</div>
<!-- Form Field End -->


	<div style="clear:both;"></div>
</div>


</body>
</html>



<?php

if(!isset($has_timed_redirect)){

	// clean up this session's data -- remove all field keys, then the qs and qs-label, followed by entry_key and page data
	
	// Build 866 - Remove Any ACI Transaction Data
	if(isset($_SESSION['aci-level1']))
		unset($_SESSION['aci-level1']); 
		
	// Build 770 - Remove Any Stripe Transaction Data
	if(isset($_SESSION['stripe']))
		unset($_SESSION['stripe']); 
	
	// Remove all singleton session data fields (selected items etc)
	$named_sesison_vars = array_keys($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]);
	
	foreach($named_sesison_vars as $var){
		// fields
		unset($_SESSION["{$var}"]);
		// isset
		unset($_SESSION["{$var}_is"]);
		// _processed - Build 712
		unset($_SESSION["{$var}_processed"]);
	}
	
	if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"])){
		foreach($_SESSION['qs']["{$_SESSION['entry_key_demo']}"] as $key=>$value){
			unset($_SESSION[$key]);
		}
	}
	
	// Build 764
	if(isset($_SESSION['fb_ecomm_demo'])){
		unset($_SESSION['fb_ecomm_demo']);
	}
	
	if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['signatures'])){
		unset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['signatures']);
	}
	
	unset($_SESSION['pages']);
	unset($_SESSION['pages-passed']["{$_SESSION['entry_key_demo']}"]);
	
	clean_output_location('tmp');
	clean_output_location('lib/jquery-upload/server/php/files/' . $_SESSION['entry_key_demo']); // Buld 860
	
	if(isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]); }
	if(isset($_SESSION['qs-entity']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs-entity']["{$_SESSION['entry_key_demo']}"]); }
	if(isset($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"])) { unset($_SESSION['qs-label']["{$_SESSION['entry_key_demo']}"]); }
	if(isset($_SESSION['entry_key_demo'])) {unset($_SESSION['entry_key_demo']); }
	
	if(isset($_SESSION['fielded_data_array'])) { unset($_SESSION['fielded_data_array']); }
	
	// Build 757 - unset main indentifiers, which is trnasformed at build time to specific element for this form.
	unset($_SESSION['entry_key_demo']);
		
	// Build 836
	if(isset($_SESSION['fb_entry_id_auto']))
		unset($_SESSION['fb_entry_id_auto']);

} // $has_timed_redirect

?>