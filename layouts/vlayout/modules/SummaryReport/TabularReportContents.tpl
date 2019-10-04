{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
	<div class="contents-topscroll">
		<div class="topscroll-div container-fluid">
			&nbsp;
		 </div>
	</div>
	<div id="reportDetails" class="contents-bottomscroll">
		<div class="bottomscroll-div">
            {assign var=NEW_COUNT value=0}
            {assign var=HEADERS value=$DATA['header']}
            <table class="table table-bordered table-striped">
                <thead>
                <tr class="blockHeader">
                    {foreach from=$HEADERS item=HEADER_FIELD key=NAME}
                        {if in_array($NAME,array_keys($MODTRACKER_FIELDS))}
                            <th nowrap>{vtranslate($HEADER_FIELD, $MODULE)}</th>
                        {else}
                            <th nowrap>{vtranslate($HEADER_FIELD->get('label'), $REPORT_MODEL->get('modulename'))}</th>
                        {/if}
                    {/foreach}
                </tr>
                </thead>
                {if $DATA['contents']}
                    {foreach item=CONTENTS from=$DATA['contents']}
                        {assign var=CHANGES value=$CONTENTS['changes']}
                        {assign var=COMMENTS value=$CONTENTS['comments']}
                        {*Changes*}
                        {foreach from=$CHANGES item=RECENT_ACTIVITY}
                            {assign var=RECORD value=$RECENT_ACTIVITY->getParent()}
                            {assign var=PROCEED value= TRUE}
                            {if ($RECENT_ACTIVITY->isRelationLink()) or ($RECENT_ACTIVITY->isRelationUnLink())}
                                {assign var=RELATION value=$RECENT_ACTIVITY->getRelationInstance()}
                                {if !($RELATION->getLinkedRecord())}
                                    {assign var=PROCEED value= FALSE}
                                {/if}
                            {/if}
                            {if $PROCEED}
                                {if $RECENT_ACTIVITY->isCreate()}
                                    {assign var=NEW_COUNT value=$NEW_COUNT+1}
                                    <tr>
                                    {foreach from=$HEADERS item=HEADER_FIELD key=RELATED_HEADERNAME}
                                        <td>
                                            {if in_array($RELATED_HEADERNAME,array_keys($MODTRACKER_FIELDS))}
                                                {if $RELATED_HEADERNAME eq 'whodid'}
                                                    {$RECENT_ACTIVITY->getModifiedBy()->getName()}
                                                {elseif $RELATED_HEADERNAME eq 'changedon'}
                                                    {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getParent()->get('createdtime'))}
                                                {elseif $RELATED_HEADERNAME eq 'postvalue'}
                                                    {vtranslate('LBL_CREATED', $REPORT_MODEL->get('modulename'))}
                                                {elseif $RELATED_HEADERNAME eq 'prevalue' || $RELATED_HEADERNAME eq 'fieldname'}
                                                {/if}
                                            {elseif $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                                <a href="{$RECORD->getDetailViewUrl()}">{$RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                            {elseif $RELATED_HEADERNAME eq 'time_start'}
                                            {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                                                {CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                {if $RELATED_HEADERNAME eq 'listprice'}
                                                    {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                {/if}
                                            {else}
                                                {$RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                            {/if}
                                        </td>
                                    {/foreach}
                                    </tr>
                                {elseif $RECENT_ACTIVITY->isUpdate()}
                                    {foreach item=FIELDMODEL from=$RECENT_ACTIVITY->getFieldInstances()}
                                        {if $FIELDMODEL && $FIELDMODEL->getFieldInstance() && $FIELDMODEL->getFieldInstance()->isViewableInDetailView()}
                                            {assign var=NEW_COUNT value=$NEW_COUNT+1}
                                            <tr>
                                                {foreach from=$HEADERS item=HEADER_FIELD key=RELATED_HEADERNAME}
                                                    <td>
                                                    {if in_array($RELATED_HEADERNAME,array_keys($MODTRACKER_FIELDS))}
                                                        {if $RELATED_HEADERNAME eq 'whodid'}
                                                            {$RECENT_ACTIVITY->getModifiedBy()->getName()}
                                                        {elseif $RELATED_HEADERNAME eq 'changedon'}
                                                            {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getActivityTime())}
                                                        {elseif $RELATED_HEADERNAME eq 'postvalue'}
                                                            {if $FIELDMODEL->get('postvalue') eq '' || ($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELDMODEL->get('postvalue') eq '0')}
                                                                {vtranslate('LBL_DELETED')}
                                                            {else}
                                                                {Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('postvalue'))))}
                                                            {/if}
                                                        {elseif $RELATED_HEADERNAME eq 'prevalue'}
                                                            {Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('prevalue'))))}
                                                        {elseif $RELATED_HEADERNAME eq 'fieldname'}
                                                            {vtranslate($FIELDMODEL->getName(),$MODULE_NAME)}
                                                        {/if}
                                                    {elseif $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                                        <a href="{$RECORD->getDetailViewUrl()}">{$RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                                    {elseif $RELATED_HEADERNAME eq 'time_start'}
                                                    {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                                                        {CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                        {if $RELATED_HEADERNAME eq 'listprice'}
                                                            {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                        {/if}
                                                    {else}
                                                        {$RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                                    {/if}
                                                    </td>
                                                {/foreach}
                                            </tr>
                                        {/if}
                                    {/foreach}
                                {elseif ($RECENT_ACTIVITY->isRelationLink() || $RECENT_ACTIVITY->isRelationUnLink())}
                                    {assign var=NEW_COUNT value=$NEW_COUNT+1}
                                    <tr>
                                        {foreach from=$HEADERS item=HEADER_FIELD key=RELATED_HEADERNAME}
                                            <td>
                                                {if in_array($RELATED_HEADERNAME,array_keys($MODTRACKER_FIELDS))}
                                                    {if $RELATED_HEADERNAME eq 'whodid'}
                                                        {$RECENT_ACTIVITY->getModifiedBy()->getName()}
                                                    {elseif $RELATED_HEADERNAME eq 'changedon'}
                                                        {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RELATION->get('changedon'))}
                                                    {elseif $RELATED_HEADERNAME eq 'postvalue'}
                                                        {vtranslate($RELATION->getLinkedRecord()->getModuleName(), $RELATION->getLinkedRecord()->getModuleName())}
                                                        &nbsp;
                                                        {if $RECENT_ACTIVITY->isRelationLink()}
                                                            {vtranslate('LBL_ADDED', $MODULE_NAME)}
                                                        {else}
                                                            {vtranslate('LBL_REMOVED', $MODULE_NAME)}
                                                        {/if}
                                                        &nbsp;
                                                        {if $RELATION->getLinkedRecord()->getModuleName() eq 'Calendar'}
                                                            {if isPermitted('Calendar', 'DetailView', $RELATION->getLinkedRecord()->getId()) eq 'yes'} {$RELATION->getLinkedRecord()->getName()}
                                                            {else} {/if}
                                                        {else}{$RELATION->getLinkedRecord()->getName()}{/if}
                                                    {elseif $RELATED_HEADERNAME eq 'prevalue' || $RELATED_HEADERNAME eq 'fieldname'}
                                                    {/if}
                                                {elseif $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                                    <a href="{$RECORD->getDetailViewUrl()}">{$RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                                {elseif $RELATED_HEADERNAME eq 'time_start'}
                                                {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                                                    {CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                    {if $RELATED_HEADERNAME eq 'listprice'}
                                                        {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                                    {/if}
                                                {else}
                                                    {$RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                                {/if}
                                            </td>
                                        {/foreach}
                                    </tr>
                                {/if}
                            {/if}
                        {/foreach}
                        {*Comments*}
                        {foreach from=$COMMENTS key=ROWNO item=COMMENT}
                            {if $ROWNO eq 0}
                                {assign var=RECORD value=$COMMENT->getParentRecordModel()}
                            {/if}
                            <tr>
                                {foreach from=$HEADERS item=HEADER_FIELD key=RELATED_HEADERNAME}
                                    <td>
                                        {if in_array($RELATED_HEADERNAME,array_keys($MODTRACKER_FIELDS))}
                                            {if $RELATED_HEADERNAME eq 'whodid'}
                                                {assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
                                                {$COMMENTOR->getName()}
                                            {elseif $RELATED_HEADERNAME eq 'changedon'}
                                                {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($COMMENT->getCommentedTime())}
                                            {elseif $RELATED_HEADERNAME eq 'postvalue'}
                                                {nl2br($COMMENT->get('commentcontent'))}
                                            {elseif $RELATED_HEADERNAME eq 'prevalue'}
                                            {elseif $RELATED_HEADERNAME eq 'fieldname'}
                                                {vtranslate('LBL_COMMENTS')}
                                            {/if}
                                        {elseif $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                            <a href="{$RECORD->getDetailViewUrl()}">{$RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                                        {elseif $RELATED_HEADERNAME eq 'time_start'}
                                        {elseif $RELATED_HEADERNAME eq 'listprice' || $RELATED_HEADERNAME eq 'unit_price'}
                                            {CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                            {if $RELATED_HEADERNAME eq 'listprice'}
                                                {assign var="LISTPRICE" value=CurrencyField::convertToUserFormat($RECORD->get($RELATED_HEADERNAME), null, true)}
                                            {/if}
                                        {else}
                                            {$RECORD->getDisplayValue($RELATED_HEADERNAME)}
                                        {/if}
                                    </td>
                                {/foreach}
                            </tr>
                        {/foreach}
                    {/foreach}
                {else}
                    {vtranslate('LBL_NO_DATA_AVAILABLE','Reports')}
                {/if}
            </table>
            <input type="hidden" id="updatedCount" value="{$NEW_COUNT}" />
		</div>
	</div>
	<br>
   </div>
</div>
{/strip}
