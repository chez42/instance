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
    				<div class="kt-header__topbar-item dropdown">
                        <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px" aria-expanded="false">
                            <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand"><i class="flaticon2-bell-alarm-symbol"></i>
                            	<span class="kt-pulse__ring"></span>
                            </span>
            	            <span class="kt-hidden kt-badge kt-badge--dot kt-badge--notify kt-badge--sm"></span>
                	        </div>
                	        <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg" style="">
                	            <form>
                                <div class="kt-head kt-head--skin-light kt-head--fit-x kt-head--fit-b">
                                    <h3 class="kt-head__title">
                                        User Notifications
                                        &nbsp;
                                        <span class="btn btn-label-primary btn-sm btn-bold btn-font-md notificationCount">23 new</span>
                                    </h3>
                                    <div class="clearfix">&nbsp;</div>
                                </div>
                                            
                                <div class="tab-content ">
                                    <div class="notificationContentArea kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll ps" data-scroll="true" data-height="300" data-mobile-height="200" style="height: 300px; overflow: hidden;">
                                        
                            		</div>
                                </div>
            	            </form>
            	        </div>
            	    </div>
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
							
							<?php 
						  	   if($_SESSION['owner_name'] != ''){
							?>
							<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(assets/media/misc/bg-1.jpg)">
								<div class="kt-user-card__avatar">
									
									
									<img  class ="<?php echo ($_SESSION['owner_image'] == '')?'kt-hidden':''; ?>" src="<?php echo $_SESSION['owner_image'];?>"/>
									
				
									<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
									
									<span class="kt-badge <?php echo ($_SESSION['owner_image'] != '')?'kt-hidden':''; ?> kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success"><?php  echo $_SESSION['owner_name'][0]; ?></span>
								
								</div>
								
								<div class="kt-user-card__name">
									<?php 
									   echo $_SESSION['owner_name'];
									   if($_SESSION['owner_title'] != ''){
									       echo "<br/>";
									       echo "<span style = 'font-size:1.0rem;'>".$_SESSION['owner_title']."</span>";;
									   }
									   if($_SESSION['owner_email'] != ''){
									       echo "<br/>";
									       echo "<span style = 'font-size:1.0rem;'>".$_SESSION['owner_email']."</span>";;
									   }
									   if($_SESSION['owner_office_phone'] != ''){
									       echo "<br/>";
									       echo "<span style = 'font-size:1.0rem;'>".$_SESSION['owner_office_phone']."</span>";;
									   }
									 
									?>
								</div>
								<div class="kt-user-card__badge">
								
								</div>
							</div>
							<?php 
						  	   }
							?>
							

							<!--end: Head -->

							<!--begin: Navigation -->
							<div class="kt-notification">
								<a href="profile.php" class="kt-notification__item">
									<div class="kt-notification__item-icon">
										<i class="flaticon2-calendar-3 kt-font-success"></i>
									</div>
									  <div class="kt-notification__item-details">
										<div class="kt-notification__item-title kt-font-bold">
											My Profile
										</div>
										<div class="kt-notification__item-time">
											
										</div>
									</div>
								</a>
								<a href="#" class="kt-notification__item" data-toggle="modal" data-target="#change_password_form">
									<div class="kt-notification__item-icon">
										<i class="flaticon2-calendar-3 kt-font-success"></i>
									</div>
									  <div class="kt-notification__item-details">
										<div class="kt-notification__item-title kt-font-bold">
											Change Password
										</div>
									</div>
								</a>
								<a href="logout.php" class="kt-notification__item">
									<div class="kt-notification__item-icon">
										<i class="flaticon2-calendar-3 kt-font-success"></i>
									</div>
									  <div class="kt-notification__item-details">
										<div class="kt-notification__item-title kt-font-bold">
											Sign Out
										</div>
									</div>
								</a>
							</div>
							<!--end: Navigation -->
							</div>
						</div>
					</div>
				</div>