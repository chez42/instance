		<?php global $portal_profile_image;?>	
		<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
    		<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

				<!-- begin:: Header Menu -->

				<!-- Uncomment this to display the close button of the panel
                <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                -->
				<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
					<div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
					
					</div>
				</div>
				
				<div class="kt-header__topbar">
					<div class="kt-header__topbar-item kt-header__topbar-item--user">
						<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
							<div class="kt-header__topbar-user">
								<span class="kt-header__topbar-welcome kt-hidden-mobile">Welcome,</span>
								<span class="kt-header__topbar-username kt-hidden-mobile"></span>
								<img class="<?php if(!$portal_profile_image){ ?> kt-hidden <?php } ?>" alt="Pic" src="<?php echo $portal_profile_image;?>" />
								<span class="kt-badge <?php if($portal_profile_image){ ?> kt-hidden <?php } ?> kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold"><?php  echo $_SESSION['name'][0]; ?></span>
							</div>
						</div>
						<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

							<!--begin: Head -->
							<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(assets/media/misc/bg-1.jpg)">
								<div class="kt-user-card__avatar">
									<img class=" <?php if(!$portal_profile_image){ ?> kt-hidden <?php } ?>" alt="Pic" src="<?php echo $portal_profile_image;?>" />

									<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
									<span class="kt-badge <?php if($portal_profile_image){ ?> kt-hidden <?php } ?> kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success"><?php  echo $_SESSION['name'][0]; ?></span>
								</div>
								<div class="kt-user-card__name">
									<?php 
									   echo $_SESSION['name'];
									?>
								</div>
								<div class="kt-user-card__badge">
									<a href="logout.php" class="btn btn-label btn-success btn-sm btn-bold">Sign Out</a>
								</div>
							</div>

							<!--end: Head -->

							<!--begin: Navigation -->
							<div class="kt-notification">
								<!-- <a href="personal_information.php" class="kt-notification__item">
									<div class="kt-notification__item-icon">
										<i class="flaticon2-calendar-3 kt-font-success"></i>
									</div>
									  <div class="kt-notification__item-details">
										<div class="kt-notification__item-title kt-font-bold">
											My Profile
										</div>
										<div class="kt-notification__item-time">
											Account settings and more
										</div>
									</div>
								</a>-->
							</div>
							<!--end: Navigation -->
							</div>
						</div>
					</div>
				</div>