<?php
//--
// PHP Page Script - Generated: October 17, 2018
//--


if(function_exists('ini_set')){
	ini_set('display_errors', 0); // Change to 1 to display all error messages.
	ini_set('error_reporting', E_ALL);
}

// Start our main session

if(!session_id()) { session_start(); }

// SID Support - Redirect and append SID if needed. 
// Allows SESSION Vars to be saved on first page.
// Server MUST have session.use_trans_sid enabled.

$sid_url = "";

if(defined('SID'))
	$sid_url = (strlen(SID) ? ('?' . htmlspecialchars(SID)) : '');



// Path info for PHP Include
$_SESSION['MAX_PATH'] = '';
$ct_tmp = '';
$ct = substr_count('', "/");
$_SESSION['MAX_PATH_PROC'] = './';
// Build 632 - Refine this check
if($ct != 0){ // if a PHP Export Path is set, we need to create a path *back* to the include calling file
	while($ct != 0){
		$ct_tmp .= '../';
		$ct--;
	}
	$_SESSION['MAX_PATH_PROC'] = $ct_tmp;
}
// echo $_SESSION['MAX_PATH_PROC']; // Uncomment to see which path RackForms is using to process pages

// IE P3P Policy Header - must send to allow 3rd party cookies (when form page used as iFrame include)
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

// Build 695 - We now create this key here as well as on process pages.
$_SESSION['entry_key_demo'] = isset($_SESSION['entry_key_demo']) ? $_SESSION['entry_key_demo'] : md5(time() + rand(10000, 1000000));

// Form Page Security
$domain_list = explode(',',"");
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

// Login Logic
$fb_login = new stdClass();
$fb_login->login = 0;
$fb_login->persistent_login = 0;
$fb_login->persistent_login_job_id = 2;
$fb_login->username = "";
$fb_login->password = "";
$fb_login->login_attempts = 2;
$fb_login->login_message = <<<EOT
Please login to continue.
EOT;
$fb_login->login_error_message = <<<EOT
Invalid login. Please try again.
EOT;
$fb_login->login_attempts_error_message = <<<EOT
Maximum login attempts exceeded.
EOT;
$fb_login->redirect = "page0.php";

$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login'] = $fb_login;


// Global Timestamps, Etc.
$timestamp = time();
$datetime = date('Y-m-d  H:i:s', time());
$datetime_american = date('m-d-Y', time());
$datetime_european = date('d-m-Y', time());

// Visitor IP
$remote_ip = $_SERVER['REMOTE_ADDR'];

include_once "{$_SESSION['MAX_PATH']}security/secure_page.php";
include_once "{$_SESSION['MAX_PATH']}lib/utility.php";

// Build 693 - We now include database code by default.
if(file_exists("{$_SESSION['MAX_PATH']}Database.php")){
	@include_once "{$_SESSION['MAX_PATH']}Database.php";
}

// Build 836
if(0 == 3){
	if(!isset($_SESSION['fb_entry_id_auto'])){
	
		$_SESSION ['fb_entry_id_auto'] = isset($_GET['RID']) ? filter_input(INPUT_GET, 'RID', FILTER_SANITIZE_STRING) : "";
		
		if($_SESSION['fb_entry_id_auto'] == ""){
			$_SESSION['fb_entry_id_auto'] = randomPassword();
		}
	}
}

// Build 689
loadPersistantValues(0, $job_id, "page0.php");

// Build 805
if(1 == 0){
    init_stats($job_id, "page0.php", 'form');
}

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
<title>Form Page</title>
<link rel="stylesheet" type="text/css" href="formpage.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['MAX_PATH']; ?>print.css" media="print" />
<link rel="stylesheet" type="text/css" href="../assets/css/main.css" media="screen">



<!-- tinymce -->

<!-- val script -->
<script type="text/javascript">
	var phppath = '<?php echo $_SESSION['MAX_PATH']; ?>';
	var pageName = 'page0.xml';
	// error logic
	var showMessage = 0;
	var showAlert = 1;
	var showDefault = 1;
	var errorStyle = 0;
	var errorColor = "#ffebee";
	var jspopup_errormessage = "You have not completed this form correctly.\nPlease go back and review your answers.";
	var layout = 0;
	var tablemode = 0;
	
	
</script>

<script type="text/javascript" src="<?php echo $_SESSION['MAX_PATH']; ?>xmlform.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['MAX_PATH']; ?>conditional.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['MAX_PATH']; ?>lib/utility.js"></script>













