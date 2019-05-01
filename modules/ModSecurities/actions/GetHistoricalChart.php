<?php

class ModSecurities_GetHistoricalChart_Action extends Vtiger_BasicAjax_View{
    public function process(\Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        
        $historicalData = new ModSecurities_HistoricalData_View();
//        echo "HELLO: {$request->get('security_symbol')}<br />";
        if(!$request->get('security_id'))
            $security_id = ModSecurities_Module_Model::GetSecurityIdBySymbol($request->get('security_symbol'));
        else
            $security_id = $request->get('security_id');
        $req = new Vtiger_Request(array());
        $req->set('calling_module', 'PortfolioInformation');
        $req->set('security_id', $security_id);
        $req->set('module', 'ModSecurities');
        $req->set('width', $request->get('width'));
        $req->set('height', $request->get('height'));
        $req->set('advisor_prices', 1);
        /*
        $viewer->assign('HISTORICALDATA', $historicalData);
        $viewer->assign('HISTORICALSETTINGS', $req);*/
        $historicalData->process($req);
    }
}