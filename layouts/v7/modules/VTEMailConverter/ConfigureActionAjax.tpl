{*<!--
/* ********************************************************************************
* The content of this file is subject to the VTEMailConverter("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{strip}
	<tr style="display: none;">
		<td colspan="6">
			<input type="hidden" id="create_if_not_existed_value" value="{$RULE.rules.create_if_not_existed}" />
            {include file="Field_Default_Values.tpl"|@vtemplate_path:$MODULE AVAILABLE_FIELDS=$LIST_FIELDS USER_MODEL=$USER_MODEL ACTION_TYPE=$ACTION_TYPE}
		</td>
	</tr>
	{if $RULE_EXISTED}
        {assign var='RULES' value=$RULE.rules}
        {assign var='IDENTIFIERS' value=$RULES.identifier}
        {assign var='END_WITHS' value=$RULES.end_with}
        {assign var='EXTRA_SEPARATORS' value=$RULES.extra_separator}
        {assign var='EXTRA_STARTS' value=$RULES.extra_start}
        {assign var='EXTRA_LENGTHS' value=$RULES.extra_length}
        {assign var='FIELD_NAMES' value=$RULES.fieldname}
        {assign var='DEFAULT_VALUES' value=$RULES.default_value}
        {assign var='MATCH_FIELDS' value=$RULES.match_field}
		{foreach key=num item=IDENTIFIER from=$IDENTIFIERS}
            {assign var='END_WITH' value=$END_WITHS.$num}
            {assign var='EXTRA_SEPARATOR' value=$EXTRA_SEPARATORS.$num}
            {assign var='EXTRA_START' value=$EXTRA_STARTS.$num}
            {assign var='EXTRA_LENGTH' value=$EXTRA_LENGTHS.$num}
            {assign var='FIELD_NAME' value=$FIELD_NAMES.$num}
            {assign var='DEFAULT_VALUE' value=$DEFAULT_VALUES.$num}
            {assign var='MATCH_FIELD' value=$MATCH_FIELDS.$num}
			<tr class="listViewEntries">
				<td>
					<input name="rules[identifier][]" value="{$IDENTIFIER}" type="text" class="inputElement vte-email-converter-identifier"/>
				</td>
				<td style="text-align: center;">*<input type="hidden" name="rules[middle_condition][]" value="*" ></td>
				<td>
					<input type="text" name="rules[end_with][]" class="inputElement autoComplete end-with input-medium" value="{$END_WITH}"/>
				</td>

				<td class="advanced-options">
					<input type="text" value="{$EXTRA_SEPARATOR}" name="rules[extra_separator][]"  class="inputElement input-small autoComplete advanced-separator" placeholder="Separator"/>
				</td>
				<td class="advanced-options">
					<input type="number" value="{$EXTRA_START}" name="rules[extra_start][]" class="inputElement input-medium" placeholder="Start Position" title="Start Position"/>
				</td>
				<td class="advanced-options">
					<input type="number" value="{$EXTRA_LENGTH}" name="rules[extra_length][]" class="inputElement input-medium" placeholder="Number of Letters(Characters)" title="Number of Letters(Characters)"/>
				</td>

				<td>&nbsp;</td>
				<td class="input-large">
					<select name="rules[fieldname][]" class="inputElement input-large select2 field-name">
						{*{if $ACTION_TYPE eq 'UPDATE'}
							{if $MODULENAME1}
								<option value="{$MODULENAME1}_vte_match_record_no" {if $FIELD_NAME eq $MODULENAME1|cat:'_vte_match_record_no'}selected {/if}>
									{vtranslate('MATCH_RECORD_NO', $MODULE)}&nbsp;({vtranslate({$MODULENAME1}, {$MODULENAME1})})
								</option>
							{/if}
							{if $MODULENAME2}
								<option value="{$MODULENAME2}_vte_match_record_no" {if $FIELD_NAME eq $MODULENAME2|cat:'_vte_match_record_no'}selected {/if}>
									{vtranslate('MATCH_RECORD_NO', $MODULE)}&nbsp;({vtranslate({$MODULENAME2}, {$MODULENAME2})})
								</option>
							{/if}
							{if $MODULENAME1 eq 'Contacts'}
								<option value="{$MODULENAME1}_vte_match_email" {if $FIELD_NAME eq $MODULENAME1|cat:'_vte_match_email'}selected {/if}>
									{vtranslate('MATCH_RECORD_EMAIL', $MODULE)}&nbsp;({vtranslate({$MODULENAME1}, {$MODULENAME1})})
								</option>
							{/if}
						{/if}*}
						{foreach item=FIELD_MODEL from=$LIST_FIELDS}
							<option value="{$FIELD_MODEL->getModuleName()}_{$FIELD_MODEL->get('name')}"
									{if $FIELD_NAME eq $FIELD_MODEL->getModuleName()|cat:'_'|cat:$FIELD_MODEL->get('name')} selected{/if}>
								{vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModuleName())}
								{if $MODULENAME2}&nbsp;({vtranslate($FIELD_MODEL->getModuleName(), $FIELD_MODEL->getModuleName())}){/if}
							</option>
						{/foreach}
					</select>
				</td>
				<td class="match-field" style="display: {if $ACTION_TYPE eq 'CREATE'}none{/if};">
					<select name="rules[match_field][]" class="inputElement match-field-element input-medium select2">
						<option value="0" {if $MATCH_FIELD neq 1}selected {/if}>{vtranslate('LBL_NO', $MODULE)}</option>
						<option value="1" {if $MATCH_FIELD eq 1}selected {/if}>{vtranslate('LBL_YES', $MODULE)}</option>
					</select>
				</td>
				<td class="default-value">
					<input name="rules[default_value][]" class="inputElement default_value_field" value="{$DEFAULT_VALUE}"/>
				</td>
				<td>
					<a class="deleteRecordButton"><i title="Delete" class="fa fa-trash alignMiddle"></i></a>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr class="listViewEntries">
			<td>
				<input name="rules[identifier][]" value="" type="text" class="inputElement vte-email-converter-identifier"/>
			</td>
			<td style="text-align: center;">*<input type="hidden" name="rules[middle_condition][]" value="*" ></td>
			<td>
				<input type="text" name="rules[end_with][]" class="inputElement autoComplete end-with input-medium" value="{$END_WITH}"/>
			</td>

			<td class="advanced-options">
				<input type="text" value="" name="rules[extra_separator][]"  class="inputElement input-small autoComplete advanced-separator" placeholder="Separator"/>
			</td>
			<td class="advanced-options">
				<input type="number" value="" name="rules[extra_start][]" class="inputElement input-medium" placeholder="Start Position" title="Start Position"/>
			</td>
			<td class="advanced-options">
				<input type="number" value="" name="rules[extra_length][]" class="inputElement input-medium" placeholder="Number of Letters(Characters)" title="Number of Letters(Characters)"/>
			</td>

			<td>&nbsp;</td>
			<td class="input-large">
				<select name="rules[fieldname][]" class="inputElement input-large select2 field-name">
    				{*{if $ACTION_TYPE eq 'UPDATE'}
						{if $MODULENAME1}
							<option value="{$MODULENAME1}_vte_match_record_no">
								{vtranslate('MATCH_RECORD_NO', $MODULE)}&nbsp;({vtranslate({$MODULENAME1}, {$MODULENAME1})})
							</option>
						{/if}
						{if $MODULENAME2}
							<option value="{$MODULENAME2}_vte_match_record_no">
								{vtranslate('MATCH_RECORD_NO', $MODULE)}&nbsp;({vtranslate({$MODULENAME2}, {$MODULENAME2})})
							</option>
						{/if}
						{if $MODULENAME1 eq 'Contacts'}
							<option value="{$MODULENAME1}_vte_match_email" {if $FIELD_NAME eq $MODULENAME1|cat:'_vte_match_email'}selected {/if}>
								{vtranslate('MATCH_RECORD_EMAIL', $MODULE)}&nbsp;({vtranslate({$MODULENAME1}, {$MODULENAME1})})
							</option>
						{/if}
					{/if}*}
					{foreach item=FIELD_MODEL from=$LIST_FIELDS}
						<option value="{$FIELD_MODEL->getModuleName()}_{$FIELD_MODEL->get('name')}">
							{vtranslate($FIELD_MODEL->get('label'), $FIELD_MODEL->getModuleName())}
							{if $MODULENAME2}&nbsp;({vtranslate($FIELD_MODEL->getModuleName(), $FIELD_MODEL->getModuleName())}){/if}
						</option>
					{/foreach}
				</select>
			</td>
			<td class="match-field">
				<select name="rules[match_field][]" class="inputElement match-field-element input-medium select2">
					<option value="0" selected>{vtranslate('LBL_NO', $MODULE)}</option>
					<option value="1">{vtranslate('LBL_YES', $MODULE)}</option>
				</select>
			</td>
			<td class="default-value">
				<input name="rules[default_value][]" class="inputElement" />
			</td>
			<td>
				<a class="deleteRecordButton"><i title="Delete" class="fa fa-trash alignMiddle"></i></a>
			</td>
		</tr>
	{/if}
{/strip}
