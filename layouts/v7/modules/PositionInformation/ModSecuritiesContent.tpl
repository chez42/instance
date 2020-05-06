{strip}
	<div class="col-lg-12 resizable-summary-view">
		<div class="left-block col-lg-4">
			
			<div class="summaryView">
				<div class="summaryViewHeader">
					<h4 class="display-inline-block">{vtranslate('LBL_KEY_FIELDS', $MODULE_NAME)}</h4>
				</div>
				<div class="summaryViewFields">
					<div class="recordDetails">
						{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
						   <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
						{/if}
						<table class="summary-table no-border">
							<tbody>
							{foreach item=FIELD_MODEL key=FIELD_NAME from=$SUMMARY_RECORD_STRUCTURE['SUMMARY_FIELDS']}
								{assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
								{if $FIELD_MODEL->get('name') neq 'modifiedtime' && $FIELD_MODEL->get('name') neq 'createdtime'}
									<tr class="summaryViewEntries">
										<td class="fieldLabel" >
												<label class="muted textOverflowEllipsis" title="{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}">
													{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}
													{if $FIELD_MODEL->get('uitype') eq '71' || $FIELD_MODEL->get('uitype') eq '72'}
													{assign var=CURRENCY_INFO value=getCurrencySymbolandCRate($USER_MODEL->get('currency_id'))}
													&nbsp;({$CURRENCY_INFO['symbol']})
													{/if}
												</label>
											</td>
										<td class="fieldValue">
											<div class="row">
												{assign var=DISPLAY_VALUE value="{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get("fieldvalue"))}"}                  
												
												<span class="value textOverflowEllipsis" data-field-type="{$FIELD_MODEL->getFieldDataType()}" title="{strip_tags($DISPLAY_VALUE)}"  {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'}style="word-wrap: break-word;white-space: unset !important;"{/if}>
													{include file=$FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName()|@vtemplate_path:$MODULE_NAME FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
												</span>
											   
											</div>
										</td>
									</tr>
								{/if}
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
		</div>
		<div class="middle-block col-lg-8">	
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_historical_information" >
					<div class="widget_header">
						<h4 class="display-inline-block">Historical Data</h4>
					</div>
					<div class="widget_contents">
						<input type="hidden" class="price_data" value='{$PRICE_DATA}' />
						<div id="chartdiv" style="width: {$WIDTH}; height: {$HEIGHT};"></div>
						
					</div>
				</div>
			</div>
			
		</div>
	</div>
	{foreach key=index item=jsModel from=$SCRIPTS}
		<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
	{/foreach}
{/strip}