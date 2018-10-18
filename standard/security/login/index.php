<?php
/**
 * RackForms Page Login Script
 * ---------------------------------------
 * Note that username is optional. If none exists we process without.
 * 
 * @version 1
 * @author nicSoft
 */
session_start();

if(isset($_SESSION['entry_key_demo']) && isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login'])){
	$fb_login = $_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_login'];
} else {
	// If no login logic is set we die for security purposes.
	die('Form Login Fatal Error. You must access this page from the forms main page, not directly.');
}

$message = "";

// Build 882 - Persist Original Query String.

$query_string = "";

if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {

	$query_string = '?' . htmlspecialchars($_SERVER['QUERY_STRING']);

}

if(isset($_POST['submit'])){
	
	$pass = 1;
	
	$query_string_post = filter_input(INPUT_POST, 'query-string', FILTER_SANITIZE_STRING);
	
	// login attempts logic
	if(!isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_attempts'])){
		$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_attempts'] = 1;
	} else {
		$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_attempts'] += 1;
	}
	
	switch((int)$fb_login->login_attempts){
		case 0 :
			if($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_attempts'] > 3){
				$pass = 0;
				$message = $fb_login->login_attempts_error_message;
			}
			break;
		case 1 :
			if($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_attempts'] > 5){
				$pass = 0;
				$message = $fb_login->login_attempts_error_message;
			}
			break;
	}
		
	if($pass == 1){

		if($fb_login->username != ""){
			if(!isset($_POST['username']) || $_POST['username'] == ""){
				$pass = 0;
			} else {
				$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_username'] = $_POST['username'];
				if($_POST['username'] != $fb_login->username){
					$pass = 0;
				}
			}
		}
		
		if(!isset($_POST['password']) || $_POST['password'] == ""){
			$pass = 0;
		} else {
			if($_POST['password'] != $fb_login->password){
				$pass = 0;
			}
		}
	
		// result	
		if($pass == 1){
			$_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_authenticated'] = true;
			$_SESSION['fb_basic_login_authenticated_persistent']["{$fb_login->persistent_login_job_id}"] = true; // Build 863 - Allows for persistent logins.
			header("Location: ../../{$fb_login->redirect}{$query_string_post}");
			exit;
		} else {	
			$message = $fb_login->login_error_message;
		}
		
	} // attempts logic
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Form</title>

<style type="text/css">

body { text-align: center; width: 100%; margin:0px auto; } 

#center { position: absolute; top: 25%; width: 100%; height: 1px; overflow: visible }

#main { position: absolute; left: 50%; width: 720px; margin-left: -360px; height: 540px; top: -270px } 

#input-username { width:142px; }

#input-password { width:142px; }

.content { width: 200px; margin-left: auto; margin-right: auto; text-align: left; background-color: #006666; }  

.main-text { font-family:Arial, Helvetica, sans-serif; font-size:12px; font-weight:normal; color:#666; }

.color-red { color:red; }

.header { font-size:14px; }
.label { float:left; width:70px; }

.clear { clear:both; }

</style>

</head>

<body>
<div id="center">

<div id="content" align="center">

<form id="login" name="login" action="index.php" method="post" >

<div id="form-wrapper" style="width:620px; height:282px; background-image:url(images/background.gif); position:relative;">

	<div id="content-inner" style="position:absolute; left:190px; top:<?php if($fb_login->username != ""){ echo 52; } else { echo 83; } ?>px; width:221px;">
    
        <div id="title" style="float:left;">
            <span class="main-text"><?php echo $fb_login->login_message; ?></span>
        </div>
        
        <div class="clear">&nbsp;</div>
        
        <input type="hidden" id="query-string" name="query-string" value="<?php echo $query_string; ?>" />
    
        <?php if($fb_login->username != ""){ ?>
            <div class="label" style="float:left;"><label class="main-text" for="username" title="Username">Username:</label></div>
            <div id="username" style="float:left;">
                <input type="text" id="input-username" name="username" value="<?php isset($_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_username']) ? 
                		print $_SESSION['qs']["{$_SESSION['entry_key_demo']}"]['fb_basic_login_username'] : ""; ?>" />
            </div>
            
            <div class="clear">&nbsp;</div>
            
        <?php } ?>
    
        <div id="password" style="float:left;">
            <div  class="label" style="float:left;"><label class="main-text" for="password" title="Password">Password:</label></div>
            <input type="password" id="input-password" name="password" />
        </div>
        
        <div class="clear">&nbsp;</div>
        
        <div id="submit" style="float:right;">
            <input type="submit" id="submit" name="submit" value="Submit" />
        </div>
    
        <div class="clear">&nbsp;</div>
    
        <div id="message" style="float:left;">
            <span class="main-text color-red"><?php if($message != "") { echo $message; } ?></span>
        </div>
        
	</div><!-- end #content-inner -->

</div> <!-- end #wrapper-wrapper -->

</form>

</div><!-- end #main -->

</div><!-- end #center -->
</body>

</html>