<?php

class Documents_DocFolderPicklist_UIType extends Vtiger_DocumentsFolder_UIType {

	/**
	 * Function to get the Template name for the current UI Type object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/DocFolderPicklist.tpl';
	}

	/**
	 * Function to get the Display Value, for the current field type with given DB Insert Value
	 * @param <Object> $value
	 * @return <Object>
	 */
	public function getDisplayValue($value) {
	    
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT vtiger_documentfolder.* FROM vtiger_documentfolder
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
		WHERE vtiger_documentfolder.documentfolderid = ? and vtiger_crmentity.deleted = 0', array($value));
		if($db->num_rows($result)) {
			return $db->query_result($result, 0, 'folder_name');
		}
		return false;
		
	}
	
	public function getListSearchTemplateName() {
        return 'uitypes/DocFolderFieldSearchView.tpl';
    }
}