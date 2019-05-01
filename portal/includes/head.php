<?php 
    require_once './languages/en_us.php';

	include_once("includes/config.php");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <title><?php if(isset($GLOBALS['portal_title'])) echo $GLOBALS['portal_title']." - "; echo 'Omniscient Customer Portal'; ?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
		<meta name="description" content="">
		<meta name="author" content="">
		
        
		<link href="https://fonts.googleapis.com/css?family=Oswald:400,300,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        
        <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="metronic/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <link href="metronic/css/vendors.bundle.css" rel="stylesheet" type="text/css" />
        <link href="metronic/css/datatables.bundle.css" rel="stylesheet" type="text/css" />
        <link href="metronic/css/style.css" rel="stylesheet" type="text/css" />
		<!-- END THEME GLOBAL STYLES -->
        
		<?php 	
			if(isset($_SESSION["ID"]) && !empty($_SESSION["ID"])){
				
				include_once('header.php');
			}
		?>
		
		<link rel="shortcut icon" href="favicon.ico" /> 
			
		<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
		<style>
            * {
              font-family: Arial, sans-serif;
            }
			
			.no-js #loader { display: none;  }
            .js #loader { display: block; position: absolute; left: 100px; top: 0; }
            .se-pre-con {
            	position: fixed;
            	left: 0px;
            	top: 0px;
            	width: 100%;
            	height: 100%;
            	z-index: 9999;
            	background: url(assets/img/loading.gif) center no-repeat #fff;
            }
        </style>
	</head>
	<!-- END HEAD -->
	
	<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-light m-aside-left--fixed m-aside-left--offcanvas m-aside-left--minimize m-brand--minimize m-footer--push m-aside--offcanvas-default page-header-fixed <?php if(!$module) echo 'login'; else echo "module_".$module."_container"; ?>">
	<div class="se-pre-con"></div>