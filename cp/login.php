<?php		
	include_once('includes/config.php');
	
	include_once('includes/head.php');
	
	global $portal_title, $portal_subtitle, $portal_main_title;
?>

	<div class="kt-grid kt-grid--ver kt-grid--root">
		<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v5 kt-login--signin" id="kt_login">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile" style="background-image: url(assets/media//bg/bg-3.jpg);">
				
				<div class="kt-login__left">
					<div class="kt-login__wrapper">
						<div class="kt-login__content">
							<a class="kt-login__logo" href="#">
								<img src="<?php echo $portal_logo;?>" style="width:100% !important;">
							</a>
							<h3 class="kt-login__title"><?php echo $portal_main_title;?></h3>
							<span class="kt-login__desc" >
								<?php echo $portal_subtitle;?>
							</span>
							<div class="kt-login__actions" style = "display:none;">
								<button type="button" id="kt_login_signup" class="btn btn-outline-brand btn-pill">Get An Account</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="kt-login__divider">
					<div></div>
				</div>
				
				<div class="kt-login__right">
					
					<div class="kt-login__wrapper">
						<div class="kt-login__signin">
							<div class="kt-login__head">
								<h3 class="kt-login__title">Login To Your Account</h3>
							</div>
							<div class="kt-login__form">
								<form class="kt-form" action="">
									<div class="form-group">
										<input class="form-control" type="text" placeholder="Username" name="email" autocomplete="off">
									</div>
									<div class="form-group">
										<input class="form-control form-control-last" type="Password" placeholder="Password" name="password">
									</div>
									<div class="row kt-login__extra" style = "display:none;">
										<div class="col kt-align-left">
											<label class="kt-checkbox">
												<input type="checkbox" name="remember"> Remember me
												<span></span>
											</label>
										</div>
										<div class="col kt-align-right" >
											<a href="javascript:;" id="kt_login_forgot" class="kt-link">Forget Password ?</a>
										</div>
									</div>
									<div class="kt-login__actions">
										<button id="kt_login_signin_submit" class="btn btn-brand btn-pill btn-elevate">Sign In</button>
									</div>
								</form>
							</div>
						</div>
							
							
						<div class="kt-login__forgot">
							<div class="kt-login__head">
								<h3 class="kt-login__title">Forgotten Password ?</h3>
								<div class="kt-login__desc">Enter your email to reset your password:</div>
							</div>
							<div class="kt-login__form">
								<form class="kt-form" action="">
									<div class="form-group">
										<input class="form-control" type="text" placeholder="Email" name="fgtemail" id="kt_email" autocomplete="off">
									</div>
									<div class="kt-login__actions">
										<button id="kt_login_forgot_submit" class="btn btn-brand btn-pill btn-elevate">Request</button>
										<button id="kt_login_forgot_cancel" class="btn btn-outline-brand btn-pill">Cancel</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

		
	<script>
		var KTAppOptions = {
			"colors": {
				"state": {
					"brand": "#5d78ff",
					"dark": "#282a3c",
					"light": "#ffffff",
					"primary": "#5867dd",
					"success": "#34bfa3",
					"info": "#36a3f7",
					"warning": "#ffb822",
					"danger": "#fd3995"
				},
				"base": {
					"label": [
						"#c5cbe3",
						"#a1a8c3",
						"#3d4465",
						"#3e4466"
					],
					"shape": [
						"#f0f3ff",
						"#d9dffa",
						"#afb4d4",
						"#646c9a"
					]
				}
			}
		};
	</script>

	<?php 
	   include_once "includes/common-js.php";
	?>
	<script src="assets/js/pages/custom/login/login-general.js" type="text/javascript"></script>
	</body>
</html>