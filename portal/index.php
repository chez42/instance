<?php

include_once('includes/head.php');

if(isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1){
    unset($_SESSION['ID']);
    header('Location: login.php');
}

if(!isset($_SESSION["ID"])) {
    header("Location: login.php");
} else {
    
    if(!isset($module)){
        $module = 'Dashboard';
    }
    
    include_once('includes/menu.php');
    include_once('includes/function.php');
}

$data = array();

$mod_stats_values=array(
    "Reports" => array("Contacts" => "My Accounts"),
    "Contacts" => "total_count",
    "Documents" => "total_count"
);

$is_enabled_household_accounts = false;

if(isset($GLOBALS['user_basic_details']) && !empty($GLOBALS['user_basic_details'])){
    
    $basic_details = $GLOBALS['user_basic_details'];
    
    if(isset($basic_details['enable_household_accounts']) && $basic_details['enable_household_accounts'] == 1)
        $is_enabled_household_accounts = true;
}

if($is_enabled_household_accounts){
    
    if(isset($_SESSION['accountid']) && $_SESSION['accountid'] > 0)
        $accountid = $_SESSION['accountid'];
        else {
            $res = $adb->pquery("select accountid from vtiger_contactdetails where contactid=?", array($_SESSION['ID']));
            $accountid=$adb->query_result($res,0,'accountid');
        }
        
        if($accountid)
            $mod_stats_values['Reports']['Accounts'] = 'Household Accounts';
}

foreach($mod_stats_values as $modname => $modfields){
    
    if(in_array($modname, $GLOBALS['avmod'])){
        
        $sparams = array(
            'id' => $_SESSION['ID'],
            'block'=>$modname,
            'only_mine' => false,
        );
        
        if($modname == "Reports"){
            
            foreach($modfields as $field => $wigetLabel){
                
                $sparams = array(
                    'accountid'	=> $accountid,
                    'module'	=> "Reports",
                    'contactid'	=> $_SESSION['ID'],
                    'show_report' => $field,
                    'page_limit' => 10
                );
                
                $lmod = get_reports($sparams);
                
                if(!empty($lmod) && isset($lmod['recordsTotal']))
                {
                    $data[$modname][$wigetLabel]['count'] = $lmod['recordsTotal'];
                }else{
                    $data[$modname][$wigetLabel]['count'] = 0;
                }
                $data[$modname][$wigetLabel]['module_list_view_url'] = "reports.php?show_reports=$field";
                
            }
            
        } else {
            
            $moddata = get_module_list_values($sparams);
            
            $data[$modname][$modname]['count'] = 0;
            
            if(!empty($moddata)){
                if(isset($moddata[2]) && isset($moddata[2][$modname]) && isset($moddata[2][$modname]['totalRecords']))
                    $data[$modname][$modname]['count'] = $moddata[2][$modname]['totalRecords'];
                    else {
                        if(isset($moddata[1]) && isset($moddata[1][$modname]) && isset($moddata[1][$modname]['data']))
                            $data[$modname][$modname]['count'] = count($moddata[1][$modname]['data']);
                    }
            }
            
            $data[$modname][$modname]['module_list_view_url'] = strtolower($modname).".php?module=$modname";
        }
    }
    
}
$data['dashboarddata'] = $data;

$account_numbers = getContactAccessibleAccounts($_SESSION['ID']);

global $adb;
$widgetsPosition = array();

$posSizeQuery = $adb->pquery("SELECT portal_widget_position FROM vtiger_contactdetails WHERE contactid = ?",
    array($_SESSION['ID']));

if($adb->num_rows($posSizeQuery)){
    
    $widgetsPosition = Zend_Json::decode(decode_html($adb->query_result($posSizeQuery, 0, 'portal_widget_position')));
    
}

?>


