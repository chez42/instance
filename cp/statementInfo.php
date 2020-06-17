<?php
include_once "includes/config.php";
if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit;
}
include_once("includes/head.php");
include_once "includes/aside.php";
include_once 'includes/top-header.php';

$user_info = $_SESSION['customer_data'];

?>

			<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
				<!-- begin:: Subheader -->
				<div class="kt-subheader   kt-grid__item" id="kt_subheader">
					<div class="kt-container  kt-container--fluid ">
						<div class="kt-subheader__main">
							<h3 class="kt-subheader__title">
								<button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
								User Profile</h3>
							<span class="kt-subheader__separator kt-hidden"></span>
						</div>
					</div>
				</div>

				<!-- end:: Subheader -->

				<!-- begin:: Content -->
				<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

					<!--Begin::App-->
					<div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">

						<!--Begin:: App Aside Mobile Toggle-->
						<button class="kt-app__aside-close" id="kt_user_profile_aside_close">
							<i class="la la-close"></i>
						</button>

						<!--End:: App Aside Mobile Toggle-->

						<!--Begin:: App Aside-->
						
						<?php include_once "includes/info_aside.php"; ?>

						<!--End:: App Aside-->

						<!--Begin:: App Content-->
						<div class="kt-grid__item kt-grid__item--fluid kt-app__content">
							<div class="row">
								<div class="col-xl-12">
									<div class="kt-portlet">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">Statement Information <small>update your Statement informaiton</small></h3>
											</div>
										</div>
										<form class="kt-form kt-form--label-right" action="saveUserInfo.php" method="post">
											
											<input type="hidden" name="block" value="statementInfo">
											
											<div class="kt-portlet__body">
												<div class="kt-section kt-section--first">
													<div class="kt-section__body">
														<div class="row">
															<label class="col-xl-3"></label>
															<div class="col-lg-9 col-xl-6">
																<h3 class="kt-section__title kt-section__title-sm">Statement Info:</h3>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Current Month Name</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" name="current_month_name" type="text" value="<?php echo $user_info['cf_982']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Current Month Balance</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="current_month_balance" value="<?php echo $user_info['cf_984']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -1 Name</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="month_1_name" value="<?php echo $user_info['cf_986'];?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -1 Balance</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<input type="text" class="form-control" name="month_1_balance" value="<?php echo $user_info['cf_992'];?>">
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -2 Name</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<input type="text" class="form-control" name="month_2_name" value="<?php echo $user_info['cf_988'];?>" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -2 Balance</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<input type="text" class="form-control" name="month_2_balance" value="<?php echo $user_info['cf_994'];?>" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -3 Name</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<input type="text" class="form-control" name="month_3_name" value="<?php echo $user_info['cf_990'];?>" >
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Month -3 Balance </label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="month_3_balance" value="<?php echo $user_info['cf_996']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Statement Date</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="statement_date" value="<?php echo $user_info['cf_980']; ?>">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<div class="row">
														<div class="col-lg-3 col-xl-3">
														</div>
														<div class="col-lg-9 col-xl-9">
															<button type="submit" class="btn btn-success">Submit</button>&nbsp;
															<button type="reset" class="btn btn-secondary">Cancel</button>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<!--End:: App Content-->
					</div>
					<!--End::App-->
				</div>
				<!-- end:: Content -->
			</div>
		
	<?php 
	   include_once "includes/footer.php";
	?>
	</body>
	<script type="text/javascript">
		
	</script>
	
</html>   