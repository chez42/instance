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
	
	$widgetsPosition = array();
	
	$widgetsPosition['ticketbytype'] = array(
	    'row' => 0,
	    'col' => 0,
	    'width' => 4,
	    'height' => 5
	);
	
	$widgetsPosition['ticketbystatus'] = array(
	    'row' => 4,
	    'col' => 0,
	    'width' => 4,
	    'height' => 5
	);
	
	$widgetsPosition['ticketbytimespent'] = array(
	    'row' => 8,
	    'col' => 0,
	    'width' => 4,
	    'height' => 5
	);
	
	$balances = $response['result']['balances'];
	 
	$margin_balance = $response['result']['margin_balance'];
	 
	$net_credit_debit = $response['result']['net_credit_debit'];
	 
	$unsettled_cash = $response['result']['unsettled_cash'];
	 
	$assetclasstables = $response['result']['assetclasstables'];
	 
	$pie = $response['result']['pie'];
	
	if(in_array('HelpDesk', $avmod)){
	
    	$ticketStatus = $response['result']['ticketWidget']['ticketStatus'];
    	
    	$ticketTime = $response['result']['ticketWidget']['timeResult'];
    	
    	$ticketType = $response['result']['ticketWidget']['catData'];
    	
    	$ticketProgress = array();
    	
	}
	
	$recentDocuments = $response['result']['recentWidget'];
	
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
                    color: red;
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
            		
            		<?php if(!empty($avmod)){?>
            		<div class="gridstack grid-stack" data-gs-width="12" >
            			
            			<!-- <div data-gs-id="recent_upload_widget" id="recent_upload_widget" 
                			data-gs-x=<?php echo $widgetsPosition['recent_upload_widget']['row'];?> 
                			data-gs-y=<?php echo $widgetsPosition['recent_upload_widget']['col'];?> 
                			data-gs-width=<?php if($widgetsPosition['recent_upload_widget']['width'])echo $widgetsPosition['recent_upload_widget']['width'];else echo'12';?>
                			data-gs-height=<?php if($widgetsPosition['recent_upload_widget']['height'])echo $widgetsPosition['recent_upload_widget']['height'];else echo'5';?>
                			class="dashboardWidget grid-stack-item">
                    		
                            <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                            	
                            	<div class="kt-portlet__head">
                    				<div class = "kt-portlet__head-label">
                    					<h3 class="kt-portlet__head-title">Recent Uploads</h3>
                    				</div>
                				</div>
                    			
                    			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent " style="padding:10px;">
                        			<div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                        	<tr>
                                        		<th>FileName</th>
                                        		<th>FolderName</th>
                                        		<th>Preview</th>
                                        	</tr>
                                        	<?php foreach($recentDocuments as $recDoc){?>
                                            	<tr>
                                            		<td><?php echo $recDoc['filename'];?></td>
                                            		<td><?php echo $recDoc['foldername'];?></td>
                                            		<td><?php
                                            		  $html = '<a href="javascript:void(0)" data-filelocationtype="I" data-filename="" data-fileid="'.$recDoc['docid'].'">
                                                        <span class="document_preview" title="Preview" style="font-size:1.5em!important;">
                        								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        										<rect x="0" y="0" width="24" height="24"></rect>
                        										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"></path>
                        									</g>
                        								</svg></span></a>';
                                            		  echo $recDoc['attid'] ? $html : '';?></td>
                                            	</tr>
                                        	<?php }?>
                                        </table>
                                    </div>
                    			</div>
                    		
                    		</div>
                    		
                    	</div> -->
            			<?php 
            			    
            			    $element = array('ID' => $_SESSION['ID'], 'owner_id' => $_SESSION['ownerId'], 'emptyFolder' => false);

                        	$postParams = array(
                        		'operation'=>'get_documents',
                        		'sessionName'=>$session_id,
                        		'element'=>json_encode($element)
                        	);
                        	
                        	$response = postHttpRequest($ws_url, $postParams);
                        	
                        	$response = json_decode($response,true);
                        
                        	$html = $response['result'];
                        	
                    	?>
            			<div data-gs-id="document_widget" id="document_widget" 
                			data-gs-x=<?php echo $widgetsPosition['document_widget']['row'];?> 
                			data-gs-y=<?php echo $widgetsPosition['document_widget']['col'];?> 
                			data-gs-width=<?php if($widgetsPosition['document_widget']['width'])echo $widgetsPosition['document_widget']['width'];else echo'12';?>
                			data-gs-height=<?php if($widgetsPosition['document_widget']['height'])echo $widgetsPosition['document_widget']['height'];else echo'5';?>
                			class="dashboardWidget grid-stack-item">
                    		
                            <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                            	
                            	<div class="kt-portlet__head">
                    				<div class = "kt-portlet__head-label">
                    					<h3 class="kt-portlet__head-title">Documents</h3>
                    					<style>
                    					   .kt-subheader__breadcrumbs-separator{
                        					    display: inline-block;
                                                width: 4px;
                                                height: 4px;
                                                border-radius: 50%;
                                                content: " ";
                                                background: rgb(231, 232, 239);
                                            }
                    					</style>
                    					<div class="kt-subheader__breadcrumbs" style="padding:10px;">
                                       		<a href="#" class="folderBreadcrumb kt-subheader__breadcrumbs-home" data-folder-id = "">
                                        		<b><i style= "font-size:1.5rem !important;" class="la la-home"></i></b>
                                        	</a>
                                    	</div>
                    				</div>
                    				<div class="kt-portlet__head-label pull-right">
                    					<button class="add-doc-btn btn btn-brand btn-sm" title="Add Documents">
                            				<i class="fa fa-upload"></i> Upload
                                        </button>
                                        <a href="#" class="btn ">
                                          	Show Empty Folders : &nbsp;
                                            <input class="pull-right" title="Empty Folders" type="checkbox" name="emptyFolder" value='1' >
                                            <i class = "fa fa-question-circle" data-toggle = "kt-tooltip" style = "margin-left:5px;font-size:15px !important;color:#5867dd;" data-content = "By Default Folders with Documents are shown, click this checkbox to view all Folders"  data-original-title = "By Default Folders with Documents are shown, click this checkbox to view all Folders"></i>
                            		  	</a>
                    				</div>
                				</div>
                    			
                    			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent " style="padding:10px;">
                        			<input type="hidden" name="startIndex" value="50" />
                    				<div class="folderContent">
                            			<?php if(!empty($html)){?>
                                			<?php echo $html;?>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                    				</div>
                    			</div>
                    		
                    		</div>
                    		
                    	</div>
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
                		<?php if(in_array('HelpDesk', $avmod)){
                		    //if(!empty($ticketStatus)){
                		?>
                			<div data-gs-id="ticketbystatus" id="ticketbystatus" 
                    			data-gs-x=<?php echo $widgetsPosition['ticketbystatus']['row'] ? $widgetsPosition['ticketbystatus']['row'] : 0;?> 
                    			data-gs-y=<?php echo $widgetsPosition['ticketbystatus']['col'] ? $widgetsPosition['ticketbystatus']['col'] : 0;?> 
                    			data-gs-width=<?php if($widgetsPosition['ticketbystatus']['width'])echo $widgetsPosition['ticketbystatus']['width'];else echo'4';?>
                    			data-gs-height=<?php if($widgetsPosition['ticketbystatus']['height'])echo $widgetsPosition['ticketbystatus']['height'];else echo'5';?>
                    			class="dashboardWidget grid-stack-item">
                        		
                                <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                                	
                                	<div class="kt-portlet__head">
                        				<div class = "kt-portlet__head-label">
                        					<h3 class="kt-portlet__head-title">Ticket By Status</h3>
                        				</div>
                    				</div>
                        			
                        			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent ">
                            			<input type="hidden" id="ticket_status" value='<?php echo json_encode($ticketStatus);?>' />
                            			<?php if(!empty($ticketStatus)){?>
                                			<div id="ticketStatusWidget row">
                                				<div class="col-md-4">
            										<div class="ticket_status_pie_holder" style="width:300px; display:block; float:left;">
                                                        <div id="ticket_status_filtered_pie" style="height:200px; width:300px;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                	<div id="ticketstatuslegenddiv"></div>
                                                </div>
                    						</div>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                        			</div>
                        		
                        		</div>
                        	</div>    
                    	<?php //}?>  
                    	<?php //if(!empty($ticketTime)){?>
                        	<div data-gs-id="ticketbytimespent" id="ticketbytimespent" 
                    			data-gs-x=<?php echo $widgetsPosition['ticketbytimespent']['row'] ? $widgetsPosition['ticketbytimespent']['row'] : 0;?> 
                    			data-gs-y=<?php echo $widgetsPosition['ticketbytimespent']['col'] ? $widgetsPosition['ticketbytimespent']['col'] : 0;?> 
                    			data-gs-width=<?php if($widgetsPosition['ticketbytimespent']['width'])echo $widgetsPosition['ticketbytimespent']['width'];else echo'4';?>
                    			data-gs-height=<?php if($widgetsPosition['ticketbytimespent']['height'])echo $widgetsPosition['ticketbytimespent']['height'];else echo'5';?>
                    			class="dashboardWidget grid-stack-item">
                        		
                                <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                                	
                                	<div class="kt-portlet__head">
                        				<div class = "kt-portlet__head-label">
                        					<h3 class="kt-portlet__head-title">Ticket By Time Spent</h3>
                        				</div>
                    				</div>
                        			
                        			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent ">
                        				<input type="hidden" id="ticket_time" value='<?php echo json_encode($ticketTime);?>' />
                            			<?php if(!empty($ticketTime)){?>
                                			<div id="ticketTimeWidget row">
                                    			<div class="col-md-4">
            										<div class="ticket_time_pie_holder" style="width:300px; display:block; float:left;">
                                                        <div id="ticket_time_filtered_pie" style="height:200px; width:300px;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div id="tickettimelegenddiv"></div>
                                                </div>
                    						</div>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                        			</div>
                        		</div>
                        	</div> 
                    	<?php //}?>
                    	<?php if(!empty($ticketProgress)){?>
                        	<div data-gs-id="ticketbyprogress" id="ticketbyprogress" 
                    			data-gs-x=<?php echo $widgetsPosition['ticketbyprogress']['row'] ? $widgetsPosition['ticketbyprogress']['row'] : 0;?> 
                    			data-gs-y=<?php echo $widgetsPosition['ticketbyprogress']['col'] ? $widgetsPosition['ticketbyprogress']['col'] : 0;?> 
                    			data-gs-width=<?php if($widgetsPosition['ticketbyprogress']['width'])echo $widgetsPosition['ticketbyprogress']['width'];else echo'4';?>
                    			data-gs-height=<?php if($widgetsPosition['ticketbyprogress']['height'])echo $widgetsPosition['ticketbyprogress']['height'];else echo'5';?>
                    			class="dashboardWidget grid-stack-item">
                        		
                                <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                                	
                                	<div class="kt-portlet__head">
                        				<div class = "kt-portlet__head-label">
                        					<h3 class="kt-portlet__head-title">Ticket By Progress</h3>
                        				</div>
                    				</div>
                        			
                        			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent ">
                            			<?php if(!empty($ticketProgress)){?>
                                			<div id="ticket_progress" data-vals='<?php echo $ticketProgress;?>' style="min-height:200px;"></div>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                        			</div>
                        		</div>
                        	</div>    
                    	<?php }?>
                    	<?php //if(!empty($ticketType)){?>
                        	<div data-gs-id="ticketbytype" id="ticketbytype" 
                    			data-gs-x=<?php echo $widgetsPosition['ticketbytype']['row'] ? $widgetsPosition['ticketbytype']['row'] : 0;?> 
                    			data-gs-y=<?php echo $widgetsPosition['ticketbytype']['col'] ? $widgetsPosition['ticketbytype']['col'] : 0;?> 
                    			data-gs-width=<?php if($widgetsPosition['ticketbytype']['width'])echo $widgetsPosition['ticketbytype']['width'];else echo'4';?>
                    			data-gs-height=<?php if($widgetsPosition['ticketbytype']['height'])echo $widgetsPosition['ticketbytype']['height'];else echo'5';?>
                    			class="dashboardWidget grid-stack-item">
                        		
                                <div class='col-lg-12 grid-stack-item-content kt-portlet'>
                                	
                                	<div class="kt-portlet__head">
                        				<div class = "kt-portlet__head-label">
                        					<h3 class="kt-portlet__head-title">Ticket By Type</h3>
                        				</div>
                    				</div>
                        			
                        			<div class=" kt-portlet__body kt-portlet__body--fit dashboardWidgetContent ">
                        				<input type="hidden" id="ticket_type" value='<?php echo json_encode($ticketType);?>' />
                            			<?php if(!empty($ticketType)){?>
                        					<div id="ticketCatWidget row">
                        						<div class="col-md-4">
            										<div class="ticket_cat_pie_holder" style="width:300px; display:block; float:left;">
                                                        <div id="ticket_cat_filtered_pie" style="height:200px; width:300px;"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div id="ticketcatlegenddiv"></div>
                                                </div>
                    						</div>
                        				<?php }else{?>
                        					<div class="fullscreenDiv"> <strong class="center">No Data Available!</strong></div>
                        				<?php }?>
                        			</div>
                        		</div>
                        	</div>    
                    	<?php //}
                		}
            		      ?>  
                		</div>
                	</div> 
				</div>
				<?php }else{?>
        		    <div class="fullscreenDiv"> 
    		    		<strong class="center">No Data Available!</strong>
		    		</div>
        		<?php }?>
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
    		var widgetContent = jQuery('.dashboardWidgetContent', jQuery('.dashboardWidget'));
        	for (var index=0, len = widgetContent.length; index < len; ++index) {
        		var widget = jQuery(widgetContent[index]);
        		widget.slimScroll({
        			height: widget.closest('.dashboardWidget').height()-100
        		});
        	}
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
        	
    	    widgetContent.slimScroll({
    			height: widgetContent.closest('.dashboardWidget').height()-100
    		});
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
        	
        	Historical_Js.registerEvents();
        	Consolidated_Js.registerEvents();
        	
        	//$("#mydiv").load(location.href + " #mydiv");
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

        $(document).on('click',".folderFiles",function(){

			$('body').waitMe({effect : 'orbit',text : '' });
			
			var folderId = $(this).data('folderid');
			
			var hasClass = $(this).hasClass('filterName');

			var empty = false;
			if($('[name="emptyFolder"]').prop('checked')){
				var empty = true;
			}
			
			$.ajax({

				url:'folderData.php',

				data: 'folder_id='+folderId+'&emptyFolder='+empty,

				error: function(errorThrown) {
					$('body').waitMe('hide');
				},
				success: function(data) {
    				
					$('.folderContent').html(data);

					var folderId = jQuery('.folderContent').find("[name='folderId']").val();

					var folderName = jQuery('.folderContent').find("[name='folderName']").val();

					if(hasClass)
						$('.kt-subheader__breadcrumbs').find('a:first').nextAll().remove();
					
					if(jQuery('.kt-subheader__breadcrumbs').find('.folderBreadcrumb').length > 0 ){

						var html = '<span class="kt-subheader__breadcrumbs-separator"></span>';
                    	html += '<a href="" class="kt-subheader__breadcrumbs-link folderBreadcrumb"  data-folder-id="'+folderId+'" style="padding: 5px;">';
                    	html += folderName + '</a>';
                    	
						jQuery('.kt-subheader__breadcrumbs').find('a:last').after(html);
						
					}
					
					$('body').waitMe('hide');

    			},

				beforeSend: function() {}
				
			});
		});

		$(document).on('click','.folderBreadcrumb', function(e){

			e.preventDefault();
			
			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });

			var curEle = $(this);
			
			var folderId = $(this).data('folderId');

			var empty = false;

			if($('[name="emptyFolder"]').prop('checked')){
				var empty = true;
			}
			
			$.ajax({
				url:'folderData.php',
				
				data: 'emptyFolder='+empty+'&folder_id='+folderId,

				error: function(errorThrown) {},
				
				success: function(data) {
					$('.folderContent').html(data);

					curEle.nextAll().remove();
					curEle.prev().remove();

					$('body').waitMe('hide');
				}
			});
		});

		$(document).on('change','[name="emptyFolder"]', function(){
    		
			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });

			var empty = false;

			if($(this).prop('checked')){
				var empty = true;
			}

			var folderId = $(document).find('.foldersData').data('parentFolder');

    		var index = parseInt(jQuery('[name="startIndex"]').val());
    		
			$.ajax({
				url:'folderData.php',
				data:'folder_id='+folderId+'&emptyFolder='+empty,
				error: function(errorThrown) {},
				success: function(data) {
					$('.folderContent').html(data);
					$('body').waitMe('hide');
				}
			});
			
		});
		
		$('div.dashboardWidgetContent').scroll(function() {
			if($(document).find('.fileDrag').length > 0){
				 if ($(this).scrollTop() + $(this).height() >= $('.folderContent').outerHeight() - 30 && 
	    			jQuery('[name="scrollevent"]').val() == 1){
					$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
		    		var folderId = $(document).find('.foldersData').data('parentFolder');
		    		var index = parseInt(jQuery('[name="startIndex"]').val());
					var empty = true;
		    		$.ajax({
						url:'folderData.php',
						data: 'folder_id='+folderId+'&index='+index+'&emptyFolder='+empty,
						success: function(data) {
							jQuery('[name="scrollevent"]').remove();
							$(document).find('.foldersData').append(data);
							jQuery('[name="startIndex"]').val(index + 50);
							$('body').waitMe('hide');
						}
					});
		    	}
			}
		});

  		jQuery(document).on('click','.document_preview, .document_download', function(){
      		var self = $(this);
      		$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
      		var currentTargetObject = self.closest('a');
      		var fileId = currentTargetObject.data('fileid');
    		var fileLocationType = currentTargetObject.data('filelocationtype');
	        var fileName = currentTargetObject.data('filename'); 
	        
	       	if(self.hasClass('document_download')){
				var mode = 'download';
	       	}else if(self.hasClass('document_preview')){
	       		var mode = 'preview';
	       	}
	       
  			if(fileLocationType == 'I'){
	        	
	            $.ajax({
					url:'filePreview.php',
					data: 'file_id='+fileId+'&mode='+mode,
					error: function(errorThrown) {
						console.log(errorThrown);
					},
					success: function(data) {
						var success;
					 	try {
					        var data = JSON.parse(data);
					        if(data.success)
						        success = true;
					    } catch (e) {
				      		success = false;
					    }
					    if(success){
					    	window.location.href = data.downloadUrl;
					    }else{
							$(document).find('#chatfilePreviewModal').html(data);
							$('#chatfilePreviewModal').modal('show');
					    }
					    $('body').waitMe('hide');
					}
				});
	            
	        } else {
	            var win = window.open(fileName, '_blank');
	            win.focus();
	        }
  		});

    </script>
    <span class = "upload-docs"></span>
    <link href="assets/plugins/custom/uppy/dist/uppy.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/plugins/custom/uppy/dist/uppy.min.js" type="text/javascript"></script>
    <script type="text/javascript">
    	$(document).ready(function(){

			$(".add-doc-btn").click(function(){
        		if($( 'body' ).find('.foldersData').data('parentFolder')){
					$(".upload-docs").trigger("click");
        		} else {
        			var params = [];
                    params['message'] = 'Please click any Folder before Upload';
                	toastr.error(params['message']);
   				}
        	});

    	    var uppy = Uppy.Core({
    			autoProceed: false,
				allowMultipleUploads: true,
				restrictions: {
    			    maxFileSize: 20971520,
    			    //allowedFileTypes: ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx']
    			}
			}).use(Uppy.Dashboard, {
          		inline: false,
          		trigger: '.upload-docs',
          		target: '.add_doc_modal',
          		replaceTargetContent: true,
          	  	showProgressDetails: true,
           	 	height: 470,
        	}).use(Uppy.XHRUpload, { endpoint: 'upload-document.php'})

      		uppy.on('file-added', (result) => {
      			uppy.setMeta({ doc_folder_id: $("[name='folderId']").val()})
      		});
      		
      		uppy.on('complete', (result) => {

				 var index = parseInt(jQuery('[name="startIndex"]').val());

				 var empty = true;

	    	     $.ajax({
					url:'folderData.php',
					data: 'folder_id='+$("[name='folderId']").val()+'&emptyFolder='+empty,
					
					success: function(data) {
						$('.folderContent').html(data);
						$( 'body' ).waitMe('hide');
					}
				});
			});
    	});
    	
    </script>
    <div class="add_doc_modal"></div>
	</body>
</html>    