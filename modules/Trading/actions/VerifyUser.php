<?php

class Trading_VerifyUser_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {
        $bridge = new Trading_Bridge_Action();
        $output = $bridge->process($request);
        echo $output;
    }
}

?>