<?php /*if(isset($data['dashboarddata']) && count($data['dashboarddata'])>0 && $data['dashboarddata']!=""){ ?>
	<div class="m-portlet ">
    	<div class="m-portlet__body  m-portlet__body--no-padding">
    		<div class="row m-row--no-padding m-row--col-separator-xl">
        	<?php 
        		
        		    $widgetArray =array();
        		    foreach($data['dashboarddata'] as $modname => $modinfos){
        		        foreach($modinfos as $widgetLabel => $modinfo){
        		            $widgetArray[] = $widgetLabel;
        		        }
        		    }
        		    if(count($widgetArray) == 1)
        		        $col='col-xl-12';
    		        elseif(count($widgetArray) == 2)
    		            $col='col-xl-6';
		            elseif(count($widgetArray) == 3)
		                $col='col-xl-4';
	                else
	                    $col='col-xl-3';
	                
        		    foreach($data['dashboarddata'] as $modname => $modinfos): 
        				foreach($modinfos as $widgetLabel => $modinfo):
        	?>
    			<div class="col-md-12 col-lg-6 <?php echo $col;?>">
    				<!--begin::Total Profit-->
    				<div class="m-widget24">	
        				<a href="<?php echo $modinfo['module_list_view_url']; ?>">				 
        				    <div class="m-widget24__item">
        				        <h4 class="m-widget24__title">
        				           <?php echo vtranslate($widgetLabel,'Vtiger'); ?>
        				        </h4><br>
        				       	
								 <span class="m-widget24__desc">
								 	<?php 
    									if($modname == 'Contacts')
    									    echo '<i class="m-menu__link-icon flaticon-users" style="font-size:2rem !important;"></i>';
    									else if($modname == 'Documents')
    										echo '<i class="m-menu__link-icon flaticon-notes" style="font-size:2rem !important;"></i>';
    									else if($modname == 'Reports')	
    										echo '<i class="m-menu__link-icon flaticon-diagram" style="font-size:2rem !important;"></i>';
									?>
        				        </span>
        				        <span class="m-widget24__stats m--font-brand">
        				            <?php echo $modinfo['count'];?>
        				        </span>		
        				        <div class="m--space-10"></div>
        				       
        				    </div>
    				    </a>				      
    				</div>
    				<!--end::Total Profit-->
    			</div>
    			
	 <?php 
				endforeach;
			endforeach; 
        		
	?>
			
				
    		</div>
    	</div>
    </div>
   
	<?php }*/?>
	<style>
/* 	   .gridster ul { */
/* 	       width :100% !important; */
/* 	       float :left; */
/* 	   } */
   </style>
