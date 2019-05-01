<?php

require_once('libraries/simple_html_dom/simple_html_dom.php');

class ModSecurities_Record_Model extends Vtiger_Record_Model {
	
	function getYahooFinanceSymbolDetail($symbol){
					
		$symbol_data = array();

		$summaryFields = array("YTD Return", "Category", "Beta (3y)", "Yield");

		$holdings_html = file_get_html("https://finance.yahoo.com/quote/$symbol/holdings?p=$symbol");

		if(is_object($holdings_html)){
			
			$holding_section = $holdings_html->find('div[id=Col1-0-Holdings-Proxy] section',0);

			if(is_object($holding_section)){

				if($holding_section->hasChildNodes()){
						
					$divElement = $holding_section->childNodes(0);
					
					if(is_object($divElement)){
						
						if($divElement->hasChildNodes()){
							
							$opComposition = $divElement->childNodes(0);
					
							$opCompositionContainer = $opComposition->find('div',0);
							
							if(is_object($opCompositionContainer)){
								
								if($opCompositionContainer->hasChildNodes()){
									
									$opCompositionDivs = $opCompositionContainer->find('div');
									
									if(!empty($opCompositionDivs)){
										
										foreach($opCompositionDivs as $composition){
											
											if($composition->hasChildNodes()){
												
												$compositionLabel = $composition->childNodes(0);
												$compositionValue = $composition->childNodes(1);
									
												$symbol_data[trim($compositionLabel->text())] = trim($compositionValue->plaintext);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		
			$scripts = $holdings_html->find('script');

			if(!empty($scripts)){
				
				foreach($scripts as $script) {
					
					if(strpos($script->innertext(), 'longBusinessSummary') !== false) {
						
						$scriptText = $script->innertext();
						
						preg_match('/root.App.main[\s=]*([{].*"[}])/', $scriptText, $matches); 
						
						$data = $matches[1]."}}}";
						
						$data = json_decode($data, true);
						
						$fund_summary = $data['context']['dispatcher']['stores']['QuoteSummaryStore']['assetProfile']['longBusinessSummary'];
						
						if($fund_summary)
							$symbol_data["Fund Summary"] = trim($fund_summary);
							
						$fund_performance_five_year_trailing = $data['context']['dispatcher']['stores']['QuoteSummaryStore']['fundPerformance']['trailingReturns']['fiveYear'];
						
						if($fund_performance_five_year_trailing)
							$symbol_data["5y Average Return"] = trim($fund_performance_five_year_trailing['fmt']);
					}
				}
			}
			
			if(!empty($symbol_data)){
				
				if(!isset($symbol_data["Fund Summary"]))
					$symbol_data["Fund Summary"] = "";
				
				if(!isset($symbol_data["5y Average Return"]))
					$symbol_data["5y Average Return"] = "";
			}
			
			// clean up memory
			$holdings_html->clear();
			unset($holdings_html);
		}
		
		$summary_html = file_get_html("https://finance.yahoo.com/quote/$symbol?p=$symbol");

		if(is_object($summary_html)){
			
			$quoteSummary = $summary_html->find('div[id=quote-summary]',0);
			
			if(is_object($quoteSummary)){
				
				if($quoteSummary->hasChildNodes()){
					
					$summaryDivs = $quoteSummary->childNodes();
					
					if(!empty($summaryDivs) && count($summaryDivs) == 2){
						
						foreach($summaryDivs as $summaryDiv){
							
							$summaryTable = $summaryDiv->find('table',0);
							
							if(is_object($summaryTable)){

								foreach($summaryTable->find('tr') as $row) {
									
									$tdLabel = trim($row->childNodes(0)->text());
									$tdValue = trim($row->childNodes(1)->text());
									
									if(in_array($tdLabel, $summaryFields))
										$symbol_data[$tdLabel] = $tdValue;
								}
							}
						}
					}
				}
			}
			
			$summary_html->clear();
			unset($summary_html);
		}
		
		return $symbol_data;
	}
}