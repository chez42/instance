var changePassword = {
	
	registerValidateChangePasswordEvent : function() {
		$('#change_password').validate({
			errorElement: 'span', 
			errorClass: 'help-block', 
			focusInvalid: false,
			rules: {
				old_password: {
					required: true
				},
				new_password: {
					required: true
				},
				confirm_password: {
					equalTo: "#new_password"
				}
			},

			invalidHandler: function (event, validator) { 
			},

			highlight: function (element) { 
				$(element).closest('.form-group').addClass('has-error');
			},

			success: function (label) {
				label.closest('.form-group').removeClass('has-error');
				label.remove();
			},

			errorPlacement: function (error, element) {
				error.insertAfter(element.closest('.input-group'));
			},

			submitHandler: function (form) {
				App.blockUI({
                    target: jQuery("#responsive_change_password_container"),
                    animate: true,
				});		
				$.ajax({
					type: "POST",
					url : "index.php",
					data: $(form).serialize(),
					success: function(response){
						App.unblockUI(jQuery("#responsive_change_password_container"));
						response = $.parseJSON(response);
						toastr.clear();
						if(response.success){
							toastr.success(response.message);
							jQuery('.modal').modal('hide');
						} else {
							toastr.error(response.message, "Error");
						}
					}
				});
				return false;
			}
		});
	}
};
jQuery("document").ready(function(){
	changePassword.registerValidateChangePasswordEvent();
});