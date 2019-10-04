{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
-->*}

{strip}

	<div class="container-fluid main-scroll" id="tabColumnViewContainer">
		<input id="selectedModuleName" type="hidden" value="{$SELECTED_MODULE_NAME}" />
		<input class="selectedTab" type="hidden" value="{$SELECTED_TAB}">
		<input class="selectedMode" type="hidden" value="{$MODE}">
		<input type="hidden" id="selectedModuleLabel" value="{vtranslate($SELECTED_MODULE_NAME,$SELECTED_MODULE_NAME)}" />
		<div class="widget_header row">
			<label class="col-sm-2 textAlignCenter" style="padding-top: 7px;">
				{vtranslate('SELECT_MODULE', $QUALIFIED_MODULE)}
			</label>
			<div class="col-sm-6">
				<select class="select2 col-sm-6" name="tabColumnViewModules">
					<option value=''>{vtranslate('LBL_SELECT_OPTION', $QUALIFIED_MODULE)}</option>
					{foreach item=MODULE_NAME key=TRANSLATED_MODULE_NAME from=$SUPPORTED_MODULES}
						<option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>
							{$TRANSLATED_MODULE_NAME}
						</option>
					{/foreach}
				</select>
			</div>
		</div>
		<br>
		<br>
		{if $SELECTED_MODULE_NAME}
			<div class="contents tabbable">
			{assign var=IS_SORTABLE value=$SELECTED_MODULE_MODEL->isSortableAllowed()}
			{assign var=ALL_BLOCK_LABELS value=[]}
				<div class="row fieldsListContainer" style="padding:1% 0">
					<div class="col-sm-6">
						<div class="row">
							<div class=" col-sm-3 {if !$IS_TAB}hide{/if} convertTab">
								<button class="btn btn-default addButton addTab" type="button">
									<i class="fa fa-plus"></i>&nbsp;&nbsp;
									{vtranslate('Add Tab', $QUALIFIED_MODULE)}
								</button>
							</div>
							<div class="blockActions col-sm-5">
								<span>
									<i class="fa fa-info-circle customtab-tooltip"></i>&nbsp; {vtranslate('Enable Tab View', $QUALIFIED_MODULE)}&nbsp;
									<input style="opacity: 0;" type="checkbox"
										{if $IS_TAB} checked value='0' {else} value='1' {/if} class ='cursorPointer bootstrap-switch' name="is_tab" id="is_tab"
										data-on-text="{vtranslate('LBL_YES', $QUALIFIED_MODULE)}" data-off-text="{vtranslate('LBL_NO', $QUALIFIED_MODULE)}" data-on-color="primary"  />
								</span>
							</div>
						</div>
					</div>
					
				</div>
				<div class="row {if !$IS_TAB}hide{/if} convertTab">
					<div class="col-sm-12 tabcolumncontent">
						<div id="moduleBlocks"  style="margin-top:17px;">
							{foreach key=BLOCK_LABEL_KEY item=BLOCK_MODEL from=$BLOCKS}
								{assign var=IS_BLOCK_SORTABLE value=$SELECTED_MODULE_MODEL->isBlockSortableAllowed($BLOCK_LABEL_KEY)}
								{assign var=BLOCK_ID value=$BLOCK_MODEL->get('id')}
								{if $BLOCK_LABEL_KEY neq 'LBL_INVITE_USER_BLOCK'}
									{$ALL_BLOCK_LABELS[$BLOCK_ID] = $BLOCK_MODEL}
								{/if}
								<div class="{if $COLUMNS[$BLOCK_ID]}nonTabModules{/if} tabModules" style="margin-top:17px;">
									<div id="block_{$BLOCK_ID}" class="col-sm-2 editFieldsTable block_{$BLOCK_ID} marginBottom10px border1px mainBlock " 
									data-block-id="{$BLOCK_ID}" data-sequence="{$BLOCK_MODEL->get('sequence')}" style="background: white;margin-left:5px;"
									 data-custom-fields-count="{$BLOCK_MODEL->getCustomFieldsCount()}">
										<div class="tabColumnViewBlockHeader row">
											<div class="blockLabel {*if $COLUMNS[$BLOCK_ID]}block-tooltip{/if*} col-sm-8 marginLeftZero" style="word-break: break-all;padding: 5px;"
											title="{vtranslate($BLOCK_LABEL_KEY, $SELECTED_MODULE_NAME)}">
												<img class="cursorPointerMove" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;
												<strong class="translatedBlockLabel">{vtranslate($BLOCK_LABEL_KEY, $SELECTED_MODULE_NAME)|substr:0:9}...</strong>
											</div>
											<div class="col-sm-4 actions marginLeftZero" style="padding: 5px;">
												<div class="blockActions" style="float:left !important;" id="blockActions{$BLOCK_ID}">
													<span>
														{assign var = NUMBER_LIST  value=array(2,3,4,5)}
														<select id="num_of_columns_{$BLOCK_ID}" class="select2 num_of_columns" name="num_of_columns_{$BLOCK_ID}" data-block="{$BLOCK_ID}"  style="min-width:30px;
														width:45px!important;">
															{foreach item=NUMBER from=$NUMBER_LIST}
																<option value="{$NUMBER}" {if $NUMBER eq $COLUMNS[$BLOCK_ID]}selected{/if}>{$NUMBER}</option>
															{/foreach}
														</select>
													</span>
												</div>
											</div>
										</div>
										<div class=" row">
										</div>
									</div>
								</div>	
							{/foreach}
						</div>
					</div>
				</div>
				<div class=" row {if !$IS_TAB}hide{/if} convertTab" id="data-body">
					<div class="col-sm-12 tabcolumncontent">
						{foreach item=TABDATA key=TABID from=$customTabData}
							<div class="editFieldsTable col-md-2 marginBottom10px border1px ui-droppable block_{$TABID}" data-sequence='{$SEQUENCE[$TABID]}' 
							style="margin-top:15px;margin-left:5px;" data-block="{$TABID}" >
								<div class="layoutBlockHeader">
									<div class="col-sm-12 blockLabel padding10 marginLeftZero" style="word-break: break-all;" 
									title="{vtranslate($TABNAME[$TABID], $SELECTED_MODULE_NAME)}">
										<div class="row">
											<div class="col-sm-9">
												<img class="cursorPointerMove" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;
												<strong class="translatedBlockLabel"> {vtranslate($TABNAME[$TABID], $SELECTED_MODULE_NAME)|substr:0:7}...</strong>
											</div>
											<div class="col-sm-3">
												<div class="pull-right ">
													<button type="button" class="close deleteTab" data-tabid="{$TABID}" title="deleteTab" aria-label="Close" >
														<span aria-hidden="true" class="fa fa-close"></span>
													</button>
												</div>
											</div>
										</div>
										<hr>
									</div>
								</div>
								<div class="connectedSortable tabModules row" style="margin-top:17px; min-height: 200px;">
									{foreach item=DATA from=$TABDATA}
										{if $DATA['block_id']}
											<div id="block_{$DATA['block_id']}" class="col-sm-11 editFieldsTable block_{$DATA['block_id']} marginBottom10px border1px " 
											data-block-id="{$DATA['block_id']}" data-sequence="{$DATA['blocksequence']}" style="background: white;margin-left:5px;" title="{vtranslate($DATA['blocklabel'], $SELECTED_MODULE_NAME)}">
												<div class="tabColumnViewBlockHeader row">
													<div class="blockLabel col-sm-12 padding10 marginBottom10px marginLeftZero" style="word-break: break-all;">
														<img class="cursorPointerMove" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;
														<strong class="translatedBlockLabel">{vtranslate($DATA['blocklabel'], $SELECTED_MODULE_NAME)|substr:0:15}...</strong>
													</div>
												</div>
												<div class=" row">
												</div>
											</div>
										{/if}	
									{/foreach}
								</div>
							</div>	
						{/foreach}
						
						<div class="newTabCopy hide col-md-2 marginBottom10px border1px  "  data-block="" data-sequence="">
							<div class="layoutBlockHeader">
								<div class="col-sm-12 blockLabel padding10 marginLeftZero" style="word-break: break-all;" title=''>
									<div class="row">
										<div class="col-sm-9">
											<img class="cursorPointerMove" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;
											<strong class="translatedBlockLabel"> </strong>
										</div>
										<div class="col-sm-3">
											<div class="pull-right ">
												<button type="button" class="close deleteTab" data-tabid="" title="deleteTab" aria-label="Close" >
													<span aria-hidden="true" class="fa fa-close"></span>
												</button>
											</div>
										</div>
									</div>
									<hr>
								</div>
							</div>
							<div id="tabModules" class="connectedSortable row  " style="margin-top:17px; min-height: 200px;">
							</div>
							<div class=" row">
							</div>
						</div>
					</div>		
				</div>	
				<div class="modal-dialog modal-content addTabModal hide">
					{assign var=HEADER_TITLE value={vtranslate('Add New Tab', $QUALIFIED_MODULE)}}
					{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
					<form class="form-horizontal addTabForm">
						<div class="modal-body">
							<div class="form-group">
								<label class="control-label fieldLabel col-sm-5">
									<span>{vtranslate('Tab Name', $QUALIFIED_MODULE)}</span>
									<span class="redColor">*</span>
								</label>
								<div class="controls col-sm-6">
									<input type="text" name="label" class="col-sm-3 inputElement" data-rule-required='true' style='width: 75%'/>
								</div>
							</div>
						</div>
						{include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
					</form>
				</div>
			</div>
			
			<div class='modal-overlay-footer clearfix saveViewButton' style="opacity:0;margin-right:0px;">
				<div class="row clearfix">
					<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
						<button class="btn btn-success saveTabView" type="button" >
							{vtranslate('LBL_SAVE_LAYOUT', $QUALIFIED_MODULE)}
						</button>
					</div>
				</div>
			</div>
		{/if}
	</div>
{/strip}
