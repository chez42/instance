<div class="detailViewContainer extensionDetailContents">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
		<div class="contents ">
			<div class="clearfix">
				<h4 class="pull-left">	
					{vtranslate('LBL_MSEXCHANGE_CONFIG', $MODULE)}
				</h4>
				<div class="btn-group pull-right">
					{if $EXCHANGELICENSE->result eq 'ok' or $EXCHANGELICENSE->result eq 'valid'}
						<button id="licenseSettingButton" class="btn btn-success" type="button" style="margin-right: 10px;">{vtranslate('LBL_LICENSE_UPGRADE',$MODULE)}</button>
					{/if}
					<button class="btn btn-default editMSExchangeConfigButton" type="button">{vtranslate('LBL_EDIT', $MODULE)}</button>
                </div>
			</div>
			<hr>
			<br>
			<div class="detailViewInfo">	
				<div class="form-group m-form__group row">
					<label class="col-sm-2">{vtranslate('URL', $MODULE)}</label>
					<div class="col-sm-4 col-xs-4">
						{$MSEXCHANGE_SETTINGS['url']}
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label class="col-sm-2">{vtranslate('Version', $MODULE)}</label>
					<div class="col-sm-4 col-xs-4">
						{$MSEXCHANGE_SETTINGS['exchange_version']}
					</div>
				</div>
				<div class="form-group m-form__group row">
					<label class="col-sm-2">{vtranslate('Use Impersonation', $MODULE)}</label>
					<div class="col-sm-4 col-xs-4">
						{if $MSEXCHANGE_SETTINGS['impersonate_user_account'] eq '1'}
							{vtranslate('Yes', $MODULE)}
						{else}
							{vtranslate('NO', $MODULE)}
						{/if}
					</div>
				</div>
				{if $MSEXCHANGE_SETTINGS['impersonate_user_account'] eq '1'}
					<div class="form-group m-form__group row">
						<label class="col-sm-2">{vtranslate('Username', $MODULE)}</label>
						<div class="col-sm-4 col-xs-4">
							{$MSEXCHANGE_SETTINGS['username']}
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label class="col-sm-2">{vtranslate('Password', $MODULE)}</label>
						<div class="col-sm-4 col-xs-4">
							******
						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>		
</div>

