{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($QUALIFIED_MODULE, 'VTEForecast')}</h3>
    </div>
    <hr>
    <div class="row-fluid">
        <div class="span12 btn-toolbar">
		<ul class="nav nav-tabs">
		  <li role="panelHierarchy" {if ($TAB eq 'hierarchy')}class="active"{/if} ><a href="javascript:void(0)">{vtranslate('Forecast Hierarchy', 'VTEForecast')}</a></li>
		  <li role="panelConfiguration" {if ($TAB eq 'configuration')}class="active"{/if}><a href="javascript:void(0)">{vtranslate('FY Configuration', 'VTEForecast')}</a></li>
          <li role="panelSaleStage" {if ($TAB eq 'salestage')}class="active"{/if}><a href="javascript:void(0)">{vtranslate('Sales Stage Mapping', 'VTEForecast')}</a></li>
          <li role="panelOpportunityType" {if ($TAB eq 'opportunityyype')}class="active"{/if}><a href="javascript:void(0)">{vtranslate('Opportunity Type Mapping', 'VTEForecast')}</a></li>
          <li role="panelTarget" {if ($TAB eq 'target')}class="active"{/if}><a href="javascript:void(0)">{vtranslate('Targets', 'VTEForecast')}</a></li>
		</ul>           
		<a class="btn btn-warning" style="position: absolute;top:75px;right:20px;" href="index.php?module=VTEForecast&amp;view=List">{vtranslate('View Forecast', 'VTEForecast')}</a>
        </div> 
		
    </div>
    <div class="panelHierarchy" id="panelHierarchy" style="display:{if ($TAB eq 'hierarchy')}block{else}none{/if};">
        {include file='SettingHierarchy.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
	<div class="panelConfiguration" id="panelConfiguration" style="display:{if ($TAB eq 'configuration')}block{else}none{/if};">
		{include file='SettingConfiguration.tpl'|@vtemplate_path:$QUALIFIED_MODULE}      
    </div>
    <div class="panelSaleStage" id="panelSaleStage" style="display:{if ($TAB eq 'salestage')}block{else}none{/if};">
        {include file='SettingSaleStage.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
    <div class="panelOpportunityType" id="panelOpportunityType" style="display:{if ($TAB eq 'opportunityyype')}block{else}none{/if};">
        {include file='SettingOpportunityType.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
    <div class="panelTarget" id="panelTarget" style="display:{if ($TAB eq 'target')}block{else}none{/if};">
        {include file='SettingTarget.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
</div>

<style type="text/css">
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
    div.treeView a.btnRemove, div.treeView a.btnDelete{
        margin: 0;
        padding:0;
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


    .btn-inverse:hover{
        border: 4px solid {$THEME_COLOR->shadeColor(50)};
        background-color: {$THEME_COLOR->baseColor};
    }
    .btn-inverse:active, .btn-inverse.active, .btn-inverse.disabled, .btn-inverse[disabled]{
        border: 4px solid {$THEME_COLOR->shadeColor(50)};
        background-color: {$THEME_COLOR->baseColor};
    }
    .treeView li .toolbar-handle .btn.active{
        border: 4px solid {$THEME_COLOR->shadeColor(50)};
    }

    tr.sub0 td:first-child,tr.sub0 th:first-child{
        background-color: {$THEME_COLOR->shadeColor(-20)};
        background-image: none;
        color: #FFF;
    }
    tr.sub0 th:first-child{
        border-color: {$THEME_COLOR->shadeColor(-20)};
    }
    .table tbody tr.sub0:hover td:first-child, .table tbody tr.sub0:hover th:first-child{
        background-color:{$THEME_COLOR->shadeColor(-20)};
    }
    tr.sub1 td:first-child,tr.sub1 th:first-child{
        background-color: {$THEME_COLOR->shadeColor(0)};
        background-image: none;
        color: #FFF;
    }
    tr.sub1 th:first-child{
        border-color: {$THEME_COLOR->shadeColor(0)};
    }
    .table tbody tr.sub1:hover td:first-child, .table tbody tr.sub1:hover th:first-child{
        background-color:{$THEME_COLOR->shadeColor(0)};
    }
    tr.sub2 td:first-child, tr.sub2 th:first-child{
        background-color:{$THEME_COLOR->shadeColor(30)};
        color: #FFF;
        background-image: none;
    }
    tr.sub2 th:first-child{
        border-color: {$THEME_COLOR->shadeColor(30)};
    }
    .table tbody tr.sub2:hover td:first-child, tr.sub2:hover th:first-child{
        background-color:{$THEME_COLOR->shadeColor(30)};
    }
    tr.sub3 td:first-child, tr.sub3 th:first-child{
        background-color:{$THEME_COLOR->shadeColor(50)};
        background-image: none;
        color: #000;
    }
    tr.sub3 th:first-child{
        border-color: {$THEME_COLOR->shadeColor(50)};
    }
    .table tbody tr.sub3:hover td:first-child, .table tbody tr.sub3:hover th:first-child{
        background-color:{$THEME_COLOR->shadeColor(50)};
    }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{
        border: 1px solid #ddd;
        border-bottom: none;
    }
</style>

