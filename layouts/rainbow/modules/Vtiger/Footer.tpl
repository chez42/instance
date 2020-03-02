{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}


</div>

<style>
	.stylerUi{
		position: fixed;
		right: 0;
		bottom: 0;
		width: 450px;
		height: calc(100vh - 63px);
		background-color: white;
		z-index: 9999;
		padding: 20px;
		overflow: auto;
		box-shadow: 0px 5px 10px 0px #555;
		padding-left: 0px;
		padding-right: 0px;
		padding-top: 0px;
	}
	.styleOption{
		padding: 20px;
		border-bottom: 1px solid lightgray;
		margin-top: 0px !important;
		margin-bottom: 0px;
		padding-top: 10px;
		padding-bottom: 10px;
	}
	
	#fontPreview{
		max-height: 150px;
		overflow: auto;
	}	
	.chosePresetStyle .radio input, .chosePresetStyleMYC .radio input{
		display: none;
	}
	
	.chosePresetStyle .radio label, .chosePresetStyleMYC .radio label{
		width: 55%;
		padding-left: 0px;
	}
	.stylerUi .radio.selected{
		background-color: lightgray;
	}
	.pick-a-color{
		height: 33px;
	}
	.styleOption .helpText{
		width: 100%;
		font-size: 12px;
	}
	.styleOption .helpText a{
		color: red;
		text-decoration: underline !important;
	}	
	.presetsTypeTab{
		padding: 0px;
	}
	.presetsTypeTab .btn.active {
	    background: rgba(44, 59, 73, 0.73);
	    color: #fff;
	}
	.presetsTypeTab .btn{
	    border-radius: 0px !important;
	}
	.previewSyleColors div{
		height: 10px;
		width: 30%;
		float: left;
		margin-left: 2px;
		border: 1px solid lightgray;
	}
	.styleOption > div.pull-right{
		margin-top: 5px;
	}

</style>
<div class="stylerUi" style="display: none">
	
	<div class="presetsListContainer">
		<div class="presetsTypeTab">
			<div class="btn-group btn-group-justified" role="group" aria-label="...">
			<a class="btn btn-default btn-lg" onclick="$('.chosePresetStyle').hide();$('.chosePresetStyleMYC').show(); $('.presetsTypeTab .btn').removeClass('active'); $(this).addClass('active')">Default Styles</a>
			<a class="btn btn-default btn-lg active"  onclick="$('.chosePresetStyleMYC').hide();$('.chosePresetStyle').show(); $('.presetsTypeTab .btn').removeClass('active'); $(this).addClass('active')">Custom Styles</a>
			</div>
		</div>
		
		<div class="chosePresetStyleMYC" style="display: none">
			<div class="presets"></div>
		</div>
		
		<div class="chosePresetStyle">
			<div class="presets"></div>
			<div class="styleOption text-center">
				<a class="btn btn-primary" onclick="addNewStyle();">Add Custom Style</a>
			</div>
		</div>
	</div>
	<div class="addPresetStyle" style="display: none">
		<input type="hidden" id="presetKey" name="presetKey">
		<div class="styleOption text-center">
			<a class="btn btn-default" onclick="loadThemePresets(true); $('.addPresetStyle').hide(); $('.presetsListContainer').show();">Close</a>&nbsp;
			<a class="btn btn-success" onclick="saveCustomStyle()">Save</a>
		</div>
		<div class="styleOption">
			<label>Theme Name</label>
			<input type="text" value="" class="themeName form-control" name="theme-name" >
		</div>
		<div class="styleOption">
			<label>Theme Font</label>
			<div id="fontPreview"></div>
			<input type="text" value="Roboto Condensed" name="font-name" class="form-control textParam"><p class="helpText">
			Choose one of the hundreds fonts available on Google Fonts, please visit the following url <a href="https://fonts.google.com/" target="_blank">https://fonts.google.com/</a> copy and paste here the full name of the font including spaces and uppercase letters.</p>
		</div>
		<div class="styleOption">
			<label>Theme Font Zoom</label>
			<input type="range" value="0" name="font-zoom" class="form-control textParam" min="-3" max="3" step="1">
		</div>

		<div class="styleOption">
			<label>Top Bar Color</label>
			<input type="text" value="f5f5f5" name="topbar-color" class="pick-a-color form-control">
			<div class="clearfix"></div>
		</div>
		<div class="styleOption">
			<label>Top Bar Font Color</label>
			<input type="text" value="6b6b6b" name="topbar-font-color" class="pick-a-color form-control">
			<div class="clearfix"></div>
		</div>
		<div class="styleOption">
			<label>Menu Style</label>
			<select class="form-control" name="menu-style">
				<option value="top-menu-dropdown">Top Menu Dropdown</option>
				<option value="sidebar-menu">Sidebar Menu</option>
			</select>
		</div>
		<div class="styleOption">
			<label>Menu Color</label>
			<input type="text" value="FFFFFF" name="menu-color" class="pick-a-color form-control">
			<div class="clearfix"></div>
		</div>
		<div class="styleOption">
			<label>Menu Font Color</label>
			<input type="text" value="000000" name="menu-font-color" class="pick-a-color form-control">
			<div class="clearfix"></div>
		</div>
		<div class="styleOption">
			<label>Page Color</label>
			<input type="text" value="FFFFFF" name="container-color" class="pick-a-color form-control">
			<div class="clearfix"></div>
		</div>
		<div class="styleOption">
			<label>Round Borders</label>
			<input type="range" value="0" name="border-radius" class="form-control textParam" min="0" max="25" step="5">
			<div class="clearfix"></div>
		</div>
		
		<div class="advancedOptions hide">
			<div class="styleOption">
				<label>Field Labels Color</label>
				<input type="text" value="fafafa" name="field-labels-color" class="pick-a-color form-control">
				<div class="clearfix"></div>
			</div>
			<div class="styleOption">
				<label>Field Labels Font Color</label>
				<input type="text" value="6f6f6f" name="field-labels-font-color" class="pick-a-color form-control">
				<div class="clearfix"></div>
			</div>
			<div class="styleOption">
				<label>Field Value Color</label>
				<input type="text" value="ffffff" name="field-value-color" class="pick-a-color form-control">
				<div class="clearfix"></div>
			</div>
			<div class="styleOption">
				<label>Field Value Font Color</label>
				<input type="text" value="444444" name="field-value-font-color" class="pick-a-color form-control">
				<div class="clearfix"></div>
			</div>
			<div class="styleOption">
				<label>Field Border Color</label>
				<input type="text" value="dddddd" name="field-border-color" class="pick-a-color form-control">
				<div class="clearfix"></div>
			</div>
			<div class="styleOption text-center">
				<a class="btn btn-info" onclick="toggleAdvancedStyleOptions()">Hide Advanced Options <i class="material-icons">remove</i></a>
			</div>
		</div>
		
		<div class="showAdvancedOptions styleOption text-center">
			<a class="btn btn-info" onclick="toggleAdvancedStyleOptions()">Advanced Options <i class="material-icons">add</i></a>
		</div>
		
		
		<div class="styleOption text-center">
			<a class="btn btn-default" onclick="loadThemePresets(true); $('.addPresetStyle').hide(); $('.presetsListContainer').show();">Close</a>&nbsp;
			<a class="btn btn-success" onclick="saveCustomStyle()">Save</a>
		</div>
		
	</div>
