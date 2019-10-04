{*/* * *******************************************************************************
* The content of this file is subject to the VTE History Log ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{literal}
    <link type="text/css" rel="stylesheet" href="layouts/vlayout/modules/VTEHistoryLog/resources/VTEHistoryLog.css" media="screen">
{/literal}
{strip}

    <div class="recentActivitiesContainer vteRecentActivitiesContainer">
        <input type="hidden" id="updatesCurrentPage" value="{$PAGING_MODEL->get('page')}"/>
        <input type="hidden" id="total_page" value="{$TOTAL_PAGE}"/>
        <div class='history'>
            {if !empty($RECENT_ACTIVITIES)}
                {assign var=LIMIT_FIELD value= $MODULE_MODEL->getLimitField()}
                {assign var=LIMIT_CHARACTER value= $MODULE_MODEL->getLimitCharacter()}
                <ul class="updates_timeline">
                    {foreach item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
                        {assign var=PROCEED value= TRUE}
                        {if ($RECENT_ACTIVITY->isRelationLink()) or ($RECENT_ACTIVITY->isRelationUnLink())}
                            {assign var=RELATION value=$RECENT_ACTIVITY->getRelationInstance()}
                            {if !($RELATION->getLinkedRecord())}
                                {assign var=PROCEED value= FALSE}
                            {/if}
                        {/if}
                        {if $PROCEED}
                            {assign var=PARENT_MODULE_NAME_LOWER value= $RECENT_ACTIVITY->get('parent_modulename')|strtolower}
                            {assign var=MODULE_NAME_LOWER value= $RECENT_ACTIVITY->get('module')|strtolower}
                            {if $MODULE_NAME_LOWER eq 'events'}
                                {assign var = MODULE_NAME_LOWER value = 'calendar'}
                            {/if}
                            {if $RECENT_ACTIVITY->isCreate() && $RECENT_ACTIVITY->get('module') neq 'ModComments'}
                                <li>
                                    <time class="update_time cursorDefault">
                                        {assign var=CREATED_DATE_TIME value= Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getActivityTime())}
                                        {assign var=CREATED_DATE_TIME_ARR value=' '|explode:$CREATED_DATE_TIME}
                                        <small title="{$CREATED_DATE_TIME_ARR[1]} {$CREATED_DATE_TIME_ARR[2]}" class="module-qtip">
                                            {$CREATED_DATE_TIME_ARR[0]}
                                        </small>
                                    </time>
                                    <div class="update_icon bg-info bg-info-{$PARENT_MODULE_NAME_LOWER}">
                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon($RECENT_ACTIVITY->get('parent_modulename'))}" alt="{vtranslate($RECENT_ACTIVITY->get('parent_modulename'), $RECENT_ACTIVITY->get('parent_modulename'))}" title="{vtranslate($RECENT_ACTIVITY->get('parent_modulename'), $RECENT_ACTIVITY->get('parent_modulename'))}">
                                    </div>
                                    <div class="update_info">
                                        <div class="update_info_block">
                                            <div class="update_info_block_header">
                                                <i class="fa fa-caret-left" aria-hidden="true"></i>

                                                <span class="update_icon bg-info vte-bg-info-small bg-info-{$MODULE_NAME_LOWER}">
                                                    <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon()}" alt="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}" title="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}">
                                                </span>
                                                {if $RECENT_ACTIVITY->get('module') eq 'Emails'}
                                                    <a class="vte-history-log-title" href="javascript:Vtiger_VTEAHistoryLog_Js.showComposeEmail('{$RECENT_ACTIVITY->get('crmid')}')" target="_blank">{$RECENT_ACTIVITY->get('label')}</a>
                                                {else}
                                                    <a class="vte-history-log-title" href="javascript:Vtiger_VTEAHistoryLog_Js.showDetailOverlay('index.php?module={$RECENT_ACTIVITY->get('module')}&view=Detail&record={$RECENT_ACTIVITY->get('crmid')}')" target="_blank">{$RECENT_ACTIVITY->get('label')}</a>
                                                {/if}
                                                <i class="vte-history-log-type">&nbsp;-&nbsp;{vtranslate('SINGLE_'|cat:$RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}&nbsp;{vtranslate('LBL_CREATED', $MODULE_NAME)}</i>

                                                <span class="pull-right">{$RECENT_ACTIVITY->getModifiedBy()->getName()}</span>
                                            </div>
                                            <div class="update_info_block_content">
                                                {assign var=COUNT_FIELD value=1}
                                                {foreach item=FIELDMODEL from=$RECENT_ACTIVITY->getFieldInstances()}
                                                    {if ($FIELDMODEL->get('postvalue') neq '' || !($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'currency' && $FIELDMODEL->get('prevalue') neq '0')) && $FIELDMODEL->get('fieldname') neq "source"}
                                                        <div class='font-x-small updateInfoContainer {if $COUNT_FIELD gt $LIMIT_FIELD} hide{/if}'>
                                                            <div class='update-name'>
                                                                <span class="field-name">{vtranslate($FIELDMODEL->getName(),$MODULE_NAME)}:&nbsp;</span>
                                                            </div>
                                                            <div class="update-to created-{$MODULE_NAME_LOWER}-{$FIELDMODEL->get('fieldname')}">
                                                                {if $RECENT_ACTIVITY->get('module') eq 'Emails' && $FIELDMODEL->get('fieldname') eq 'description'}
                                                                    {$MODULE_MODEL->truncate($FIELDMODEL->get('postvalue'), $LIMIT_CHARACTER)}
                                                                    {assign var=EMAILS_DESCRIPTION_FIELD_MODEL value=$FIELDMODEL}
                                                                {else}
                                                                    &nbsp;{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('postvalue'))))}
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        {assign var=COUNT_FIELD value=$COUNT_FIELD+1}
                                                    {/if}
                                                {/foreach}
                                                {if $COUNT_FIELD gt 6 && $RECENT_ACTIVITY->get('module') neq 'Emails'}
                                                    <div class='font-x-small updateInfoContainer-show-more'>
                                                        <a class="show-more-fields" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogMoreField(this);">{vtranslate('LBL_SHOW_MORE_FIELD', $MODULE_NAME)}</a>
                                                    </div>
                                                    <div class='font-x-small updateInfoContainer-show-less hide'>
                                                        <a class="show-less-fields" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogLessField(this);">{vtranslate('LBL_SHOW_LESS_FIELD', $MODULE_NAME)}</a>
                                                    </div>
                                                {elseif $EMAILS_DESCRIPTION_FIELD_MODEL && !$MODULE_MODEL->checkLimitCharacter(decode_html($EMAILS_DESCRIPTION_FIELD_MODEL->get('postvalue')), $LIMIT_CHARACTER)}
                                                    <div class='font-x-small updateInfoContainer-show-more' data-fullcontent="{Vtiger_Util_Helper::toVtiger6SafeHTML($EMAILS_DESCRIPTION_FIELD_MODEL->get('postvalue'))}">
                                                        <a class="show-more-character" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogMoreEmailCharacter(this);">{vtranslate('LBL_SHOW_MORE_CHARACTER', $MODULE_NAME)}</a>
                                                    </div>
                                                    <div class='font-x-small updateInfoContainer-show-less hide' data-lesscontent="{Vtiger_Util_Helper::toVtiger6SafeHTML($MODULE_MODEL->truncate($EMAILS_DESCRIPTION_FIELD_MODEL->get('postvalue'), $LIMIT_CHARACTER))}">
                                                        <a class="show-more-character" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogLessEmailCharacter(this);">{vtranslate('LBL_SHOW_LESS_CHARACTER', $MODULE_NAME)}</a>
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            {elseif $RECENT_ACTIVITY->isUpdate()}
                                <li>
                                    <time class="update_time cursorDefault">
                                        {assign var=CREATED_DATE_TIME value= Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RECENT_ACTIVITY->getActivityTime())}
                                        {assign var=CREATED_DATE_TIME_ARR value=' '|explode:$CREATED_DATE_TIME}
                                        <small title="{$CREATED_DATE_TIME_ARR[1]} {$CREATED_DATE_TIME_ARR[2]}" class="module-qtip">
                                            {$CREATED_DATE_TIME_ARR[0]}
                                        </small>
                                    </time>
                                    <div class="update_icon bg-info bg-info-{$PARENT_MODULE_NAME_LOWER}">
                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon($RECENT_ACTIVITY->get('parent_modulename'))}" alt="{vtranslate($RECENT_ACTIVITY->get('parent_modulename'), $RECENT_ACTIVITY->get('parent_modulename'))}" title="{vtranslate($RECENT_ACTIVITY->get('parent_modulename'), $RECENT_ACTIVITY->get('parent_modulename'))}">
                                    </div>
                                    <div class="update_info">
                                        <div class="update_info_block">
                                            <div class="update_info_block_header">
                                                <i class="fa-caret-left" aria-hidden="true"></i>
                                                <span class="update_icon bg-info vte-bg-info-small bg-info-{$MODULE_NAME_LOWER}">
                                                    {if $MODULE_NAME_LOWER eq 'modcomments'}
                                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon()}" alt="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}" title="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}">
                                                    {else}
                                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon()}" alt="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}" title="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}">
                                                    {/if}
                                                </span>
                                                {if $RECENT_ACTIVITY->get('module') neq 'ModComments'}
                                                    <a class="vte-history-log-title" href="javascript:Vtiger_VTEAHistoryLog_Js.showDetailOverlay('index.php?module={$RECENT_ACTIVITY->get('module')}&view=Detail&record={$RECENT_ACTIVITY->get('crmid')}')" target="_blank">{$RECENT_ACTIVITY->get('label')}</a>
                                                {else}
                                                    <a class="vte-history-log-title" href="javascript:void(0);" target="_blank">{$RECENT_ACTIVITY->get('label')}</a>
                                                {/if}
                                                <i class="vte-history-log-type">&nbsp;-&nbsp;{vtranslate('SINGLE_'|cat:$RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}&nbsp;{vtranslate('LBL_UPDATED', $MODULE_NAME)}</i>
                                                <span class="pull-right">{$RECENT_ACTIVITY->getModifiedBy()->getName()}</span>
                                            </div>
                                            <div class="update_info_block_content">
                                                {assign var=COUNT_FIELD value=1}
                                                {foreach item=FIELDMODEL from=$RECENT_ACTIVITY->getFieldInstances()}
                                                    <div class='font-x-small updateInfoContainer {if $COUNT_FIELD gt $LIMIT_FIELD} hide{/if}'>
                                                        <div class='update-name'>
                                                            <span class="field-name">{vtranslate($FIELDMODEL->getName(),$MODULE_NAME)}:&nbsp;</span>
                                                        </div>
                                                        <div class="update-to">
                                                            {if $FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELDMODEL->get('postvalue') eq '0'}
                                                                <del>{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('prevalue'))))}</del>&nbsp;
                                                                <span class="update-from">
                                                                    <em style="white-space:pre-line;">
                                                                        (<span class="from_field_value">{vtranslate('LBL_IS_REMOVED')}</span>)
                                                                    </em>
                                                                </span>
                                                            {elseif $FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'reference' && $FIELDMODEL->get('postvalue') neq '0' && $FIELDMODEL->get('prevalue') eq '0'}
                                                                {Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('postvalue'))))}&nbsp;
                                                                <span class="update-from">
                                                                    <em style="white-space:pre-line;">
                                                                        (<span class="from_field_value">{vtranslate('LBL_LINKED', 'VTEHistoryLog')}</span>)
                                                                    </em>
                                                                </span>
                                                            {else}
                                                                {if $FIELDMODEL->get('postvalue') eq ''}
                                                                    -----
                                                                {else}
                                                                    {Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('postvalue'))))}&nbsp;
                                                                {/if}

                                                                {if $FIELDMODEL->get('prevalue') neq '' && !($FIELDMODEL->getFieldInstance()->getFieldDataType() eq 'currency' && $FIELDMODEL->get('prevalue') neq '0')}
                                                                    <span class="update-from">
                                                                    <em style="white-space:pre-line;">
                                                                        &nbsp;(<span class="lbl_from_value">{vtranslate('LBL_FROM', $MODULE_NAME)}&nbsp;</span>
                                                                        <span class="from_field_value">{Vtiger_Util_Helper::toVtiger6SafeHTML($FIELDMODEL->getDisplayValue(decode_html($FIELDMODEL->get('prevalue'))))}</span>)
                                                                    </em>
                                                                </span>
                                                                {/if}
                                                            {/if}
                                                        </div>
                                                    </div>
                                                    {assign var=COUNT_FIELD value=$COUNT_FIELD+1}
                                                {/foreach}
                                                {if $COUNT_FIELD gt 6}
                                                    <div class='font-x-small updateInfoContainer updateInfoContainer-show-more'>
                                                        <a class="show-more-fields" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogMoreField(this);">{vtranslate('LBL_SHOW_MORE_FIELD', $MODULE_NAME)}</a>
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            {elseif ($RECENT_ACTIVITY->isRelationLink() || $RECENT_ACTIVITY->isRelationUnLink())}
                                {assign var=RELATED_MODULE value= $RELATION->getLinkedRecord()->getModuleName()}
                                {if $RELATED_MODULE eq 'Calendar'}
                                    {if isPermitted('Calendar', 'DetailView', $RELATION->getLinkedRecord()->getId()) eq 'yes'}
                                        {assign var=PERMITTED value=1}
                                    {else}
                                        {assign var=PERMITTED value=0}
                                    {/if}
                                {else}
                                    {assign var=PERMITTED value=1}
                                {/if}
                                <li>
                                    <time class="update_time cursorDefault">
                                        {assign var=CREATED_DATE_TIME value= Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($RELATION->get('changedon'))}
                                        {assign var=CREATED_DATE_TIME_ARR value=' '|explode:$CREATED_DATE_TIME}
                                        <small title="{$CREATED_DATE_TIME_ARR[1]} {$CREATED_DATE_TIME_ARR[2]}" class="module-qtip">
                                            {$CREATED_DATE_TIME_ARR[0]}
                                        </small>
                                    </time>
                                    <div class="update_icon bg-info bg-info-{$RELATION->getParent()->get('module')|strtolower}">
                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon($RELATION->getParent()->get('module'))}" alt="{vtranslate($RELATION->getParent()->get('module'), $RELATION->getParent()->get('module'))}" title="{vtranslate($RELATION->getParent()->get('module'), $RELATION->getParent()->get('module'))}">
                                    </div>

                                    <div class="update_info">
                                        <div class="update_info_block">
                                            <div class="update_info_block_header">
                                                <i class="fa fa-caret-left" aria-hidden="true"></i>
                                                <span class="update_icon bg-info bg-info-{$RELATED_MODULE|strtolower} vte-bg-info-small">
                                                    {if $RELATED_MODULE eq 'ModComments'}
                                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon()}" alt="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}" title="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}">
                                                    {else}
                                                        <img class="alignMiddle module-qtip update_image" src="{$RECENT_ACTIVITY->getIcon()}" alt="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}" title="{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}">
                                                    {/if}
                                                </span>
                                                {if $RELATED_MODULE eq 'ModComments'}
                                                    <a class="vte-history-log-title" href="javascript:void(0);">{vtranslate($RELATED_MODULE, $RELATED_MODULE)}</a>
                                                {else}
                                                    {if $RELATED_MODULE eq 'Emails'}
                                                        <a class="vte-history-log-title" href="javascript:Vtiger_VTEAHistoryLog_Js.showComposeEmail('{$RELATION->getLinkedRecord()->getId()}')" target="_blank">{$RELATION->getLinkedRecord()->getName()}</a>
                                                    {else}
                                                        <a class="vte-history-log-title" href="javascript:Vtiger_VTEAHistoryLog_Js.showDetailOverlay('index.php?module={$RELATED_MODULE}&view=Detail&record={$RELATION->getLinkedRecord()->getId()}')" target="_blank">{$RELATION->getLinkedRecord()->getName()}</a>
                                                    {/if}
                                                {/if}
                                                {if $RELATED_MODULE eq 'ModComments'}
                                                    <i class="vte-history-log-type">&nbsp;-&nbsp;{vtranslate('SINGLE_'|cat:$RELATED_MODULE, $RELATED_MODULE)}&nbsp;{vtranslate('LBL_CREATED', $MODULE_NAME)}</i>
                                                {else}
                                                    <i class="vte-history-log-type">&nbsp;-&nbsp;{vtranslate('SINGLE_'|cat:$RELATED_MODULE, $RELATED_MODULE)}&nbsp;{if $RECENT_ACTIVITY->isRelationLink()}{vtranslate('LBL_LINKED', $MODULE_NAME)}{else}{vtranslate('LBL_UNLINKED', $MODULE_NAME)}{/if}</i>
                                                {/if}
                                                <span class="pull-right">{$RECENT_ACTIVITY->getModifiedBy()->getName()}</span>
                                            </div>

                                            {if $RELATED_MODULE eq 'ModComments' && $PERMITTED}
                                                <div class="update_info_block_content" data-fullcontent="{Vtiger_Util_Helper::toVtiger6SafeHTML($RELATION->getLinkedRecord()->get('commentcontent'))}" data-lesscontent="{Vtiger_Util_Helper::toVtiger6SafeHTML($MODULE_MODEL->truncate($RELATION->getLinkedRecord()->get('commentcontent'), $LIMIT_CHARACTER))}">

                                                    <div class='font-x-small updateInfoContainer updateInfoContainer-full-content'>
                                                        {$MODULE_MODEL->truncate($RELATION->getLinkedRecord()->get('commentcontent'), $LIMIT_CHARACTER)}
                                                    </div>

                                                    {if !$MODULE_MODEL->checkLimitCharacter($RELATION->getLinkedRecord()->get('commentcontent'), $LIMIT_CHARACTER)}
                                                        <div class='font-x-small updateInfoContainer-show-more'>
                                                            <a class="show-more-character" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogMoreCharacter(this);">{vtranslate('LBL_SHOW_MORE_CHARACTER', $MODULE_NAME)}</a>
                                                        </div>
                                                        <div class='font-x-small updateInfoContainer-show-less hide'>
                                                            <a class="show-more-character" href="javascript:void(0);" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogLessCharacter(this);">{vtranslate('LBL_SHOW_LESS_CHARACTER', $MODULE_NAME)}</a>
                                                        </div>
                                                    {/if}
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                </li>
                            {elseif $RECENT_ACTIVITY->isRestore()}
                            {/if}
                        {/if}
                    {/foreach}
                    {if $PAGING_MODEL->isNextPageExists()}
                        <li id='more_button'>
                            <div class='update_icon' id="moreLink">
                                <button type="button" class="btn btn-success moreRecentUpdatesVTE" onclick="Vtiger_VTEAHistoryLog_Js.showHistoryLogMore(this);">{vtranslate('LBL_MORE',$MODULE_NAME)}..</button>
                            </div>
                        </li>
                    {/if}
                </ul>
            {else}
                <div class="summaryWidgetContainer">
                    <p class="textAlignCenter">{vtranslate('LBL_NO_RECENT_UPDATES')}</p>
                </div>
            {/if}
        </div>
    </div>
{/strip}
