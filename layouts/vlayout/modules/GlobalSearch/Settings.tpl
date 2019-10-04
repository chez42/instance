{*<!--
/* ********************************************************************************
* The content of this file is subject to the Global Search ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('GlobalSearch', 'GlobalSearch')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer" id="global_search_settings">
        <ul class="nav nav-tabs massEditTabs">
            <li class="active">
                <a href="#fields" data-toggle="tab">
                    <strong>
                        {vtranslate('LBL_FIELDS', 'GlobalSearch')}
                    </strong>
                </a>
            </li>
            <li>
                <a href="#arrangeModules" data-toggle="tab" >
                    <strong>
                        {vtranslate('LBL_ARRANGE_MODULES', 'GlobalSearch')}
                    </strong>
                </a>
            </li>
        </ul>
        <div class="tab-content massEditContent">
            <div class="tab-pane active" id="fields">
                <div class="row-fluid">
                    <div class="select-search" style="margin-left:100px;margin-top:15px;">
                        <select class="chzn-select" id="search_module" name="search_module" style="width: 300px;">
                            <option value="">{vtranslate('LBL_SELECT_MODULE', 'GlobalSearch')}</option>
                            {foreach from=$ALL_MODULE item=MODULE}
                                <option value="{$MODULE}">{vtranslate($MODULE, $MODULE)}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <br/>
                <div id="selectedFields">

                </div>
            </div>
            <div class="tab-pane" id="arrangeModules" style="">
                <div class="row-fluid">
                    <div style="margin-left:100px;margin-top:15px;">
                        <span class="span4">
                            <ul class="searchModulesList" style="list-style: outside none none;">
                                {foreach from=$SEARCH_MODULES key=MODULE item=SEQUENCE}
                                    <li class="searchModule contentsBackground" style="width: 200px; padding: 5px;" data-module="{$MODULE}" data-sequence="{$SEQUENCE}">
                                        <a><img src="{vimage_path('drag.png')}" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>&nbsp;&nbsp;
                                        <span class="moduleLabel">{vtranslate($MODULE, $MODULE)}</span>
                                    </li>
                                {/foreach}
                            </ul>
                        </span>
                        <span class="span5" style="padding: 5% 0;">
                            <i class="icon-info-sign alignMiddle"></i>
                            {vtranslate('LBL_DRAG_DROP_ORDER', 'GlobalSearch')}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>