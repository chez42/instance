<div style='padding:4%;padding-top: 0;margin-bottom: 2%'>

	<div class="row-fluid" style="padding:5px">
		{foreach item=FIELD_MODEL from=$HEADERS}
			{assign var=FIELD_LABEL value=$FIELD_MODEL->get("field_label")}
			<div class="span4"><strong>{vtranslate($FIELD_LABEL,$MODULE_NAME)}</strong></div>
		{/foreach}
	</div>

	{if count($DATA) gt 0}
		{foreach item=RECORD from=$DATA}
			<div class="row-fluid" style="padding:5px">
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$HEADERS}
					<div class="span4 textOverflowEllipsis" title="{strip_tags($RECORD->get($FIELD_NAME))}">
						{if $FIELD_MODEL->get('uitype') eq '72'  or $FIELD_MODEL->get('uitype') eq '71'}
							{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
							{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
								{$RECORD->get($FIELD_NAME)}{$RECORD->get('currencySymbol')}
							{else}
								{$RECORD->get('currencySymbol')}{$RECORD->get($FIELD_NAME)}
							{/if}
						{else}
							{$RECORD->get($FIELD_NAME)}
						{/if}
					</div>
				{/foreach}
			</div>
		{/foreach}	
	{else}
		<span class="noDataMsg">
			{vtranslate('LBL_EQ_ZERO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</span>
	{/if}
	
	{if $PAGING->get('nextPageExists') eq 'true'}
		<div class="row-fluid" style="padding:5px;padding-bottom:10px;">
			<a class="pull-right" href='{$MORE_LINK_URL}'>{vtranslate('LBL_MORE')}</a>
		</div>
	{/if}

</div>