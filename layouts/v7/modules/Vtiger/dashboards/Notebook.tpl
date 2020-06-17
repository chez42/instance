{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
<div class="dashboardWidgetHeader">
	{foreach key=index item=cssModel from=$STYLES}
        <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
    {/foreach}
    {foreach key=index item=jsModel from=$SCRIPTS}
        <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
    {/foreach}
        
    <div class="title clearfix">
        <div class="dashboardTitle col-lg-12 pull-left" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}" style="width: 25em;"><b>{vtranslate($WIDGET->getTitle(), $MODULE_NAME)|@escape:'html'}</b></div>
    	<div class="col-lg-12 dashboard_notebookWidget_text" style="display:none;">
			<span class="pull-right">
				<button class="btn btn-mini btn-success pull-right dashboard_notebookWidget_save">
					<strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
				</button>
			</span>
		</div>
    </div>
</div>

<div class="dashboardWidgetContent" style='padding:5px'>
	{include file="dashboards/NotebookContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>