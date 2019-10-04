<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-04
 * Time: 11:50 AM
 */

/*
$chart_type = $request->get('chart_type');
$record = $request->get('record_id');
$group_type = $request->get('group_type');
*/

class PortfolioInformation_PhantomChart_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        $chart = new PortfolioInformation_GetChartValues_Action();

        switch($request->get('graphtype')){
            case 'pie':

                break;
        }
    }
}