{strip}
	
	{assign var="dateFormat" value=$CURRENT_USER_MODEL->get('date_format')}
	
	<div class="dashboardWidgetHeader">
		{assign var=WidgetTitle value=$WIDGET->getTitle()}
		{assign var="dateFormat" value=$CURRENT_USER_MODEL->get('date_format')}
		<div class="title clearfix">
	        <div class="dashboardTitle pull-left" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}" style="width: 20em;"><b>{vtranslate('Transactions')}</b></div>
			
			<div class="moreLinkDivContent pull-right hide">
				<a class="miniListMoreLink" target="_blank" href='{$MORE_LINK_URL}'>{vtranslate('LBL_MORE')}...</a>
			</div>
		
	    </div>
	    <div class="filterContainer">
			<div class="row" style="margin-bottom:10px;">
				<div class="col-lg-4">
					<div class="pull-right">
						{vtranslate('Transaction Type', $MODULE_NAME)}
					</div>
				</div>
				<div class="col-md-8">
					<select name="transaction_activity" id = "transaction_activity" multiple  data-fieldtype="multipicklist" class="select2 widgetFilter" style="min-width:150px;" >
						<option value="Deposit of funds" {if in_array('Deposit of funds',$ACTVITY)}selected{/if}>Deposit</option>
						<option value="Withdrawal of funds"{if in_array('Withdrawal of funds',$ACTVITY)}selected{/if}>Withdrawal</option>
						<optgroup label="{vtranslate('Trades')}">
							<option value="Buy"{if in_array('Buy',$ACTVITY)}selected{/if}>Buy</option>
							<option value="Sell"{if in_array('Sell',$ACTVITY)}selected{/if}>Sell</option>
						</optgroup>
						<optgroup label="{vtranslate('Transfers')}">
							<option value="Receipt of securities"{if in_array('Receipt of securities',$ACTVITY)}selected{/if}>In</option>
							<option value="Transfer of securities"{if in_array('Transfer of securities',$ACTVITY)}selected{/if}>Out</option>
						</optgroup>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
					<div class="pull-right">
						{vtranslate('Expected Date', $MODULE_NAME)} 
					</div>
				</div>
				{assign var=start_date value=Vtiger_Date_UIType::getDisplayDateValue($TRADE_DATE['start_date'])}
				{assign var=end_date value=Vtiger_Date_UIType::getDisplayDateValue($TRADE_DATE['end_date'])}
				<div class="col-lg-8">
	                <div class="input-daterange input-group dateRange widgetFilter" id="datepicker" name="trade_date">
	                    <input type="text" class="input-sm form-control" name="start" value="{$start_date}" style="height:30px;"/>
	                    <span class="input-group-addon">to</span>
	                    <input type="text" class="input-sm form-control" name="end" value="{$end_date}" style="height:30px;"/>
	                </div>
				</div>
			</div>
		</div>
	</div>
	
			
	<div class="dashboardWidgetContent" style='padding:5px'>
		{include file="dashboards/TransactionContent.tpl"|@vtemplate_path:$MODULE_NAME}
	</div>
	
	<div class="widgeticons dashBoardWidgetFooter">
	    <div class="footerIcons pull-right">
	        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
	    </div>
	</div>
	{literal}
	<script>
		Vtiger_Widget_Js('Vtiger_YesterdayTransactions_Widget_Js', {}, {
			postLoadWidget: function() {
			  	this._super();
		        app.helper.hideModal();
		        this.restrictContentDrag();
		        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		        var adjustedHeight = this.getContainer().height()-50;
		        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		        widgetContent.css({height: widgetContent.height()-40});
				
			},
		    
		    postResizeWidget: function() {
		    	this._super();
		        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
		        var adjustedHeight = this.getContainer().height()-100;
		        widgetContent.css({height: adjustedHeight});
		        slimScrollDiv.css({height: adjustedHeight});
			}
		});
	</script>
	{/literal}
{/strip}	 
