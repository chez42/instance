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
                echo "<font color=red><strong>{$filename}</strong> has no Contact or Account number, import failure</font><br />";
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
            
            $parent = $adb->query_result($fac,0,'pid');
            $owner = $adb->query_result($fac, 0, 'owner');

//            $doc = Documents_Record_Model::getCleanInstance("Documents");
            $d = new Documents();

            $d->column_fields['notes_title'] = $request->get('title');
            $d->column_fields['notecontent'] = $request->get('description');
            $d->column_fields['fileversion'] = 1;
            $d->column_fields['filelocationtype'] = 'I';
            $d->column_fields['folderid'] = $request->get('folder_id');
            $d->column_fields['filestatus'] = 1;
            if($request->get('auto_assign') == 1)
                $d->column_fields['assigned_user_id'] = $owner;
            else
                $d->column_fields['assigned_user_id'] = $request->get('assigned_to');
            
            unset($_FILES);
            
            $f=array(
            'name'=>substr($filename,strpos($filename,'_')+1),
            'type'=>finfo_file($finfo, $ffn),
            'tmp_name'=>$ffn,
            'error'=>0,
            'size'=>filesize($ffn)
            );
            $_FILES["file$numfiles"] = $f;
            $d->column_fields["filename"] = substr($filename,strpos($filename,'_')+1);
            $d->column_fields["filesize"] = $f['size'];
            $d->column_fields["filetype"] = $f['type'];
            
            $d->parentid = $parent;
            $d->saveentity("Documents");
            echo "<font color=green>File <strong>{$d->column_fields["filename"]}</strong> import successful</font><br />";
            $numfiles++;
        }
        echo "Num Files Imported: {$numfiles}<br />";
    }
}

?>