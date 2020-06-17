<?php
	error_reporting(E_ALL);
	ini_set('display_errors',1);

$mysql_host = "192.168.102.224";

$mysql_user_name = "syncuser";

$mysql_password = "Concert222";

$default_db_name = 'instance_clone';

$base_path = "/var/www/sites";

$_REQUEST['instance_name']  = str_replace(" ", "_", strtolower($_REQUEST['instance_name']));

$instance_name = $_REQUEST['instance_name'];

$response = array();

if($instance_name != ''){

        if(file_exists($base_path . "/" . $instance_name)){

                $response = array("ERROR" => "Domain not Available");

        } else {

        //      system("mkdir ". $base_path . "/" . $instance_name);
        //      system("sudo chown syncuser:apache ". $base_path . "/" . $instance_name);
                $result = system("sudo /usr/bin/git clone https://gituser:Concert222@dev.omnisrv.com:3443/git/Omniver4hq.git $base_path" . "/" . $instance_name . ' 2>&1', $err);
echo 'r - ' . $result . '<br />';
print_r($err);
echo "Cloning finished<br />";

#$command = "sudo chown -R syncuser:apache ". $base_path . "/" . $instance_name;
#echo $command . '<br />';
#$result = system($command, $err);
$result = system("sudo chown -R syncuser:apache ". $base_path . "/" . $instance_name, $err);
#echo "ownership result: {$result}";
#print_r($err);
echo "ownership changed<br />";

system("sudo chmod 770 " . $base_path . "/" . $instance_name);
echo "chmod changed<br />";

                $db_name = '360vew_'.$instance_name;

                $con = mysqli_connect($mysql_host,$mysql_user_name, $mysql_password) or die(mysqli_error());

                mysqli_query($con, "create database $db_name CHARACTER SET utf8 COLLATE utf8_general_ci") or die(mysqli_error());

                //exec("mysqldump -h crmdbsrv -u $mysql_user_name -p'$mysql_password' --routines $default_db_name > backup.sql");
                exec("mysql -h crmdbsrv -u $mysql_user_name -p'$mysql_password' $db_name < /home/syncuser/instance_clone.sql");
                //unlink("backup.sql");

                $newInstanceInfo = array();

                $newInstanceInfo['db_name'] = $db_name;

                $newInstanceInfo['site_url'] = "https://$instance_name.360vew.com/";

                $newInstanceInfo['root_directory'] = $base_path . "/" . $instance_name . "/";

system("/usr/bin/bash /var/www/sites/360vew/fixperms.sh " . $instance_name);
echo "Permissions run<br />";


// Copy the 'clean' storage and user_privileges directories.
#system("/usr/bin/bash /var/www/sites/360vew/copydirs.sh " . $instance_name);
#echo "Directories copied<br />";
create_directory($instance_name);
copy_files("/var/www/sites/user_privileges/user_privileges_1.php","/var/www/sites/{$instance_name}/user_privileges/user_privileges_1.php");
copy_files("/var/www/sites/user_privileges/sharing_privileges_1.php","/var/www/sites/{$instance_name}/user_privileges/sharing_privileges_1.php");
copy_files("/var/www/sites/user_privileges/index.html","/var/www/sites/{$instance_name}/user_privileges/index.html");

copy_files("/var/www/sites/storage/.htaccess","/var/www/sites/{$instance_name}/storage/.htaccess");

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
                "_DBC_SERVER_" => "192.168.102.224",
                "_DBC_PORT_" => "3306",
                "_DBC_USER_" => "syncuser",
                "_DBC_PASS_" => "Concert222",
                "_DBC_TYPE_" => "mysqli",
                "_DB_STAT_" => "true",
                "_VT_TMPDIR_" => "cache/images/",
                "_VT_CACHEDIR_" => "cache/",
                "_VT_UPLOADDIR_" => "cache/upload/",
                "_MASTER_CURRENCY_" => 'USA, Dollars',
                "_VT_CHARSET_" => "UTF-8",
                "_VT_DEFAULT_LANGUAGE_" => "en_us",
                "_VT_APP_UNIQKEY_" => "392339bb7b455c98c71fdf3cc06bb408",
                "_PORTAL_URL_" => $newInstanceInfo['site_url'] . "cp/"
        );

        foreach($config_info as $key => $val){
                $fileReplacements[$key] = $val;
        }

        $filesInfo = array();

        $filesInfo['template_file_path'] = '/var/www/sites/360vew/config.template.php';
        $filesInfo['target_file_path'] = $root_dir. 'config.inc.php';

        //remove the config file if it already exists.
        //unlink($filesInfo['target_file_path']);

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

function copy_files($src, $dest){
        if(!copy($src, $dest)){
                echo "failed to copy file..." . $src;
        }else{
                echo $src . " copied!<br />";
        }
}

function create_directory($dest){
 if (!file_exists("/var/www/sites/{$dest}/storage")) {
    mkdir("/var/www/sites/{$dest}/storage", 0755, true);
 }
}
?>