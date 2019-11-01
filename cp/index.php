<?php
    include_once "includes/config.php";
    
    if(!isset($_SESSION['ID'])){
       header("Location: login.php");
       exit;
    }
    
    include_once("includes/head.php");
    
    include_once "includes/aside.php";
	
    include_once 'includes/top-header.php';
	 
	global $api_username, $api_accesskey, $api_url, $user_basic_details, $avmod;
	
	$data = array();
     
    
	$ws_url =  $api_url . '/webservice.php';
	 
	$loginObj = login($ws_url, $api_username, $api_accesskey);
	
	$session_id = $loginObj->sessionName;
	 
	$element = array(
       'ID' => $_SESSION['ID'],
       'accountid' => $_SESSION['accountid'], 
    );
	 
	$postParams = array(
        'operation' => 'widget_data',
        'sessionName' => $session_id,
        'element' => json_encode($element)
    );
	 
	$response = postHttpRequest($ws_url, $postParams);
	 
	$response = json_decode($response,true);
	 
	$widgetsPosition = $response['result']['widgetsPosition'];
	 
	$balances = $response['result']['balances'];
	 
	$margin_balance = $response['result']['margin_balance'];
	 
	$net_credit_debit = $response['result']['net_credit_debit'];
	 
	$unsettled_cash = $response['result']['unsettled_cash'];
	 
	$assetclasstables = $response['result']['assetclasstables'];
	 
	$pie = $response['result']['pie'];
	
