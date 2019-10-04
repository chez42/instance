<?php
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_GetSelectedIds_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
   		return;
   	}

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        $params = $request->getAll();
        $cvid = (int)$params["viewname"];

        $customViewModel = CustomView_Record_Model::getInstanceById($cvid);
        if($customViewModel) {
              $searchKey = $request->get('search_key');
              $searchValue = $request->get('search_value');
              $operator = $request->get('operator');
              if(!empty($operator)) {
                  $customViewModel->set('operator', $operator);
                  $customViewModel->set('search_key', $searchKey);
                  $customViewModel->set('search_value', $searchValue);
              }

            $recordIds =  $customViewModel->getRecordIds(array(), $customViewModel->getModule());
        }


        die(json_encode(array('ids' => $recordIds)));
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }
}