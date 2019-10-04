var MAX_UPLOAD_LIMIT_MB;
var MAX_UPLOAD_LIMIT_BYTES;
var Extension_DragDrop_ToUpload_Js = {
    init : function(){
        var thisInstance = this;
        var container = $( 'body' );

        var params = {};
        params.module = 'DragDropDocuments';
        params.action = 'GetMaxLimitAjax';
        AppConnector.request(params).then(
            function(data) {
                if (data.success == true){
                    MAX_UPLOAD_LIMIT_MB = data.result.MAX_UPLOAD_LIMIT_MB;
                    MAX_UPLOAD_LIMIT_BYTES = MAX_UPLOAD_LIMIT_MB * 1024 *1024;
                    if (data.result.license){
                        thisInstance.registerDragDropToUploadEvent(container);
                    }
                }
            },
            function(error) {

            }
        );
    },

    registerEventShowAreaDropToUpload : function (container) {
        var elementDragDrop = container.find('#dragdropToUpload');
        if (elementDragDrop.length == 0){
            var dragdropContainerHtml =
                '<div id="dragdropToUpload" class="full-width text-center" style="width : 100%; height: 100vh; position: fixed; z-index: 9999999; text-align: center; top: 0; left: 0;border: 2px dashed rgb(0, 135, 247); border-radius: 5px; background: rgb(255, 255, 255); opacity: 0.5">' +
                '   <h3 style="margin-top: 25%"><span class="fa fa-upload"></span> DRAG & DROP FILE TO UPLOAD </h3>' +
                '</div>';
            container.prepend(dragdropContainerHtml);
        }
    },

    registerEventHideAreaDropToUpload : function (container) {
        container.find('#dragdropToUpload').remove();
    },
    /**
     * Function returns the record id
     */
    getRecordId: function () {
        var view = jQuery('[name="view"]').val();
        var recordId;
        if (view == "Edit") {
            recordId = jQuery('[name="record"]').val();
        } else if (view == "Detail") {
            recordId = jQuery('#recordId').val();
        }
        return recordId;
    },

    registerDragDropToUploadEvent : function (container) {
        var thisInstance = this;
        var recordId = thisInstance.getRecordId();
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
                                var progressIndicatorElement = jQuery.progressIndicator();
                                jQuery.ajax({
                                    url: 'index.php',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    type: 'POST',
                                    complete: function(){
                                        var params = [];
                                        params['type'] = 'success';
                                        params['text'] = 'Upload Success';
                                        Vtiger_Helper_Js.showMessage(params);
                                        thisInstance.registerEventHideAreaDropToUpload(container);
                                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});

                                    }
                                });
                            }else{
                                var params = [];
                                params['type'] = 'errors';
                                params['text'] = 'File upload limit '+MAX_UPLOAD_LIMIT_MB+'MB';
                                Vtiger_Helper_Js.showMessage(params);
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

jQuery(document).ready(function () {
    Extension_DragDrop_ToUpload_Js.init();
});