</div>
<footer class="app-footer">
</footer>

<div id='overlayPage'>
	<!-- arrow is added to point arrow to the clicked element (Ex:- TaskManagement), 
	any one can use this by adding "show" class to it -->
	<div class='arrow'></div>
	<div class='data'>
	</div>
</div>
<div id='helpPageOverlay'></div>
<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>
<div class="modal myModal fade"></div>

{include file='JSResources.tpl'|@vtemplate_path}

<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
<script type="text/javascript" src="layouts/rainbow/lib/pick-a-color/tinycolor-0.9.15.min.js"></script>
<script type="text/javascript" src="layouts/rainbow/lib/pick-a-color/pick-a-color.js"></script>
<script type="text/javascript" src="layouts/rainbow/modules/Vtiger/resources/ThemeStyle.js"></script>

<script>
	tippy('[tippytitle]',{
	  placement: 'top',
	  animation: 'shift-toward',
	  inertia: true,
	  duration: 600,
	  arrow: true,
	  //arrowtype: "round",
	  //arrowtransform: "scale(0.7, 1)",	  
	  theme: "fask"
	});
	/*
	$(function(){
		var prevLeft = 0;
		var prevTop = 0;
		
		var scrollingVert = false;
		var scrollingHor = false;
		    
		$(".main-container *").scroll( function(evt) {
			return true;
			clearTimeout($.data(this, 'scrollTimer'));
		    $.data(this, 'scrollTimer', setTimeout(function() {
		        scrollingVert = false;
		        scrollingHor = false;
		        $(".main-container *").removeClass("stop-hor-scrol");
		        $(".main-container *").removeClass("stop-ver-scrol");	
		    }, 1000));
		    
		    var currentLeft = $(this).scrollLeft();
		    var currentTop = $(this).scrollTop();
		    
		    if(prevLeft != currentLeft) {
			    $(".main-container *").addClass("stop-ver-scrol");	
		        prevLeft = currentLeft;
		        console.log("I scrolled horizontally.");
		    }
		    
		    if(prevTop !== currentTop) {	
			    $(".main-container *").addClass("stop-hor-scrol");		    
		        prevTop = currentTop;
		        scrollingVert = true;
		        console.log("I scrolled vertically.");
		    }
		    
		    
		    
		});
	})
	*/
</script>
</body>
</html>