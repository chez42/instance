/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	config.removePlugins = 'save,maximize,magicline'; 
	config.fullPage = true; 
    config.allowedContent = true; 
	config.disableNativeSpellChecker = false;
	config.enterMode = CKEDITOR.ENTER_BR;  
	config.shiftEnterMode = CKEDITOR.ENTER_P; 
	config.autoParagraph = false;
	config.fillEmptyBlocks = false;
//	config.filebrowserBrowseUrl = 'ckfinder/ckfinder.html?type=Images'; 
//	config.filebrowserUploadUrl = 'ckfinder/ckfinder.html?type=Images';
//	filebrowserBrowseUrl = 'ckfinder/ckfinder.html';
//    filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?type=Images';
	
	config.filebrowserBrowseUrl = 'ckfinder/ckfinder.html';
	config.filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?type=Images';
	config.filebrowserFlashBrowseUrl = 'ckfinder/ckfinder.html?type=Flash';
	config.filebrowserUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	config.filebrowserImageUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
	config.filebrowserFlashUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
	config.toolbarGroups = [ 
	        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] }, 
	        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] }, 
			{ name: 'insert' ,groups:['blocks']}, 
	        { name: 'links' }, 
	        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] }, 
      '/', 
	        { name: 'styles' }, 
	        { name: 'colors' }, 
	        { name: 'tools' }, 
	        { name: 'others' }, 
	        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },{name: 'align'}, 
      { name: 'paragraph', groups: [ 'list', 'indent' ] }, 
     ];
	
	
	//Add new custom font names in below array
	var customFonts = ['FreeStyle Script','Brush Script STD','Bradley Hand ITC','Vladimir Script'];
	for(var i = 0; i < customFonts.length; i++){
		  config.font_names = config.font_names+';'+customFonts[i];
	}
		
};
