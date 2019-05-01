    {*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="main-container main-container-{$MODULE}" style="width: 98%;">
		{assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
		<div id="modnavigator" class="module-nav">
			<div class="hidden-xs hidden-sm mod-switcher-container">
				{include file="partials/Menubar.tpl"|vtemplate_path:Vtiger}
			</div>
		</div>
		<div class="listViewPageDiv content-area full-width" id="listViewContent">
            <div class="widget_header row">
                <div class="col-lg-4">
                    <h3>{vtranslate($MODULE_NAME, $MODULE_NAME)}</h3>
                </div>
                <div class="col-lg-8">
                    <h3 class="pull-right">
                         <a class="btn btn-warning" href="index.php?module=VTEForecast&parent=Settings&view=Settings">
                            <i class="glyphicon glyphicon-cog"></i>&nbsp;<strong>{vtranslate('Forecast Settings')} </strong>
                        </a>
                    </h3>
                </div>	
            </div>
            <hr/>    
            <div class="row">
                <div class="col-lg-3" id="leftPanelF">
                    <div class="clearfix treeView">
                        <ul>
                            <li data-node="{$ROOT_RECORD->getParentString()}" data-nodeid="{$ROOT_RECORD->getId()}">
                                {assign var="RECORD" value=$CURRENT_RECORD}
                                {if $SHOW_OTHER_USERS==0}
                                    <li data-role="{$CURRENT_RECORD->getParentString()}" data-roleid="{$CURRENT_RECORD->getId()}">
                                        <div class="toolbar-handle">
                                            {if $CURRENT_RECORD->getImageName()}
                                                <img src="{$CURRENT_RECORD->getPath()}{$CURRENT_RECORD->getImageName()}" class="avar-tree-forecast" />
                                            {else}
                                                <img src="layouts/vlayout/modules/VTEForecast/resources/icon-avar-tree.png" class="avar-tree-forecast" />
                                            {/if}
                                            <a href="#"  class="btn roleEle userNode sub{$CURRENT_RECORD->getDepth()}" rel="tooltip" data-nodeid="{$CURRENT_RECORD->getId()}" data-nodename="{$CURRENT_RECORD->getName()}">{$CURRENT_RECORD->getName()}</a>
                                        </div>
                                    </li>
                                {else}
                                    <div class="toolbar-handle">
                                        {if $ROOT_RECORD->getRootImageName()}
                                            <img src="{$ROOT_RECORD->getPath()}{$ROOT_RECORD->getRootImageName()}" class="avar-tree-forecast" />
                                        {else}
                                            <img src="layouts/vlayout/modules/VTEForecast/resources/icon-avar-logo.png" class="avar-tree-forecast" />
                                        {/if}
                                        <a href="javascript:;" class="btn btn-inverse userNode" data-nodeid="{$ROOT_RECORD->getId()}" data-nodename="{$ROOT_RECORD->getName()}">{$ROOT_RECORD->getName()}</a>
                                    </div>
                                {/if}
                                {include file=vtemplate_path("RecordTreeViewForecast.tpl", $MODULE_NAME)}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="marginLeftZero col-lg-9" id="rightPanelF">
                    <div id="toggleButtonF" class="toggleButton" title="Left panel show/hide">
                        <i id="tButtonImageF" class="glyphicon glyphicon-chevron-left"></i>
                    </div>
                    {include file=vtemplate_path("ViewForecastReport.tpl", $MODULE_NAME)}
                </div>
            </div>
            <input type="hidden" name="user_id" id="user_id" value="{if $SHOW_OTHER_USERS==0}{$CURRENT_USER_ID}{else}0{/if}" />
            <input type="hidden" name="forecast_period" id="forecast_period" value="{$PERIOD}" />
        </div>
    </div>
</div>


<style type="text/css">
    .treeView ul {
        padding: 0;
        margin: 0 0 9px 25px;
        margin-top: 0px;
    }
    tspan{
        font-weight: normal !important;
        font-size: 14px;
    }
    #rightPanelF{
        border-left: 1px solid #e6e6e6;
        position: relative;
    }

    #leftPanelF {
        background-color: #eeeff2;
        background-position: right top;; background-image: url('layouts/vlayout/modules/VTEForecast/resources/bgleft.jpg'); background-repeat: no-repeat; background-attachment: scroll;
        padding-right: 15px;
    }
    
    .toggleButton {
        background: #eeeff2;
        font-weight: bold;
        padding: 5px 2px !important;
        position: absolute;
        top: 20px;
        left: -1px;
        cursor: pointer;
        width: 15px;
        z-index: 100;
        border: 1px solid #dddddd;
        border-left: 0;
        display: inline-block;
    }

    .treeView >ul{
        margin-left: 0px;
    }
    .treeView li{
        border-left: 4px solid {$THEME_COLOR->shadeColor(-20)};
    }
    .treeView ul li ul{
        margin-left: 33px;
    }
    .treeView li .toolbar-handle a:before{
        width: 20px;
        top:14px;
        background: none repeat scroll 0 0 {$THEME_COLOR->shadeColor(-20)};
    }
    .treeView li .toolbar-handle .btn{
        margin-left: 10px;
    }

    div.treeView a{
        border-radius: 15px !important;
        padding: 6px 10px 6px;
        font-size:12px;
    }

    .avar-tree-forecast{
        position: absolute;
        left: -27px;
        width: 30px;
        height: 30px;
        border-radius: 15px;
        z-index: 200;
        background-color: #CCC;
    }
    .btn-inverse{
        background-color: {$THEME_COLOR->baseColor};
        border: 4px solid {$THEME_COLOR->shadeColor(-20)};
    }

    .treeView li .toolbar-handle .btn{
        color: #FFF;
        text-shadow: none;
        box-shadow: none;
        border: 4px solid {$THEME_COLOR->shadeColor(-20)};
    }
    div.treeView a.sub1{
        background-color:{$THEME_COLOR->shadeColor(10)};

    }
    div.treeView a.sub2{
        background-color:{$THEME_COLOR->shadeColor(15)};

    }
    div.treeView a.sub3{
        background-color:{$THEME_COLOR->shadeColor(20)};
    }
    div.treeView a.sub4{
        background-color:{$THEME_COLOR->shadeColor(25)};
    }
    div.treeView a.sub5{
        background-color:{$THEME_COLOR->shadeColor(30)};
    }
    div.treeView a.sub6{
        background-color:{$THEME_COLOR->shadeColor(35)};
    }
    div.treeView a.sub7{
        background-color:{$THEME_COLOR->shadeColor(40)};
    }
    div.treeView a.sub8{
        background-color:{$THEME_COLOR->shadeColor(40)};
    }
    div.treeView a.sub9{
        background-color:{$THEME_COLOR->shadeColor(40)};
    }
    div.treeView a.sub10{
        background-color:{$THEME_COLOR->shadeColor(40)};
    }

    .treeView li .toolbar-handle .btn.sub3{
        color: #000;
    }
    .btn-inverse:hover{
        border: 4px solid {$THEME_COLOR->shadeColor(50)};
        background-color: {$THEME_COLOR->baseColor};
    }
    .btn-inverse:active, .btn-inverse.active, .btn-inverse.disabled, .btn-inverse[disabled]{
        border: 4px solid red;//{$THEME_COLOR->shadeColor(50)};
        background-color: {$THEME_COLOR->baseColor};
    }
    .treeView li .toolbar-handle .btn.active{
        border: 4px solid red;//{$THEME_COLOR->shadeColor(50)};
    }

    tr.sub0 td:first-child{
        background-image: none;
        position: relative;
        padding: 0;
    }

    .table tbody tr.sub0:hover td:first-child{
        background-color:transparent;
    }
    tr.sub1 td:first-child{
        background-image: none;
        position: relative;
        padding: 0;
    }

    .table tbody tr.sub1:hover td:first-child{
        background-color:transparent;
    }
    tr.sub2 td:first-child{
        background-image: none;
        position: relative;
        padding: 0;
    }

    .table tbody tr.sub2:hover td:first-child{
        background-color:transparent;
    }
    tr.sub3 td:first-child{
        background-image: none;
        position: relative;
        padding: 0;
    }
    .table tbody tr.sub3:hover td:first-child{
        background-color:transparent;
    }
    span.spanExpand {
        float:right;
        cursor:pointer;
        width:41px;
        height:41px;
        text-indent:-999px;
        position: absolute;
        bottom: 0px;
        right: 0px;
        display: block;
        visibility: visible;
    }
    .table td{
        white-space:nowrap;
        overflow: hidden;
    }
    span.expand{
        background:url('layouts/vlayout/modules/VTEForecast/resources/acc_opened.png') no-repeat;
    }
    span.collapse{
        background:url('layouts/vlayout/modules/VTEForecast/resources/acc_closed.png') no-repeat;
    }
    .show-detail-dash{
        background:url('layouts/vlayout/modules/VTEForecast/resources/drd_detail_dash_ex.png') no-repeat 0px 2px;
        float: left;
        padding-right: 5px;
        text-indent: -9999px;
        text-decoration: none;
        background-size: auto 15px;
        width:26px;
    }
    .show-detail-dash:hover{
        opacity: 0.8;
    }
    .show-detail-dash.expand{
        background:url('layouts/vlayout/modules/VTEForecast/resources/drd_detail_dash.png') no-repeat 0px 2px;
        background-size: auto 15px;
    }
    .sale_stage_detail{
        background: #fdffca;
    }
    td div{
        width: 100%;
        float: left;
        color: #FFF;
        white-space:normal;
        padding: 10px;
    }
    div.sp0{
        margin-left: 0px;
        background-color: {$THEME_COLOR->shadeColor(-20)};
    }
    div.sp1{
        margin-left: 10px;
        background-color: {$THEME_COLOR->shadeColor(0)};
    }
    div.sp2{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(5)};
    }
    div.sp3{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(10)};
    }
    div.sp4{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(15)};
    }
    div.sp5{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(20)};
    }
    div.sp6{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(25)};
    }
    div.sp7{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(30)};
    }
    div.sp8{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(35)};
    }
    div.sp9{
        margin-left: 20px;
        background-color:{$THEME_COLOR->shadeColor(40)};
    }
    div.sp10{
        margin-left: 30px;
        background-color:{$THEME_COLOR->shadeColor(50)};
        color:#000;
    }
    div.sp11{
        margin-left: 30px;
        background-color:{$THEME_COLOR->shadeColor(50)};
        color:#000;
    }
    div.sp12{
        margin-left: 30px;
        background-color:{$THEME_COLOR->shadeColor(50)};
        color:#000;
    }
    div.sp13{
        margin-left: 30px;
        background-color:{$THEME_COLOR->shadeColor(50)};
        color:#000;
    }
    div.sp14{
        margin-left: 30px;
        background-color:{$THEME_COLOR->shadeColor(50)};
        color:#000;
    }
</style>
{/strip}
{include file="modules/Vtiger/partials/SidebarAppMenu.tpl"}