/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_Header_Js", {
   
    previewFile : function(e,recordId) {
        e.stopPropagation();
        var currentTarget = e.currentTarget;
        var currentTargetObject = jQuery(currentTarget);
        if(typeof recordId == 'undefined') {
            if(currentTargetObject.closest('tr').length) {
                recordId = currentTargetObject.closest('tr').data('id');
            } else {
                recordId = currentTargetObject.data('id');
            }
        }
        var fileLocationType = currentTargetObject.data('filelocationtype');
        var fileName = currentTargetObject.data('filename'); 
        if(fileLocationType == 'I'){
        	app.helper.showProgress();
            var params = {
                module : 'Documents',
                view : 'FilePreview',
                record : recordId
            };
            app.request.post({"data":params}).then(function(err,data){
                app.helper.showModal(data,{'cb':function(){
                	app.helper.hideProgress();
                	$('.modal-content').resizable({
                		stop : function(event, ui){
                			var height = ui.size.height;
                			var width =  ui.size.width;
                			if($('iframe').length)
                				$('iframe').height(height-90);
                			
                			$('.filePreview .modal-body.row').height(height-90);
                		}
            	    });
                	
            	    $('.modal-dialog').draggable();
            	    
                	$('.viewerDownload').ready(function() {
            		   setTimeout(function() {
            		      $('.viewerDownload').contents().find('#download').remove();
            		      $('.viewerDownload').contents().find('#print').remove();
            		   }, 100);
            		});
                	
                }});
            });
        } else {
            var win = window.open(fileName, '_blank');
            win.focus();
        }
    }
},{
});