<div class="mainContainer">
    
    <div class="device-xl d-none d-xl-block"></div>
    
	<div class="gridstack grid-stack" data-gs-width="12" >
		<div data-gs-id="balance_history" id="balance_history" 
			data-gs-x=<?php echo $widgetsPosition['balance_history']['row'];?> 
			data-gs-y=<?php echo $widgetsPosition['balance_history']['col'];?> 
			data-gs-width=<?php echo $widgetsPosition['balance_history']['width'];?>
			data-gs-height=<?php echo $widgetsPosition['balance_history']['height'];?>
			class="dashboardWidget grid-stack-item">
    		<?php 
                $balances = PortfolioInformation_HistoricalInformation_Model::GetConsolidatedBalances($account_numbers, '1900-01-01', date("Y-m-d"));
                $ACCOUNTS = json_encode($account_numbers);
                $CONSOLIDATED = json_encode($balances);
            ?>
            <div class='col-lg-12 grid-stack-item-content m-portlet'>
    			<h5 class="page-title">&nbsp;</h5>
    			<h4 class="page-title">Balance History</h4>
    			<div class="dashboardWidgetContent ">
        			<?php if(!empty($CONSOLIDATED)){?>
            			<div id="consolidated_chart" data-vals='<?php echo $CONSOLIDATED;?>' style="min-height:200px;"></div>
    				<?php }else{?>
    					<div class="text-center"> <strong>No Data Available!</strong></div>
    				<?php }?>
    			</div>
    			<h5 class="page-title">&nbsp;</h5>
    		</div>
    	</div>
    	<div data-gs-id="accounts" id="accounts" 
    		data-gs-x=<?php echo $widgetsPosition['accounts']['row'];?> 
    		data-gs-y=<?php echo $widgetsPosition['accounts']['col'];?> 
    		data-gs-width=<?php echo $widgetsPosition['accounts']['width'];?> 
    		data-gs-height=<?php echo $widgetsPosition['accounts']['height'];?> 
    		class="dashboardWidget grid-stack-item">
    		<div class='col-lg-12 grid-stack-item-content m-portlet' >
    			<h5 class="page-title">&nbsp;</h5>
    			<h4 class="page-title">Accounts</h4>
    			<div class="dashboardWidgetContent " >
    				<?php 
                    	$sparams = array(
                    	    'module'	=> "Reports",
                    	    'contactid'	=> $_SESSION['ID'],
                    	    'show_report' => 'Contacts',
                    	);
                    	$data = array();
                    	$data = get_reports($sparams);
                	?>
                	<?php if(!empty($data['summary_info'])){?>
    					<table class="table table-striped- table-bordered table-hover"  style="width:100%;">
    						<thead>
    							<tr role="row" class="heading">
    								<th>Account Number</th>
    								<th>Total Value</th>
    							</tr>
    						</thead>
    						<tbody> 
    							<?php foreach($data['summary_info'] as $reportData){?>
    							<tr>
    								<td <?php echo $reportData['portfolioinformationid'];?>><?php echo $reportData['account_number'];?></td>
    								<td>$<?php echo number_format($reportData['total_value'],2);?></td>
    							</tr>
    							<?php }?>
    							<tr>
    								<td><b>Total</b></td>
    								<td><b>$<?php echo $data['grandTotals']['total_value'];?></b></td>
    							</tr>
    						</tbody>
    					</table>
					<?php }else{?>
						<div class="text-center"> <strong>No Data Available!</strong></div>
					<?php }?>
				</div>
    			<h5 class="page-title">&nbsp;</h5>
    		</div>
		</div>
		<div data-gs-id="asset_allocation" id="asset_allocation" 
			data-gs-x=<?php echo $widgetsPosition['asset_allocation']['row'];?>
			data-gs-y=<?php echo $widgetsPosition['asset_allocation']['col'];?>
			data-gs-width=<?php echo $widgetsPosition['asset_allocation']['width'];?>
			data-gs-height=<?php echo $widgetsPosition['asset_allocation']['height'];?>
			class="dashboardWidget grid-stack-item">
    		<div class='col-lg-12 grid-stack-item-content m-portlet'>
    			<h5 class="page-title">&nbsp;</h5>
    			<h4 class="page-title">Asset Allocation</h4>
    			<div class="dashboardWidgetContent ">
					<?php 
                		$margin_balance = PortfolioInformation_HoldingsReport_Model::GetMarginBalanceTotal($account_numbers);
                		$net_credit_debit = PortfolioInformation_HoldingsReport_Model::GetNetCreditDebitTotal($account_numbers);
                		$unsettled_cash = PortfolioInformation_HoldingsReport_Model::GetDynamicFieldTotal($account_numbers, "unsettled_cash");
                        PortfolioInformation_HoldingsReport_Model::GenerateAssetClassTables($account_numbers);
                        $pie = PortfolioInformation_Reports_Model::GetPieFromTable();
                		$ASSET_PIE = json_encode($pie);
                        $ACCOUNTS = json_encode($account_numbers);
                		$MARGIN_BALANCE = $margin_balance;
                        $NET_CREDIT_DEBIT = $net_credit_debit;
                        $UNSETTLED_CASH = $unsettled_cash;
		          ?>
		          	<input type="hidden" id="asset_pie" value='<?php echo $ASSET_PIE;?>' />
					<?php if(!empty($ASSET_PIE)){?>
                        <div id="AssetAllocationWidget" >
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <div class="pie_holder" style="width:300px; display:block; float:left;">
                                            <div id="filtered_pie" style="height:175px; width:300px; float:left; padding-left:5px;"></div>
                                            <div style="clear:both;"></div>
                                            <div id="legenddiv" style="margin: 5px 0 20px 0; width:300px; height:175px; float:left;"></div>
                                        </div>
                                    
                                    </td>
                                </tr>
                                <?php if($MARGIN_BALANCE != 0){?>
                                    <tr>
                                        <td>
                                        	<p>Margin Balance: <span style="<?php if($MARGIN_BALANCE < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($MARGIN_BALANCE,2);?></span></p>
                                        </td>
                                    </tr>
                                <?php } 
                                if($NET_CREDIT_DEBIT != 0){?>
                                    <tr>
                                        <td>
                                        <p>Net Credit Debit: <span style="<?php if($NET_CREDIT_DEBIT < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($NET_CREDIT_DEBIT,2);?></span></p>
                                        </td>
                                    </tr>
                               <?php }
                               if($UNSETTLED_CASH != 0){?>
                                    <tr>
                                        <td>
                                        <p>Unsettled Cash: <span style="<?php if($UNSETTLED_CASH < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($UNSETTLED_CASH,2);?></span></p>
                                        </td>
                                    </tr>
                                <?php }?>
                            
                            </table>
                        </div>
                    <?php }else{?>
						<div class="text-center"> <strong>No Data Available!</strong></div>
					<?php }?>
    			</div>
    			<h5 class="page-title">&nbsp;</h5>
    		</div>
    	</div>
    </div>          
</div>
 
<?php 
	include_once("includes/footer.php");
