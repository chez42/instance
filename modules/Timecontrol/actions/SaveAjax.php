<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");

class Timecontrol_SaveAjax_Action extends Vtiger_SaveAjax_Action {

    public function process(Vtiger_Request $request) {
//
//        $datefield = new DateTimeField(null);
//        $date = $datefield->convertToDBTimeZone($request->get('date_start').' '.$request->get('time_start'));
//
//        $request->set('date_start', $date->format('Y-m-d'));
//        $request->set('time_start', $date->format('H:i:s'));

        parent::process($request);
    }
}