{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}

	<div class="editViewPageDiv editViewContainer" id="stratificonfig" style="padding-top:0px;">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="clearfix">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
					<h3 style="margin-top: 0px;">{vtranslate('Stratifi Configuration', $QUALIFIED_MODULE)}</h3>
				</div>
				{if $mode neq 'edit'}
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="btn-group pull-right">
							<button class="btn btn-default editButton" data-url='{$URL}&mode=edit' type="button" title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}">{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}</button>
						</div>
					</div>
				{/if}
			</div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<form id="updatedetails" data-url="{$URL}&mode=detail" method="POST">
				
				<div class="blockData">
					<br>
					<div class="block">
						
						{if $mode eq 'edit'}
							<div>
								<h4>{vtranslate('Stratifi Configuration', $QUALIFIED_MODULE)}</h4>
							</div>
							<hr>
							<table class="table editview-table no-border">
								<tbody>
									<tr>
										<td class="{$WIDTHTYPE} fieldLabel">
											<label>{vtranslate('Users', $QUALIFIED_MODULE)}</label>&nbsp
										</td>
										<td class="{$WIDTHTYPE} fieldValue">
											<div class=" col-lg-6 col-md-6 col-sm-12">
												<select class="inputElement select2" name="users" id="users">
													<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
													{foreach item=USERNAME key=USERNO from=$USERPICK}
														<option value="{$USERNO}">{$USERNAME}</option>
													{/foreach}
												</select>
											</div>
										</td>
									</tr>
									<tr>
										<td class="{$WIDTHTYPE} fieldLabel">
											<label>{vtranslate('Rep Code', $QUALIFIED_MODULE)}</label>&nbsp;<span class="redColor">*</span>
										</td>
										<td class="{$WIDTHTYPE} fieldValue">
											<div class=" col-lg-6 col-md-6 col-sm-12">
												<textarea class="form-control" name="rep_codes" data-rule-required="true">{$REP_CODES}</textarea>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						{else if $mode eq 'detail'}
							<div>
								<h4>{vtranslate('Stratifi Configuration', $QUALIFIED_MODULE)}</h4>
							</div>
							<hr>
							<table class="table editview-table no-border">
								<tbody>
									<tr>
										<td class="{$WIDTHTYPE} fieldLabel"style="width:25%" ><label>{vtranslate('Rep Codes', $QUALIFIED_MODULE)}</label></td>
										<td class="{$WIDTHTYPE} fieldValue"><span>{$REP_CODES}</span></td>
									</tr>
								</tbody>
							</table>
						{/if}	
					</div>
					<br>	
					{if $mode neq 'detail'}
						<div class='modal-overlay-footer clearfix'>
							<div class="row clearfix">
								<div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
									<button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
									<a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
								</div>
							</div>
						</div>
					{/if}
				</div>
			</form>
		</div>
	</div>
	
	
{/strip}