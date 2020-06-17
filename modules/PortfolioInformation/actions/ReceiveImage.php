<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2017-06-26
 * Time: 11:41 AM
 */

class PortfolioInformation_ReceiveImage_Action extends Vtiger_BasicAjax_Action{
    function process(Vtiger_Request $request) {
        $image = $request->get('image');
        $uid = $request->get('uid');

        switch(strtolower($todo)){
            case "endvalues":
                $result = PortfolioInformation_Module_Model::GetEndValuesForAccounts('678015458');
                echo json_encode($result);
                break;
        }

    }
}