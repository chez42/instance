var MAX_UPLOAD_LIMIT_MB;
var MAX_UPLOAD_LIMIT_BYTES;
var Extension_DragDrop_ToUpload_Js = {
    init : function(){
        var thisInstance = this;
        var container = $( '#page' );
        var params = {};
        params.module = 'DragDropDocuments';
        params.action = 'GetMaxLimitAjax';
        app.request.post({data:params}).then(
            function(error, response) {
                if (error === null){
                    MAX_UPLOAD_LIMIT_MB = response.MAX_UPLOAD_LIMIT_MB;
                    MAX_UPLOAD_LIMIT_BYTES = MAX_UPLOAD_LIMIT_MB * 1024 * 1024;
                    if (response.license){
                        thisInstance.registerDragDropToUploadEvent(container);
                    }
                }
        });
    },

    registerEventShowAreaDropToUpload : function (container) {
        var elementDragDrop = container.find('#dragdropToUpload');
        if (elementDragDrop.length == 0){
            var dragdropContainerHtml =
                '<div id="dragdropToUpload" class="full-width text-center"style="height: 100vh; position: fixed; z-index: 9999999; border: 2px dashed rgb(0, 135, 247); border-radius: 5px; background: rgb(255, 255, 255); opacity: 0.5">' +
                '   <h3 style="margin-top: 25%"><span class="fa fa-upload"></span> DRAG & DROP FILE TO UPLOAD </h3>' +
                '</div>';
            container.prepend(dragdropContainerHtml);
        }
    },

    registerEventHideAreaDropToUpload : function (container) {
        container.find('#dragdropToUpload').remove();
    },

    registerDragDropToUploadEvent : function (container) {
        var thisInstance = this;
        var recordId = app.getRecordId();
        var moduleName = app.getModuleName();
        container.on({
            'dragover dragenter': function(e) {
                e.preventDefault();
                e.stopPropagation();
                var file = e.originalEvent.dataTransfer;
                var formUploadOfDocumentModule = container.find('form[name="upload"]');
                if (formUploadOfDocumentModule.length == 0 && recordId != false && file.types.indexOf('Files') != -1){
                    thisInstance.registerEventShowAreaDropToUpload(container);
                }
            },
            'drop': function(e) {
                var formUploadOfDocumentModule = container.find('form[name="upload"]');
                if (formUploadOfDocumentModule.length == 0 && recordId != false){
                    var dataTransfer =  e.originalEvent.dataTransfer;
                    if( dataTransfer && dataTransfer.files.length) {
                        e.preventDefault();
                        e.stopPropagation();
                        $.each( dataTransfer.files, function(i, file) {
                            if (file.size < MAX_UPLOAD_LIMIT_BYTES){
                                var formData = new FormData();
                                formData.append("filename", file);
                                formData.append("module", "Documents");
                                formData.append("action", "SaveAjax");
                                formData.append("notes_title", file.name);
                                formData.append("filelocationtype", "I");

                                if (recordId !== false){
                                    formData.append("relationOperation", true);
                                    formData.append("sourceModule", moduleName);
                                    formData.append("sourceRecord", recordId);
                                }
                                app.helper.showProgress();
                                jQuery.ajax({
                                    url: 'index.php',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    type: 'POST',
                                    complete: function(){
                                        var params = [];
                                        params['message'] = 'Upload Success';
                                        app.helper.showSuccessNotification(params);
                                        thisInstance.registerEventHideAreaDropToUpload(container);
										
										var parentId = jQuery('#recordId').val();
										var parentModule = app.getModuleName();
										var relatedModuleName = jQuery('.relatedModuleName').val();
										var selectedRelatedTabElement = jQuery('div.related-tabs').find('li').filter('.active');
										var relatedList = Vtiger_RelatedList_Js.getInstance(parentId, parentModule, selectedRelatedTabElement, relatedModuleName);
										relatedList.loadRelatedList();
										
										app.helper.hideProgress();
                                    }
                                });
                            }else{
                                var params = [];
                                params['message'] = 'File upload limit '+MAX_UPLOAD_LIMIT_MB+'MB';
                                app.helper.showErrorNotification(params);
                                thisInstance.registerEventHideAreaDropToUpload(container);
                            }
                        });
                    }
                }
                thisInstance.registerEventHideAreaDropToUpload(container);

            },

            'dragleave' : function (e) {
                var formUploadOfDocumentModule = container.find('form[name="upload"]');
                if (formUploadOfDocumentModule.length == 0){
                    if (e.target.id == 'dragdropToUpload'){
                        thisInstance.registerEventHideAreaDropToUpload(container);
                    }
                }
            }
        });
    },
}
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_DragDropDocuments();
    }, 5000);
});
function initData_DragDropDocuments() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('DragDropDocuments')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208
    
    Extension_DragDrop_ToUpload_Js.init();
}