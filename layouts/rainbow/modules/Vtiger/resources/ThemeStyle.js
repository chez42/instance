/*!
    Autosize v1.18.1 - 2013-11-05
	Automatically adjust textarea height based on user input.
	(c) 2013 Jack Moore - http://www.jacklmoore.com/autosize
	license: http://www.opensource.org/licenses/mit-license.php
*/
(function ($) {
	var
	defaults = {
		className: 'autosizejs',
		append: '',
		callback: false,
		resizeDelay: 10
	},

	// border:0 is unnecessary, but avoids a bug in Firefox on OSX
	copy = '<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',

	// line-height is conditionally included because IE7/IE8/old Opera do not return the correct value.
	typographyStyles = [
		'fontFamily',
		'fontSize',
		'fontWeight',
		'fontStyle',
		'letterSpacing',
		'textTransform',
		'wordSpacing',
		'textIndent'
	],

	// to keep track which textarea is being mirrored when adjust() is called.
	mirrored,

	// the mirror element, which is used to calculate what size the mirrored element should be.
	mirror = $(copy).data('autosize', true)[0];

	// test that line-height can be accurately copied.
	mirror.style.lineHeight = '99px';
	if ($(mirror).css('lineHeight') === '99px') {
		typographyStyles.push('lineHeight');
	}
	mirror.style.lineHeight = '';

	$.fn.autosize = function (options) {
		if (!this.length) {
			return this;
		}

		options = $.extend({}, defaults, options || {});

		if (mirror.parentNode !== document.body) {
			$(document.body).append(mirror);
		}

		return this.each(function () {
			var
			ta = this,
			$ta = $(ta),
			maxHeight,
			minHeight,
			boxOffset = 0,
			callback = $.isFunction(options.callback),
			originalStyles = {
				height: ta.style.height,
				overflow: ta.style.overflow,
				overflowY: ta.style.overflowY,
				wordWrap: ta.style.wordWrap,
				resize: ta.style.resize
			},
			timeout,
			width = $ta.width();

			if ($ta.data('autosize')) {
				// exit if autosize has already been applied, or if the textarea is the mirror element.
				return;
			}
			$ta.data('autosize', true);

			if ($ta.css('box-sizing') === 'border-box' || $ta.css('-moz-box-sizing') === 'border-box' || $ta.css('-webkit-box-sizing') === 'border-box'){
				boxOffset = $ta.outerHeight() - $ta.height();
			}

			// IE8 and lower return 'auto', which parses to NaN, if no min-height is set.
			minHeight = Math.max(parseInt($ta.css('minHeight'), 10) - boxOffset || 0, $ta.height());

			$ta.css({
				overflow: 'hidden',
				overflowY: 'hidden',
				wordWrap: 'break-word', // horizontal overflow is hidden, so break-word is necessary for handling words longer than the textarea width
				resize: ($ta.css('resize') === 'none' || $ta.css('resize') === 'vertical') ? 'none' : 'horizontal'
			});

			// The mirror width must exactly match the textarea width, so using getBoundingClientRect because it doesn't round the sub-pixel value.
			function setWidth() {
				var style, width;
				
				if ('getComputedStyle' in window) {
					style = window.getComputedStyle(ta, null);
					width = ta.getBoundingClientRect().width;

					$.each(['paddingLeft', 'paddingRight', 'borderLeftWidth', 'borderRightWidth'], function(i,val){
						width -= parseInt(style[val],10);
					});

					mirror.style.width = width + 'px';
				}
				else {
					// window.getComputedStyle, getBoundingClientRect returning a width are unsupported and unneeded in IE8 and lower.
					mirror.style.width = Math.max($ta.width(), 0) + 'px';
				}
			}

			function initMirror() {
				var styles = {};

				mirrored = ta;
				mirror.className = options.className;
				maxHeight = parseInt($ta.css('maxHeight'), 10);

				// mirror is a duplicate textarea located off-screen that
				// is automatically updated to contain the same text as the
				// original textarea.  mirror always has a height of 0.
				// This gives a cross-browser supported way getting the actual
				// height of the text, through the scrollTop property.
				$.each(typographyStyles, function(i,val){
					styles[val] = $ta.css(val);
				});
				$(mirror).css(styles);

				setWidth();

				// Chrome-specific fix:
				// When the textarea y-overflow is hidden, Chrome doesn't reflow the text to account for the space
				// made available by removing the scrollbar. This workaround triggers the reflow for Chrome.
				if (window.chrome) {
					var width = ta.style.width;
					ta.style.width = '0px';
					var ignore = ta.offsetWidth;
					ta.style.width = width;
				}
			}

			// Using mainly bare JS in this function because it is going
			// to fire very often while typing, and needs to very efficient.
			function adjust() {
				var height, original;

				if (mirrored !== ta) {
					initMirror();
				} else {
					setWidth();
				}

				mirror.value = ta.value + options.append;
				mirror.style.overflowY = ta.style.overflowY;
				original = parseInt(ta.style.height,10);

				// Setting scrollTop to zero is needed in IE8 and lower for the next step to be accurately applied
				mirror.scrollTop = 0;

				mirror.scrollTop = 9e4;

				// Using scrollTop rather than scrollHeight because scrollHeight is non-standard and includes padding.
				height = mirror.scrollTop;

				if (maxHeight && height > maxHeight) {
					ta.style.overflowY = 'scroll';
					height = maxHeight;
				} else {
					ta.style.overflowY = 'hidden';
					if (height < minHeight) {
						height = minHeight;
					}
				}

				height += boxOffset;

				if (original !== height) {
					ta.style.height = height + 'px';
					if (callback) {
						options.callback.call(ta,ta);
					}
				}
			}

			function resize () {
				clearTimeout(timeout);
				timeout = setTimeout(function(){
					var newWidth = $ta.width();

					if (newWidth !== width) {
						width = newWidth;
						adjust();
					}
				}, parseInt(options.resizeDelay,10));
			}

			if ('onpropertychange' in ta) {
				if ('oninput' in ta) {
					// Detects IE9.  IE9 does not fire onpropertychange or oninput for deletions,
					// so binding to onkeyup to catch most of those occasions.  There is no way that I
					// know of to detect something like 'cut' in IE9.
					$ta.on('input.autosize keyup.autosize', adjust);
				} else {
					// IE7 / IE8
					$ta.on('propertychange.autosize', function(){
						if(event.propertyName === 'value'){
							adjust();
						}
					});
				}
			} else {
				// Modern Browsers
				$ta.on('input.autosize', adjust);
			}

			// Set options.resizeDelay to false if using fixed-width textarea elements.
			// Uses a timeout and width check to reduce the amount of times adjust needs to be called after window resize.

			if (options.resizeDelay !== false) {
				$(window).on('resize.autosize', resize);
			}

			// Event for manual triggering if needed.
			// Should only be needed when the value of the textarea is changed through JavaScript rather than user input.
			$ta.on('autosize.resize', adjust);

			// Event for manual triggering that also forces the styles to update as well.
			// Should only be needed if one of typography styles of the textarea change, and the textarea is already the target of the adjust method.
			$ta.on('autosize.resizeIncludeStyle', function() {
				mirrored = null;
				adjust();
			});

			$ta.on('autosize.destroy', function(){
				mirrored = null;
				clearTimeout(timeout);
				$(window).off('resize', resize);
				$ta
					.off('autosize')
					.off('.autosize')
					.css(originalStyles)
					.removeData('autosize');
			});

			// Call adjust in case the textarea already contains text.
			adjust();
		});
	};
}(window.jQuery || window.$)); // jQuery or jQuery-like library, such as Zepto