?>
<script src="js/Reports/Consolidated.js" type="text/javascript"></script>
<script src="js/Reports/HistoricalCharts.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<link href="assets/global/plugins/gridstack/gridstack.css" rel="stylesheet" type="text/css" />
<link href="assets/global/plugins/gridstack/gridstack-extra.css" rel="stylesheet" type="text/css" />
<script src="assets/global/plugins/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/gridstack/lodash.js" type="text/javascript"></script>
<script src="assets/global/plugins/gridstack/gridstack.js" type="text/javascript"></script>
<script src="assets/global/plugins/gridstack/gridstack.jQueryUI.js" type="text/javascript"></script>

<script type="text/javascript">

	$(function(){
	
    	var widgetContent = jQuery('.dashboardWidgetContent', jQuery('.dashboardWidget'));
    	for (var index=0, len = widgetContent.length; index < len; ++index) {
    		var widget = jQuery(widgetContent[index]);
    		widget.slimScroll({
    			height: widget.closest('.dashboardWidget').height()-100
    		});
    	}
    	
    });

	var waitForFinalEvent=function(){var b={};return function(c,d,a){a||(a="I am a banana!");b[a]&&clearTimeout(b[a]);b[a]=setTimeout(c,d)}}();
    
    var fullDateString = new Date();
    
    var activeGridstack = jQuery(".gridstack");
   
    var options = {
        float: false,
    };
    
    var items = activeGridstack.find('div.grid-stack-item');
    
    activeGridstack.gridstack(options);
    
    $(window).resize(function () {
        waitForFinalEvent(function() {
        	resizeGrid();
        }, 300, fullDateString.getTime());
    });
    
    var serializedData = [];
    
    items.each(function(index,item){
    	serializedData.push({
    		x		: jQuery(item).attr("data-gs-x"),
    		y		: jQuery(item).attr("data-gs-y"),
    		width	: jQuery(item).attr("data-gs-width"),
    		height 	: jQuery(item).attr("data-gs-height"),
    		id		: jQuery(item).attr("id")
    	});
    });
    
    var grid = jQuery(".gridstack").data('gridstack');
    
    grid.removeAll(false);
    
    var items = GridStackUI.Utils.sort(serializedData);
    
   	resizeGrid();
      
    _.each(items, function (node, i) {
    	var item = jQuery("div[id='"+node.id+"']");
    	grid.addWidget(item,node.x, node.y, node.width, node.height);
    }, this);
    
    activeGridstack.on('dragstop', function(event, ui) {
    	setTimeout(function() {
    		gridStackSavePositions(activeGridstack.find('.dashboardWidget'));
    	},1);
    });
    
    activeGridstack.on('resizestop', function(event, elem) {
    	var widget = jQuery(elem);
    	var widgetContent = jQuery('.dashboardWidgetContent', widget[0]['originalElement']);
    	
	    widgetContent.slimScroll({
			height: widgetContent.closest('.dashboardWidget').height()-100
		});
    	gridStackSavePositions(activeGridstack.find('.dashboardWidget'));
    });
    
  gridStackSavePositions(activeGridstack.find('.dashboardWidget'));
   
   	function isBreakpoint(alias) {
    	return $('.device-' + alias).is(':visible');
    }
    
    function resizeGrid() {
    	var grid = jQuery(".gridstack").data('gridstack');

    	if (isBreakpoint('xs')) {
        } else if (isBreakpoint('sm')) {
            grid.setGridWidth(3);
        } else if (isBreakpoint('md')) {
            grid.setGridWidth(3);
        } else if (isBreakpoint('lg')) {
            grid.setGridWidth(3);
        } else if (isBreakpoint('xl')) {
            grid.setGridWidth(3);
        }
    }

    function gridStackSavePositions(widgets, newWidget = false) {
		
    	var widgetRowColPositions = {}
    	
    	if(isBreakpoint('lg') || isBreakpoint('xl')){
	        
	    	for (var index=0, len = widgets.length; index < len; ++index) {
				var widget = jQuery(widgets[index]);
				widgetRowColPositions[widget.attr('id')] = JSON.stringify({
					row: widget.attr('data-gs-x'),
	                col: widget.attr('data-gs-y'),
	                width: widget.attr('data-gs-width'),
	                height: widget.attr('data-gs-height')
				});
			}
	    	
    		$.ajax({
    			type: "POST",
    			url:'includes/saveWidgetValues.php',
    			data: {'positionSize':widgetRowColPositions},
    			error: function(errorThrown) {
    				console.log(errorThrown);
    			},
    			success: function(data) {

    			}
    		});
    	}
	}
</script>
