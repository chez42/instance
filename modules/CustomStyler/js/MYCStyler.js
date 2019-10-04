var themePresets = {};

var themePresetsDefault = {};

var tmpEditParams = {};

var defaultParams = {
	
	"theme-name": "Default",
	
	"font-name": "Open Sans",
	"font-zoom": 0,
		
	"topbar-color": "#FFFFFF",
	"topbar-font-color": "#000000",
	
	"menu-style": "top-menu-dropdown",
	"menu-color": "#FFFFFF",
	"menu-font-color": "#000000",
	"menu-active-font-color": "#000000",
	
	"container-color": "#FFFFFF",

};

var defaultStyleParams = {	
	"theme-name": "",
	"menu-style": "top-menu-dropdown",
	"font-name": "Open Sans",
	"font-zoom": 0,
	"topbar-color": "#FFFFFF",
	"topbar-font-color": "#000000",
	"menu-style": "top-menu-dropdown",
	"menu-color": "#FFFFFF",
	"menu-font-color": "#000000",
	"menu-active-font-color": "#000000",
	"container-color": "#FFFFFF",
	"border-radius": "0",
};

var currentStyle = "";

var currentUser = "";

var isAdminUser = "";

function getCurrentUserStyle(){
	
	var params = {
		'module' : 'CustomStyler',
		'action' : 'AjaxActions',
		'mode' : "getStyleForCurrentUser"
	}
	
	$.post("index.php",params).then(function(data) {
		
		if(data.result.success) {
			
			currentStyle = data.result.style;
			
			currentUser = data.result.user;
			
			isAdminUser = data.result.isAdmin;
			
			loadThemePresets();	
			
			loadThemePresetsMYC();	
			
			return currentStyle;
			
		} else {	
		
			var errstring="";
			
			for(var m = 0 ;m < data.result.messages.length; m++){
				var me = m+1;
				errstring+=me+") "+data.result.messages[m]+" <br>";
			}
			
			$("#errormsg").html("There was some error doing the requested operation! The following are the error details: <br>"+errstring);							
			$("#errormsg").show();
		}
	},
	function(error,err){});
}

