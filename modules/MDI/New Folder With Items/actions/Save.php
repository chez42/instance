<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class MDI_Save_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $current_user, $currentModule, $adb, $root_directory;
        $filedirectory=$root_directory.'docImport';

        $dh  = opendir($filedirectory);
        $numfiles=0;
        $finfo = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
        
        while (false !== ($filename = readdir($dh))) {
            $ffn=$filedirectory.'/'.$filename;
            if (!is_file($ffn)) continue;
            
            if (strpos($filename,'_')==0) {
                echo "<font color=red><strong>{$filename}</strong> has no Contact or Account number</font><br />";
                continue;
            }
            
            $cod=substr($filename,0,strpos($filename,'_'));
            $mod='Accounts';
            $fac=$adb->pquery('select a.accountid as pid, e.smownerid AS owner from vtiger_account a '
                            . 'join vtiger_crmentity e ON e.crmid = a.accountid '
                            . 'where a.account_no=? ',array($cod));
            if ($adb->num_rows($fac)==0) { // Not found, look in contact
                    $mod='Contacts';
                    $fac=$adb->pquery('select contactid as pid, e.smownerid AS owner from vtiger_contactdetails c '
                                    . 'join vtiger_crmentity e ON e.crmid = c.contactid '
                                    . 'where c.contact_no=?',array($cod));
                    if ($adb->num_rows($fac)==0) { // Not found, can't upload
                    echo "<font color=red>File <strong>{$filename}</strong> not found</font><br />";
                    continue; 
                }
            }
            $doc = Documents_Record_Model::getCleanInstance("Documents");
            $data = $doc->getData();
            $data['notes_title'] = $request->get('title');
            $data['notecontent'] = $request->get('description');
            $data['fileversion'] = 1;
            $data['filelocationtype'] = 'I';
            $data['folderid'] = '3';
            $data['filestatus'] = 1;
            $data['assigned_user_id'] = $owner;

            $parent = $adb->query_result($fac,0,'pid');
            $owner = $adb->query_Result($fac, 0, 'owner');
            unset($_FILES);
            
            $f=array(
            'name'=>substr($filename,strpos($filename,'_')+1),
            'type'=>finfo_file($finfo, $ffn),
            'tmp_name'=>$ffn,
            'error'=>0,
            'size'=>filesize($ffn)
            );
            $_FILES["file$numfiles"] = $f;
            $data["filename"] = substr($filename,strpos($filename,'_')+1);
            $data["filesize"] = $f['size'];
            $data["filetype"] = $f['type'];
            print_r($data);
//            $doc->save("Documents");
            $numfiles++;
        }
        echo "Num Files Imported: {$numfiles}<br />";
    }
}

?>