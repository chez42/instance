{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
    <div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop">
        <div class="module-action-content clearfix {$MODULE}-module-action-content">
            <div class="col-lg-7 col-md-7 module-breadcrumb module-breadcrumb-{$smarty.request.view} transitionsAllHalfSecond">
                {assign var=MODULE_MODEL value=Vtiger_Module_Model::getInstance($MODULE)}
                {assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getDefaultUrl()}
                <a title="{vtranslate($MODULE, $MODULE)}" href='{$DEFAULT_FILTER_URL}&app={$SELECTED_MENU_CATEGORY}'><h4 class="module-title pull-left text-uppercase"> {vtranslate($MODULE, $MODULE)} </h4>&nbsp;&nbsp;</a>
            </div>
            {if $smarty.request.view eq 'List'}
                <div class="col-lg-5 col-md-5 pull-right">
                    <div id="appnav" class="navbar-right">
                    <span class="btn-group">
                        <button class="btn btn-default dropdown-toggle module-buttons" data-toggle="dropdown" id="Reports_listView_basicAction_Add" aria-expanded="true" style="width: 158px;">
                            <i class="fa fa-plus"></i>&nbsp;Add Report&nbsp;<i class="caret icon-white"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li id="VTEReports_listView_basicAction_tabular_report">
                                <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=tabular">Tabular Report</a>
                            </li>
                            <li id="VTEReports_listView_basicAction_summaries_report">
                                <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=summaries">Summaries Report</a>
                            </li>
                            <li id="VTEReports_listView_basicAction_summaries_matrix_report">
                                <a href="index.php?module=VTEReports&view=Edit&folder=&reporttype=summaries_matrix">Matrix Report</a>
                            </li>
                        </ul>
                    </span>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/strip}
