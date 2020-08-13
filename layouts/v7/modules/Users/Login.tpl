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
                            {if $OFFICE_ACTIVE}
	                            <div class="row" style = "margin-top:30px;">
	                                <div class="col-sm-12" style = "text-align:center;padding-right:30px;">
	                                	<button class="btn green officeLogin" data-url="{$AUTH_URL}" type="button" style = "background:#DD4B39;border-color:rgba(0,0,0,0.2);border-radius:10px !important; font-weight:600; color:#FFFFFF !important">Sign In With Office365</button>
	                                </div>
	                            </div>
                            {/if}
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
