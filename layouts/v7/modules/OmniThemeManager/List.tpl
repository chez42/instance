
<style>
	.themecard{
		margin: 15px;
		padding: 10px;
		border: 1px solid black;
		background-color: white;
		border-radius: 5px;
		-webkit-box-shadow: 6px 6px 15px -4px rgba(0,0,0,0.75);
		-moz-box-shadow: 6px 6px 15px -4px rgba(0,0,0,0.75);
		box-shadow: 6px 6px 15px -4px rgba(0,0,0,0.75);
		text-align: center;
		cursor: pointer;
		min-height: 460px;
		height: auto !important;
	}
	
	.mycthemeswitchercnt{
		background-color: white;
		border: 3px dashed lightgray;
		width:90%;
		margin:auto;
		margin-top:20px;
		margin-bottom:20px;
		padding-bottom:20px;
	}

	
	.themescontainer{
		width: 100%;
		padding-bottom: 20px;
	}
	
	.themescontainer .span4{
		width: 30%;
		margin-left: 3%;
	}
	@media (max-width: 800px) {
	.themescontainer .span4{
		width: 100%;
	}
}
	.themethumbs{
		width: 100%;
		height: auto;
	}
	
	.globalapplytooltip{
		padding: 5px;
	    padding-bottom: 0px;
	    padding-top: 0px;
	    border-radius: 40%;
	    background-color: rgba(0,0,0,0.3);
	    font-size: smaller;
	}
	
	.licensebox{
		background-color: #5bb75b;
		border-radius: 5px;
		padding: 5px;
		color: white !important;
	}
	
	.myclogo{
		max-width: 80px;
		height: auto;
		
		margin-right:5px;
	}
	.myctsheader{
		padding-top: 10px;    height: 80px;
	}
	.myctsheader h1{
		line-height: 40px;
	}
</style>


