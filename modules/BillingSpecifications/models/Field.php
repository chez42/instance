<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class BillingSpecifications_Field_Model extends Vtiger_Field_Model {

	
    public function isAjaxEditable() {
        if($this->getName() == 'value' || $this->getName() == 'billing_type') {
            return false;
        }
        
        return parent::isAjaxEditable();
    }

}