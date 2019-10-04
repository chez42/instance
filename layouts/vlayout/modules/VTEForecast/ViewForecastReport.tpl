{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row-fluid" id="divNodeName">
        <h3>My Team's Forecast</h3>
    </div>
    <hr>    
	<div class="row-fluid">
		<div class="span4">
            <b>{$STAGESNAME} Chart</b>
		</div>
		<div class="span8">
			<select class="select2 span2" id="financial_time_summary">				
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_SUMMARY eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<hr>
	<div class="row-fluid">
		<div class="span12" id="forecastSummary" style="text-align: center; width:100%;">
			
		</div>
	</div>
    <hr>    
	<div class="row-fluid">
		<div class="span4">
			<b>Forecast</b>
		</div>
		<div class="span4"><span style="float: left;line-height: 30px;">From </span>
			<select class="select2 span2" id="financial_time_from">
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_FORM eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
		<div class="span4"><span style="float: left;line-height: 30px;">To </span>
			<select class="select2 span2" id="financial_time_to">
				{foreach item=ITEM from=$MONTHYEARS}
					<option value="{$ITEM->value}" {if $CURRENT_TIME_TO eq $ITEM->value}selected{/if}>{$ITEM->text}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<hr>
    <div class="row-fluid">
        <div class="span12">
            <button id="btnExpandAll" class="btn">Expand All</button>
        </div>
     </div>
	<div class="row-fluid">
		<div class="span12" id="forecastDashboardFrom">
			
		</div>
	</div>	
</div>

