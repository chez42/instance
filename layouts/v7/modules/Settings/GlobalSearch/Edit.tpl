
{strip}
<div class="editViewPageDiv editViewContainer" style="padding-top:0px;">

	<div class="col-lg-12 col-md-12 col-sm-12">
		<div>
			<h3 style="margin-top: 0px;">{vtranslate('LBL_GLOBALSEARCH_SETTINGS', $QUALIFIED_MODULE)}</h3>&nbsp;{vtranslate('Select Fields For Global Search', $QUALIFIED_MODULE)}
		</div>
		{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
		
		<form id="GlobalSearchSettings" class="form-horizontal" method="POST">
			<div class="blockData">
				<br>
				
				<div class="block">
					<div>
						<h4>{vtranslate('LBL_GLOBALSEARCH_SETTINGS', $QUALIFIED_MODULE)}</h4>
					</div>
					<hr>
					
					<table class="table editview-table no-border">
						<tbody>
							<tr>
								<td class="{$WIDTHTYPE} fieldLabel">
									<label><b>Select Module</b></label>
									&nbsp;<span class="redColor">*</span>
								</td>
								<td class="{$WIDTHTYPE} fieldValue">
									<div class=" col-lg-6 col-md-6 col-sm-12">
										<select class="select2" id="modulename" name="modulename" style="width:150px;" data-rule-required="true" >
											<option value="">Select Module</option>
										
											{foreach key=MODULE_NAME item=fieldObject from=$SEARCHABLE_MODULES}
												{if $MODULE_NAME !== $MODULE}
													{if isset($SEARCHED_MODULE) && $SEARCHED_MODULE eq $MODULE_NAME && $SEARCHED_MODULE !== 'All'}
														<option value="{$MODULE_NAME}" class="globalSearch_module_{$MODULE_NAME}" selected>{vtranslate($MODULE_NAME,$MODULE_NAME)}</option>
													{else}
														<option value="{$MODULE_NAME}" class="globalSearch_module_{$MODULE_NAME}">{vtranslate($MODULE_NAME,$MODULE_NAME)}</option>
													{/if}
												{/if}
											{/foreach}
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td class="{$WIDTHTYPE} fieldLabel">
									<label><b>Search Fields</b></label>
									&nbsp;<span class="redColor">*</span>
								</td>
								<td class="{$WIDTHTYPE} fieldValue">
									<div class=" col-lg-6 col-md-6 col-sm-12">
										<select class="select2" id="fieldnames"style='width:250px;height:30px;'  multiple name="fieldnames[]" data-rule-required="true">
										</select>
									</div>	
								</td>
							</tr>
							<tr>
								<td class="{$WIDTHTYPE} fieldLabel">
									<label><b>Show Fields</b></label>
									&nbsp;<span class="redColor">*</span>
								</td>
								<td class="{$WIDTHTYPE} fieldValue">
									<div class=" col-lg-6 col-md-6 col-sm-12">
										<select class="select2" id="fieldnames_show"style='width:250px;height:30px;'  multiple name="fieldnames_show[]" data-rule-required="true">
										</select>
									</div>	
								</td>
							</tr>
							<tr>
								<td class="{$WIDTHTYPE} fieldLabel">
									<label><b>Allow Global Search</b></label>
								</td>
								<td class="{$WIDTHTYPE} fieldValue">
									<div class=" col-lg-6 col-md-6 col-sm-12">
										<input type="checkbox" value="1" name="allow_global_search" />
									</div>	
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>	
				<div class='modal-overlay-footer clearfix'>
					<div class="row clearfix">
						<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
							<button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
							<a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
						</div>
					</div>
				</div>
			</div>
			
		</form>
	</div>
</div>
{/strip}