Settings_Workflows_Edit_Js.prototype.preSaveRequestFeedbackTask = function(tasktype) {
    var textAreaElement = jQuery('#content');
    //To keep the plain text value to the textarea which need to be
    //sent to server
    textAreaElement.val(CKEDITOR.instances['content'].getData());
};

Settings_Workflows_Edit_Js.prototype.registerRequestFeedbackTaskEvents = function () {
    var textAreaElement = jQuery('#content');
    console.log("textAreaElement",textAreaElement);
    var ckEditorInstance = this.getckEditorInstance();
    ckEditorInstance.loadCkEditor(textAreaElement);
    this.registerFillMailContentEvent();
    this.registerTooltipEventForSignatureField();
    this.registerFillTaskFromEmailFieldEvent();
    this.registerCcAndBccEvents();
};