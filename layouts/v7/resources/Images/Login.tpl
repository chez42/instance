{strip}
<input type="hidden" id="auto_login" data-uname="{$AUTO_U}" data-pword="{$AUTO_P}" />
		<div class="user-login-5">
<style>
	background: url(layouts/v7/resources/Images/login-background_own.jpg);

</style>

            <div class="row bs-reset">
            	<div class="col-md-12">
            		<img class="login-logo" src="layouts/v7/resources/Images/logo.png" style = "width:210px;"/>
            	</div>
            	 <div class="col-md-6 col-md-push-6 col-sm-12 col-xs-12 bs-reset mt-login-5-bsfix">
                	<div class="col-md-1 col-sm-1 col-xs-1"></div>
                	<div class="col-md-10 col-sm-10 col-xs-10" style="display: inline-block; padding: 15% 0px 0px 0px">
	                    <div class="adCarousel ads">
				            <div class="overlay">
				               <img class="slideshow-img" src="layouts/v7/resources/marketing/img4.jpg">
				               <div class="text-block txt-load">
				                  <h3> Beautiful Improved Desktop </h3>          <!-- Title 1st Slide -->
				                  <p> Brighter and sharper working environment makes the system fun to use. </p>  <!-- Body text 1st Slide -->
				               </div>
				            </div>
				            <div class="overlay">
				               <img class="slideshow-img" data-lazy="layouts/v7/resources/marketing/img1.jpg">
				               <div class="text-block txt-load">
				                  <h3> Enterprise Quality Automation </h3>      <!-- Title 2nd Slide -->
				                  <p> Workflows and email marketing automation integrated right into Omniscient.</p>  <!-- Body text 2nd Slide -->
				               </div>
				            </div>
				            <div class="overlay">
				               <img class="slideshow-img" data-lazy="layouts/v7/resources/marketing/img3.jpg">
				               <div class="text-block txt-load">
				                  <h3>Better Integration</h3>  <!-- Title 3rd Slide -->
				                  <p> Ring Central, Wealthkit, Stratifi and Microsoft Exchange are a few of the new world class integrations incorporated in version 4. </p> <!-- Body text 3rd Slide -->
				               </div>
				            </div>
				            <div class="overlay">
				               <img class="slideshow-img" data-lazy="layouts/v7/resources/marketing/img2.jpg">
				               <div class="text-block txt-load">
				                  <h3> Your World In One Place </h3>  <!-- Title 4th Slide -->
				                  <p> See every aspect of your business with beautiful reports and dashboards...then take action with a single click!. </p>      <!-- Body text 4th Slide -->
				               </div>
				            </div>
				            <div class="overlay">
				               <img class="slideshow-img" data-lazy="layouts/v7/resources/marketing/img5.jpg">
				               <div class="text-block txt-load">
				                  <h3>Secure Client Portal</h3>  <!-- Title 5th Slide -->
				                  <p>Share information to and from your clients.</p>  <!-- Body text 5th Slide -->
				               </div>
				            </div>
				         </div>
			         </div>
			         <div class="col-md-1 col-sm-1 col-xs-1"></div>
                </div>
                <div class="col-md-6 col-md-pull-6 col-sm-12 col-xs-12 login-container bs-reset mt-login-5-bsfix">
                    <div class="login-content">
                        <h1>Login to Omniscient CRM - test</h1>
                        <form class="form-horizontal login-form" action="index.php" method="POST">
                         	<input type="hidden" name="module" value="Users"/>
                  			<input type="hidden" name="action" value="Login"/>
                        	<div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                <span>Enter any username and password.</span>
                            </div>
                        	{if isset($smarty.request.error)}
	                        	<div class="alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Invalid username or password.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.fpError)}
								<div class="alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Invalid Username or Email address.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.status)}
								<div class="alert alert-success">
	                                <button class="close" data-close="alert"></button>
	                                <span>Mail has been sent to your inbox, please check your e-mail.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.statusError)}
								<div class="alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Outgoing mail server was not configured.</span>
	                            </div>
							{/if}
                            <div class="row">
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group auto_u" type="text" autocomplete="off" placeholder="OMNI ID" name="username" required/> </div>
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group auto_p" type="password" autocomplete="off" placeholder="Passphrase" name="password" required/> </div>
                            </div>
                            <div class="row">

                                <div class="col-sm-12 text-right">
                                    <div class="forgot-password">
                                        <a class="loginIssues"  href="https://ompw.omnisrv.com/pm/" class="forget-password" target="_blank">Login Issues?</a>
                                    </div>
                                    <button class="btn green auto_submit" type="submit">Sign In</button>
                                </div>
                            </div>
                        </form>

                        <!-- BEGIN FORGOT PASSWORD FORM -->

                        <form class="form-horizontal login-form forget-form1" action="forgotPassword.php" method="POST">
						    <h3 class="font-green">Forgot Password ?</h3>
                            <p>&nbsp;</p>
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                <span>Enter any username and email.</span>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <input class="form-control placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Username" name="user_name" />
                           		</div>
                           		<div class="col-xs-6">
                                    <input class="form-control placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Email" name="emailId" />
                           		</div>
                            </div>
                            <div class="row">
	                           	<div class="form-actions">
	                                <button type="button" id="back-btn" class="btn green btn-outline">Back</button>
	                                <button type="submit" class="btn btn-success uppercase pull-right">Submit</button>
	                            </div>
                            </div>
                        </form>

                        <!-- END FORGOT PASSWORD FORM -->
                    </div>

                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-5 bs-reset">
                                <ul class="login-social">
	                                <li>
                                        <a href="https://www.facebook.com/OMNI2016/" target="_blank">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/OMNIGlobal2016" target="_blank">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                            		<a href="https://www.linkedin.com/company/omni-global-group?" target="_blank">
                                            <i class="fa fa-linkedin"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.youtube.com/channel/UC53BQe0wPV9_TYohwQl2E0g" target="_blank">
                                            <i class="fa fa-youtube"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.instagram.com/omni_global_2016/?hl=en" target="_blank">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-xs-7 bs-reset">
                                <div class="login-copyright text-right">
                                   <span>&copy; 2004-{date('Y')} </span>
                                    <a href="http://www.omniscientcrm.com">Omniscient CRM</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END : LOGIN PAGE 5-1 -->
        <!--[if lt IE 9]>
		<script src="layouts/v7/modules/Users/resources/respond.min.js"></script>
		<script src="layouts/v7/modules/Users/resources/excanvas.min.js"></script>
		<script src=".layouts/v7/modules/Users/resources/ie8.fix.min.js"></script>
		<![endif]-->
        <script src="layouts/v7/modules/Users/resources/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
      	<script src="layouts/v7/modules/Users/resources/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="layouts/v7/modules/Users/resources/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="layouts/v7/modules/Users/resources/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="layouts/v7/modules/Users/resources/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="layouts/v7/modules/Users/resources/login.js" type="text/javascript"></script>
        <script src="libraries/jquery/boxslider/jquery.bxslider.min.js" type="text/javascript"></script>
    	<script type="text/javascript" src="layouts/v7/lib/slick/slick.js"></script>
        <script type="text/javascript" src="layouts/v7/modules/Users/resources/AutoLogin.js"></script>
    	<script>

    	</script>
    </body>
</html>
{/strip}
