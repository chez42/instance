{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row" id="divNodeName">
        <h3>My Team's Forecast</h3>
    </div>
    <hr>    
	<div class="row">
		<div class="col-lg-5">
            <b>{$STAGESNAME} Chart</b>
		</div>
		<div class="col-lg-7">
			<select class="select2 col-lg-2" id="financial_time_summary">				
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_SUMMARY eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<hr>
	<div class="row">
		<div class="col-lg-12" id="forecastSummary" style="text-align: center; width:100%;">
			
		</div>
	</div>
    <hr>    
	<div class="row">
		<div class="col-lg-4">
			<b>Forecast</b>
		</div>
		<div class="col-lg-4"><span style="float: left;line-height: 30px;">From </span>
			<select class="select2" id="financial_time_from">
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_FORM eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
		<div class="col-lg-4"><span style="float: left;line-height: 30px;">To </span>
			<select class="select2" id="financial_time_to">
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_TO eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<hr>
    <div class="row">
        <div class="col-lg-12">
            <button id="btnExpandAll" class="btn">Expand All</button>
        </div>
     </div>
	<div class="row">
		<div class="col-lg-12" id="forecastDashboardFrom">
			
		</div>
	</div>	
</div>

