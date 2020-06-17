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

$rating = array(
			'GOLD LEVEL' => 'GOLD LEVEL',
			'SILVER LEVEL' => 'SILVER LEVEL',
			'BRONZE LEVEL' => 'BRONZE LEVEL',
		);

$accounttype = array (
					'HOSPITALITY' 		=> 'HOSPITALITY',
					'FITNESS' 			=> 'FITNESS',
					'TOURISM' 			=> 'TOURISM',
					'EDUCATION' 		=> 'EDUCATION',
					'TECHNOLOGY' 		=> 'TECHNOLOGY',
					'FASHION' 			=> 'FASHION',
					'DANCE' 			=> 'DANCE',
					'LARGE CORPORATE' 	=> 'LARGE CORPORATE',
					'EVENT  COMPANIES' 	=> 'EVENT  COMPANIES',
					'Other' 			=> 'Other',
				);

?>

			<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
				<!-- begin:: Subheader -->
				<div class="kt-subheader   kt-grid__item" id="kt_subheader">
					<div class="kt-container  kt-container--fluid ">
						<div class="kt-subheader__main">
							<h3 class="kt-subheader__title">
								<button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
								User Profile </h3>
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
												<h3 class="kt-portlet__head-title">Personal Information <small>update your personal informaiton</small></h3>
											</div>
										</div>
										<form class="kt-form kt-form--label-right" action="saveUserInfo.php" method="post">
											
											<input type="hidden" name="block" value="personal_information">
											
											<div class="kt-portlet__body">
												<div class="kt-section kt-section--first">
													<div class="kt-section__body">
														<div class="row">
															<label class="col-xl-3"></label>
															<div class="col-lg-9 col-xl-6">
																<h3 class="kt-section__title kt-section__title-sm">Customer Info:</h3>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Organization Name</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" name="accountname" type="text" value="<?php echo $user_info['accountname']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Website</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="website" value="<?php echo $user_info['website'];?>">
																<span class="form-text text-muted">If you want your invoices addressed to a company. Leave blank to use your full name.</span>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Primary Email</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<div class="input-group-prepend"><span class="input-group-text"><i class="la la-at"></i></span></div>
																	<input type="text" class="form-control" name="email1" value="<?php echo $user_info['email1'];?>" placeholder="Email" aria-describedby="basic-addon1">
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Primary Phone</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<div class="input-group-prepend"><span class="input-group-text"><i class="la la-phone"></i></span></div>
																	<input type="text" class="form-control" name="phone" value="<?php echo $user_info['phone'];?>" placeholder="Phone" aria-describedby="basic-addon1">
																</div>
																<span class="form-text text-muted">We'll never share your email with anyone else.</span>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Secondary Email</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<div class="input-group-prepend"><span class="input-group-text"><i class="la la-at"></i></span></div>
																	<input type="text" class="form-control" name="email2" value="<?php echo $user_info['email2'];?>" placeholder="Email" aria-describedby="basic-addon1">
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Secondary Phone</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<div class="input-group-prepend"><span class="input-group-text"><i class="la la-phone"></i></span></div>
																	<input type="text" class="form-control" name="otherphone" value="<?php echo $user_info['otherphone'];?>" placeholder="Phone" aria-describedby="basic-addon1">
																</div>
																<span class="form-text text-muted">We'll never share your email with anyone else.</span>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Fax</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="fax" value="<?php echo $user_info['fax']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Email Opt Out</label>
															<div class="col-lg-9 col-xl-6">
																<div class="input-group">
																	<label class="kt-checkbox kt-checkbox--solid kt-checkbox--single">
    																	<input type="checkbox" name="emailoptout" <?php if($user_info['emailoptout'] == 1) echo "checked"; ?> >
    																	<span></span>
    																</label>
																</div>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">ABN</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="abn" value="<?php echo $user_info['cf_881']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">FIRST NAME</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="firstname" value="<?php echo $user_info['cf_921']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">LAST NAME</label>
															<div class="col-lg-9 col-xl-6">
																<input class="form-control" type="text" name="lastname" value="<?php echo $user_info['cf_923']; ?>">
															</div>
														</div>
														<div class="form-group row">
															<label class="col-xl-3 col-lg-3 col-form-label">Rating</label>
															<div class="col-lg-9 col-xl-6">
    															<select class="form-control" name="rating">
																	<?php foreach($rating as $key =>$value) { ?>
																		<option value="<?php echo $key; ?>" <?php if($user_info['rating'] == $key){ echo "selected"; }?> ><?php echo $value; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="form-group form-group-last row">
															<label class="col-xl-3 col-lg-3 col-form-label">Type</label>
															<div class="col-lg-9 col-xl-6">
    															<select class="form-control" name="accounttype">
																	<?php foreach($accounttype as $key =>$value) { ?>
																		<option value="<?php echo $key; ?>" <?php if($user_info['accounttype'] == $key){ echo "selected"; }?> ><?php echo $value; ?></option>
																	<?php } ?>
																</select>
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