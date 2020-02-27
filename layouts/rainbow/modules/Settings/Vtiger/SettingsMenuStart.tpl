{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Settings/Vtiger/views/Index.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{include file="modules/Vtiger/partials/Topbar.tpl"|myclayout_path}

<div class="container-fluid app-nav">
    <div class="row">
        {include file="modules/Settings/Vtiger/SidebarHeader.tpl"|myclayout_path}
        {include file="modules/Settings/Vtiger/ModuleHeader.tpl"|myclayout_path}
    </div>
</div>
</nav>
 <div id='overlayPageContent' class='fade modal overlayPageContent content-area overlay-container-300' tabindex='-1' role='dialog' aria-hidden='true'>
        <div class="data">
        </div>
        <div class="modal-dialog">
        </div>
    </div>
{if $FIELDS_INFO neq null}
    <script type="text/javascript">
        var uimeta = (function() {
            var fieldInfo  = {$FIELDS_INFO};
            return {
                field: {
                    get: function(name, property) {
                        if(name && property === undefined) {
                            return fieldInfo[name];
                        }
                        if(name && property) {
                            return fieldInfo[name][property]
                        }
                    },
                    isMandatory : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].mandatory;
                        }
                        return false;
                    },
                    getType : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].type
                        }
                        return false;
                    }
                },
            };
        })();
    </script>
{/if}
<div class="main-container clearfix" style="margin-top:10px">
		{assign var=LEFTPANELHIDE value=$USER_MODEL->get('leftpanelhide')}
        <div class="module-nav clearfix settingsNav hidden-sm hidden-xs" id="modnavigator">
            <div class=" height100Per">
                {include file="modules/Settings/Vtiger/Sidebar.tpl"|myclayout_path}
            </div>
        </div>
        
<div id="sidebar-essentials" class="sidebar-essentials visible-xs visible-sm">
        <div class="col-xs-12 text-center visible-xs visible-sm" style="margin-top: 40px;margin-bottom:20px;">
        <a class="btn btn-info" onclick="$('.sidebar-menu-u, .settingsNav').toggleClass('hidden-xs hidden-sm');  $('.settingsNav').find('.settingsgroup').toggleClass('hidden-xs hidden-sm');" style=" width: 100%">Sidebar 
        &nbsp;<span class="toggleButton"><i class="ti-angle-down"></i></span></a>
        </div>
        <br/><div class="clearfix"></div><br/>
        <div class="sidebar-menu-u hidden-xs hidden-sm hide">
			

            <div class="settingsgroup" style="overflow: scroll">
	            <div class="col-xs-12 text-center visible-xs visible-sm" style="margin-top: 40px;margin-bottom:20px; width: 100%">
        <a class="btn btn-info" onclick="$('.sidebar-menu-u, .settingsNav').toggleClass('hidden-xs hidden-sm');  $('.settingsNav').find('.settingsgroup').toggleClass('hidden-xs hidden-sm');">Sidebar 
        &nbsp;<span class="toggleButton"><i class="ti-angle-down"></i></span></a>
        </div>
            <div>
                <input type="text" placeholder="{vtranslate('LBL_SEARCH_FOR_SETTINGS', $QUALIFIED_MODULE)}" class="search-list col-lg-8" id='settingsMenuSearch'>
            </div>
            <br><br>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                {foreach item=BLOCK_MENUS from=$SETTINGS_MENUS}
                    {assign var=BLOCK_NAME value=$BLOCK_MENUS->getLabel()}
                    {assign var=BLOCK_MENU_ITEMS value=$BLOCK_MENUS->getMenuItems()}
                    {assign var=NUM_OF_MENU_ITEMS value= $BLOCK_MENU_ITEMS|@sizeof}
                    {if $NUM_OF_MENU_ITEMS gt 0}
                        <div class="settingsgroup-panel panel panel-default instaSearch">
                            <div id="{$BLOCK_NAME}_accordion" class="app-nav" role="tab">
                                <div class="app-settings-accordion">
                                    <div class="settingsgroup-accordion">
                                        <a data-toggle="collapse" data-parent="#accordion" class='collapsed' href="#{$BLOCK_NAME}">
                                            <i class="indicator {if $ACTIVE_BLOCK['block'] eq $BLOCK_NAME} ti-angle-down {else} ti-angle-right {/if}"></i>
                                            &nbsp;<span>{vtranslate($BLOCK_NAME,$QUALIFIED_MODULE)}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div id="{$BLOCK_NAME}" class="panel-collapse collapse ulBlock {if $ACTIVE_BLOCK['block'] eq $BLOCK_NAME} in {/if}">
                                <ul class="list-group widgetContainer">
                                    {foreach item=MENUITEM from=$BLOCK_MENU_ITEMS}
                                        {assign var=MENU value= $MENUITEM->get('name')}
                                        {assign var=MENU_LABEL value=$MENU}
                                        {if $MENU eq 'LBL_EDIT_FIELDS'}
                                            {assign var=MENU_LABEL value='LBL_MODULE_CUSTOMIZATION'}
                                        {elseif $MENU eq 'LBL_TAX_SETTINGS'}
                                            {assign var=MENU_LABEL value='LBL_TAX_MANAGEMENT'}
                                        {elseif $MENU eq 'INVENTORYTERMSANDCONDITIONS'}
                                            {assign var=MENU_LABEL value='LBL_TERMS_AND_CONDITIONS'}
                                        {/if}

                                        {assign var=MENU_URL value=$MENUITEM->getUrl()}
                                        {assign var=USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
                                        {if $MENU eq 'My Preferences'}
                                            {assign var=MENU_URL value=$USER_MODEL->getPreferenceDetailViewUrl()}
                                        {elseif $MENU eq 'Calendar Settings'}
                                            {assign var=MENU_URL value=$USER_MODEL->getCalendarSettingsDetailViewUrl()}
                                        {/if}
                                        <li>
                                            <a data-name="{$MENU}" href="{$MENU_URL}" class="menuItemLabel {if $ACTIVE_BLOCK['menu'] eq $MENU} settingsgroup-menu-color {/if}">
                                                {vtranslate($MENU_LABEL,$QUALIFIED_MODULE)}
                                                <img id="{$MENUITEM->getId()}_menuItem" data-id="{$MENUITEM->getId()}" class="pinUnpinShortCut cursorPointer pull-right"
                                                     data-actionurl="{$MENUITEM->getPinUnpinActionUrl()}"
                                                     data-pintitle="{vtranslate('LBL_PIN',$QUALIFIED_MODULE)}"
                                                     data-unpintitle="{vtranslate('LBL_UNPIN',$QUALIFIED_MODULE)}"
                                                     data-pinimageurl="{{vimage_path('pin.png')}}"
                                                     data-unpinimageurl="{{vimage_path('unpin.png')}}"
                                                     {if $MENUITEM->isPinned()}
                                                         title="{vtranslate('LBL_UNPIN',$QUALIFIED_MODULE)}" src="{vimage_path('unpin.png')}" data-action="unpin"
                                                     {else}
                                                         title="{vtranslate('LBL_PIN',$QUALIFIED_MODULE)}" src="{vimage_path('pin.png')}" data-action="pin" 
                                                     {/if} />
                                            </a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div></div>

        <div class="settingsPageDiv content-area clearfix">
