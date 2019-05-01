{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Users/views/Login.php *}
{strip}

<!--
###################################
EDIT SLIDE INFORMATION near line 557
###################################
-->


<!--<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.0/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.0/slick/slick-theme.css"/>-->
<head>
  <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<style>

	.slick-prev:hover{
		color: #00A6E8!important;
	}
	.slick-next:hover{
		color:#00A6E8 !important;
	}

	.slick-prev:before, .slick-next:before {
		color: black !important;
		font-size: 21px;
		border-radius: 15%;
		box-shadow: 0 0 16px gray;
	}

	.round {
		border-radius: 90%;
	}

	body {
		margin: 0;
		font-family: Arial;
		font-size: 17px;
	}
	#myVideo {
		position: fixed;
		right: 0;
		bottom: 0;
		min-width: 100%;
		min-height: 100%;
	}
	hr {
		margin-top: 15px;
		background-color: #7C7C7C;
		height: 2px;
		border-width: 0;
	}
	h3, h4 {
		margin-top: 0px;
	}
	hgroup {
		text-align:center;
		margin-top: 4em;
	}
	input {
		font-size: 16px;
		padding: 10px 10px 10px 0px;
		-webkit-appearance: none;
		display: block;
		color: #636363;
		width: 100%;
		border: none;
		border-radius: 0;
		border-bottom: 1px solid #757575;
	}
	input:focus {
		outline: none;
	}
	label {
		font-size: 16px;
		font-weight: normal;
		position: absolute;
		pointer-events: none;
		left: 0px;
		top: 10px;
		transition: all 0.2s ease;
	}
	input:focus ~ label, input.used ~ label {
		top: -20px;
		transform: scale(.75);
		left: -12px;
		font-size: 18px;
	}
	input:focus ~ .bar:before, input:focus ~ .bar:after {
		width: 50%;
	}
	#page {
		padding-top: 6%;
	}

	.loginDiv {
		width: 380px;
		height:280px;
		border-radius: 8px;
		box-shadow: 0 0 10px rgba(21,66,105,0.5);
		background: rgba(255,250,240,0.2);
		margin-left: 35%;
		padding-top: 10px;
	}
	/*
	.separatorDiv {
		margin-left: 20px;
	}
	*/
	.user-logo {
		height: 110px;
		margin: 0 auto;
		padding-top: 40px;
		padding-bottom: 20px;
	}
	.blockLink {
		border: 1px solid #303030;
		padding: 3px 5px;
	}
	.group {
		position: relative;
		margin: 20px 20px 40px;
	}
	.failureMessage {
		color: red;
		display: block;
		text-align: center;
		padding: 0px 0px 10px;
	}
	.successMessage {
		color: green;
		display: block;
		text-align: center;
		padding: 0px 0px 10px;
	}
	.inActiveImgDiv {
		padding: 5px;
		text-align: center;
		margin: 30px 0px;
	}
	/*
	.app-footer p {
		margin-top: 0px;
	}
	.footer {
		background-color: transparent;
		height:26px;
	}
	*/
	.bar {
		position: relative;
		display: block;
		width: 100%;
	}
	.bar:before, .bar:after {
		content: '';
		width: 0;
		bottom: 1px;
		position: absolute;
		height: 1px;
		background: #0097CE;
		transition: all 0.2s ease;
		box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
	}
	.bar:before {
		left: 50%;
	}
	.bar:after {
		right: 50%;
	}

/* Sign in Button */
 	.button {
		position: relative;
		display: inline-block;
		padding: 1px;
		margin: .3em 0 1em 1.5em;
		height:38px;
		width: 285px;
		vertical-align: middle;
		color: rgb(21,66,105);
		font-size: 17px;
		line-height: 23px;
		text-align: center;
		letter-spacing: 1px;
		background-color: rgba(255,250,240,0.3);
		border: 0;
		cursor: pointer;
		border-radius: 8px;
		transition: all 0.6s ease;
		box-shadow: 0px 4px 13px rgba(21,66,105,0.5);
		background-position: center;
	}

	.button:focus {
		outline: 0;
	}

	.button:hover {
		background: rgba(21,66,105,0.6);
		box-shadow: 0px 4px 13px rgba(255,250,240,0.5);
		color: rgba(255,255,255,1.5);
		transform: translateY(-3px);
		-webkit-transition: transform 0.3s ;
	}

/*sign End*/
	.ripples {
		position: absolute;
		top: 0
		left: 0;
		width: 100%;
		height: 100%;
		overflow: hidden;
		background: transparent;
	}
	.overlay {
		position: relative;
		font-family: Arial;
		align: center;
	}
	.text-block {
		font-family: 'PT Sans', sans-serif;
		margin: auto;
		width: 100%
		position: absolute;
		background: transparent;
		color: black;
		padding-left: 20px;
		padding-right: 20px;
	}
	.slideshow-img {
		width: 100%;
		height: 100%;
	}