?>

        	<style>
                .fullscreenDiv {
                    width: 100%;
                    height: auto;
                    bottom: 0px;
                    top: 0px;
                    left: 0;
                    position: absolute;
                }
                .center {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    margin-left: -50px;
                }
           </style>
           
		   <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
				
				<div class="kt-subheader  kt-grid__item" id="kt_subheader">
					<div class="kt-container  kt-container--fluid ">
						<div class="kt-subheader__main">
							<h3 class="kt-subheader__title">Dashboard</h3>
						</div>
					</div>
				</div>
				
				<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            		<div class="gridstack grid-stack" data-gs-width="12" >
            		
            		<?php 
                        //$ACCOUNTS = json_encode($account_numbers);
                        
            		    $CONSOLIDATED = json_encode($balances);
					    
					    if(!empty($CONSOLIDATED)){
					?>
                    		<div data-gs-id="balance_history" id="balance_history" 
                    			data-gs-x=<?php echo $widgetsPosition['balance_history']['row'];?> 
                    			data-gs-y=<?php echo $widgetsPosition['balance_history']['col'];?> 
                    			data-gs-width=<?php if($widgetsPosition['balance_history']['width'])echo $widgetsPosition['balance_history']['width'];else echo'12';?>
                    			data-gs-height=<?php if($widgetsPosition['balance_history']['height'])echo $widgetsPosition['balance_history']['height'];else echo'5';?>
                    			class="dashboardWidget grid-stack-item">
                        		
                                <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                                	
                                	<div class="kt-portlet__head">
                        				<div class = "kt-portlet__head-label">
                        					<h3 class="kt-portlet__head-title">Balance History</h3>
                        				</div>
                    				</div>
                        			
                        			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent ">
                            			<?php if(!empty($CONSOLIDATED)){?>
                                			<div id="consolidated_chart" data-vals='<?php echo $CONSOLIDATED;?>' style="min-height:200px;"></div>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                        			</div>
                        		
                        		</div>
                        	</div>
                	<?php 
					       }
                    	   
                    	   $sparams = array(
                    	       'module'	=> "Reports",
                    	       'contactid'	=> $_SESSION['ID'],
                    	       'show_report' => 'Contacts',
                    	    );
                    	   
                        	$data = array();
                    	    $data = get_reports($sparams);
                    	    
                    	    if(!empty($data['summary_info'])){
                	?>
                    	<div  data-gs-id="accounts" id="accounts" 
                    		data-gs-x=<?php echo $widgetsPosition['accounts']['row'];?> 
                    		data-gs-y=<?php echo $widgetsPosition['accounts']['col'];?> 
                    		data-gs-width=<?php if($widgetsPosition['accounts']['width']) echo $widgetsPosition['accounts']['width'];else echo'4';?> 
                    		data-gs-height=<?php if($widgetsPosition['accounts']['height'])echo $widgetsPosition['accounts']['height'];else echo'5';?> 
                    		class=" dashboardWidget grid-stack-item">
                    		<div class='col-lg-12 grid-stack-item-content kt-portlet' >
                    			<div class="kt-portlet__head">
                    				<div class = "kt-portlet__head-label">
                    					<h3 class="kt-portlet__head-title">Accounts</h3>
                    				</div>
                    			</div>
                    			<div class="kt-portlet__body kt-portlet__body--fit dashboardWidgetContent " >
                    				<div class = "row row-no-padding row-col-separator-xl">
                    					<div class = "col-md-12 col-lg-12 col-xl-4">
                    						<div class = "kt-widget1">
                        				<?php 
                                        	if(!empty($data['summary_info'])){
                                    	       foreach($data['summary_info'] as $reportData){
                                    	?>	
                                    		<div class = "kt-widget1__item">
                                    			<div class = "kt-widget1__info">
                                    				<h3 class = "kt-widget1__title">
                                    					<?php echo ($reportData['account_number'])?$reportData['account_number']:'N/A';?>
                                    				</h3>
                                    			</div>
                                    			<span class = "kt-widget1__number kt-font-brand">
                                    				$<?php echo number_format($reportData['total_value'],2);?>
                                    			</span>
                                    		</div>
                                    	<?php }?>
                                    	       <div class = "kt-widget1__item">
                                    			<div class = "kt-widget1__info">
                                    				<h3 class = "kt-widget1__title">Total</h3>
                                    			</div>
                                    			<span class = "kt-widget1__number kt-font-brand">
                                    				$<?php echo $data['grandTotals']['total_value'];?>
                                    			</span>
                                    		</div>
                                	   <?php  } else {
                                    	?>
                								<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                						<?php 
                                    	     }
                                    	?>
                							</div>
                						</div>
                					</div>
                				</div>
                			</div>
            		  <?php 
                            }
                            
                    		$ASSET_PIE = json_encode($pie);
                    		
                            $MARGIN_BALANCE = $margin_balance;
                            
                            $NET_CREDIT_DEBIT = $net_credit_debit;
                            
                            $UNSETTLED_CASH = $unsettled_cash;
                            
                            if(!empty($ASSET_PIE)){
                      ?>
                      
                		<div  data-gs-id="asset_allocation" id="asset_allocation" 
                			data-gs-x=<?php echo $widgetsPosition['asset_allocation']['row'];?>
                			data-gs-y=<?php echo $widgetsPosition['asset_allocation']['col'];?>
                			data-gs-width=<?php if($widgetsPosition['asset_allocation']['width'])echo $widgetsPosition['asset_allocation']['width'];else echo'4';?>
                			data-gs-height=<?php if($widgetsPosition['asset_allocation']['height'])echo $widgetsPosition['asset_allocation']['height'];else echo'5';?>
                			class="dashboardWidget grid-stack-item">
                    		
                    		<div class='col-lg-12 grid-stack-item-content kt-portlet'>
                    			
                    			<div class="kt-portlet__head">
                    				<div class = "kt-portlet__head-label">
                    					<h3 class="kt-portlet__head-title">Asset Allocation</h3>
                    				</div>
                    			</div>
                    			
                    			<div class="dashboardWidgetContent kt-portlet__body" style = "padding-top:0px;">
                		          	
                		          	<input type="hidden" id="asset_pie" value='<?php echo $ASSET_PIE;?>' />
                					
                					<?php 
                					   if(!empty($ASSET_PIE)){
                					?>
                						<div id="AssetAllocationWidget">
    										<div class="pie_holder" style="width:300px; display:block; float:left;">
                                                <div id="filtered_pie" style="height:175px; width:300px;"></div>
                                                <div style="clear:both;"></div>
                                                <div id="legenddiv"></div>
                                            </div>
                						</div>
                                        <!--  <div id="AssetAllocationWidget" >
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
                                        </div> -->
                                        
                                        
                                    <?php 
                					   } else {
                					?>
                						<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                					<?php 
                					   }
                					?>
                    			</div>
                    		</div>
                		<?php }?>
                	</div>
                </div>          
				</div>
			</div>
		</div>
	
	
	<?php 
	   include_once "includes/footer.php";
	?>

	<script src="assets/js/pages/dashboard.js" type="text/javascript"></script>
	
	<script src="js/Reports/Consolidated.js" type="text/javascript"></script>
	
	<script src="js/Reports/HistoricalCharts.js" type="text/javascript"></script>
	
	<script src="assets/js/jquery.slimscroll.min.js" type="text/javascript"></script>
    
    <link href="assets/js/gridstack.css" rel="stylesheet" type="text/css" />
    
    <link href="assets/js/gridstack-extra.css" rel="stylesheet" type="text/css" />
    
    <script src="assets/js/lodash.js" type="text/javascript"></script>
    
    <script src="assets/js/gridstack.js" type="text/javascript"></script>
    
    <script src="assets/js/gridstack.jQueryUI.js" type="text/javascript"></script>
    
    <script type="text/javascript">
    
    	$(function(){
    		/*var widgetContent = jQuery('.dashboardWidgetContent', jQuery('.dashboardWidget'));
        	for (var index=0, len = widgetContent.length; index < len; ++index) {
        		var widget = jQuery(widgetContent[index]);
        		widget.slimScroll({
        			height: widget.closest('.dashboardWidget').height()-100
        		});
        	}*/
        });
    
    	//var waitForFinalEvent=function(){var b={};return function(c,d,a){a||(a="I am a banana!");b[a]&&clearTimeout(b[a]);b[a]=setTimeout(c,d)}}();
        
        var fullDateString = new Date();
        
        var activeGridstack = jQuery(".gridstack");
       
        var options = {
            float: false,
        };
        
        var items = activeGridstack.find('div.grid-stack-item');
        
        activeGridstack.gridstack(options);
        
        /*$(window).resize(function () {
            waitForFinalEvent(function() {
            	resizeGrid();
            }, 300, fullDateString.getTime());
        });*/
        
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
        	
    	    /*widgetContent.slimScroll({
    			height: widgetContent.closest('.dashboardWidget').height()-100
    		});*/
        	gridStackSavePositions(activeGridstack.find('.dashboardWidget'));
        });
        
      //gridStackSavePositions(activeGridstack.find('.dashboardWidget'));
       
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
        	
        	//if(isBreakpoint('lg') || isBreakpoint('xl')){
    	    
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
    			url:'update-widget-positions.php',
    			data: {'positionSize': widgetRowColPositions},
    			error: function(errorThrown) {},
    			success: function(data) {}
    		});
    		
        	//}
    	}
    </script>
	</body>
</html>    