{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

{strip}
		<input type="hidden" id="updatedCount" value="{$NEW_COUNT}" />
<div id="vtereports_html" style="padding: 10px 10px 10px 10px;background-color: white;">
                {if !empty($CALCULATION_FIELDS)}
                    <table class=" table-bordered table-condensed marginBottom10px" width="100%">
                        <thead>
                            <tr class="blockHeader">
                                <th>{vtranslate('LBL_FIELD_NAMES',$MODULE)}</th>
                                <th>{vtranslate('LBL_SUM',$MODULE)}</th>
                                <th>{vtranslate('LBL_AVG',$MODULE)}</th>
                                <th>{vtranslate('LBL_MIN',$MODULE)}</th>
                                <th>{vtranslate('LBL_MAX',$MODULE)}</th>
                            </tr>
                        </thead>
                        {assign var=ESCAPE_CHAR value=array('_SUM','_AVG','_MIN','_MAX')}
                        {foreach from=$CALCULATION_FIELDS item=CALCULATION_FIELD key=index}
                            <tr>
                                {assign var=CALCULATION_FIELD_KEYS value=array_keys($CALCULATION_FIELD)}
                                {assign var=CALCULATION_FIELD_KEYS value=$CALCULATION_FIELD_KEYS|replace:$ESCAPE_CHAR:''}
                                {assign var=FIELD_IMPLODE value=explode('_',$CALCULATION_FIELD_KEYS['0'])}
                                {assign var=MODULE_NAME value=$FIELD_IMPLODE['0']}
                                {assign var=FIELD_LABEL value=" "|implode:$FIELD_IMPLODE}
                                {assign var=FIELD_LABEL value=$FIELD_LABEL|replace:$MODULE_NAME:''}
                                <td>{vtranslate($MODULE_NAME,$MODULE)} {vtranslate($FIELD_LABEL, $MODULE)}</td>
                                {foreach from=$CALCULATION_FIELD item=CALCULATION_VALUE}
                                        <td width="15%">{$CALCULATION_VALUE}</td>
                                {/foreach}
                            </tr>
                        {/foreach}
                    </table>
                {/if}

		{if $DATA neq ''}
			{assign var=HEADERS value=$DATA[0]}
                        <table style="width: 100%;">
				{foreach from=$DATA item=VALUES}
					<tr>
						{foreach from=$VALUES item=VALUE key=NAME}
							<td style="background-color:white;">{$VALUE}</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
			{if $LIMIT_EXCEEDED}
				<center>{vtranslate('LBL_LIMIT_EXCEEDED',$MODULE)} <span class="pull-right"><a href="#top" >{vtranslate('LBL_TOP',$MODULE)}</a></span></center>
			{/if}
		{else}
			{vtranslate('LBL_NO_DATA_AVAILABLE',$MODULE)}
		{/if}
</div>
</div>
<div class="no-print">
{/strip}
