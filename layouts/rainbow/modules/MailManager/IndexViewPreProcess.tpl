{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{include file="modules/Vtiger/partials/Topbar.tpl"|myclayout_path}

<div class="container-fluid app-nav">
    <div class="row">
       {include file="modules/MailManager/partials/SidebarHeader.tpl"|myclayout_path}
       {include file="modules/Vtiger/ModuleHeader.tpl"|myclayout_path}
    </div>
</div>
</nav>
    <div id='overlayPageContent' class='fade modal overlayPageContent content-area overlay-container-60' tabindex='-1' role='dialog' aria-hidden='true'>
        <div class="data">
        </div>
        <div class="modal-dialog">
        </div>
    </div>
<div class="main-container main-container-{$MODULE}">
   <!-- {assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
<div id="modnavigator" class="module-nav">
    <div class="hidden-xs hidden-sm mod-switcher-container">
        {include file="partials/Menubar.tpl"|vtemplate_path:$MODULE}
    </div>
</div>-->




<div id="sidebar-essentials" class="sidebar-essentials {if $LEFTPANELHIDE eq '1'} hide {/if}">
                <!--svv-->{include file="partials/FoldersSidebar.tpl"|vtemplate_path:$MODULE}


    <!--svv-->
        </div>

<div class="listViewPageDiv content-area {if $LEFTPANELHIDE eq '1'} full-width {/if}" id="listViewContent">

{if $MODULE neq 'EmailTemplates' && $SEARCH_MODE_RESULTS neq true}
        {assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
        <div class="essentials-toggle" style="    left: 230px;
    z-index: 999;" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
            <span class="essentials-toggle-marker fa {if $LEFTPANELHIDE eq '1'}fa-chevron-right{else}fa-chevron-left{/if} cursorPointer"></span>
        </div>
    {/if}