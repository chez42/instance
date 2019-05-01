<div class="col-sm-12 col-xs-12 extensionContents" id = "ringcentral">
	{if !$SHOW_SETTINGS }
		<div class="row">
			<h3 class="module-title pull-left"> {vtranslate('LBL_CONNECT_RINGCENTRAL', $QUALIFIED_MODULE)} </h3>
			<div>
				<div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
					<button type="submit" class="btn btn-success saveButton" onclick="javascript:RingCentral_RingCentral_Js.triggerRingCentralConnect('{$CONNECT_URL}')">Connect</button>&nbsp;&nbsp;
				</div>
			</div>
		</div>
	{else}
		
        <div class="row" style="margin-bottom:5px;">
			<div class="col-md-12">
				<a href="#" class=" btn btn-default pull-right" onclick="javascript:RingCentral_RingCentral_Js.triggerRingCentralConnect('{$CONNECT_URL}')">
					{vtranslate('ReConnect', $MODULE)}
				</a>
			</div>
		</div>
		<div class="editViewContents">
			
			<div class="fieldBlockContainer" data-block="LBL_WEBFORM_INFORMATION">
				<h4 class="fieldBlockHeader">Settings</h4><hr>
				<div class="row">
					<div class="col-md-2 fieldLabel alignMiddle">
						{vtranslate('Ring Central From No', $MODULE)} &nbsp;<span class="redColor">*</span>
					</div>
					<div class="col-md-2 fieldValue">
						<input type="text" name="from_no" id = "from_no" class="inputElement" data-rule-required="true" value="{$FROM_NO}" />
					</div>
					<div class="col-md-2 fieldValue">
						<button id="saveSettings" type="submit" class="btn btn-success saveButton">{vtranslate('Save', $MODULENAME)}</button>
					</div>
				</div>
			</div>
		
			<div class="logsList fieldBlockContainer">
				{include file='Logs.tpl'|@vtemplate_path:'RingCentral'}
			</div>
			
		</div>
		
	{/if}
	
</div>
