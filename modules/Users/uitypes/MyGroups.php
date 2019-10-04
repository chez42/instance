<?php

class Users_MyGroups_UIType extends Vtiger_Base_UIType {

    public function getEditViewDisplayValue($value) {
	    return "";
    }

    public function getTemplateName() {
        return 'uitypes/MyGroups.tpl';
    }

	/**
	 * Function to get the Detailview template name for the current UI Type Object
	 * @return <String> - Template Name
	 */
	public function getDetailViewTemplateName(){
		return 'uitypes/MyGroups.tpl';
	}
}
?>