<div class="mycthemeswitchercnt" >
		<div class="myctsheader text-center" style="height:100px;width: 100%">
			
			<h1 class=""><img class="myclogo" src="layouts/v7/modules/OmniThemeManager/images/MYCThemeSwitcher.png">MYC Theme Switcher
			</h1>
	</div>
			
			
			<br>
			<div class="container themescontainer">
			{if $MCRYPT_ERROR}
				<div class="alert alert-danger"><b>PHP-MCRYPT EXTENSION NOT FOUND!</b><br> The <b>php-mcrypt</b> extension is <b>not loaded</b> on this server, please setup and load it to use the MYC Theme Switcher module, after you installed and loaded correctly the php-mcrypt extension this error will disappear, you need then to disable and enable again the MYC Theme Switcher module from the Module Manager to complete the setup! </div>
			{/if}
			<div class="alert alert-danger" id="errormsg" style="display:none;">Error</div>
				<div class="row">
					
					{foreach key=Index item=LAYOUT from=$AVAILABLE_LAYOUTS}
					<div class="col-sm-12 col-md-4">
					  <div class="themecard">	
						<h3>{$LAYOUT['label']} {if $SELECTED_LAYOUTUID eq $LAYOUT["layoutuid"]}&nbsp;&nbsp;<span class="label label-success">Selected</span>{/if}{if $FORCED_LAYOUTUID eq $LAYOUT["layoutuid"]}&nbsp;&nbsp;<span class="label label-warning">Forced</span>{/if}</h3>
						<img src="modules/OmniThemeManager/themesthumbs/{$LAYOUT['name']}.jpg" class="themethumbs"><br>
						<input type="hidden" id="{$LAYOUT['name']}-layoutuid" value="{$LAYOUT['layoutuid']}">
						<div class="clearfix"></div>
						<br>
						{if $SELECTED_LAYOUTUID neq $LAYOUT["layoutuid"]}
							{if $USER_MODEL->isAdminUser()}
								<a class="btn btn-warning pull-left" onclick="applyTheme('{$LAYOUT["name"]}',false)">Apply</a>
								<a class="btn btn-success  pull-right" onclick="applyTheme('{$LAYOUT["name"]}',true)">Apply Globally&nbsp;&nbsp;<b class="globalapplytooltip" title="This will apply the selected layout for all the Users of this CRM">?</b></a>
							{else}
								<a class="btn btn-warning " onclick="applyTheme('{$LAYOUT["name"]}',false)">Apply</a>								{/if}
						{else}
							{if $USER_MODEL->isAdminUser()}
								<a class="btn btn-success " onclick="applyTheme('{$LAYOUT["name"]}',true)">Apply Globally&nbsp;&nbsp;<b class="globalapplytooltip" title="This will apply the selected layout for all the Users of this CRM">?</b></a>
								
							{/if}
						{/if}
						{if $USER_MODEL->isAdminUser()}
						<br><br>
						{if $FORCED_LAYOUTUID eq $LAYOUT["layoutuid"]}
						
						<a class="btn btn-danger" onclick="removeGlobalLayoutBlock('{$LAYOUT["name"]}')">Remove Global Theme Block&nbsp;&nbsp;<b class="globalapplytooltip" title="This will remove the selected layout block for all the Users of this CRM">?</b></a>

						{else}
						<a class="btn btn-warning" onclick="forceTheme('{$LAYOUT["name"]}')">Force Theme for all Users&nbsp;&nbsp;<b class="globalapplytooltip" title="This will force the selected layout for all the Users of this CRM">?</b></a>
						
						{/if}
						{/if}
						
						<div class="clearfix"></div>
						
						
						<br>					
					  </div>
					  <div class="clearfix"></div>
					</div>
					{/foreach}
				</div>
			</div>
			
			{literal}
			<script>
				
				$(function(){
					//$(".globalapplytooltip").tooltip();
				});
				
				function applyTheme(themename,applyglobally){
					
					
					if(applyglobally) {
						var cr = confirm("Are you sure you want apply this layour for ALL users in this crm ?");
						if (cr == true) var ajmode="setLayoutForAllUsers";
						else return false;
					}
					else var ajmode="setLayoutForCurrentUser";
					
					var params = {
						'module' : 'OmniThemeManager',
						'action' : 'AjaxActions',
						'mode' : ajmode,
						'layoutuid'	: $("#"+themename+"-layoutuid").val()
					}
					$.post("index.php",params).then(function(data) {
						console.log(data);
						
						if(data.result.success) window.location.reload();						
						else {							
							var errstring="";
							for(var m=0;m<data.result.messages.length;m++){
								var me=m+1;
								errstring+=me+") "+data.result.messages[m]+" <br>";
							}
							$("#errormsg").html("There was some error doing the requested operation! The following are the error details: <br>"+errstring);							
							$("#errormsg").show();
						}
						
					},
					function(error,err){
						console.log(error);
					});
				}
				
				function forceTheme(themename){
					
					
					
						var cr = confirm("Are you sure you want apply this layour for ALL users in this crm ?");
						if (cr == true) var ajmode="blockLayoutGlobally";
						else return false;
					
					
					var params = {
						'module' : 'OmniThemeManager',
						'action' : 'AjaxActions',
						'mode' : ajmode,
						'layoutuid'	: $("#"+themename+"-layoutuid").val()
					}
					$.post("index.php",params).then(function(data) {
						console.log(data);
						
						if(data.result.success) window.location.reload();						
						else {							
							var errstring="";
							for(var m=0;m<data.result.messages.length;m++){
								var me=m+1;
								errstring+=me+") "+data.result.messages[m]+" <br>";
							}
							$("#errormsg").html("There was some error doing the requested operation! The following are the error details: <br>"+errstring);							
							$("#errormsg").show();
						}
						
					},
					function(error,err){
						console.log(error);
					});
				}
				
				function removeGlobalLayoutBlock(themename){

					
					
						var cr = confirm("Are you sure you want remove the layout block for ALL users in this crm ?");
						if (cr == true) var ajmode="removeGlobalLayoutBlock";
						else return false;
					
					
					var params = {
						'module' : 'OmniThemeManager',
						'action' : 'AjaxActions',
						'mode' : ajmode,
					}
					$.post("index.php",params).then(function(data) {
						console.log(data);
						
						if(data.result.success) window.location.reload();						
						else {							
							var errstring="";
							for(var m=0;m<data.result.messages.length;m++){
								var me=m+1;
								errstring+=me+") "+data.result.messages[m]+" <br>";
							}
							$("#errormsg").html("There was some error doing the requested operation! The following are the error details: <br>"+errstring);							
							$("#errormsg").show();
						}
						
					},
					function(error,err){
						console.log(error);
					});
				}
				
			</script>
			{/literal}
</div>
