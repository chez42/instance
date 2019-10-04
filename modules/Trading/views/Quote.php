<?php

class Trading_Quote_View extends Vtiger_Popup_View {
    
	public function process(Vtiger_Request $request) {
            $viewer = $this->getViewer($request);
            $mode = $request->get('mode');
            if(!empty($mode)){
                $this->invokeExposedMethod($mode,$request);
            } else{
//                $this->initializeListViewContents($request, $viewer);
                $moduleName = $request->get('module');

                $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
                $companyLogo = $companyDetails->getLogo();
                $bridge = new Trading_Bridge_Action();
                $symbol_info = $bridge->process($request);
                $viewer->assign('COMPANY_LOGO',$companyLogo);
                $symbol = $symbol_info->model->getQuotesJson->quote[0];
                $d = strtotime($symbol->lastTradeDate);
                $symbol->lastTradeDate = date("m-d-Y",$d);
                include_once("include/utils/cron/cPricingAccess.php");
                $last_price = cPricingAccess::GetLatestPriceBySymbol($symbol->symbol);
                if($last_price > $symbol->last){
                    $diff_amount = $last_price - $symbol->last;
                    $percent = number_format($diff_amount/$symbol->last*100, 2);
                    $difference = "<span style='color:red; font-size:12px;'>-{$diff_amount} ({$percent}%)</span>";
                }else if($last_price < $symbol->last){
                    $diff_amount = $symbol->last - $last_price;
                    $percent = number_format($diff_amount/$symbol->last*100, 2);
                    $difference = "<span style='color:green; font-size:12px;'>+{$diff_amount} ({$percent}%)</span>";                    
                }
                
                $viewer->assign("SCRIPTS", $this->getHeaderScripts($request));
                $viewer->assign("SYMBOL_INFO", $symbol);
                
                $viewer->assign("DIFFERENCE", $difference);
                $viewer->view('quote_information.tpl', $moduleName);
            }
	}
        
        public function GetQuoteInformationTemplateOnly($request){
            $viewer = $this->getViewer($request);
                $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
                $companyLogo = $companyDetails->getLogo();
                $bridge = new Trading_Bridge_Action();
                $symbol_info = $bridge->process($request);
                $viewer->assign('COMPANY_LOGO',$companyLogo);
                $symbol = $symbol_info->model->getQuotesJson->quote[0];
                $d = strtotime($symbol->lastTradeDate);
                $symbol->lastTradeDate = date("m-d-Y",$d);
                include_once("include/utils/cron/cPricingAccess.php");
                $last_price = cPricingAccess::GetLatestPriceBySymbol($symbol->symbol);
                if($last_price > $symbol->last){
                    $diff_amount = $last_price - $symbol->last;
                    $percent = number_format($diff_amount/$symbol->last*100, 2);
                    $difference = "<span style='color:red; font-size:12px;'>-{$diff_amount} ({$percent}%)</span>";
                }else if($last_price < $symbol->last){
                    $diff_amount = $symbol->last - $last_price;
                    $percent = number_format($diff_amount/$symbol->last*100, 2);
                    $difference = "<span style='color:green; font-size:12px;'>+{$diff_amount} ({$percent}%)</span>";                    
                }
                
                $viewer->assign("SYMBOL_INFO", $symbol);                
                $viewer->assign("DIFFERENCE", $difference);
                return $viewer->view('quote_information_only.tpl', "Trading", true);            
        }
        
        // Injecting custom javascript resources
        public function getHeaderScripts(Vtiger_Request $request) {
                $headerScriptInstances = parent::getHeaderScripts($request);
                $moduleName = $request->getModule();
                $jsFileNames = array(
                    "modules.Trading.resources.Quote"
//                    "modules.ModSecurities.resources.HistoricalDataChart",
                );
                $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
                return $headerScriptInstances;
        }
}

?>
