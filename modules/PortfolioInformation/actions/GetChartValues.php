<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2018-05-31
 * Time: 3:32 PM
 */

class PortfolioInformation_GetChartValues_Action extends Vtiger_BasicAjax_Action{

    public function process(Vtiger_Request $request) {
        $chart_type = $request->get('chart_type');
        $record = $request->get('record_id');
        $group_type = $request->get('group_type');
        $accounts = $request->get('accounts');
        $as_of_date = $request->get('as_of_date');
        $start_date = null;
        $end_date = null;
/*        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $start_date = date("Y-m-d", strtotime("first day of " . $start_date));
        $end_date = date("Y-m-d", strtotime("last day of " . $end_date));*/

#        $currentUser = Users_Record_Model::getCurrentUserModel();
#        $adb = PearDatabase::getInstance();

        switch(strtolower($chart_type)){
            case "trailing12revenue":
                echo json_encode($this->GetAllTrailingRevenue($start_date, $end_date));
                break;
            case "trailing12zoom":
                echo json_encode($this->GetAllTrailingZoomRevenue($start_date, $end_date));
                break;
            case "trailingbalanceszoom":
                echo json_encode($this->GetAllTrailingBalancesZoom());
                break;
            case "accountactivity":
                echo json_encode($this->GetAccountActivity());
                break;
            case "record_pie_chart":
                echo json_encode($this->GetPieForRecord($record, $group_type));
                break;
            case "holdings_widget":
                echo json_encode($this->GetHoldingsWidgetDatasets($record));
                break;
            case "asset_allocationv4":
                echo json_encode($this->GetAssetAllocationData());
                break;
            case "pie_as_of_date_for_accounts":
                return $this->GetPieChartAsOfDateForAccounts($accounts, $as_of_date);
        }
    }

    public function GetAllTrailingRevenue($start_date, $end_date){
        return PortfolioInformation_Chart_Model::getTrailing12RevenueChartData($start_date, $end_date);
    }

    public function GetAllTrailingZoomRevenue($start_date, $end_date){
        return PortfolioInformation_Chart_Model::getTrailing12ZoomRevenueChartData($start_date, $end_date);
    }

    public function GetAllTrailingBalancesZoom(){
        return PortfolioInformation_Chart_Model::getTrailingBalancesChartData();
    }

    public function GetAccountActivity(){
        return PortfolioInformation_Chart_Model::getAccountActivity();
    }

    public function GetPieForRecord($record, $group_type){
        return PortfolioInformation_Chart_Model::getPieChartForRecord($record, $group_type);
    }

    public function GetHoldingsWidgetDatasets($record){
        return PortfolioInformation_Chart_Model::getHoldingsWidgetDatasetsForRecord($record);
    }
    public function GetAssetAllocationData(){
        return PortfolioInformation_Chart_Model::getAssetAllocationData();
    }
    public function GetPieChartAsOfDateForAccounts($accounts, $as_of_date){
        return PortfolioInformation_Chart_Model::getPieChartAsOfDateForAccounts($accounts, $as_of_date);
    }

}