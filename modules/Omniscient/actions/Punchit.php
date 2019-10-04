<?php

class Omniscient_Punchit_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $action = $request->get('punch_action');
        switch($action){
            case "contacts":
                $process = new Omniscient_TransferContacts_Action();
                $result = $process->TransferToCRM100($request);
                echo $result;
                break;
            case "households":
                $process = new Omniscient_TransferHouseholds_Action();
                $result = $process->TransferToCRM100($request);
                echo $result;
                break;
            case "service_tickets":
                $process = new Omniscient_TransferServiceTickets_Action();
                $result = $process->TransferToCRM100($request);
                echo $result;
                break;
            case "comments":
                $process = new Omniscient_TransferComments_Action();
                $result = $process->TransferToCRM100($request);
                echo $result;
                break;

        }
    }
}

?>