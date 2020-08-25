{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Settings/MenuEditor/views/Index.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{assign var=APP_IMAGE_MAP value=Vtiger_MenuStructure_Model::getAppIcons()}

			<style>
				.wrapper .col-md-3{
				    width: 100%;
				    padding-top: 5px;
					padding-right: 15px;
					padding-left: 15px;
				    display: inline-block;
				    float: none;
			    }
			</style>
			
<div class="listViewPageDiv detailViewContainer col-sm-12" id="listViewContent">
	<div class="col-sm-12">
		<div class="row">
			<button class="btn btn-default addButton addApp" type="button">
				<i class="fa fa-plus"></i>&nbsp;&nbsp;
				{vtranslate('Add Menu', $QUALIFIED_MODULE)}
			</button>
		</div>
		{*<div class="row">
			<div class=" vt-default-callout vt-info-callout">
				<h4 class="vt-callout-header"><span class="fa fa-info-circle"></span>{vtranslate('LBL_INFO', $QUALIFIED_MODULE_NAME)}</h4>
				<p>{vtranslate('LBL_MENU_EDITOR_INFO', $QUALIFIED_MODULE_NAME)}</p>
			</div>
		</div>*}
	</div>
	<div class="col-lg-12" style="margin-top: 10px;">
		
		<div class="row wrapper" style="column-count:4">
			
			{assign var=APP_LIST value=Vtiger_MenuStructure_Model::getAppMenuList()}
			{assign var=APP_SEQ value=Vtiger_MenuStructure_Model::getAppSequence()}
			{assign var=APP_COLOR value=Vtiger_MenuStructure_Model::getAppColor()}
			{foreach item=APP_IMAGE key=APP_NAME from=$APP_IMAGE_MAP name=APP_MAP}
				{if !in_array($APP_NAME, $APP_LIST)} {continue} {/if}
				<div class="col-md-3 menuContainer{if $smarty.foreach.APP_MAP.index eq 0 or count($APP_LIST) eq 1}{/if} appSortable" data-app="{$APP_NAME}" 
				data-sequence="{$APP_SEQ[$APP_NAME]}" data-color="{$APP_COLOR[$APP_NAME]}" data-icon="{$APP_IMAGE}">
					<div class="menuEditorActions pull-right" style="background:{$APP_COLOR[$APP_NAME]} !important">
						<i data-appname="{$APP_NAME}" class="fa fa-pencil whiteIcon menuEditItem" style="/*padding:5px 0;*/" ></i>
						<i data-appname="{$APP_NAME}" class="fa fa-times whiteIcon menuRemoveItem" style="/*padding:5px 0;*/" ></i>
					</div>
					
					<div class="menuEditorItem menuName app-{$APP_NAME}" data-app-name="{$APP_NAME}" style="background:{$APP_COLOR[$APP_NAME]} !important">
						<span class="fa {$APP_IMAGE}" style = "padding-left: 20px; padding-bottom: 5px;"></span>
						{assign var=TRANSLATED_APP_NAME value={vtranslate("$APP_NAME")}}
						<div class="textOverflowEllipsis" title="{$TRANSLATED_APP_NAME}">{$TRANSLATED_APP_NAME}</div>
					</div>
					<div class="sortable appContainer" data-appname="{$APP_NAME}">
						{foreach key=moduleName item=moduleModel from=$APP_MAPPED_MODULES[$APP_NAME]}
							<div class="modules noConnect" data-module="{$moduleName}">
								<i data-appname="{$APP_NAME}" class="fa fa-times pull-right whiteIcon menuEditorRemoveItem" style="margin: 5%;padding-top:15px;"></i>
								<div class="menuEditorItem menuEditorModuleItem">
									<span class="pull-left marginRight10px marginTop5px">
										<img class="alignMiddle cursorDrag" src="{vimage_path('drag.png')}"/>
									</span>
									{assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
									<span>
										<span class="marginRight10px marginTop5px pull-left">{$moduleModel->getModuleIcon()}</span>
									</span>
									<div class="textOverflowEllipsis marginTop5px textAlignLeft" title="{$translatedModuleLabel}">{$translatedModuleLabel}</div>
								</div>
							</div>
						{/foreach}
						<div class="menuEditorItem menuEditorModuleItem menuEditorAddItem" data-appname="{$APP_NAME}">
							<i class="fa fa-plus pull-left marginTop5px"></i>
							<div class="marginTop10px">{vtranslate('LBL_SELECT_HIDDEN_MODULE', $QUALIFIED_MODULE_NAME)}</div>
						</div> 
					</div>
				</div>
			{/foreach}
		</div>
	</div>
	<div class="col-lg-2 newAppCopy hide menuContainer" data-app="" data-sequence="" data-color="" data-icon="" >
		<div class="menuEditorActions pull-right">
			<i data-appname="" class="fa fa-pencil whiteIcon menuEditItem" style="/*padding:5px 0;*/" ></i>
			<i data-appname="" class="fa fa-times whiteIcon menuRemoveItem" style="/*padding:5px 0;*/" ></i>
		</div>
		<div class="menuEditorItem menuName " data-app-name="">
			<span class="fa " style = "padding-left: 20px; padding-bottom: 5px;"></span>
			<div class="textOverflowEllipsis" title=""></div>
		</div>
		<div class="sortable appContainer" data-appname="">
			<div class="menuEditorItem menuEditorModuleItem menuEditorAddItem" data-appname="">
				<i class="fa fa-plus pull-left marginTop5px"></i>
				<div class="marginTop10px">{vtranslate('LBL_SELECT_HIDDEN_MODULE', $QUALIFIED_MODULE_NAME)}</div>
			</div> 
		</div>
	</div>
	<div class="modal-dialog modal-content addAppModal hide">
		<style>
			.colorpicker{
				z-index : 10000;
			}
		</style>
		{assign var=HEADER_TITLE value={vtranslate('Add New Menu', $QUALIFIED_MODULE)}}
		{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
		<form class="form-horizontal addMenuForm">
			<div class="modal-body">
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						<span>{vtranslate('Name', $QUALIFIED_MODULE)}</span>
						<span class="redColor">*</span>
					</label>
					<div class="controls col-sm-6">
						<input type="text" name="label" class="col-sm-3 inputElement" data-rule-required='true' style='width: 75%'/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						<span>{vtranslate('Icon', $QUALIFIED_MODULE)}</span>
						<span class="redColor">*</span>
					</label>
					<div class="controls col-sm-6">
						<input type="text" name="icon" class="col-sm-3 inputElement" data-rule-required='true' style='width: 75%'/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						<span>{vtranslate('Color', $QUALIFIED_MODULE)}</span>
						<span class="redColor">*</span>
					</label>
					<div class="controls col-sm-6">
						<input type="text" name="color_code" class="col-sm-3 inputElement" data-rule-required='true' style='width: 75%'/>
					</div>
				</div>
			</div>
			{include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
		</form>
	</div>
</div>
