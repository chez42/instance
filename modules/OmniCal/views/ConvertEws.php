<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class OmniCal_ConvertEws_View extends Vtiger_BasicAjax_View{
    public function __construct() {
    }
    
    public function process(\Vtiger_Request $request) {
        if($request->get('convert_all') == 1){
            $user_info = OmniCal_ConvertEws_Model::GetUserConversionRequirementsAll();
        }
        else if(strlen($request->get('user_name')) > 0){
            $user_name = $request->get('user_name');
            $user_info = OmniCal_ConvertEws_Model::GetUserConversionRequirementsForSingleUser($user_name);
        }
        else{
            echo "A user name wasn't specified and convert_all wasn't set to 1";
            return;
        }
        $convert = new OmniCal_ConvertEws_Action();
        $convert->ConvertEws($user_info);
    }
}

?>