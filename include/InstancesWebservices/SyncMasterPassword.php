<?php
function vtws_sync_master_password($element){
    
    global $adb, $current_user, $site_URL;
    
    $fileContent = file_get_contents('config.inc.php');
    
    $patternString = "\$%s = '%s';";
    
    $fieldName = 'master_password';
    
    $fieldValue = $element['master_password'];
    
    $pattern = '/\$' . $fieldName . '[\s]+=([^;]+);/';
    
    $replacement = sprintf($patternString, $fieldName, ltrim($fieldValue, '0'));
    $fileContent = preg_replace($pattern, $replacement, $fileContent);
    
    $filePointer = fopen('config.inc.php', 'w');
    fwrite($filePointer, $fileContent);
    fclose($filePointer);
    
    
    return array('success'=>true);
    
}