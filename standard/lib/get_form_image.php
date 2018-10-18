<?php
/**
 * RackForms Form Library Files - get_form_image.php
 * @version 1.5
 * @author RackForms
 * @category Database
 * @copyright 2009-2013 nicSoft
 * @name get_form_image.php
 * 
 * ----------------------------------------------
 * How to use:
 * ----------------------------------------------
 * 
 * Create an image element in RackForms, and set the desired values for
 * its given parameters.
 *
 */

ini_set('display_errors', 0); // Change to 1 to display all error messages.
ini_set('error_reporting', E_ALL);

if(!session_id())
	session_start();

//if(!isset($_SESSION['form_image_token']))
//	return;

$debug = 0;

$fb_img_id = isset($_GET['id']) ? $_GET['id'] : -1;
$fb_img_size = isset($_GET['size']) ? $_GET['size'] : 'normal';

$action = isset($_GET['action']) ? $_GET['action'] : '';

$connector = isset($_GET['connector']) ? $_GET['connector'] : 'inline';

if($fb_img_id != -1){
	
	if(file_exists('../Database.php'))
		include_once '../Database.php';
	
	$dbh_img = new Database();
	
	$pass = false;

	switch($action){
		case 'name' :
			$sql_img = "SELECT image_name FROM fb_images WHERE image_id = ?";
			$params = array($fb_img_id);
			$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_img[0]['image_name'] != ''){
				echo $result_img[0]['image_name'];
			}
			exit(0);
			break;
		case 'mime' :
			$sql_img = "SELECT image_mime FROM fb_images WHERE image_id = ?";
			$params = array($fb_img_id);
			$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_img[0]['image_mime'] != ''){
				echo $result_img[0]['image_mime'];
			}
			exit(0);
			break;
		default :
			
			if($fb_img_size == 'normal'){
				
				$rand = isset($_GET['rand']) ? $_GET['rand'] : '';
				
				// Custom Image Call - Table and Column Dynamic.
				if(isset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name']) && $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'] != ""){
					
					$table = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'];
					$column = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name'];
					$columnid = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id'];
					$database_image_id = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_database_image_id'];
					

					$get = $column;
					$sql_img = "SELECT {$column} FROM {$table} WHERE {$columnid} = ?";
					
					$params = array($database_image_id);
					
					// Clear SESSION Items.
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name']);
					
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name']);
					
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id']);
					
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_database_image_id'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_database_image_id']);
					
					$pass = true;
					
				} else {
				
					$get = 'image_data';
					$sql_img = "SELECT image_name, image_mime, image_data FROM fb_images WHERE image_id = ?";
					
					$params = array($fb_img_id);
					
					$pass = true;
					
				}
			}
			
			if($fb_img_size == 'thumb'){
				
				// Custom Image Call - Table and Column Dynamic.
				if(isset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name']) && $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'] != ""){
						
					$table = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'];
					$column = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name'];
					$columnid = $_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id'];
						
					
					$get = 'image_data';
					$sql_img = "SELECT {$column} FROM {$table} WHERE {$columnid} = ?";
					
					$params = array($database_image_id);
						
					// Clear SESSION Items.
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_table_name']);
						
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_name']);
					
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_column_id']);
					
					$_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_database_image_id'] = "";
					unset($_SESSION["{$_SESSION['entry_key_demo']}"]["{$rand}"]['imagecall_database_image_id']);
					
					$pass = true;
						
				} else {
					
					$get = 'image_thumb';
					$sql_img = "SELECT image_name, image_mime, image_thumb FROM fb_images WHERE image_id = ?";
					
					$params = array($fb_img_id);
					
					$pass = true;
					
				}
				
			}
			
			// must pass a size!
			if($pass){
				
				$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
				
				if(isset($result_img[0]) && isset($result_img[0][$get]) && $result_img[0][$get] != ''){
					
					$mime = 'image/jpeg';
					
					if(isset($result_img[0]['image_mime']))
						$mime = $result_img[0]['image_mime'];
					
					header("Content-type: {$mime}");
					
					// Build 844 - Must unescape PostgreSQL data.
					if(isset($db_type) && $db_type == 'postgresql'){
						
						if(function_exists('pg_unescape_bytea'))
							echo pg_unescape_bytea($result_img[0][$get]);
					} else {
						echo $result_img[0][$get];
					}
					
				}
			}
	}

}
/**
 * Database create code
 * You can always customize this code, but if you simply want to get up and running
 * copy and paste this SQL create code into your DB admin tool.
 */

/*

CREATE TABLE fb_images(
  image_id INT (11) NOT NULL AUTO_INCREMENT,
  entry_id INT (11) DEFAULT NULL,
  image_caption LONGTEXT DEFAULT NULL,
  image_name VARCHAR (100) DEFAULT NULL,
  image_mime VARCHAR (40) DEFAULT NULL,
  image_size INT (11) DEFAULT NULL,
  image_data LONGBLOB DEFAULT NULL,
  image_thumb LONGBLOB DEFAULT NULL,
  PRIMARY KEY (image_id)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
AVG_ROW_LENGTH = 30720
CHARACTER SET utf8
COLLATE utf8_general_ci;

*/
?>