<style type="text/css">
body { margin:0px;  font-family: 'Helvetica'; }
html { margin:0px; }
</style>

<!-- custom style -->
<style type="text/css">

span.error { background: #ffebee; } div.error { background-color:#ffebee; }

.req-star { color: #cc0000  }

div.validation-style-3-line, span.validation-style-3-line {
  border-top: 8px solid #ffebee;
  height: 1px;
  padding-top: 0;
  position: absolute;
  top: -12px;
  width: 104%;
  z-index: -1;
}
  		
.validation-style-3-icon {
  background-image: url("icons/validation/style-3/warning-icon-simple-16.png");
  height: 16px;
  position: absolute;
  right: -19px;
  top: -17px;
  width: 16px;
}

.validation-style-3-message { 
  position:absolute; 
  top:-15px; 
  right:26px; 
  height:16px; 
  font-size:10px; 
  font-weight:bold; 
  color:#A6A6A6
}

</style>

<!-- End Custom Style -->

<style type="text/css">

/* link colors */
.rackforms-output-div a:link { color:#0D00AE; text-decoration:none; }
.rackforms-output-div a:visited { color:#0D00AE; text-decoration:none; }
.rackforms-output-div a:hover { color:#0D00AE; text-decoration:underline; }
.rackforms-output-div a:active { color:#0D00AE; text-decoration:none; }

span.errormsg { color:#A6A6A6; }
</style>

<!--[if lte IE 8]>
<style type="text/css">
.btn { 	margin: 0; padding: 0 .70em; width: auto; overflow: visible; }
</style>
<![endif]-->



</head>
<body onload="importXML(getXmlUrl(), parseFormXml ,checkCookie()) ; ">
<a id="top"></a>


<!-- OUTPUT START -->


<div id="rackforms-output-div-page0" class="rackforms-output-div"> <!-- " col-lg-9 ui form" style=" font-family: DejaVu Sans, Arial, Helvetica, sans-serif;    width:500px; margin-left:auto; margin-right:auto; padding:15px; box-shadow: 0 2px 8px #111111; border-radius: 15px; width:500px;  border: 1px solid #cecece; "> -->
<form class="rackforms-output-sortable" action="<?php echo $_SESSION['MAX_PATH']; ?>page0_process.php<?php echo $sid_url; ?>" method="post" enctype="application/x-www-form-urlencoded" name="page0" id="page0" target="_self" >

<?php $visible = ''; $enabled= ''; ?>

<!-- ~~~~~~~~~~~~~~~~ -->
<!-- FORM FIELD START -->
<!-- ~~~~~~~~~~~~~~~~ -->

<div id="fb_fld-577848" rf-field="true" class="form-field-wrapper type-sectionheader " style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >

<?php
// Process Array Variables.
if(!isset($array_vars_processed)){ $vars = array(); process_array_variables($vars, ', '); $array_vars_processed = true; }
?>
		
<div class="section-head " style="color:#333333; font-size:15pt; font-weight:; width:100%;    border-radius:0px;  text-align:left;   padding-left:0px;  padding-right:0px;  padding-top:0px;  padding-bottom:0px;  ">
Admit One!
</div>

</div>
<!-- Form Field End -->


<div style="clear:both;"></div><!-- Clear DIV -->


<?php $visible = ''; $enabled= ''; ?>

<!-- ~~~~~~~~~~~~~~~~ -->
<!-- FORM FIELD START -->
<!-- ~~~~~~~~~~~~~~~~ -->

<div id="fb_fld-email" rf-field="true" class="form-field-wrapper type-text " style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >
<div style=" " class="heading-main">
	<label for="email" style="color:#222222; font-size:10pt;   font-weight:; ">email<span class="req-star">&#42;</span>&nbsp;</label>
	<div style="position:relative">
		<div id="email-validation-style-3-line" class="validation-style-3-line" style="display:none;">&nbsp;</div>
		<div id="email-validation-style-3-icon" class="validation-style-3-icon" style="display:none;">&nbsp;</div>
		<div id="email-validation-style-3-message" class="validation-style-3-message errormsg" style="display:none;">&nbsp;</div>
	</div>
</div>

<input <?php echo $enabled; ?>  class="form-control fld-full" type="text"   name="email" id="email"   value="<?php isset($_SESSION['email']) ? print htmlentities($_SESSION['email'], ENT_COMPAT, 'UTF-8') : print ""; ?>"  style=" color:#444444; width:98%;   "  />
<span class="tailtext"></span>

<div class="fbtooltip-email fbtooltip" style="display:none;"></div>

<br />

</div>
<!-- Form Field End -->


<div style="clear:both;"></div><!-- Clear DIV -->


<?php $visible = ''; $enabled= ''; ?>

<!-- ~~~~~~~~~~~~~~~~ -->
<!-- FORM FIELD START -->
<!-- ~~~~~~~~~~~~~~~~ -->

<div id="fb_fld-f_Name" rf-field="true" class="form-field-wrapper type-text " style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >
<div style=" " class="heading-main">
	<label for="f_Name" style="color:#222222; font-size:10pt;   font-weight:; ">First Name<span class="req-star">&#42;</span>&nbsp;</label>
	<div style="position:relative">
		<div id="f_Name-validation-style-3-line" class="validation-style-3-line" style="display:none;">&nbsp;</div>
		<div id="f_Name-validation-style-3-icon" class="validation-style-3-icon" style="display:none;">&nbsp;</div>
		<div id="f_Name-validation-style-3-message" class="validation-style-3-message errormsg" style="display:none;">&nbsp;</div>
	</div>
</div>

<input <?php echo $enabled; ?>  class="form-control fld-full" type="text"   name="f_Name" id="f_Name"   value="<?php isset($_SESSION['f_Name']) ? print htmlentities($_SESSION['f_Name'], ENT_COMPAT, 'UTF-8') : print ""; ?>"  style=" color:#444444; width:98%;   "  />
<span class="tailtext"></span>

<div class="fbtooltip-f_Name fbtooltip" style="display:none;"></div>

<br />

</div>
<!-- Form Field End -->


<div style="clear:both;"></div><!-- Clear DIV -->


<?php $visible = ''; $enabled= ''; ?>

<!-- ~~~~~~~~~~~~~~~~ -->
<!-- FORM FIELD START -->
<!-- ~~~~~~~~~~~~~~~~ -->

<div id="fb_fld-l_Name" rf-field="true" class="form-field-wrapper type-text " style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >
<div style=" " class="heading-main">
	<label for="l_Name" style="color:#222222; font-size:10pt;   font-weight:; ">Last Name<span class="req-star">&#42;</span>&nbsp;</label>
	<div style="position:relative">
		<div id="l_Name-validation-style-3-line" class="validation-style-3-line" style="display:none;">&nbsp;</div>
		<div id="l_Name-validation-style-3-icon" class="validation-style-3-icon" style="display:none;">&nbsp;</div>
		<div id="l_Name-validation-style-3-message" class="validation-style-3-message errormsg" style="display:none;">&nbsp;</div>
	</div>
</div>

<input <?php echo $enabled; ?>  class="form-control fld-full" type="text"   name="l_Name" id="l_Name"   value="<?php isset($_SESSION['l_Name']) ? print htmlentities($_SESSION['l_Name'], ENT_COMPAT, 'UTF-8') : print ""; ?>"  style=" color:#444444; width:98%;   "  />
<span class="tailtext"></span>

<div class="fbtooltip-l_Name fbtooltip" style="display:none;"></div>

<br />

</div>
<!-- Form Field End -->


<div style="clear:both;"></div><!-- Clear DIV -->


<?php $visible = ''; $enabled= ''; ?>

<!-- ~~~~~~~~~~~~~~~~ -->
<!-- FORM FIELD START -->
<!-- ~~~~~~~~~~~~~~~~ -->

<div id="fb_fld-Submit" rf-field="true" class="form-field-wrapper type-submit " style="float:left; width:500px; margin-bottom:5px; <?php echo $visible; ?>" >

<style type="text/css" scoped>

#Submit{

}

#Submit:hover{

}

#Submit:focus{

}

</style>
		
<input name="Submit" id="Submit" style="width:98%;" class="nolabel btn btn-primary  fld-full" type="submit"  value="Get Your Ticket!"  onclick="fb.disable_submit(event);" />

</div>
<!-- Form Field End -->


<div style="clear:both;"></div><!-- Clear DIV -->


</form>
<?php if(isset($_SESSION["e_message"])){  ?>
<div class="err-msg"><?php echo html_entity_decode($_SESSION["e_message"], ENT_QUOTES); ?></div>
<?php unset($_SESSION["e_message"]); } ?>

<div style="clear:both;"></div>
</div>

</body>
</html>