function applyPresetStyle(themeName){
	
	console.log("applyPresetStyle");
	
	if(themePresets[themeName] === undefined) defaultParams = themePresetsDefault[themeName];
	else defaultParams = themePresets[themeName];
	
	defaultParams["isApplied"]=true;
	
	var urlParams = encodeURIComponent(btoa(JSON.stringify(defaultParams)));
	
	$("#mycCustomStyle").attr("href","index.php?module=CustomStyler&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
	
	
	var themePresetsNew = {};
	themePresetsNew["save"]=true;
	themePresetsNew["presetparams"]=defaultParams;
	themePresetsNew["presetKey"]=themeName;
	themePresetsNew["isApplied"]=true;

	
}	


function applyThemeForUser(themeName,applyglobally){
	
	console.log("applyThemeForUser");				
	
	if(themePresets[themeName] === undefined) 
		defaultParams = themePresetsDefault[themeName];
	else 
		defaultParams = themePresets[themeName];
	
	defaultParams["isApplied"] = true;
	
	var urlParams = encodeURIComponent(btoa(JSON.stringify(defaultParams)));
	$("#mycCustomStyle").attr("href","index.php?module=CustomStyler&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
				
	if(applyglobally) {
		var cr = confirm("Are you sure you want apply this style for ALL users in this crm ?");
		if (cr == true) var ajmode="setStyleForAllUsers";
		else return false;
	}
	else var ajmode="setStyleForCurrentUser";
					
	var params = {
		'module' : 'CustomStyler',
		'action' : 'AjaxActions',
		'mode' : ajmode,
		'styleid'	: themeName
	}
	
	app.helper.showProgress();
	
	$.post("index.php",params).then(function(data) {
		selectStyleUi(themeName);
		app.helper.hideProgress();
		app.helper.showSuccessNotification({"message":'Style successfuly applied!'});
		
	},
	function(error,err){});
	
}


	
function updateParam(paramName,newValue){
	
	tmpEditParams[paramName] = newValue;
	
	var urlParams = encodeURIComponent(btoa(JSON.stringify(tmpEditParams)));
	
	$("#mycCustomStyle").attr("href","index.php?module=CustomStyler&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
}


function addNewStyle(){
	
	tmpEditParams = defaultStyleParams;
	
	$("#presetKey").val("");
	
	for(param in defaultStyleParams){
		
		if($(".addPresetStyle input[name='"+param+"']").hasClass("pick-a-color")){
			$(".addPresetStyle input[name='"+param+"']").val(tmpEditParams[param].substring(1));
		} else if($(".addPresetStyle input[name='"+param+"']").attr("type")=="checkbox"){
			if(tmpEditParams[param]) 
				$(".addPresetStyle input[name='"+param+"']").prop("checked",true);
			else $(".addPresetStyle input[name='"+param+"']").prop("checked",false);
		} else $(".addPresetStyle [name='"+param+"']").val(tmpEditParams[param]);
		
		updateParam(param,tmpEditParams[param]);
	}
	
	$('.addPresetStyle').show(); 
	$('.presetsListContainer').hide();
}

function updateParamsFromEdit(){
	for(param in defaultStyleParams){
		if($(".addPresetStyle input[name='"+param+"']").hasClass("pick-a-color")){
			defaultParams[param] = "#"+$(".addPresetStyle input[name='"+param+"']").val();
		}
		else if($(".addPresetStyle input[name='"+param+"']").attr("type")=="checkbox"){
			defaultParams[param] = $(this).is(':checked');
		}
		else defaultParams[param] = $(".addPresetStyle [name='"+param+"']").val();
		
		updateParam(param,defaultParams[param]);
		//$(".addPresetStyle input[name='"+param+"']").val("");
	}
}


function string_to_slug (str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();
  
    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaeeeeiiiioooouuuunc------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}

function saveCustomStyle(){
	
	if($(".addPresetStyle input[name='theme-name']").val()==""){
		alert("You must chose a theme name!");
		return false;
	}
	
	app.helper.showProgress();
	$('.addPresetStyle').hide(); $('.presetsListContainer').show();
	
	var themePresetsNew = {};
	themePresetsNew["save"]=true;
	
	
	if($("#presetKey").val()!=""){ 
		var newStyleName = $("#presetKey").val(); 
		themePresetsNew["presetparams"]=themePresets[newStyleName];
	}
	else{
		//var newStyleName = Object.keys(themePresets).length+1; //string_to_slug($(".addPresetStyle input[name='theme-name']").val());
		themePresetsNew["presetparams"]=defaultParams;
	}
	
	themePresetsNew["presetKey"]=newStyleName;
	
	
	for(param in defaultStyleParams){
		if($(".addPresetStyle input[name='"+param+"']").hasClass("pick-a-color")){
			themePresetsNew["presetparams"][param] = "#"+$(".addPresetStyle input[name='"+param+"']").val();
		}
		else if($(".addPresetStyle input[name='"+param+"']").attr("type")=="checkbox"){
			themePresetsNew["presetparams"][param] = $(this).is(':checked');
		}
		else themePresetsNew["presetparams"][param] = $(".addPresetStyle [name='"+param+"']").val();
		$(".addPresetStyle [name='"+param+"']").val("");
	}
	themePresetsNew["presetparams"]["isApplied"]=true;
	themePresetsNew["isApplied"]=true;
	
	
	var params = {
		'module' : 'CustomStyler',
		'action' : 'AjaxActions',
		'mode' : 'saveStylePreset',
		"presetparams"	: themePresetsNew["presetparams"],
		"presetKey"	: themePresetsNew["presetKey"]
	}
	$.post("index.php",params).then(function(data) {
			loadThemePresets(true);
			app.helper.hideProgress();
			app.helper.showSuccessNotification({"message":'Style successfuly saved!'});
		},
		function(error,err){
			console.log(error);
	});
	
	/*
	$.post( "stylePresets.php", themePresetsNew)
	  .done(function( data ) {
		loadThemePresets(true);
	    $('.addPresetStyle').hide(); $('.chosePresetStyle').show();
	});
	*/
  
}

function deleteStyle(styleName){
	
	var r = confirm("Are you sure you want to delete the style \""+themePresets[styleName]["theme-name"]+"\" ?");
	if (r == true) {
		app.helper.showProgress();
		var themePresetsNew = {};
		themePresetsNew["delete"]=true;
		themePresetsNew["presetKey"]=styleName;
		
		var params = {
			'module' : 'CustomStyler',
			'action' : 'AjaxActions',
			'mode' : 'deleteStylePreset',
			"presetKey"	: themePresetsNew["presetKey"]
		}
		$.post("index.php",params).then(function(data) {
				loadThemePresets();
				app.helper.hideProgress();
				app.helper.showSuccessNotification({"message":'Style successfuly deleted!'});
				$('.addPresetStyle').hide(); $('.presetsListContainer').show();
			},
			function(error,err){
				console.log(error);
		});
		/*
		$.post( "stylePresets.php", themePresetsNew)
		  .done(function( data ) {
			loadThemePresets();
		    $('.addPresetStyle').hide(); $('.chosePresetStyle').show();
		});
		*/
	}
}

function selectStyleUi(themeName){
	$(".styleOption").removeClass("selected");
	$(".styleOption .applyStyleBtn").removeClass("disabled");
						
	var selectedDiv = $(".styleOption input[value='"+themeName+"']").closest(".styleOption");
	selectedDiv.addClass("selected");
	selectedDiv.find(".applyStyleBtn").addClass("disabled");
}

function editStyle(styleName){
	
	selectStyleUi(styleName);
	tmpEditParams = themePresets[styleName];
	$("#presetKey").val(styleName);
	for(param in themePresets[styleName]){
		if($(".addPresetStyle input[name='"+param+"']").hasClass("pick-a-color")){
			$(".addPresetStyle input[name='"+param+"']").val(themePresets[styleName][param].substring(1));
		}
		else if($(".addPresetStyle input[name='"+param+"']").attr("type")=="checkbox"){
			if(themePresets[styleName][param]) 
				$(".addPresetStyle input[name='"+param+"']").prop("checked",true);
			else $(".addPresetStyle input[name='"+param+"']").prop("checked",false);
		}
		else $(".addPresetStyle [name='"+param+"']").val(themePresets[styleName][param]);
		//$(".addPresetStyle input[name='"+param+"']").val("");
		
		updateParam(param,themePresets[styleName][param]);
	}
	
	$('.addPresetStyle').show(); $('.presetsListContainer').hide();
}

function duplicateStyle(styleName){
	
	selectStyleUi(styleName);
	if(themePresets[styleName] === undefined) tmpEditParams = themePresetsDefault[styleName];
	else tmpEditParams = themePresets[styleName];
	
	console.log(themePresetsDefault);
	console.log(tmpEditParams);
	
	$("#presetKey").val(styleName);
	$("#presetKey").val("");
	
		
	for(param in tmpEditParams){
		if(param=="theme-name") tmpEditParams[param] = "Copy of "+tmpEditParams["theme-name"];
		
		if($(".addPresetStyle input[name='"+param+"']").hasClass("pick-a-color")){
			$(".addPresetStyle input[name='"+param+"']").val(tmpEditParams[param].substring(1));
		}
		else if($(".addPresetStyle input[name='"+param+"']").attr("type")=="checkbox"){
			if(tmpEditParams[param]) 
				$(".addPresetStyle input[name='"+param+"']").prop("checked",true);
			else $(".addPresetStyle input[name='"+param+"']").prop("checked",false);
		}
		else $(".addPresetStyle [name='"+param+"']").val(tmpEditParams[param]);
		//$(".addPresetStyle input[name='"+param+"']").val("");
		
		updateParam(param,tmpEditParams[param]);
	}
	
	$('.addPresetStyle').show(); $('.presetsListContainer').hide();
}

function loadThemePresets(refresh){
	
	var presetFile = "index.php?module=CustomStyler&view=CustomStyle&mode=getStylePresets";
	
	$.getJSON(presetFile, function( data ) {
		
		themePresets = data;
			
		$(".chosePresetStyle .presets").html("");
		
		for(var themePreset in themePresets){
			var selected = "";
			var checked = "";
			var disabled = "";
			var currentStyle = themePresets[themePreset]['currentStyle'];
			if(themePreset == currentStyle){
				var selected = "selected";
				var checked = 'checked="checked"';
				var disabled = "disabled";
				if(refresh===true)
					applyPresetStyle(currentStyle);
			}
			
			var editable = true;
			
			if(parseFloat(themePresets[themePreset]["owner"])!=parseFloat(themePresets[themePreset]["current_user"])) editable = false;
			if(themePresets[themePreset]["isadmin"]) editable = true;
			
			var htmlOption = '<div class="styleOption radio '+selected+'"><label class="pull-left">'+themePresets[themePreset]["theme-name"]+'<input type="radio" '+checked+' value="'+themePreset+'" name="preset-style" class="form-control presetStyle"><br><div class="previewSyleColors"><div class="cl1" style="background:'+themePresets[themePreset]["topbar-color"]+'"></div><div class="cl2"  style="background:'+themePresets[themePreset]["menu-color"]+'"></div><div class="cl3"  style="background:'+themePresets[themePreset]["container-color"]+'"></div></div></label><div class="pull-right">'+
			'<a class="btn btn-success btn-xs applyStyleBtn '+disabled+'"  onclick="applyThemeForUser(\''+themePreset+'\',false)" tippytitle data-tippy-content="Apply"><i class="fa fa-check"></i></a>&nbsp;';
			
			if(editable)
				var htmlOption = htmlOption+'<a class="btn btn-info btn-xs"  tippytitle data-tippy-content="Edit" onclick="editStyle(\''+themePreset+'\')"><i class="fa fa-edit"></i></a>&nbsp;';
			
			
			if(editable)
				var htmlOption = htmlOption+'<a class="btn btn-danger btn-xs"  tippytitle data-tippy-content="Delete" onclick="deleteStyle(\''+themePreset+'\')"><i class="fa fa-trash"></i></a>';
			
			var htmlOption = htmlOption+'</div><div class="clearfix">&nbsp;</div></div>';
			$(".chosePresetStyle .presets").append(htmlOption);
			
	
			
	    }
	    
	    $('.stylerUi .chosePresetStyle input').change(function() {
		    $('.stylerUi .radio').removeClass("selected");
			$(this).parent().parent().addClass("selected");
			applyPresetStyle($(this).val());
	    });
	   	    
	});
    
}


function loadThemePresetsMYC(refresh){
	
	var presetFile = "index.php?module=CustomStyler&action=AjaxActions&mode=getMYCStylePresets";
	
	$.getJSON(presetFile, function( data ) {
		
		themePresetsDefault = data;
		
		$(".chosePresetStyleMYC .presets").html("");
		
		for(var themePreset in themePresetsDefault){
			var selected = "";
			var checked = "";
			var disabled = "";
			var currentStyle = themePresetsDefault[themePreset]['currentStyle'];
			if(themePreset == currentStyle){
				var selected = "selected";
				var checked = 'checked="checked"';
				var disabled = "disabled";
				if(refresh===true)
					applyPresetStyle(currentStyle);
			}
			
			var htmlOption = '<div class="styleOption radio '+selected+'"><label class="pull-left">'+themePresetsDefault[themePreset]["theme-name"]+'<input type="radio" '+checked+' value="'+themePreset+'" name="preset-style" class="form-control presetStyle"><br><div class="previewSyleColors"><div class="cl1" style="background:'+themePresetsDefault[themePreset]["topbar-color"]+'"></div><div class="cl2"  style="background:'+themePresetsDefault[themePreset]["menu-color"]+'"></div><div class="cl3"  style="background:'+themePresetsDefault[themePreset]["container-color"]+'"></div></div></label><div class="pull-right">'+
			'<a class="btn btn-success btn-xs applyStyleBtn '+disabled+'"  onclick="applyThemeForUser(\''+themePreset+'\',false)" tippytitle data-tippy-content="Apply"><i class="fa fa-check"></i></a>&nbsp;'+
			'&nbsp;'+
			'</div><div class="clearfix">&nbsp;</div></div>';
			$(".chosePresetStyleMYC .presets").append(htmlOption);
		
		}
	    
	    $('.stylerUi .chosePresetStyleMYC input').change(function() {
		    $('.stylerUi .radio').removeClass("selected");
			$(this).parent().parent().addClass("selected");
			applyPresetStyle($(this).val());
	    });
	   	    
	});
    
}

$(function(){
	
	$("body").append('<style>.stylerUi{position:fixed;right:0;bottom:0;width:450px;height:calc(100vh - 43px);background-color:#fff;z-index:9999;padding:20px;overflow:auto;box-shadow:0 5px 10px 0 #555;padding-left:0;padding-right:0;padding-top:0}.styleOption{padding:20px;border-bottom:1px solid #d3d3d3;margin-top:0!important;margin-bottom:0;padding-top:10px;padding-bottom:10px}#fontPreview{max-height:150px;overflow:auto}.chosePresetStyle .radio input,.chosePresetStyleMYC .radio input{display:none}.chosePresetStyle .radio label,.chosePresetStyleMYC .radio label{width:55%;padding-left:0}.stylerUi .radio.selected{background-color:#d3d3d3}.pick-a-color{height:33px}.styleOption .helpText{width:100%;font-size:12px}.styleOption .helpText a{color:red;text-decoration:underline!important}.presetsTypeTab{padding:0}.presetsTypeTab .btn.active{background:rgba(44,59,73,.73);color:#fff}.presetsTypeTab .btn{border-radius:0!important}.previewSyleColors div{height:10px;width:30%;float:left;margin-left:2px;border:1px solid #d3d3d3}.styleOption>div.pull-right{margin-top:5px}</style><div class="stylerUi" style="display: none"><div class="presetsListContainer"><div class="presetsTypeTab"><div class="btn-group btn-group-justified" role="group" aria-label="..."><a class="btn btn-default btn-lg" onclick="'+"$('.chosePresetStyle').hide();$('.chosePresetStyleMYC').show(); $('.presetsTypeTab .btn').removeClass('active'); $(this).addClass('active')"+'">Default Styles</a><a class="btn btn-default btn-lg active" onclick="'+"$('.chosePresetStyleMYC').hide();$('.chosePresetStyle').show(); $('.presetsTypeTab .btn').removeClass('active'); $(this).addClass('active')"+'">Custom Styles</a></div></div><div class="chosePresetStyleMYC" style="display: none"><div class="presets"></div></div><div class="chosePresetStyle"><div class="presets"></div><div class="styleOption text-center"><a class="btn btn-primary" onclick="addNewStyle();">Add Custom Style</a></div></div></div><div class="addPresetStyle" style="display: none"><input type="hidden" id="presetKey" name="presetKey"><div class="styleOption text-center"><a class="btn btn-default" onclick="loadThemePresets(true); '+"$('.addPresetStyle').hide(); $('.presetsListContainer').show();"+'"'+'>Close</a>&nbsp;<a class="btn btn-success" onclick="saveCustomStyle()">Save</a></div><div class="styleOption"><label>Theme Name</label><input type="text" value="" class="themeName form-control" name="theme-name" ></div><div class="styleOption"><label>Theme Font</label><div id="fontPreview"></div><input type="text" value="Open Sans" name="font-name" class="form-control textParam"><p class="helpText">Choose one of the hundreds fonts available on Google Fonts, please visit the following url <a href="https://fonts.google.com/" target="_blank">https://fonts.google.com/</a> copy and paste here the full name of the font including spaces and uppercase letters.</p></div><div class="styleOption"><label>Theme Font Zoom</label><input type="range" value="0" name="font-zoom" class="form-control textParam" min="-3" max="3" step="1"></div><div class="styleOption"><label>Top Bar Color</label><input type="text" value="f5f5f5" name="topbar-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Top Bar Font Color</label><input type="text" value="6b6b6b" name="topbar-font-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Menu Color</label><input type="text" value="FFFFFF" name="menu-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Menu Font Color</label><input type="text" value="000000" name="menu-font-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Menu Active Font Color</label><input type="text" value="000000" name="menu-active-font-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Page Color</label><input type="text" value="FFFFFF" name="container-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Round Borders</label><input type="range" value="0" name="border-radius" class="form-control textParam" min="0" max="25" step="5"><div class="clearfix"></div></div><div class="advancedOptions hide"><div class="styleOption"><label>Field Labels Color</label><input type="text" value="fafafa" name="field-labels-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Field Labels Font Color</label><input type="text" value="6f6f6f" name="field-labels-font-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Field Value Color</label><input type="text" value="ffffff" name="field-value-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Field Value Font Color</label><input type="text" value="444444" name="field-value-font-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption"><label>Field Border Color</label><input type="text" value="dddddd" name="field-border-color" class="pick-a-color form-control"><div class="clearfix"></div></div><div class="styleOption text-center"><a class="btn btn-info" href="'+"javascript:$('.advancedOptions, .showAdvancedOptions').toggleClass('hide')"+'">Hide Advanced Options <i class="fa fa-minus"></i></a></div></div><div class="showAdvancedOptions styleOption text-center"><a class="btn btn-info" href="'+"javascript:$('.advancedOptions, .showAdvancedOptions').toggleClass('hide')"+'">Advanced Options <i class="fa fa-plus"></i></a></div><div class="styleOption text-center"><a class="btn btn-default" onclick="'+"loadThemePresets(true); $('.addPresetStyle').hide(); $('.presetsListContainer').show();"+'">Close</a>&nbsp;<a class="btn btn-success" onclick="saveCustomStyle()">Save</a></div></div></div><link type="text/css" rel="stylesheet" id="mycCustomStyle" href="index.php?module=CustomStyler&view=CustomStyle&mode=getCSSForCurrentUser" media="screen">');
	
	getCurrentUserStyle();
    
    $("#navbar > ul").prepend('<li><div><a class="themeStyler fa fa-paint-brush" title="MYC Styler" aria-hidden="true"></a></div></li>');
    
	$(".themeStyler").click(function(){
		$(".stylerUi").toggle();
	});
	
	$(".stylerUi .pick-a-color").pickAColor({
		inlineDropdown: true
	});
	
	$(".stylerUi .pick-a-color").on("change", function () {
	  updateParam($(this).attr("name"),"#"+$(this).val())
	});
	
	$(".stylerUi select").on("change", function () {
	  updateParam($(this).attr("name"),$(this).val())
	});
	
	$('.stylerUi .switchField').change(function() {
		updateParam($(this).attr('name'),$(this).is(':checked'))
    });
    
    $('.stylerUi .textParam').change(function() {
		updateParam($(this).attr('name'),$(this).val())
    });
    
    
    $(".quickTopButtons button, .navbar-nav > li, .app-navigator-container button").on("click",function(){
		if(!$(this).find(".themeStyler").length)
			$(".stylerUi").hide();
	});
    
	$("#detailView .fieldLabel").each(function() {
		if($(this).is(":visible"))
	 		$(this).attr("style",'min-height:'+$(this).next(".fieldValue").outerHeight()+'px !important');
	});
   
});