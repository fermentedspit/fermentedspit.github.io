<?php

if(!session_id()) { session_start(); }

// Build 838 - Session logic for emailing a save/resume later link.

$persistancelanguage = 0;

if(isset($_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_LANGUAGE'])){
	$persistancelanguage = (int)$_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_LANGUAGE'];
}

if(isset($_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_EMAIL_LINK'])){
	
	if(isset($_POST['email']) && isset($_POST['address'])){
		
		
		if($_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_EMAIL_LINK'] >= 3){
			
			header('Content-Type: application/json');
			echo '{"EMAIL_STATUS":"' . '' . '","EMAIL_ERROR":"' . 'Email Limit Reached.' .'"}';
			exit(0);
			
		}
		
		
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
		
		$pass = true;
		
		$email_error = "";
		$email_status = "";
		
		if($email == ""){
			
			$email_error = "Please provide a valid email address.";
			
			$pass = false;
			
		}
		
		// multiple recipients
		$to  = $email;
		
		switch($persistancelanguage){
			case '0' : // English
				$loc_title = "Save And Return";
				$loc_subject = "Form Save and Return Later Link.";
				$loc_instructions = "Please visit this address to resume your form submission";
				$loc_link_sent_to = "Link sent to";
				break;
			case '1' : // Dutch
				$loc_title = "Te slaan en terug"; 
				$loc_subject = "Vormen Opslaan en teruggaan Later Link.";
				$loc_instructions = "Kunt u terecht op dit adres om uw formulier indienen hervatten";
				$loc_link_sent_to = "Link naar";
				break; 
			case 2 : // German
				$loc_title = "Zu speichern und später";
				$loc_subject = "Formular zu speichern und später zurückkommen Verbindung.";
				$loc_instructions = "Bitte besuchen Sie diese Adresse in der Form vor Wiederaufnahme";
				$loc_link_sent_to = "Zu sendender Link";
				break; 
			case 3 : // Italian
				$loc_title = "Salvare e tornare più tardi"; 
				$loc_subject = "Formare Save and Return collegamento tardi.";
				$loc_instructions = "Si prega di visitare questo indirizzo per riprendere vostro modulo di presentazione";
				$loc_link_sent_to = "Collegamento inviato a";
				break; 
			case 4 : // Spanish
				$loc_title = "Guardar y volver más tarde"; 
				$loc_subject = "Formar Guardar y volver Enlace tarde .";
				$loc_instructions = "Por favor, visite esta dirección para reanudar el envío del formulario";
				$loc_link_sent_to = "Enlace enviado a";
				break; 
			
		}
		
		
		// message
		$message = <<<EOT
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$loc_title}</title>
<style type="text/css">
* {
	margin: 0;
	padding: 0;
}
body {
	color: #535353;
	background-color: #f4f4f4;
	font-family: Arial;
}
p {
	margin-bottom: 9px;
}
h1 {
	color: #95e000;
	font-size: 24pt;
	font-weight: bold;
	text-transform: uppercase;
}
div.Absolute-Center {
	background: none repeat scroll 0 0 #fafafa;
	top: 50px;
	height: 300px;
	left: 0;
	margin: auto;
	position: absolute;
	right: 0;
	width: 100%;
}
#inner {
	padding: 15px;
}
input {
	border: 1px solid #dedede;
	color: #585549;
	font-size: 12pt;
	height: 25px;
	padding: 3px;
	width: 99%;
}
input[type="submit"] {
	border: 1px solid #dedede;
	color: #585549;
	font-size: 12pt;
	height: 30px;
	padding: 3px;
	width: 99%;
}
</style>
</head>

<body>
<div class="Absolute-Center">
  <div id="inner">
    <h1>{$loc_title}</h1>
    <p>{$loc_instructions}:</p>
    <div style="clear:both;">{$address}</div>
    <br/>
    <br/>
  </div>
</div>
</body>
</html>
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
				
EOT;
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= 'To: Form User <' . $email. '>' . "\r\n";
		$headers .= 'From: noreply <{$email}>' . "\r\n";
		
		// Mail it
		
		if($pass){
			
			mail($to, $loc_subject, $message, $headers);
			
			$_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_EMAIL_LINK'] = $_SESSION["{$_SESSION['entry_key_demo']}"]['SAVE_AND_RESUME_EMAIL_LINK'] + 1;
			
			$email_status = "{$loc_link_sent_to} {$email}!";
			
		}
		
		// Echo JSON.
		header('Content-Type: application/json');
		echo '{"EMAIL_STATUS":"' . $email_status . '","EMAIL_ERROR":"' . $email_error .'"}';
		
	}
	
}

?>