<div class="detailViewContainer hide extensionEditContents">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
		<div class="contents ">
			<form id="exchangeSettingForm" name="settingsForm" class="m-form m-form--label-align-left" action="index.php" method="POST" >
		        {if !empty($MSEXCHANGE_SETTINGS)}
		        	{assign var="HOST" value=$MSEXCHANGE_SETTINGS['url']}
		        	{assign var="UNAME" value=$MSEXCHANGE_SETTINGS['username']}
		        	{assign var="PWD" value=$MSEXCHANGE_SETTINGS['password']}
		        	{assign var="VERSION" value=$MSEXCHANGE_SETTINGS['exchange_version']}
		        	{assign var="IMPERSONATED" value=$MSEXCHANGE_SETTINGS['impersonate_user_account']}
		        	{assign var="USER_IMPERSONATION" value=$MSEXCHANGE_SETTINGS['impersonation_type']}
		        {/if}
		        <input type="hidden" name="module" value="{$MODULE}" />
		        <input type="hidden" name="action" value="MSExchangeConfigSettingsSave" />
		        <div class="clearfix">
					<h4 class="pull-left">	
						{vtranslate('LBL_MSEXCHANGE_CONFIG', $MODULE)}
					</h4>
				</div>
				<hr>
				<br>
				<div class="detailViewInfo">
					<div class="form-group m-form__group row">
						<label class="col-form-label col-sm-2">{vtranslate('URL', $MODULE)}<span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<input type="text" name="ms_exchange_url" value="{$HOST}" class="form-control" data-rule-required="true"/>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label class="col-form-label col-sm-2">{vtranslate('Version', $MODULE)}<span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<select name="ms_exchange_version" class="form-control select2" data-rule-required="true">
								<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
								<option value="Exchange2007" {if $VERSION eq 'Exchange2007'}selected{/if}>{vtranslate('Exchange2007', $MODULE)}</option>
								<option value="Exchange2007_SP1" {if $VERSION eq 'Exchange2007_SP1'}selected{/if}>{vtranslate('Exchange2007_SP1', $MODULE)}</option>
								<option value="Exchange2010" {if $VERSION eq 'Exchange2010'}selected{/if}>{vtranslate('Exchange2010', $MODULE)}</option>
								<option value="Exchange2010_SP1" {if $VERSION eq 'Exchange2010_SP1'}selected{/if}>{vtranslate('Exchange2010_SP1', $MODULE)}</option>
								<option value="Exchange2010_SP2" {if $VERSION eq 'Exchange2010_SP2'}selected{/if}>{vtranslate('Exchange2010_SP2', $MODULE)}</option>
								<option value="Exchange2013" {if $VERSION eq 'Exchange2013'}selected{/if}>{vtranslate('Exchange2013', $MODULE)}</option>
								<option value="Exchange2013_SP1" {if $VERSION eq 'Exchange2013_SP1'}selected{/if}>{vtranslate('Exchange2013_SP1', $MODULE)}</option>
								<option value="Exchange2016" {if $VERSION eq 'Exchange2016'}selected{/if}>{vtranslate('Exchange2016', $MODULE)}</option>
							</select>
						</div>
					</div>
					<div class="form-group m-form__group row">
						<label class="col-form-label col-sm-2">{vtranslate('Use Impersonation', $MODULE)}</label>
						<div class="col-sm-4 col-xs-4">
							<input type="checkbox" {if $IMPERSONATED}checked="checked" {/if} name="ms_exchange_user_impersonation">
						</div>
					</div>
					<div class="form-group m-form__group row {if !$UNAME}hide{/if} userAdminCredx">
						<label class="col-form-label col-sm-2">{vtranslate('Username', $MODULE)}<span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<input type="text" name="ms_exchange_username" value="{$UNAME}" class="form-control" data-rule-required="true"/>
						</div>
					</div>
					<div class="form-group m-form__group row {if !$PWD}hide{/if} userAdminCredx">
						<label class="col-form-label col-sm-2">{vtranslate('Password', $MODULE)}<span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<input type="password" name="ms_exchange_password" value="{$PWD}" class="form-control" data-rule-required="true"/>
						</div>
					</div>
					<div class="form-group m-form__group row {if !$USER_IMPERSONATION}hide{/if} userAdminCredx"">
						<label class="col-form-label col-sm-2">{vtranslate('Configure impersonation', $MODULE)}<span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<select id="user_impersonation_types" name="ms_exchange_user_impersonation_type" class="form-control select2" data-rule-required="true">
								<option value="upn" {if $USER_IMPERSONATION eq 'upn'}selected{/if}>The user principle name (UPN)</option>
								<option value="smtp_address" {if $USER_IMPERSONATION eq 'smtp_address'}selected{/if}>The primary SMTP address</option>
								<option value="sid" {if $USER_IMPERSONATION eq 'sid'}selected{/if}>The security identifier (SID)</option>
							</select>
						</div>
					</div>
					<div class="form-group m-form__group row {if !$USER_IMPERSONATION}hide{/if} userAdminCredx">
						<label class="col-form-label col-sm-2"><span class="redColor">*</span></label>
						<div class="col-sm-4 col-xs-4">
							<input type="text" name="ms_exchange_user_impersonation_field_value" value="" class="form-control" data-rule-required="true" />
						</div>
					</div>
				</div>
				<div class="modal-overlay-footer clearfix">
					<div class=" row clearfix">
						<div class=" textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
							<button type="submit" class="btn btn-success saveMSExchangeConfig">Save</button>
							<a href="#" class="cancelLink" type="reset">Cancel</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>