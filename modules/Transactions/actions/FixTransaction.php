<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-04-17
 * Time: 12:38 PM
 */

class Transactions_FixTransaction_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        switch(strtolower($request->get('todo'))) {
            case "fixtransaction":
            {
                $fix = new Transactions_FixTransactions_View();
                $fix->process($request);
            }break;
            case "getsymbolinfo":
            {
                $symbol = $request->get("symbol");
                $input_date = $request->get('date');
                $date = date("Y-m-d", strtotime($input_date));
                $security_info = Transactions_FixTransactions_Model::GetSecurityInfo($symbol);
                $security_pricing = ModSecurities_Module_Model::GetSecurityPriceForDate($symbol, $date);
                $return = array("security_info" => $security_info,
                                "security_pricing" => $security_pricing);
                echo json_encode($return);
            }break;
            case "savetransaction":
            {
                $symbol = $request->get("symbol");
                $trade_date = $request->get('date');
                $account_number = str_replace(",", "", $request->get('account_number'));
                $quantity = str_replace(",", "", $request->get('quantity'));
                $price =  str_replace(",", "", $request->get('price'));
                $net_amount =  str_replace(",", "", $request->get('cost_basis'));
                $asset_class = $request->get('asset_class');
                $security_type = $request->get('security_type');
                $record = Vtiger_Record_Model::getCleanInstance("Transactions");
                $record->set('mode', 'create');
                $data = $record->getData();
                $data['account_number'] = $account_number;
                $data['security_symbol'] = $symbol;
                $data['security_price'] = $price;
                $data['quantity'] = $quantity;
                $data['trade_date'] = $trade_date;
                $data['net_amount'] = $net_amount;
                $data['transaction_type'] = 'Trade';
                $data['transaction_activity'] = 'Buy';
                $data['security_type'] = $security_type;
                $data['base_asset_class'] = $asset_class;
                $data['system_fixed'] = 1;
                $record->setData($data);
                $record->save();
                echo 1;
            }break;
            case 'generatetd':{
                $account_number = str_replace(",", "", $request->get('account_number'));
                $result = Transactions_Module_Model::CreateReceiptOfSecuritiesFromTDPositions($account_number);
                echo $result;
            }
        }
    }
}