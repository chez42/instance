<?php
class Omniscient_ResetActivated_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        $workflow_id = $request->get('workflow_id');
        $record = $request->get('record');
        $query = "DELETE FROM com_vtiger_workflow_activatedonce WHERE workflow_id=? AND entity_id=?";
        $adb->pquery($query, array($workflow_id, $record));
    }
}