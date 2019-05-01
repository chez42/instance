<div style='padding-top: 0;margin-bottom: 2%;padding-right:15px;'>
	{if count($DATA) gt 0}
		
		<div class="row" style="padding:5px">
			{foreach item=FIELD_MODEL from=$HEADERS}
				{assign var=FIELD_LABEL value=$FIELD_MODEL->get("field_label")}
				<div class="col-md-3"><strong>{vtranslate($FIELD_LABEL,$MODULE_NAME)}</strong></div>
			{/foreach}
		</div>
	
		{foreach item=RECORD from=$DATA}
			<div class="row" style="padding:5px">
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$HEADERS}
					<div class="col-md-3 textOverflowEllipsis {if $FIELD_NAME eq 'net_amount'}text-center{/if}" title="{strip_tags($RECORD->get($FIELD_NAME))}" style="padding-right: 5px;">
						
						{if $FIELD_MODEL->get('uitype') eq '72' }
							{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
							{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
								{number_format($RECORD->get($FIELD_NAME)|replace:",":"")}{$RECORD->get('currencySymbol')}
							{else}
								{$RECORD->get('currencySymbol')}{number_format($RECORD->get($FIELD_NAME)|replace:",":"")}
							{/if}
						{else if  $FIELD_MODEL->get('uitype') eq '71'}	
							{CurrencyField::appendCurrencySymbol($RECORD->get($FIELD_NAME), $RECORD->get('userCurrencySymbol'))}
						{else if $FIELD_NAME eq 'account_number'}	
						    <a href="{$RECORD->getDetailViewUrl()}" target="_blank">{$RECORD->get($FIELD_NAME)}</a>
                        {else}
							{$RECORD->get($FIELD_NAME)|wordwrap:12:"<br />\n"}
						{/if}
					</div>
				{/foreach}
			</div>
		{/foreach}	
		
	{else}
	
		<span class="noDataMsg">
			{vtranslate('LBL_NO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</span>
		
	{/if}
	
	{if $PAGING->get('nextPageExists') eq 'true'}
		<div class="moreLinkDiv" style="padding-top:10px;padding-bottom:5px;">
			<a class="miniListMoreLink" target="_blank" href='{$MORE_LINK_URL}'>{vtranslate('LBL_MORE')}...</a>
		</div>
	{/if}
</div>
<script>
	$(document).ready(function(){
		if(jQuery('.moreLinkDiv').length){
			jQuery('.moreLinkDiv').addClass('hide');
			jQuery('.moreLinkDivContent').removeClass('hide');
		}
	});
</script>