//Animations
	@keyframes inputHighlighter {
		from {
			background: #4a89dc;
		}
		to   {
			width: 0;
			background: transparent;
		}
	}
	@keyframes ripples {
		0% {
			opacity: 0;
		}
		25% {
			opacity: 1;
		}
		100% {
			width: 200%;
			padding-bottom: 200%;
			opacity: 0;
		}
	}

/* The Modal (background) */
	.modalDialog {
		position: fixed;
		font-family: Arial, Helvetica, sans-serif;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background: rgba(0, 0, 0, 0.8);
		z-index: 99999;
		opacity:0;
		-webkit-transition: opacity 400ms ease-in;
		-moz-transition: opacity 400ms ease-in;
		transition: opacity 400ms ease-in;
		pointer-events: none;
	}
	.modalDialog:target {
		opacity:1;
		pointer-events: auto;
	}
	.modalDialog  {
		width: 50%;
		position: relative;
		margin: 10% auto;
		padding: 5px 20px 13px 20px;
		border-radius: 10px;
		background: #fff;
		background: -moz-linear-gradient(#fff, #999);
		background: -webkit-linear-gradient(#fff, #999);
		background: -o-linear-gradient(#fff, #999);
	}
	.close {
		background: #606061;
		color: #FFFFFF;
		line-height: 25px;
		position: absolute;
		right: -12px;
		text-align: center;
		top: -10px;
		width: 24px;
		text-decoration: none;
		font-weight: bold;
		-webkit-border-radius: 12px;
		-moz-border-radius: 12px;
		border-radius: 12px;
		-moz-box-shadow: 1px 1px 3px #000;
		-webkit-box-shadow: 1px 1px 3px #000;
		box-shadow: 1px 1px 3px #000;
	}
	.close:hover {
		background: #00d9ff;
	}

/* Social Media Buttons */

	.svg-inline--fa {
		vertical-align: -0.200em;
	}

	.rounded-social-buttons {
		text-align: center;
	}

	.rounded-social-buttons .social-button {
		display: inline-block;
		position: relative;
		cursor: pointer;
		width: 7rem;
		height: 3.8rem;
		color: #2d465b;
		font-weight: normal;
		line-height: 30px;
		border-radius: 8px;
		transition: all 0.5s ease;
		margin-right: 2em;
		margin-bottom:6em;
		text-align: center;
	}

	.rounded-social-buttons .social-button:hover, .rounded-social-buttons .social-button:focus {
    /*
		-webkit-transform: rotate(360deg);
		-ms-transform: rotate(360deg);
		transform: rotate(360deg);
	*/
		transform: translate(0,-4px);
		-webkit-transform: translate(0,-4px);
		-o-transform: translate(0,-4px);
		-moz-transform: translate(0,-4px);
		-webkit-transition: transform 0.2s ;
	}
	.rounded-social-buttons .fa-twitter, .fa-facebook-f, .fa-linkedin, .fa-youtube, .fa-instagram {
		font-size: 24px;
	}

	.rounded-social-buttons .social-button.facebook {

	}

	.rounded-social-buttons .social-button.facebook:hover, .rounded-social-buttons .social-button.facebook:focus {
		color: #3b5998;
		background: rgba(255,250,240,0.9);
	}	

	.rounded-social-buttons .social-button.twitter {

	}

	.rounded-social-buttons .social-button.twitter:hover, .rounded-social-buttons .social-button.twitter:focus {
		color: #55acee;
		background: rgba(255,250,240,0.9);
	}

	.rounded-social-buttons .social-button.linkedin {
		background: transparent;
	}

	.rounded-social-buttons .social-button.linkedin:hover, .rounded-social-buttons .social-button.linkedin:focus {
		color: #007bb5;
		background: rgba(255,250,240,0.9);
	}

	.rounded-social-buttons .social-button.youtube {
		background: transparent;
	}

	.rounded-social-buttons .social-button.youtube:hover, .rounded-social-buttons .social-button.youtube:focus {
		color: #bb0000;
		background: rgba(255,250,240,0.9);
	}

	.rounded-social-buttons .social-button.instagram {
		background: transparent;
	}

	.rounded-social-buttons .social-button.instagram:hover, .rounded-social-buttons .social-button.instagram:focus {
		color: #bc2a8d;
		background: rgba(255,250,240,0.9);
	}
/*social end */
/*
	.separatorDiv {
		background-color: black;
		width: 1px;
		height: 460px;
		margin-left: -60%;
		-webkit-box-shadow: 1px 1px 3px #000;
	}
*/
	.footer {
		position: fixed;
		left: 0;
		bottom: 0;
		width: 100%;
		background: rgba(255,250,240,0.39);
		text-align: right;
	}
	.footerText {
		position: fixed;
		font-family: 'PT Sans', sans-serif;
		left: 0;
		bottom: 0;
		width: 100%;
		color: black;
		text-align: center;
	}
	.ads{
		margin-top: 8.5%;
		border-radius: 2%;
		box-shadow: 0 0 10px rgba(21,66,105,0.5);
		margin-right: 3%;
	}
	.separatorDiv {
		background-color: black;
		width: 1px;
		height: 460px;
		margin-left: -60%;
		-webkit-box-shadow: 1px 1px 3px #000;
	}
	.errorBox{
		background-color: rgba(250,225,227,0.8);
		font-size: 15px;
		color:#E63F4E ;
		position: absolute;
		border-radius: 8px;
		top: 0px;
		width: 138%;
	}

	.inputBox{
		background-color:transparent;
		color: black;
	}
	.fixed{

	}
	.tnc{
		font-weight:bold;
	}
	.tnc:hover{
		color:blue;
	}
	.loginIssues{
		font-size: 12px;
		font-weight: bold;
	}
	.loginIssues:hover{
		color:blue;
	}

	.at-banner {
		z-index: 999999;
		position: fixed;
		top: 0;
		right: 0;
		left: 0;
		background: rgba(250,225,227,0.8);
		width: 100%;
		border-bottom: 1px solid #fffaf0;
		padding: 10px;
		box-sizing: border-box;
		transform: translateY(-150%);
		color: #E63F4E ;
		font-family: "Open Sans", sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
		animation: at-banner-slide-in 0.8s ease forwards;
		&__content {
			display: flex;
			align-items: center;
			flex-direction: row;
			justify-content: center;
			width: 90%;
			margin: 0 auto;
			padding: 10px 40px;
			box-sizing: border-box;
		}
		&__title {
			font-size: 18px;
		}
		&__text {
			margin: 0 20px 0 0;
		}
		&__button {
			display: inline-block;
			background: rgba(250,225,227,0.8);
			height: 40px;
			border: 0;
			border-radius: 2px;
			box-shadow: 0 2px 4px rgba(#000, 0.1);
			padding: 0 20px;
			color: #E63F4E ;
			font-size: 12px;
			font-weight: 700;
			line-height: 40px;
			text-decoration: none;
			white-space: nowrap;
		}
		&__close {
			position: absolute;
			top: 50%;
			right: 20px;
			width: 20px;
			height: 20px;
			transform: translateY(-50%);
			cursor: pointer;
		&:before,
		&:after {
			content: "";
			position: absolute;
			top: 50%;
			left: 50%;
			display: block;
			background:rgba(250,225,227,0.8);
			width: 100%;
			height: 3px;
			border-radius: 2px;
			transform-origin: center;
		}
		&:before {
			transform: translate(-50%, -50%) rotate(-45deg);
		}
		&:after {
			transform: translate(-50%, -50%) rotate(45deg);}
		}
	}
	@keyframes at-banner-slide-in {
		0% {
			transform: translateY(-150%);
		}
		100% {
			transform: translateY(0%);
		}
	}
</style>

<body>

    <span class="{if !$ERROR}hide{/if} failureMessage at-banner at-banner__content at-banner__text"  id="validationMessage">{$MESSAGE}</span>
    <span class="{if !$MAIL_STATUS}hide{/if} successMessage">{$MESSAGE}</span>   
    <video autoplay muted loop id="myVideo">
		<source src="layouts/v7/resources/marketing/videograss.mp4" type="video/mp4">
	</video>

   <span class="app-nav"></span>
   <div class="col-lg-12 col-md-12 col-sm-10 col-xs-10" style="background-color: transparent;" >
		<div class="hidden-lg hidden-md col-sm-12 col-xs-12" style="display:inline-block;padding:0px 0px 0px 100px">
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
   	<style>
	   	@media (max-width : 700px) {
	   		.col-xs-8{
	   			float: none!important;
	   		}
   		}
   	</style>
      <div class="col-lg-4 col-md-5 col-sm-8 col-xs-8" style="background-color: transparent;">

        <div class="fixed">
         
        <img  style=" margin-left:46.3%;" class="img-responsive user-logo" src="layouts/v7/resources/Images/logo.png">
         <div class="loginDiv widgetHeight">
            <div id="loginFormDiv" >
               <form class="form-horizontal" method="POST" action="index.php">
                  <input type="hidden" name="module" value="Users"/>
                  <input type="hidden" name="action" value="Login"/>
                  <div class="group">
                     <input class="inputBox" id="username" type="text" name="username" placeholder="Username">
                     <span class="bar"></span>
                     <label>Username</label>
                  </div>
                  <div class="group">
                     <input style="margin-bottom: 6px;" class="inputBox" id="password" type="password" name="password" placeholder="Password">
                     <span class="bar"></span>
                     <label>Password</label>
                     <p class="tncSpace" style="font-size: 10px">By signing in you agree to our <a class="tnc" href="http://omniscientcrm.com/term-conditions/" target="_blank">Terms and Conditions</a></p>
                     <button type="submit" class="button sHov">Sign In</button><br>

                     <a class="loginIssues"  href="https://ompw.omnisrv.com/pm/" target="_blank">Login Issues?</a>
                  </div>
               </form>
            </div>
         </div>
       </div>
      </div>

      <div class="col-lg-2 col-md-1 hidden-xs hidden-sm ">
      </div>

      <div class="col-lg-1 hidden-xs hidden-sm">
      <div class="separatorDiv"></div></div>

      <!--FOOTER-->
      <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js" integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe" crossorigin="anonymous"></script>

		<style>
		   	@media (max-width : 700px) {
		   		.footerText{
		   			margin-left: 10px!important;
		   			text-align: left!important;
		   		}
		   		.rounded-social-buttons .social-button{
		   			width: 1rem!important;
		   		}
	   		}
	   	</style>

      <div class="rounded-social-buttons footer">
              <div class="footerText" style="font-size:15px; ">
              <p>Powered by <a style="color: blue" href="http://omniscientcrm.com/" target="_blank">Omniscient CRM v4.0 </a></p>

          </div>
                <a class="social-button facebook" href="https://www.facebook.com/OMNI2016/" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a class="social-button twitter" href="https://twitter.com/OMNIGlobal2016" target="_blank"><i class="fab fa-twitter"></i></a>
                <a class="social-button linkedin" href="https://www.linkedin.com/company/omni-global-group?" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a class="social-button youtube" href="https://www.youtube.com/channel/UC53BQe0wPV9_TYohwQl2E0g" target="_blank"><i class="fab fa-youtube"></i></a>
                <a class="social-button instagram" href="https://www.instagram.com/omni_global_2016/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
		</div>



      <script>
			var top_level_div = document.getElementById('txt-load')
			, divs = top_level_div.getElementsByTagName('div')
			, counter = 0
			, interval = window.setInterval(function() {

			// hide the previous div if it exists (counter > 0)
			if(divs[counter - 1]) {
				divs[counter - 1].style.display = "none";
			}

			// set the current div visible
			divs[counter].style.display = "block";
			counter += 1;

			// have we finished all divs? then clear the interval
			if(counter === divs.length) {
				window.clearInterval(interval);
			}
		}, 2); // run that every 15 seconds​
	</script>


      <!-- ********************** EDIT SLIDE INFO HERE *********************** -->
      <div class="col-lg-4 col-md-4 hidden-xs hidden-sm" style="display: inline-block; padding: 50px 0px 0px 0px">

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
      </div>

      <!-- *********************** EDIT SLIDE INFO HERE ********************** -->

	<script>
		jQuery(document).ready(function () {
			var validationMessage = jQuery('#validationMessage');
			var loginFormDiv = jQuery('#loginFormDiv');
			loginFormDiv.find('#password').focus();
			loginFormDiv.find('button').on('click', function () {
			var username = loginFormDiv.find('#username').val();
			var password = jQuery('#password').val();
			var result = true;
			var errorMessage = '';
			if (username === '') {
				errorMessage = 'Please enter valid username';
				result = false;
			} else if (password === '') {
				errorMessage = 'Please enter valid password';
				result = false;
			}
			if (errorMessage) {
				validationMessage.removeClass('hide').text(errorMessage);
			}
			return result;
		});
      

		jQuery('input').blur(function (e) {
			var currentElement = jQuery(e.currentTarget);
			if (currentElement.val()) {
				currentElement.addClass('used');
			} else {
				currentElement.removeClass('used');
			}
		});
		var ripples = jQuery('.ripples');
		ripples.on('click.Ripples', function (e) {
			jQuery(e.currentTarget).addClass('is-active');
		});
		ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function (e) {
			jQuery(e.currentTarget).removeClass('is-active');
		});
		loginFormDiv.find('#username').focus();
		var slider = jQuery('.bxslider').bxSlider({
			auto: true,
			pause: 4000,
			nextText: "",
			prevText: "",
 			autoHover: true
		});
		jQuery('.bx-prev, .bx-next, .bx-pager-item').live('click',function(){ slider.startAuto(); });
		jQuery('.bx-wrapper .bx-viewport').css('background-color', 'transparent');
		jQuery('.bx-wrapper .bxslider li').css('text-align', 'left');
		jQuery('.bx-wrapper .bx-pager').css('bottom', '-15px');
		var params = {
			theme   : 'dark-thick',
			setHeight : '100%',
			advanced  : {
				autoExpandHorizontalScroll:true,
				setTop: 0
			}
		};
		jQuery('.scrollContainer').mCustomScrollbar(params);
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
  </script>
</body>
{/strip}
