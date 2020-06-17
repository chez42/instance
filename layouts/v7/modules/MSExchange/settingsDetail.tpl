{strip}
	<div class="detailViewContainer" id="OfficeSettingsDetails">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
			<div class="contents ">
				<div class="clearfix">
					<h4 class="pull-left">	
						{vtranslate('Office365 Configuration', $QUALIFIED_MODULE)}
					</h4>
					<div class="btn-group pull-right">
						{if $OFFICE_LICENSE->result eq 'ok' or $OFFICE_LICENSE->result eq 'valid'}
							<button id="licenseSettingButton" class="btn btn-success" type="button" style="margin-right: 10px;">{vtranslate('LBL_LICENSE_UPGRADE',$MODULE)}</button>
						{/if}
						<button id="settingEditButton" class="btn btn-default editButton" data-url='{$MODEL->getSettingsEditViewUrl()}' title="{vtranslate('LBL_EDIT', $QUALIFIED_MODULE)}" type="button">{vtranslate('LBL_EDIT',$QUALIFIED_MODULE)}</button>
					</div>
				</div>
				<hr>
				<br>
				<div class="detailViewInfo">
					<div class="row form-group">
						<div class="col-lg-2 col-md-2 col-sm-2 fieldLabel">
							<label>{vtranslate('Client Id', $QUALIFIED_MODULE)}</label>
						</div>
						<div class="col-lg-8 col-md-8 col-sm-8 fieldValue break-word">
							<div>{$OFFICE_CONFIG['clientid']}</div>
						</div>
					</div>
					<div class="row form-group">
						<div class="col-lg-2 col-md-2 col-sm-2 fieldLabel">
							<label>{vtranslate('Client Secret', $QUALIFIED_MODULE)}</label>
						</div>
						<div class="col-lg-8 col-md-8 col-sm-8 fieldValue break-word">
							<div>{$OFFICE_CONFIG['cliensecret']}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
{/strip}
