<div style='padding:4%;padding-top: 0;margin-bottom: 2%'>

	<div class="row-fluid" style="padding:5px">
		{foreach item=FIELD_MODEL from=$HEADERS}
			{assign var=FIELD_LABEL value=$FIELD_MODEL->get("field_label")}
			<div class="span4"><strong>{vtranslate($FIELD_LABEL,$MODULE_NAME)}</strong></div>
		{/foreach}
	</div>

	{if count($DATA) gt 0}
		{assign var=CURRENT_USER_MODEL  value=Users_Record_Model::getCurrentUserModel()}
		{foreach item=RECORD from=$DATA}
			<div class="row-fluid" style="padding:5px">
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$HEADERS name="minilistWidgetModelRowHeaders"}
					{assign var=title value=strip_tags($RECORD->get($FIELD_NAME))}
					{if $title eq '--'}
						{assign var=title value='Not Available'}
					{/if}	
					<div class="span4 textOverflowEllipsis" title="{$title}">
						{if $FIELD_NAME eq 'account_number'}
							<a href="{$RECORD->getDetailViewUrl()}">{$RECORD->get($FIELD_NAME)}</a>
						{else if $RECORD->get($FIELD_NAME) eq '--'}
							N/A
						{else if $FIELD_MODEL->get('uitype') eq '72'  or $FIELD_MODEL->get('uitype') eq '71'}
							{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
							{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
								{number_format($RECORD->get($FIELD_NAME)|replace:",":"")}{$RECORD->get('currencySymbol')}
							{else}
								{$RECORD->get('currencySymbol')}{number_format($RECORD->get($FIELD_NAME)|replace:",":"")}
							{/if}
						{else}
							{$RECORD->get($FIELD_NAME)}&nbsp;
						{/if}
					</div>
				{/foreach}
			</div>
		{/foreach}
	{else if $MESSAGE neq ''}
		<span class="noDataMsg">
			{vtranslate($MESSAGE, $MODULE_NAME)}
		</span>
	{else}
		<span class="noDataMsg">
			{vtranslate('LBL_EQ_ZERO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</span>
	{/if}
</div>