<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:16:31
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Users\CustomHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:178695ee9c34f6ffda3-24897838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9c028e0d153fc0fabdb78069fa83b3a94e1775a5' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Users\\CustomHeader.tpl',
      1 => 1589643740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '178695ee9c34f6ffda3-24897838',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PAGETITLE' => 0,
    'MODULE_NAME' => 0,
    'SKIN_PATH' => 0,
    'LANGUAGE' => 0,
    'LANGUAGE_STRINGS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c34f7718a',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c34f7718a')) {function content_5ee9c34f7718a($_smarty_tpl) {?><!DOCTYPE html><html><head><title><?php echo vtranslate($_smarty_tpl->tpl_vars['PAGETITLE']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</title><link REL="SHORTCUT ICON" HREF="layouts/v7/skins/images/favicon.ico?v=2"><meta name="viewport" content="width=device-width, initial-scale=1.0" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" /><link href="layouts/v7/modules/Users/resources/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" /><link href="layouts/v7/modules/Users/resources/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" /><link href="layouts/v7/modules/Users/resources/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" /><link href="layouts/v7/modules/Users/resources/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" /><link href="layouts/v7/modules/Users/resources/css/plugins.min.css" rel="stylesheet" type="text/css" /><link href="layouts/v7/modules/Users/resources/css/login.min.css" rel="stylesheet" type="text/css" /><link type='text/css' rel='stylesheet' href='layouts/v7/lib/slick/slick.css'><link type='text/css' rel='stylesheet' href='layouts/v7/lib/slick/slick-theme.css'><script src="layouts/v7/modules/Users/resources/jquery.min.js"></script><!--[if IE]><script type="text/javascript" src="libraries/html5shim/html5.js"></script><script type="text/javascript" src="libraries/html5shim/respond.js"></script><![endif]--></head><body class="login" data-skinpath="<?php echo $_smarty_tpl->tpl_vars['SKIN_PATH']->value;?>
" data-language="<?php echo $_smarty_tpl->tpl_vars['LANGUAGE']->value;?>
"><div id="js_strings" class="hide noprint"><?php echo Zend_Json::encode($_smarty_tpl->tpl_vars['LANGUAGE_STRINGS']->value);?>
</div>
<?php }} ?>