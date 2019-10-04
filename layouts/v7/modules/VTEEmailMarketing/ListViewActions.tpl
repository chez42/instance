{*<!--
/*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
-->*}
{strip}
    {assign var=LISTVIEW_MASSACTIONS_1 value=array()}
    <div id="listview-actions" class="listview-actions-container">
        {foreach item=LIST_MASSACTION from=$LISTVIEW_MASSACTIONS name=massActions}
            {if $LIST_MASSACTION->getLabel() eq 'LBL_EDIT'}
                {assign var=editAction value=$LIST_MASSACTION}
            {else if $LIST_MASSACTION->getLabel() eq 'LBL_DELETE'}
                {assign var=deleteAction value=$LIST_MASSACTION}
            {else if $LIST_MASSACTION->getLabel() eq 'LBL_ADD_COMMENT'}
                {assign var=commentAction value=$LIST_MASSACTION}
            {else}
                {$a = array_push($LISTVIEW_MASSACTIONS_1, $LIST_MASSACTION)}
                {* $a is added as its print the index of the array, need to find a way around it *}
            {/if}
        {/foreach}

        <div class = "row">
            <div class="btn-toolbar col-md-2" role="group" aria-label="...">
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-default viewType" title="{vtranslate('LBL_LIST_VIEW',$MODULE)}" data-mode="list" {if $VIEWTYPE eq 'list'} disabled="disabled" {/if}><i class="fa fa-th-list"></i></button>
                        <button type="button" class="btn btn-default viewType" title="{vtranslate('LBL_THUMBNAIL_VIEW',$MODULE)}" data-mode="grid" {if $VIEWTYPE eq 'grid'} disabled="disabled" {/if}><i class="fa fa-th-large"></i></button>
                        <button type="button" class="btn btn-default editVTEEmailTemplate" title="{vtranslate('Edit Template',$MODULE)}" data-mode="edit" {if $VIEWTYPE eq 'grid'} disabled="disabled" {/if}><i class="fa fa-pencil"></i></button>
                    </div>
            </div>
            <div class='col-md-3'>
                    <div style="max-width: 215px;" class="input-group">
                        <input class="inputElement search_value" style="padding-left:10px;" name="search_value_pre" type="search" placeholder="Search for template name" >
                        <span class="search-template input-group-addon cursorPointer" title="Select"><i class="fa fa-search"></i></span>
                    </div>
            </div>
            <div class='col-md-3'>
                    <span style="display: none" class="custom-content-tooltip">
                        New browser tab will open to design email template. Once you create new email template, you can close Email template tab and come back to this tab.
                        <br><br>
                        <b><u>IMPORTANT: To select newly created template, click "REFRESH" button (on the right) and the template will show up below.</u></b>
                    </span>
                    <a class="custom-tooltip glyphicon glyphicon-info-sign">
                        <span style="display: none" class="arrow-down-custom"></span>
                    </a>
                    <a target="_blank" href="index.php?module=VTEEmailMarketing&view=EditEmailTemplates&app=MARKETING">
                        <button id="EmailTemplates_listView_basicAction_addRecord" type="button"
                                class="btn addButton btn-default">
                            <div class="fa fa-plus" aria-hidden="true"></div>
                            &nbsp;&nbsp;Create New Email Template
                        </button>
                    </a>
            </div>
            <div class="col-md-1">
                    <button id="EmailTemplates_listView_basicAction_refresh" type="button" class="btn addButton btn-default">
                        <div class="fa fa-refresh" aria-hidden="true"></div>
                        &nbsp;&nbsp;Refresh
                    </button>
            </div>
            <div class="col-md-3 pull-right">
                {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
                {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
            </div>
        </div>	
     </div>
{/strip}
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
        $('.custom-tooltip').hover(
            function() {
                $('.custom-content-tooltip').show();
                $('.arrow-down-custom').show();
            }, function() {
                $('.custom-content-tooltip').hide();
                $('.arrow-down-custom').hide();
            }
        );
    });
</script>