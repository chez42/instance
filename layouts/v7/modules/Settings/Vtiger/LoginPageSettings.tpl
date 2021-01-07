{strip}
	<div class="loginPageSettingDiv padding20">
		{*<h3>{vtranslate('Login Page Setting', $QUALIFIED_MODULE)}</h3>*}
		<div class="loginPageSettingContainer">
			<form class="form-horizontal LoginPageSettingForm" id="LoginPageSettingForm" method="post" action="index.php" enctype="multipart/form-data">
			
				<input type="hidden" name="parent" value="Settings" />
				<input type="hidden" name="module" value="{$MODULE}" />
				<input type="hidden" name="action" value="LoginPageSetting" />
				<br>			
				<div class="row">
					<div class="form-group companydetailsedit">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Logo',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<div class="company-logo-content">
								<img src="{$LOGIN_LOGO}" class="alignMiddle" style="max-width:700px;"/>
								<br><hr>
								<input type="file" name="logo" id="logoFile" />
							</div>
							<br>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group companydetailsedit">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Background',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<div class="company-logo-content">
								<img src="{$LOGIN_BACKGROUND}" class="alignMiddle" style="max-width:700px;"/>
								<br><hr>
								<input type="file" name="background" id="backgroundFile" />
							</div>
							<br>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Copyright',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="copyright_text" value="{$COPYRIGHT}"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Facebook Link',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="facebook_link" value="{$FACEBOOKLINK}"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Twitter Link',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="twitter_link" value="{$TWITTERLINK}"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Linkedin Link',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="linkedin_link" value="{$LINKEDINLINK}"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Youtube Link',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="youtube_link" value="{$YOUTUBELINK}"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group">
						<label class="col-sm-2 fieldLabel control-label"> {vtranslate('Instagram Link',$QUALIFIED_MODULE)}</label>
						<div class="fieldValue col-sm-5" >
							<input type="text" class="inputElement" name="instagram_link" value="{$INSTAGRAMLINK}"/>
						</div>
					</div>
				</div>
				<br><br>
				<div class="modal-overlay-footer clearfix">
					<div class="row clearfix">
						<div class="textAlignCenter col-lg-12 col-md-12 col-sm-12">
							<button type="submit" class="btn btn-success saveButton">{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
							<a class="cancelLink" data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{/strip}