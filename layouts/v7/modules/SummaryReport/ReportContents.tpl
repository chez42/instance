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
            <input type="hidden" id="updatedCount" value="{$NEW_COUNT}" />
            {assign var=HEADERS value=$DATA['header']}
            <table class="table table-bordered table-bordered">
                <thead>
                <tr class="blockHeader">
                    {foreach from=$HEADERS item=HEADER_FIELD key=NAME}
                        <th nowrap>{vtranslate($HEADER_FIELD->get('label'), $REPORT_MODEL->get('modulename'))}</th>
                    {/foreach}
                </tr>
                </thead>
                {if $DATA['contents']}
                    {assign var=CONTENTS value=$DATA['contents']}
                    {foreach from=$CONTENTS item=CONTENT}
                        {assign var=RECORD value=$CONTENT['record']}
                        {assign var=RECORD_DELETED value=$RECORD->get('deleted')}
                        {assign var=CHANGES value=$CONTENT['changes']}
                        {assign var=COMMENTS value=$CONTENT['comments']}
                        {assign var=HEADER_COUNT value=$HEADERS|count}
                        <tr>
                            {foreach from=$HEADERS item=HEADER_FIELD}
                                {assign var=RELATED_HEADERNAME value=$HEADER_FIELD->get('name')}
                                <td {if $RECORD_DELETED}style="background-color: #ffdddd !important;"{/if}>
                                    {if $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
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

                        {if $CHANGES}
                            <tr>
                                <td colspan="{$HEADER_COUNT}" {if $RECORD_DELETED}style="background-color: #ffdddd !important;"{/if}>
                                    <ul class="unstyled">
                                        {foreach item=RECENT_ACTIVITY from=$CHANGES}
                                            {assign var=PROCEED value= TRUE}
                                            {if ($RECENT_ACTIVITY->isRelationLink()) or ($RECENT_ACTIVITY->isRelationUnLink())}
                                                {assign var=RELATION value=$RECENT_ACTIVITY->getRelationInstance()}
                                                {if !($RELATION->getLinkedRecord())}
                                                    {assign var=PROCEED value= FALSE}
                                                {/if}
                                            {/if}
                                            {if $PROCEED}
                                                {if $RECENT_ACTIVITY->isCreate()}
                                                    <li>
                                                        <div>
                                                            <span><strong>{$RECENT_ACTIVITY->getModifiedBy()->getName()}</strong> {vtranslate('LBL_ON', $MODULE)} {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getParent()->get('createdtime'))} {vtranslate('LBL_CREATED', $REPORT_MODEL->get('modulename'))}</span>
                                                        </div>
                                                    </li>
                                                {else if $RECENT_ACTIVITY->isUpdate()}
                                                    <li style="margin-top:5px;">
                                                        <div>
                                                            <span><strong>{$RECENT_ACTIVITY->getModifiedBy()->getDisplayName()}</strong> {vtranslate('LBL_ON', $MODULE)} {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getActivityTime())} {vtranslate('LBL_UPDATED', $MODULE_NAME)} : </span>
                                                        </div>

                                                        {foreach item=FIELDMODEL from=$RECENT_ACTIVITY->getFieldInstances()}
                                                            {if $FIELDMODEL && $FIELDMODEL->getFieldInstance() && $FIELDMODEL->getFieldInstance()->isViewableInDetailView()}
                                                                <div style="margin-left:40px" class='font-x-small updateInfoContainer'>
                                                                    <i>{vtranslate($FIELDMODEL->getName(),$MODULE_NAME)}</i> :&nbsp;
                                                                    {if $FIELDMODEL->get('prevalue') neq '' && $FIELDMODEL->get('postvalue') neq '' && !($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && ($FIELDMODEL->get('postvalue') eq '0' || $FIELDMODEL->get('prevalue') eq '0'))}
                                                                        &nbsp;{vtranslate('LBL_FROM')} <b style="white-space:pre;">{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('prevalue'))))}</b>
                                                                    {else if $FIELDMODEL->get('postvalue') eq '' || ($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELDMODEL->get('postvalue') eq '0')}
                                                                        &nbsp; <b> {vtranslate('LBL_DELETED')} </b> ( <del>{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('prevalue'))))}</del> )
                                                                    {else}
                                                                        &nbsp;{vtranslate('LBL_CHANGED')}
                                                                    {/if}
                                                                    {if $FIELDMODEL->get('postvalue') neq '' && !($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELDMODEL->get('postvalue') eq '0')}
                                                                        &nbsp;{vtranslate('LBL_TO')}&nbsp;<b style="white-space:pre;">{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('postvalue'))))}</b>
                                                                    {/if}

                                                                </div>
                                                            {/if}
                                                        {/foreach}

                                                    </li>
                                                {else if ($RECENT_ACTIVITY->isRelationLink() || $RECENT_ACTIVITY->isRelationUnLink())}
                                                    <li style="margin-top:5px;">
                                                        <div class="row-fluid">
                                                            {assign var=RELATION value=$RECENT_ACTIVITY->getRelationInstance()}
                                                            <span>{vtranslate($RELATION->getLinkedRecord()->getModuleName(), $RELATION->getLinkedRecord()->getModuleName())}</span> <span>
                                                            {if $RECENT_ACTIVITY->isRelationLink()}
                                                                {vtranslate('LBL_ADDED', $MODULE_NAME)}
                                                            {else}
                                                                {vtranslate('LBL_REMOVED', $MODULE_NAME)}
                                                            {/if} </span><span>
                                                            {if $RELATION->getLinkedRecord()->getModuleName() eq 'Calendar'}
                                                                {if isPermitted('Calendar', 'DetailView', $RELATION->getLinkedRecord()->getId()) eq 'yes'} <strong>{$RELATION->getLinkedRecord()->getName()}</strong> {else} {/if}
                                                            {else} <strong>{$RELATION->getLinkedRecord()->getName()}</strong> {/if}</span>
                                                                            <span class="pull-right"><p class="muted"><small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($RELATION->get('changedon'))}">{Vtiger_Util_Helper::formatDateDiffInStrings($RELATION->get('changedon'))}</small></p></span>
                                                        </div>
                                                    </li>
                                                {else if $RECENT_ACTIVITY->isRestore()}
                                                    <li>

                                                    </li>
                                                {else if $RECENT_ACTIVITY->isDelete()}
                                                    {assign var=RELATION value=$RECENT_ACTIVITY->getRelationInstance()}
                                                    <li>
                                                        <div>
                                                            <span><strong>{$RECENT_ACTIVITY->getModifiedBy()->getName()}</strong> {vtranslate('LBL_ON', $MODULE)} {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->get('changedon'))} {vtranslate('LBL_DELETED', $REPORT_MODEL->get('modulename'))}</span>
                                                        </div>
                                                    </li>
                                                {/if}
                                            {/if}
                                        {/foreach}
                                    </ul>
                                </td>
                            </tr>
                        {/if}
                        {if $COMMENTS}
                            <tr>
                                <td colspan="{$HEADER_COUNT}" {if $RECORD_DELETED}style="background-color: #ffdddd !important;"{/if}>
                                    <ul class="unstyled">
                                        {foreach item=COMMENT from=$COMMENTS}
                                            {assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
                                            <li style="margin-top:5px;">
                                                <div>
                                                    <span>{vtranslate('LBL_NEW_NOTE_BY', $MODULE)} <strong>{$COMMENTOR->getName()}</strong> {vtranslate('LBL_ON', $MODULE)} {Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($COMMENT->getCommentedTime())} : </span>
                                                </div>
                                                <div style="margin-left:40px" class='font-x-small updateInfoContainer'>
                                                    {nl2br($COMMENT->get('commentcontent'))}
                                                </div>
                                            </li>

                                        {/foreach}
                                    </ul>
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                {else}
                    {vtranslate('LBL_NO_DATA_AVAILABLE','Reports')}
                {/if}
            </table>
            {if $LIMIT_EXCEEDED}
                <center>{vtranslate('LBL_LIMIT_EXCEEDED','Reports')} <span class="pull-right"><a href="#top" >{vtranslate('LBL_TOP','Reports')}</a></span></center>
            {/if}
		</div>
	</div>
	<br>
   </div>
</div>
{/strip}
