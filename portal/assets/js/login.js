var Login = function () {

	var handleLogin = function() {
		$('.login-form').validate({
			errorElement: 'div', 
			errorClass: 'form-control-feedback', 
			focusInvalid: false,
			rules: {
				email: {
					required:!0,email:!0
				},
				pass: {
					required:!0
				},
				lang: {
					required:!0
				}
			},

			messages: {
				email: {
					required: "Email is required."
				},
				pass: {
					required: "Password is required."
				},
				lang:{
					required: "Language is required."
				}
			},

			invalidHandler: function (event, validator) { 
				$('.alert-danger', $('.login-form')).text("Enter any Email and password.").show();
			},

			highlight: function (element) { 
				$(element).closest('.form-group').addClass('has-danger');
			},

			success: function (label) {
				label.closest('.form-group').removeClass('has-danger');
				label.remove();
			},

			submitHandler: function (form) {
				form.submit();
			}
		});

		$('.login-form input').keypress(function (e) {
			if (e.which == 13) {
				if ($('.login-form').validate().form()) {
					$('.login-form').submit();
				}
				return false;
			}
		});
	}

	var handleForgetPassword = function () {
		$('.forget-form').validate({
            errorElement: 'div', //default input error message container
            errorClass: 'form-control-feedback', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
            	fgtemail: {
            		required:!0,email:!0
                }
            },

            messages: {
            	fgtemail: {
                    required: "Email is required."
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit   

            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-danger'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-danger');
                label.remove();
            },

            submitHandler: function (form) {
                form.submit();
            }
        });

	}

	var handleLanguageSelect = function () {

		function format(state) {
			if (!state.id) { 
				return state.text; 
			}
			
			var selected_lang = state.element.value.toLowerCase().split("_");
			var $state = $(
				'<span><img src="assets/global/img/flags/' + selected_lang[1] + '.png" class="img-flag" /> ' + state.text + '</span>'
			);
			return $state;
		}
		
		if (jQuery().select2 && $('select[name="lang"]').size() > 0) {

			$('select[name="lang"]').select2({
				placeholder: '<i class="fa fa-language"></i>&nbsp;Select a Language',
				templateResult: format,
                templateSelection: format,
                width: '100%', 
	            escapeMarkup: function(m) {
	                return m;
	            }
			});

	        $('select[name="lang"]').change(function() {
	            $('.login-form').validate().element($(this)); 
	        });
    	}
	}
	
	var e=$("#m_login"),
	i=function(e,i,a){
		var l=$(
			'<div class="m-alert m-alert--outline alert alert-'+i+' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>'
		);
		e.find(".alert").remove(),
		l.prependTo(e),
		mUtil.animateClass(l[0],"fadeIn animated"),
		l.find("span").html(a)
	},
	a=function(){
		e.removeClass("m-login--forget-password"),
		e.addClass("m-login--signin"),
		mUtil.animateClass(
			e.find(".m-login__signin")[0],"flipInX animated"
		)
	},
	l=function(){
		$("#m_login_forget_password").click(function(i){
			i.preventDefault(),
			e.removeClass("m-login--signin"),
			e.addClass("m-login--forget-password"),
			mUtil.animateClass(
				e.find(".m-login__forget-password")[0],"flipInX animated"
			)
		}),
		$("#m_login_forget_password_cancel").click(function(e){
			e.preventDefault(),a()
		})
	};
	
    return {
        //main function to initiate the module
        init: function () {
        	
            handleLogin();
            handleForgetPassword();
			handleLanguageSelect();
            l();
		   
        }
    };

}();

jQuery(document).ready(function() {
	Login.init();
});