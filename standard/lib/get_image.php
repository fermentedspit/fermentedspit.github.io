<?php
/**
 * RackForms Form Library Files - get_image.php
 * @version 1.3
 * @author RackForms
 * @category Database
 * @copyright 2009-2011 nicSoft
 * @name get_image.php
 * 
 * ----------------------------------------------
 * get_image.php
 * ----------------------------------------------
 * 
 * Returns(echo's) raw image data from a database to feed a template call such as:
 * <img src="lib/get_image.php?id=<?php echo $res['id'];?>" />
 * 
 * Used in Builder Image Calls in the Image Location Popup Editor as:
 * 
 * FULL SIZE IMAGES:
 * lib/get_image.php?id=#{id}&size=normal
 * 
 * THUMBNAIL IMAGES
 * lib/get_image.php?id=#{id}&size=thumb
 * 
 * ----------------------------------------------
 * How to use:
 * ----------------------------------------------
 * 
 * As a standard use scenario, when you export a form RackForms includes this file in the
 * /lib directory with your form. This file calls your RackForms database looking for three fields:
 * 
 * image_name image_mime, image_date
 * 
 * The call is based on the presence of a relational field: entry_id
 * That is, the image is related to another table in your database who's PK or other identifier
 * is the value of the entry_id field.
 * 
 * For example, you could use this SQL+ code in a form that submits an image:
 * 
 * INSERT INTO fb_images (image_data, image_thumb, image_name, image_mime, image_size, entry_id) VALUES (?,?,?,?,?,?)
 * 
 * With these paramters:
 * 
 * image, image_thumb, image_name, image_mime, image_size, ${ret_val}
 * 
 * Where ${ret_val} is the lastInsertID of a prior database INSERT.
 * 
 * Should the database call return an image, a header is sent with the mime type of your image, 
 * and the image is returned.
 * 
 * This function can also return meta information about your image.
 * To do so, in addition to the id, call the file with an action parameter, 
 * with the aciton name being the field you want:
 * lib/get_image.php?id=#{id}&action=name
 * 
 * ----------------------------------------------
 * IMPORTANT DATABASE CONNECTION INFORMATION
 * ----------------------------------------------
 * 
 * As of Build 695, by default this script uses inline connection data
 * RackForms automatically creates for us at form export.
 * 
 * We do this via the $_GET['connector'] property, which defaults to 'inline'
 * Thus, if this property is not set in our query string for this item,
 * we by default use the same connection details as the parent Repeater element.
 * 
 * This also means if we CHANGE this with a different value (just by adding the 
 * $_GET['connector'] value to the query string), we will revert to Pre-695
 * behavior which is to use the database details contained in config.php.
 * 
 * It is not recommended to change this then, as 9 times out of 10 the database
 * holding our text data is also the same as the one holding the images. 
 * 
 * ----------------------------------------------
 * Rationale
 * ----------------------------------------------
 * 
 * You may be thinking this is a rather complex process just to get an image from the database.
 * This is because while we could create a script that calls a single image field,
 * this will rarely be useful. Images will almost always be tied to some other information,
 * be it user id's, content metadata, and so on.
 * 
 * As good database design dictates that we create relational structures when possible to
 * avoid unnecessary overhead and duplication, we thusly break image calls into two major
 * components - the image data and anything else.
 * 
 * This code reflects that practice, but it also means at very minimum we add a touch
 * of complexity to the process by separating images from other data.
 * 
 * Please see below for usage code and full database create code.
 * 
 */

ini_set('display_errors', 0); // Change to 1 to display all error messages.
ini_set('error_reporting', E_ALL);

$debug = 0;

$fb_img_id = isset($_GET['id']) ? $_GET['id'] : -1;
$fb_img_size = isset($_GET['size']) ? $_GET['size'] : 'normal';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// Build 695 - Use Inline Connection Details By Default
// These values come from the jobs Repeater element.
$connector = isset($_GET['connector']) ? $_GET['connector'] : 'inline';

if($fb_img_id != -1){
	
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

	switch($action){
		case 'name' :
			$sql_img = "SELECT image_name FROM fb_images WHERE entry_id = ?";
			$params = array($fb_img_id);
			$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_img[0]['image_name'] != ''){
				echo $result_img[0]['image_name'];
			}
			exit(0);
			break;
		case 'mime' :
			$sql_img = "SELECT image_mime FROM fb_images WHERE entry_id = ?";
			$params = array($fb_img_id);
			$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
			if($result_img[0]['image_mime'] != ''){
				echo $result_img[0]['image_mime'];
			}
			exit(0);
			break;
		default :
			$pass = 0;
			if($fb_img_size == 'normal'){
				$pass = 1;
				$get = 'image_data';
				$sql_img = "SELECT image_name, image_mime, image_data FROM fb_images WHERE entry_id = ?";
			}
			if($fb_img_size == 'thumb'){
				$pass = 1;
				$get = 'image_thumb';
				$sql_img = "SELECT image_name, image_mime, image_thumb FROM fb_images WHERE entry_id = ?";
			}
			// must pass a size!
			if($pass){
				$params = array($fb_img_id);
				$result_img = $dbh_img->pdo_procedure_params($debug, $sql_img, $db_host, $db_type, $mysql_socket, $mysql_port, $dbdsn, $db_user, $db_pass, $db_catalog, $params, $return_true = 0);
				
				if($result_img[0][$get] != ''){
					header("Content-type: {$result_img[0]['image_mime']}");
					echo $result_img[0][$get];
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