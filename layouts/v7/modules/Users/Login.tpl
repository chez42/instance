{strip}
<link rel="stylesheet" type="text/css" href="layouts/v7/skins/vtiger/login.css">
	<style>
		
		.login-footer .login-social li {
		    display: inline-block;
		    list-style: none;
		    margin-right: 1em;
		}
		.fa { color:white;}
		.login-footer {
		    background: rgba(255,250,240,0.39);
	    }
	    .center {
		  display: block;
		  margin-left: auto !important;
		  margin-right: auto !important;
		  width: 50%;
		}
		{if $BGTYPE eq 'image'}
			body{
				background-image: url({$BACKGROUND});
				background-repeat: no-repeat;
			    background-position: center center;
			    background-size: 100% 100%;
			    background-attachment: fixed;
			}
		{/if}
		
	</style>

		<input type="hidden" id="auto_login" data-uname="{$AUTO_U}" data-pword="{$AUTO_P}" />
		{if $BGTYPE eq 'video'}
			<video autoplay muted loop id="myVideo">
				<source src="{$BACKGROUND}" type="video/mp4">
			</video>
		{else if $BGTYPE eq ''}
			<video autoplay muted loop id="myVideo">
				<source src="test/logo/login-video.mp4" type="video/mp4">
			</video>
		{/if}
		
		<div class="app app-header-fixed app-aside-fixed">
			<div ui-view class="fade-in-right-big smooth">
	            <div class="container w-xxl w-auto-xs" >
	            	<div class="list-group list-group-sm">
		        		{if $LOGO}
		        			<img class="login-logo center" src="{$LOGO}" style = "width:210px;"/>
		        		{else}
		        			<img class="login-logo center" src="test/logo/Omnilogo.png" style = "width:210px;"/>
	        			{/if}
	    			</div>
	          
	            	<form class="form-horizontal login-form" action="index.php" method="POST" >
	            		<input type="hidden" name="module" value="Users"/>
              			<input type="hidden" name="action" value="Login"/>
              			<div class="list-group list-group-sm">
	              			<div class="list-group-item alert alert-danger display-hide">
	                            <button class="close" data-close="alert"></button>
	                            <span>Enter any username and password.</span>
	                        </div>
	                    	{if isset($smarty.request.error)}
	                        	<div class="list-group-item alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Invalid username or password.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.fpError)}
								<div class="list-group-item alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Invalid Username or Email address.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.status)}
								<div class="list-group-item alert alert-success">
	                                <button class="close" data-close="alert"></button>
	                                <span>Mail has been sent to your inbox, please check your e-mail.</span>
	                            </div>
							{/if}
							{if isset($smarty.request.statusError)}
								<div class="list-group-item alert alert-danger">
	                                <button class="close" data-close="alert"></button>
	                                <span>Outgoing mail server was not configured.</span>
	                            </div>
							{/if}
						</div>
						<div class="list-group list-group-sm">
							<div class="list-group-item"> 
								<div class="input-group"> 
									<input style="border-radius:5px!important;" type="text" class="auto_u form-control no-border" autocomplete="off" placeholder="OMNI ID" name="username" required /> 
								</div>
			          			<div class="clearfix"></div>
							</div>
						</div>
						<div class="list-group list-group-sm">
							<div class="list-group-item"> 
								<div class="input-group"> 
									<input style="border-radius:5px!important;" class="auto_p form-control no-border" type="password" autocomplete="off" placeholder="Passphrase" name="password" required /> 
								</div>
			          			<div class="clearfix"></div>
							</div>
						</div>
						<input class="btn btn-lg btn-primary btn-block auto_submit" type="submit" value="Sign In" style = "background:#0098CF;border-color:#0098CF;">
						<div class="text-center text-white m-t m-b forgot-password">
					  		<a href="https://ompw.omnisrv.com/pm/" target="_blank" class="loginIssues forget-password text-white">Login Issues?</a>
						</div>
						{if $OFFICE_ACTIVE}
                    		<button class="btn btn-lg btn-block officeLogin oauthLogin pull-left m-b" data-url="{$AUTH_URL}" type="button" style = "padding:0px;background:#DD4B39;border-color:rgba(0,0,0,0.2);font-weight:600; color:#FFFFFF !important">
                    			<div class="officeIcon pull-left" style="padding:6px;background-color:white;">
                        			<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 278050 333334" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd">
                        				<path fill="#ea3e23" d="M278050 305556l-29-16V28627L178807 0 448 66971l-448 87 22 200227 60865-23821V80555l117920-28193-17 239519L122 267285l178668 65976v73l99231-27462v-316z"></path>
                    				</svg>
                				</div>
                				<div class="officetext" style="padding: 6px;padding-left: 40px;">
                    				Sign In With Office365
                				</div>
                			</button>
                    	{/if}
                    	{if $GOOGLE_ACTIVE}
                    		<button class="btn btn-lg btn-block googleLogin oauthLogin m-b" data-url="{$GOOGLE_AUTH_URL}" type="button" style = "padding:0px;background:#0098CF;border-color:rgba(0,0,0,0.2);font-weight:600; color:#FFFFFF !important">
                    			<div class="googleIcon pull-left" style="padding:6px;background-color:white;">
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 48 48" class="abcRioButtonSvg">
										<g>
											<path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>	
											<path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
											<path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
											<path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
											<path fill="none" d="M0 0h48v48H0z"></path>
										</g>
									</svg>
								</div>
								<div class="googletext" style="padding: 6px;padding-left: 40px;">
                    				Sign In With Google
                				</div>
                			</button>
                		{/if}
					</form> 
              	</div>
              	<div class="w-full" >
              		<div class="app-footer login-footer text-center" style="margin-left:0px!important;">
              			{if $COPYRIGHT}
			          		<div class="login-copyright " style = "padding-top:5px;">
			                   <span style = "color:white;padding-right:5px;">&copy; {$COPYRIGHT} </span>
			                </div>
		                {/if}
                        <ul class="login-social" style = "padding:0px;margin-top:5px;">
                        	{if $FACEBOOK_LINK}
	                            <li>
	                                <a href="{$FACEBOOK_LINK}" target="_blank">
	                                    <i class="fa fa-facebook"></i>
	                                </a>
	                            </li>
                            {/if}
                            {if $TWITTER_LINK}
	                            <li>
	                                <a href="{$TWITTER_LINK}" target="_blank">
	                                    <i class="fa fa-twitter"></i>
	                                </a>
	                            </li>
                            {/if}
                            {if $LINKEDIN_LINK}
                            	<li>
	                    			<a href="{$LINKEDIN_LINK}" target="_blank">
	                                    <i class="fa fa-linkedin"></i>
	                                </a>
	                            </li>
                            {/if}
                            {if $YOUTUBE_LINK}
	                            <li>
	                                <a href="{$YOUTUBE_LINK}" target="_blank">
	                                    <i class="fa fa-youtube"></i>
	                                </a>
	                            </li>
                            {/if}
                            {if $INSTA_LINK}
	                            <li>
	                                <a href="{$INSTA_LINK}" target="_blank">
	                                    <i class="fa fa-instagram"></i>
	                                </a>
	                            </li>
                            {/if}
                        </ul>
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
