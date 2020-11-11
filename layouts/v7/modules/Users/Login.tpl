{strip}
{literal}
	<style>
	
	.login-footer .login-social li {
	    display: inline-block;
	    list-style: none;
	    margin-right: 1em;
	}
	.login-social {padding-top:5px;}
	.fa { color:white;}
	.login-footer {
	position: fixed;
    bottom: 0;
    width: 100%;
    background: rgba(255,250,240,0.39);}
	</style>

{/literal}
<input type="hidden" id="auto_login" data-uname="{$AUTO_U}" data-pword="{$AUTO_P}" />
		<div>
			
			<video autoplay muted loop id="myVideo">
				<source src="test/logo/login-video.mp4" type="video/mp4">
			</video>
			
            <div class="row">
            	
            	<div class="col-md-12">
            		<img class="login-logo" src="test/logo/Omnilogo.png" style = "width:210px;margin:30px;"/>
            	</div>
            
            </div>
            
            <div class="row" style = "margin-top:30px;">
            	<div class="col-md-4">
            	
            	</div>
            	<div class="col-md-4 col-sm-12">
                    <div class="login-content">
                    
                        <h1 style = "font-weight:600;color:white;font-size:24px;">Login to Omniscient CRM</h1>
                        
                        <form class="form-horizontal login-form" action="index.php" method="POST" style = "margin-top:30px;">
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
                                <div class="">
                                    <input style = "border-radius:5px !important;" class="form-control  placeholder-no-fix form-group auto_u" type="text" autocomplete="off" placeholder="OMNI ID" name="username" required/>
                                </div>
                            </div>
                            <div class="row" style = "margin-top:30px;">
                                <div class="">
                                    <input style = "border-radius:5px !important;" class="form-control  placeholder-no-fix form-group auto_p" type="password" autocomplete="off" placeholder="Passphrase" name="password" required/> 
                                </div>
                            </div>
                            <div class="row" style = "margin-top:30px;">
                                <div class="col-sm-12 text-right" style = "padding-right:30px;">
                                    <div class="forgot-password" style = "display:inline-block;float:left;line-height:30px;vertical-align:middle;">
                                        <a style = "color:white;margin-right:10px;"" class="loginIssues"  href="https://ompw.omnisrv.com/pm/" class="forget-password" target="_blank">Login Issues?</a>
                                    </div>
                                    <button class="btn green auto_submit" type="submit" style = "background:#0098CF;border-color:#0098CF;">Sign In</button>
                                </div>
                            </div>
                           
                            <div class="row" style = "margin-top:30px;">
                                <div class="col-sm-12" style = "text-align:center;padding-right:30px;">
                                	{if $OFFICE_ACTIVE}
                                		<button class="btn green officeLogin oauthLogin" data-url="{$AUTH_URL}" type="button" style = "padding:0px;background:#DD4B39;border-color:rgba(0,0,0,0.2);border-radius:10px !important; font-weight:600; color:#FFFFFF !important">
                                			<div class="officeIcon pull-left" style="padding:6px;background-color:white;border-bottom-left-radius:10px!important;border-top-left-radius:10px!important;">
	                                			<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 278050 333334" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd">
	                                				<path fill="#ea3e23" d="M278050 305556l-29-16V28627L178807 0 448 66971l-448 87 22 200227 60865-23821V80555l117920-28193-17 239519L122 267285l178668 65976v73l99231-27462v-316z"></path>
	                            				</svg>
                            				</div>
                            				<div class="officetext" style="padding: 6px;padding-left: 40px;">
                                				Sign In With Office365
                            				</div>
                            			</button>&nbsp;&nbsp;
                                	{/if}
                                	{if $GOOGLE_ACTIVE}
                                		<button class="btn green googleLogin oauthLogin" data-url="{$GOOGLE_AUTH_URL}" type="button" style = "padding:0px;background:#0098CF;border-color:rgba(0,0,0,0.2);border-radius:10px !important; font-weight:600; color:#FFFFFF !important">
                                			<div class="googleIcon pull-left" style="padding:6px;background-color:white;border-bottom-left-radius:10px!important;border-top-left-radius:10px!important;">
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
                                </div>
                            </div>
                        </form>
                    </div>
	            </div>
	         
	            <div class="col-md-4">
	            	
	           	</div>
             </div>   
                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-5 bs-reset">
                                <ul class="login-social">
	                                <li>
                                        <a href="https://facebook.com/omnisrv/" target="_blank">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://twitter.com/omnisrv" target="_blank">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                            		<a href="https://linkedin.com/company/omnisrv" target="_blank">
                                            <i class="fa fa-linkedin"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.youtube.com/channel/UC53BQe0wPV9_TYohwQl2E0g" target="_blank">
                                            <i class="fa fa-youtube"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://instagram.com/omnisrv" target="_blank">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-xs-7 bs-reset">
                                <div class="login-copyright text-right" style = "padding-top:5px;">
                                   <span style = "color:white;padding-right:5px;">&copy; 2004-{date('Y')} </span>
                                    <a href="http://www.omniscientcrm.com" style = "color:#0098CF;">Omniscient CRM</a>
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
