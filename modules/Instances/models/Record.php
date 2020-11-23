<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Instances_Record_Model extends Vtiger_Record_Model {
	
	public function getImageDetails() {
		
		$db = PearDatabase::getInstance();

		$imageDetails = array();
		
		$recordId = $this->getId();
		
		if ($recordId) {

		    $query = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
			INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
			WHERE vtiger_seattachmentsrel.crmid = ?";
		    
			$result = $db->pquery($query, array($recordId));
			
			if($db->num_rows($result)){
			    
			    while($user_image = $db->fetchByAssoc($result)){
			        $imageId = $user_image['attachmentsid'];
			        $imagePath = $user_image['path'];
			        $imageName = $user_image['name'];
			        
			        //decode_html - added to handle UTF-8 characters in file names
			        $imageOriginalName = decode_html($imageName);
			        
			        $setype = $user_image['setype'];
			        
					if($setype == 'Instances Image File') {
			             $imageDetails['imagename'][] = array(
        					'id' => $imageId,
        					'orgname' => $imageOriginalName,
        					'path' => $imagePath.$imageId,
        					'name' => $imageName
            			 );
            			
					} else if($setype == 'Instances Portal Icon') {
			            $imageDetails['portalfavicon'][] = array(
			                'id' => $imageId,
			                'orgname' => $imageOriginalName,
			                'path' => $imagePath.$imageId,
			                'name' => $imageName
			            );
					}else if($setype == 'Instances Logo') {
					    $imageDetails['instance_logo'][] = array(
					        'id' => $imageId,
					        'orgname' => $imageOriginalName,
					        'path' => $imagePath.$imageId,
					        'name' => $imageName
					    );
					}else if($setype == 'Instances Background') {
					    $imageDetails['instance_background'][] = array(
					        'id' => $imageId,
					        'orgname' => $imageOriginalName,
					        'path' => $imagePath.$imageId,
					        'name' => $imageName
					    );
			        }
			    }
			}
		}
		return $imageDetails;
	}

}