var themePresets={};
var themePresetsDefault={};

var tmpEditParams = {};


var defaultParams = {
	
	"theme-name": "Default",
	
	"font-name": "Roboto Condensed",
	"font-zoom": 0,
		
	"topbar-color": "#FFFFFF",
	"topbar-font-color": "#000000",
	
	"menu-style": "top-menu-dropdown",
	"menu-color": "#FFFFFF",
	"menu-font-color": "#000000",
	
	"container-color": "#FFFFFF",
};

var defaultStyleParams = {	
		"theme-name": "",
		"menu-style": "top-menu-dropdown",
		"font-name": "Roboto Condensed",
		"font-zoom": 0,
		"topbar-color": "#FFFFFF",
		"topbar-font-color": "#000000",
		"menu-style": "top-menu-dropdown",
		"menu-color": "#FFFFFF",
		"menu-font-color": "#000000",	
		"container-color": "#FFFFFF",
		"border-radius": "0",
};

var currentStyle = "";
var currentUser = "";
var isAdminUser = "";

function toggleAdvancedStyleOptions(){
	$('.advancedOptions, .showAdvancedOptions').toggleClass('hide');
}

function getCurrentUserStyle(){
	var params = {
		'module' : 'OmniThemeManager',
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
		}					
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
	function(error,err){});
}

