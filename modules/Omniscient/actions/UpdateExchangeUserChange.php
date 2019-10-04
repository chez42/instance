<?php

class Omniscient_UpdateExchangeUserChange_Action extends Vtiger_BasicAjax_Action{
    public function process(\Vtiger_Request $request) {
        $table_id = $request->get('table_id');
        switch($request->get('todo')){
            case 'save_sync_state':
                $state = $request->get('state');
                OmniCal_CRMExchangeHandler_Model::UpdateSyncState($table_id, $state);
                break;
            case 'save_enabled':
                $enabled = $request->get('enabled');
                OmniCal_CRMExchangeHandler_Model::UpdateEnabled($table_id, $enabled);
                break;
        }
    }
}

?>