var Login = function() {

    var handleLogin = function() {

        $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
        	username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
            	username: {
                    required: "Username is required."
                },
                password: {
                    required: "Password is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $('.forget-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
        		user_name: {
                    required: true
                },
                emailId: {
                    required: true,
                    email: true
                },
            },

            messages: {
            	user_name: {
                    required: "Username is required."
                },
                emailId: {
                    required: "Email is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.forget-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit(); // form validation success, call ajax form submit
            }
        });

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        /*$('#forget-password').click(function(){
            $('.login-form').hide();
            $('.forget-form').show();
        });*/

        $('#back-btn').click(function(){
            $('.login-form').show();
            $('.forget-form').hide();
        });
    }

 
  

    return {
        //main function to initiate the module
        init: function() {

            handleLogin();

            // init background slide images
            /*$('.login-bg').backstretch([
                "layouts/v7/resources/marketing/img4.jpg",
                "layouts/v7/resources/marketing/img1.jpg",
                "layouts/v7/resources/marketing/img3.jpg",
				"layouts/v7/resources/marketing/img2.jpg",
				"layouts/v7/resources/marketing/img5.jpg"
				], {
                  fade: 1000,
                  duration: 8000
                }
            );*/
        	jQuery(document).ready(function () {
		    	var slider = jQuery('.bxslider').bxSlider({
					auto: true,
					pause: 4000,
					nextText: "",
					prevText: "",
		 			autoHover: true
				});
				//jQuery('.bx-prev, .bx-next, .bx-pager-item').live('click',function(){ slider.startAuto(); });
				jQuery('.bx-wrapper .bx-viewport').css('background-color', 'transparent');
				jQuery('.bx-wrapper .bxslider li').css('text-align', 'left');
				jQuery('.bx-wrapper .bx-pager').css('bottom', '-15px');
				
				$(".adCarousel").slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false,
					arrows: true,
					autoplay: true,
					autoplaySpeed: 3000,
					infinite: true,
					adaptiveHeight: true,
					adaptiveWidth: true,
					lazyLoad: 'progressive'
				});
			});
            $('.forget-form1').hide();
            
            $('.officeLogin').on('click', function(){
            	
            	var url = $(this).data('url');
            	
            	var win= window.open(url,'','height=600,width=600,channelmode=1');
				
				window.RefreshPage = function(code) {
					
					var data = [];
					$.each($('.login-form').serializeArray(), function(i, field){
					    data[field.name] = field.value;
					});
					data['code'] = code;
					
					$.ajax({
		    			type: "POST",
		    			url:'index.php?'+$('.login-form').serialize()+'&code='+code,
		    			error: function(errorThrown) {
		    				console.log(errorThrown)
		    			},
		    			success: function(url) {
		    				window.location = url;
		    			}
		    		});
				}
            	
            });

        }

    };

}();

jQuery(document).ready(function() {
    Login.init();
});