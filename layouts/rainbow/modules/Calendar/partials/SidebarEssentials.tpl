{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<div class="col-xs-12 text-center visible-xs visible-sm visible-md" style="margin: 20px 0px;">
<div class="btn-group">
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=Calendar">
                    <i class="material-icons">event</i>
                    <span class="">My Calendar</span>
                </a>
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=SharedCalendar">
                    <i class="material-icons">share</i>
                    <span class="hidden-sm hidden-xs">Shared Calendar</span>
                </a>
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=List">
                    <i class="material-icons">list</i>
                    <span class="hidden-sm hidden-xs">List View</span>
                </a>    
</div></div>

<div class="col-xs-12 text-center visible-lg " style="margin: 20px 0px;">
<div class="btn-group">
<a data-toggle="toosltip" tippytitle data-tippy-content="My Calendar" data-placement="top" title="My Calendar" class="btn module-buttons" href="index.php?module=Calendar&amp;view=Calendar">
                    <i class="material-icons">event</i>
                </a>
<a data-toggle="toosltip" tippytitle data-tippy-content="Shared Calendar" data-placement="top" title="Shared Calendar" class="btn module-buttons" href="index.php?module=Calendar&amp;view=SharedCalendar">
                    <i class="material-icons">share</i>
                </a>
<a data-toggle="toosltip" tippytitle data-tippy-content="Calendar List" data-placement="top" title="Calendar List" class="btn module-buttons" href="index.php?module=Calendar&amp;view=List">
                    <i class="material-icons">list</i>
                </a>   

</div></div>

{if $smarty.get.view eq 'Calendar' OR $smarty.get.view eq 'SharedCalendar'}

<div class="sidebar-menu noprint">


    <div class="module-filters" id="module-filters">
        <div class="sidebar-container lists-menu-container">
            {foreach item=SIDEBARWIDGET from=$QUICK_LINKS['SIDEBARWIDGET']}
            {if $SIDEBARWIDGET->get('linklabel') eq 'LBL_ACTIVITY_TYPES' || $SIDEBARWIDGET->get('linklabel') eq 'LBL_ADDED_CALENDARS'}
            <div class="calendar-sidebar-tabs sidebar-widget" id="{$SIDEBARWIDGET->get('linklabel')}-accordion" role="tablist" aria-multiselectable="true" data-widget-name="{$SIDEBARWIDGET->get('linklabel')}">
                <div class="calendar-sidebar-tab">
                    <div class="sidebar-widget-header" role="tab" data-url="{$SIDEBARWIDGET->getUrl()}">
                        <div class="sidebar-header clearfix">
                            {*<i class="ti-angle-right widget-state-indicator"></i>*}
                            <button class="btn btn-info btn-sm btn-block add-calendar-feed">
                                 <i class="material-icons" aria-hidden="true" style="float: left">add</i>
                                {vtranslate($SIDEBARWIDGET->get('linklabel'),$MODULE)}
                            </button> 
                        </div>
                    </div>
                    <hr style="margin: 5px 0;">
                    <div class="list-menu-content">
                        <div id="{$SIDEBARWIDGET->get('linklabel')}" class="sidebar-widget-body activitytypes" style="max-height: 100%;">
                            <div style="text-align:center;"><img src="layouts/v7/skins/images/loading.gif"></div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            {/foreach}    
        </div>
    </div>
</div>
{else}
    {include file="partials/SidebarEssentials.tpl"|vtemplate_path:'Vtiger'}
{/if}