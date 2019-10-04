<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $current_user, $currentModule, $adb, $root_directory;

checkFileAccess("modules/$currentModule/$currentModule.php");
require_once("modules/$currentModule/$currentModule.php");
require_once('modules/Documents/Documents.php');

$filedirectory=$root_directory.'docImport';

$dh  = opendir($filedirectory);
$numfiles=0;
$finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension

$doc = new Documents();

$doc->column_fields["notes_title"] = $_REQUEST['title'];
$doc->column_fields["notecontent"] = $_REQUEST['description'];
$doc->column_fields["fileversion"] = 1;
$doc->column_fields["docyear"] = date('Y');
$doc->column_fields["filelocationtype"] = 'I';
$doc->column_fields["folderid"]=$_REQUEST['folderid'];
$doc->column_fields["filestatus"] = '1';
$doc->date_due_flag = 'off';
if($_REQUEST['assigntype'] == 'U') {
	$doc->column_fields['assigned_user_id'] = $_REQUEST['assigned_user_id'];
} elseif($_REQUEST['assigntype'] == 'T') {
	$doc->column_fields['assigned_user_id'] = $_REQUEST['assigned_group_id'];
}

echo "<br><H2>".getTranslatedString('Procesando Ficheros','MDI')."</H2><br>";
while (false !== ($filename = readdir($dh))) {
	$ffn=$filedirectory.'/'.$filename;
    if (!is_file($ffn)) continue;
    if (strpos($filename,'_')==0) {
    	echo "<font color=red>".getTranslatedString('Fichero','MDI')." <b>$filename</b> ".getTranslatedString('noInitialId','MDI').".</font> ".getTranslatedString('ContactOrAccountNo','MDI').".<br>";
    	continue; 
    }
	// Buscar cuenta relacionada
	$cod=substr($filename,0,strpos($filename,'_'));
	$mod='Accounts';
	$fac=$adb->pquery('select accountid as pid from vtiger_account where account_no=?',array($cod));
	if ($adb->num_rows($fac)==0) { // Not found, look in contact
		$mod='Contacts';
		$fac=$adb->pquery('select contactid as pid from vtiger_contactdetails where contact_no=?',array($cod));
		if ($adb->num_rows($fac)==0) { // Not found, can't upload
	    	echo "<font color=red>".getTranslatedString('Fichero','MDI')." <b>$filename</b> ".getTranslatedString('notFound','MDI').".</font> ".getTranslatedString('ContactOrAccountNo','MDI').".<br>";
	    	continue; 
	    }
	}
	$doc->parentid = $adb->query_result($fac,0,'pid');
	unset($_FILES);
    $f=array(
    'name'=>substr($filename,strpos($filename,'_')+1),
    'type'=>finfo_file($finfo, $ffn),
    'tmp_name'=>$ffn,
    'error'=>0,
    'size'=>filesize($ffn)
    );
	$_FILES["file$numfiles"] = $f;
	$doc->column_fields["filename"] = substr($filename,strpos($filename,'_')+1);
	$doc->column_fields["filesize"] = $f['size'];
	$doc->column_fields["filetype"] = $f['type'];
	$doc->save("Documents");
	$numfiles++;
	// Sacar informacion
	$pnm=getEntityName($mod,array($doc->parentid));
	$pnm=$pnm[$doc->parentid];
	echo "<font color=green>".getTranslatedString('Fichero','MDI')." <b>$filename</b> ".getTranslatedString('subido','MDI').".</font> ".getTranslatedString('Asociado con','MDI')." <a href=\"index.php?action=DetailView&module=$mod&record=".$doc->parentid."\">$pnm</a><br>";
}
if ($numfiles==0) {
	echo "<br/><font color=red>".getTranslatedString('noneFound','MDI').'.</font><br/><br/>';
}
echo "<br/>".getTranslatedString('Proceso terminado','MDI').'.  <a href="index.php">'.getTranslatedString('Volver','MDI')."</a><br/><br/>";
die();
?>