<?php

class OmniCal_DeleteActivity_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $activity_id = $request->get('activity_id');
        $start_date = $request->get('start_date');
        $model = Calendar_Record_Model::getInstanceById($activity_id, 'Calendar');
        $activity = new OmniCal_Activity_Model();
        $data['recurring_info'] = $activity->GetRecurringInfo($activity_id);     
        if($data['recurring_info']){//This is a recurring event, so remove an item by date rather than deleting the actual event
            $sd = $request->get('start_date');
            $start_date = date('Y-m-d', strtotime($sd));
            OmniCal_RepeatActivities_Model::SetIgnoreDates($activity_id, $start_date);
        }else{
            $model->delete();
        }
        $ids = array();
        $exchange_info = OmniCal_CRMExchangeHandler_Model::GetActivityIdAndChangeKey($activity_id);
        if($exchange_info){
            $ids[] = $exchange_info['id'];
            $tmp = new OmniCal_ExchangeEws_Model();
            $current_user = Users_Record_Model::getCurrentUserModel();
            $tmp->SetImpersonation($current_user->get('user_name'));
            $tmp->DeleteItemsFromExchange($ids);
        }

    }
}
?>