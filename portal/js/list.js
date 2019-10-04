var Documents_List_JS = {
	registerDropzoneMethodEvents :	function(){
		Dropzone.options.myDropzone = {
			previewsContainer: ".dropzone-previews",
			init: function() {
				this.on("success", function(file, response) {
					
					this.removeFile(file);
					
					response = $.parseJSON(response);
					
					toastr.clear();
					
					if(response.success){
						
						toastr.success(response.message);
						location.reload(true);
						
					} else {	
						toastr.error(response.message, "Error");
					}
				});
				this.on("addedfile", function(file) {
					App.blockUI({animate: true})//this.removedfile(file);
				});
				this.on("complete", function(file){
					App.unblockUI();
				});
			}
		}; 
	},
	
	registerEvents : function(){
		this.registerDropzoneMethodEvents();
	}
};

jQuery("document").ready(function(){
	Documents_List_JS.registerEvents();	 
});