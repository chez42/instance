<?php
$mysql_host = "localhost";

$mysql_user_name = "syncuser";

$mysql_password = "Concert222";

$default_db_name = 'live_omniscient_copy_2';

$base_path = "/var/www/sites/ver4manish";

$_REQUEST['instance_name']  = str_replace(" ", "_", strtolower($_REQUEST['instance_name']));

$instance_name = $_REQUEST['instance_name'];

$response = array();

if($instance_name != ''){

	if(file_exists($base_path . "/" . $instance_name)){

		$response = array("ERROR" => "Domain not Available");

	} else {

		system("mkdir ". $base_path . "/" . $instance_name);

		exec("git clone https://gituser:Concert222@dev.omnisrv.com:3443/git/Omniver4_clone.git $base_path" . "/" . $instance_name);
		
		$db_name = 'live_omniscient_'.$instance_name;
		
		$con = mysqli_connect($mysql_host,$mysql_user_name, $mysql_password) or die(mysqli_error());
		
		mysqli_query($con, "create database $db_name CHARACTER SET utf8 COLLATE utf8_general_ci") or die(mysqli_error());
		
		exec("mysqldump -u $mysql_user_name -p'$mysql_password' --routines $default_db_name > backup.sql");
		exec("mysql -u $mysql_user_name -p'$mysql_password' $db_name < backup.sql");
		unlink("backup.sql");
		
		$newInstanceInfo = array();

		$newInstanceInfo['db_name'] = $db_name;

		$newInstanceInfo['site_url'] = "https://dev.omnisrv.com/ver4manish/$instance_name";

		$newInstanceInfo['root_directory'] = $base_path . "/" . $instance_name . "/";

		createConfigurationFiles($newInstanceInfo);
		
		$response = array("SUCCESS" => "Instance Set UP Successfully " . $newInstanceInfo['site_url']);
	
	}
	
	

} else {
	
	$response = array("ERROR" => "Please provide Domain Name");

}

echo json_encode($response);

exit;

function createConfigurationFiles($newInstanceInfo){

	$root_dir = $newInstanceInfo['root_directory'];

	$fileReplacements = array();

	$config_info = array(
		"_DBC_NAME_" => $newInstanceInfo['db_name'],
		"_SITE_URL_" =>  $newInstanceInfo['site_url'],
		"_VT_ROOTDIR_" => $newInstanceInfo['root_directory'],
		"_DBC_SERVER_" => "localhost",
		"_DBC_PORT_" => "3306",
		"_DBC_USER_" => "syncuser",
		"_DBC_PASS_" => "Concert222",
		"_DBC_TYPE_" => "mysqli",
		"_DB_STAT_" => "true",
		"_VT_CACHEDIR_" => "cache/",
		"_VT_UPLOADDIR_" => "cache/upload/",
		"_MASTER_CURRENCY_" => 'USA, Dollars',
		"_VT_CHARSET_" => "UTF-8",
		"_VT_DEFAULT_LANGUAGE_" => "en_us",
		"_VT_APP_UNIQKEY_" => "392339bb7b455c98c71fdf3cc06bb408",
	);

	foreach($config_info as $key => $val){
		$fileReplacements[$key] = $val;
	}

	$filesInfo = array();

	$filesInfo['template_file_path'] = $root_dir . 'config.template.php';
	$filesInfo['target_file_path'] = $root_dir. 'config.inc.php';

	//remove the config file if it already exists.
	unlink($filesInfo['target_file_path']);

	//This function is in app_controller
	createFileFromTemplateFile($filesInfo, $fileReplacements);

	return true;
}

function createFileFromTemplateFile($filesInfo = array(), $fileReplacements = array()){

	$templateFilename = $filesInfo['template_file_path'];

	//open template file in read only mode
	$templateHandle = fopen($templateFilename, "r");

	if($templateHandle) {

		$targetFilename = $filesInfo['target_file_path'];

		//open target file in write only mode
		$targetHandle = fopen($targetFilename, "w");
		if($targetHandle) {
			while (!feof($templateHandle)) {
				$buffer = fgets($templateHandle);

				//replace the strings in target file.
				foreach($fileReplacements as $key => $value){
						$buffer = str_replace("$key",$value, $buffer);
				}
				fwrite($targetHandle, $buffer);
			}
			fclose($targetHandle);
		}

		fclose($templateHandle);
	}
}
?>