function applyPresetStyle(themeName){
	
	if(themePresets[themeName] === undefined) defaultParams = themePresetsDefault[themeName];
	else defaultParams = themePresets[themeName];
	
	defaultParams["isApplied"]=true;
	var urlParams = encodeURIComponent(btoa(JSON.stringify(defaultParams)));
	$("#mycCustomStyle").attr("href","index.php?module=OmniThemeManager&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
	
	
	var themePresetsNew = {};
	themePresetsNew["save"]=true;
	themePresetsNew["presetparams"]=defaultParams;
	themePresetsNew["presetKey"]=themeName;
	themePresetsNew["isApplied"]=true;
}	


function applyThemeForUser(themeName,applyglobally){
					
	if(themePresets[themeName] === undefined) defaultParams = themePresetsDefault[themeName];
	else defaultParams = themePresets[themeName];
	
	defaultParams["isApplied"]=true;
	var urlParams = encodeURIComponent(btoa(JSON.stringify(defaultParams)));
	$("#mycCustomStyle").attr("href","index.php?module=OmniThemeManager&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
				
	if(applyglobally) {
		var cr = confirm("Are you sure you want apply this style for ALL users in this crm ?");
		if (cr == true) var ajmode="setStyleForAllUsers";
		else return false;
	}
	else var ajmode="setStyleForCurrentUser";
	
	var params = {
		'module' : 'OmniThemeManager',
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
	tmpEditParams[paramName]=newValue;
	var urlParams = encodeURIComponent(btoa(JSON.stringify(tmpEditParams)));
	$("#mycCustomStyle").attr("href","index.php?module=OmniThemeManager&view=CustomStyle&mode=getCSSStyle&tp="+urlParams);
}


function addNewStyle(){
	

	tmpEditParams = defaultStyleParams;
	$("#presetKey").val("");
	for(param in defaultStyleParams){
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
		var newStyleName = Object.keys(themePresets).length+1; //string_to_slug($(".addPresetStyle input[name='theme-name']").val());
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
		'module' : 'OmniThemeManager',
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
	function(error,err){});
	
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
			'module' : 'OmniThemeManager',
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
		function(error,err){});
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
	var presetFile = "stylePresets.json";
	var presetFile = "index.php?module=OmniThemeManager&view=CustomStyle&mode=getStylePresets";
	$.getJSON(presetFile, function( data ) {
		themePresets = data;
			
		$(".chosePresetStyle .presets").html("");
		

		for(var themePreset in themePresets){
			var selected = "";
			var checked = "";
			var disabled = "";
			
			if(themePreset == currentStyle){
				var selected = "selected";
				var checked = 'checked="checked"';
				var disabled = "disabled";
				if(refresh===true)
					applyPresetStyle(currentStyle);
			}
			
			var editable = true;
			if(parseFloat(themePresets[themePreset]["owner"])!=parseFloat(currentUser)) editable = false;
			if(isAdminUser) editable = true;
			
			var htmlOption = '<div class="styleOption radio '+selected+'"><label class="pull-left">'+themePresets[themePreset]["theme-name"]+'<input type="radio" '+checked+' value="'+themePreset+'" name="preset-style" class="form-control presetStyle"><br><div class="previewSyleColors"><div class="cl1" style="background:'+themePresets[themePreset]["topbar-color"]+'"></div><div class="cl2"  style="background:'+themePresets[themePreset]["menu-color"]+'"></div><div class="cl3"  style="background:'+themePresets[themePreset]["container-color"]+'"></div></div></label><div class="pull-right">'+
			'<a class="btn btn-success btn-xs applyStyleBtn '+disabled+'"  onclick="applyThemeForUser(\''+themePreset+'\',false)" tippytitle data-tippy-content="Apply"><i class="fa fa-check"></i></a>&nbsp;'+
			'<a class="btn btn-primary btn-xs"  tippytitle data-tippy-content="Apply Globally" onclick="applyThemeForUser(\''+themePreset+'\',true)"><i class="fa fa-globe"></i></a>&nbsp;';
			
			if(editable)
				var htmlOption = htmlOption+'<a class="btn btn-info btn-xs"  tippytitle data-tippy-content="Edit" onclick="editStyle(\''+themePreset+'\')"><i class="fa fa-edit"></i></a>&nbsp;';
			
			var htmlOption = htmlOption+'<a class="btn btn-warning btn-xs"  tippytitle data-tippy-content="Duplicate" onclick="duplicateStyle(\''+themePreset+'\')"><i class="fa fa-files-o"></i></a>&nbsp;';
			
			if(editable)
				var htmlOption = htmlOption+'<a class="btn btn-danger btn-xs"  tippytitle data-tippy-content="Delete" onclick="deleteStyle(\''+themePreset+'\')"><i class="fa fa-trash"></i></a>';
			
			var htmlOption = htmlOption+'</div><div class="clearfix">&nbsp;</div></div>';
			$(".chosePresetStyle .presets").append(htmlOption);
			
			tippy('.styleOption [tippytitle]',{
			  placement: 'top',
			  animation: 'shift-toward',
			  inertia: true,
			  duration: 600,
			  arrow: true,
			  //arrowtype: "round",
			  //arrowtransform: "scale(0.7, 1)",	  
			  theme: "fask"
			});
			
	    }
	    
	    $('.stylerUi .chosePresetStyle input').change(function() {
		    $('.stylerUi .radio').removeClass("selected");
			$(this).parent().parent().addClass("selected");
			applyPresetStyle($(this).val());
	    });
	   	    
	});
    
}


function loadThemePresetsMYC(refresh){
	var presetFile = "stylePresets.json";
	var presetFile = "index.php?module=OmniThemeManager&view=CustomStyle&mode=getMYCStylePresets";
	$.getJSON(presetFile, function( data ) {
		themePresetsDefault = data;
			
		$(".chosePresetStyleMYC .presets").html("");
		
		for(var themePreset in themePresetsDefault){
			var selected = "";
			var checked = "";
			var disabled = "";
			
			if(themePreset == currentStyle){
				var selected = "selected";
				var checked = 'checked="checked"';
				var disabled = "disabled";
				if(refresh===true)
					applyPresetStyle(currentStyle);
			}
			
			var htmlOption = '<div class="styleOption radio '+selected+'"><label class="pull-left">'+themePresetsDefault[themePreset]["theme-name"]+'<input type="radio" '+checked+' value="'+themePreset+'" name="preset-style" class="form-control presetStyle"><br><div class="previewSyleColors"><div class="cl1" style="background:'+themePresetsDefault[themePreset]["topbar-color"]+'"></div><div class="cl2"  style="background:'+themePresetsDefault[themePreset]["menu-color"]+'"></div><div class="cl3"  style="background:'+themePresetsDefault[themePreset]["container-color"]+'"></div></div></label><div class="pull-right">'+
			'<a class="btn btn-success btn-xs applyStyleBtn '+disabled+'"  onclick="applyThemeForUser(\''+themePreset+'\',false)" tippytitle data-tippy-content="Apply"><i class="fa fa-check"></i></a>&nbsp;'+
			'<a class="btn btn-primary btn-xs"  tippytitle data-tippy-content="Apply Globally" onclick="applyThemeForUser(\''+themePreset+'\',true)"><i class="fa fa-globe"></i></a>&nbsp;'+
			'<a class="btn btn-warning btn-xs"  tippytitle data-tippy-content="Duplicate" onclick="duplicateStyle(\''+themePreset+'\')"><i class="fa fa-files-o"></i></a>&nbsp;'+
			'</div><div class="clearfix">&nbsp;</div></div>';
			$(".chosePresetStyleMYC .presets").append(htmlOption);
			
			tippy('.styleOption [tippytitle]',{
			  placement: 'top',
			  animation: 'shift-toward',
			  inertia: true,
			  duration: 600,
			  arrow: true,
			  //arrowtype: "round",
			  //arrowtransform: "scale(0.7, 1)",	  
			  theme: "fask"
			});
			
	    }
	    
	    $('.stylerUi .chosePresetStyleMYC input').change(function() {
		    $('.stylerUi .radio').removeClass("selected");
			$(this).parent().parent().addClass("selected");
			applyPresetStyle($(this).val());
	    });
	   	    
	});
    
}

$(function(){
	getCurrentUserStyle();
    
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
    
    $('textarea').autosize({append: "\n"});
  
    /*
    $.getJSON('https://raw.githubusercontent.com/jonathantneal/google-fonts-complete/master/google-fonts.json', function(fonts) {
        $.each(fonts, function(k, v) {
            
            var css = "@import url('https://fonts.googleapis.com/css?family="+k+"');";
            $('<style/>').append(css).appendTo(document.head);
            $("<h3 style=\"font-family: '"+k+"' !important; \"/>").text(k).appendTo($('#fontPreview'));
        });
    });  
    */  
});