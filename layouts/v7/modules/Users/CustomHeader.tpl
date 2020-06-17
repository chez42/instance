{strip}
<!DOCTYPE html>
<html>
	<head>
		<title>
			{vtranslate($PAGETITLE, $MODULE_NAME)}
		</title>
		<link REL="SHORTCUT ICON" HREF="layouts/v7/skins/images/favicon.ico?v=2">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
       	<link href="layouts/v7/modules/Users/resources/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="layouts/v7/modules/Users/resources/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="layouts/v7/modules/Users/resources/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="layouts/v7/modules/Users/resources/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="layouts/v7/modules/Users/resources/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="layouts/v7/modules/Users/resources/css/login.min.css" rel="stylesheet" type="text/css" />
    	<link type='text/css' rel='stylesheet' href='layouts/v7/lib/slick/slick.css'>
        <link type='text/css' rel='stylesheet' href='layouts/v7/lib/slick/slick-theme.css'>
		{* This is needed as in some of the tpl we are using jQuery.ready *}
		<script src="layouts/v7/modules/Users/resources/jquery.min.js"></script>
		<!--[if IE]>
		<script type="text/javascript" src="libraries/html5shim/html5.js"></script>
		<script type="text/javascript" src="libraries/html5shim/respond.js"></script>
		<![endif]-->
		{* ends *}

	</head>

	<body class="login" data-skinpath="{$SKIN_PATH}" data-language="{$LANGUAGE}">
		<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>
		
{/strip}
