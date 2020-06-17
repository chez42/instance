<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:33
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Settings\Vtiger\SidebarHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:243625ee9c3c9ccfca6-14673518%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eb03514dcc802d3e6f783a846aa194b5af857436' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Settings\\Vtiger\\SidebarHeader.tpl',
      1 => 1589643815,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '243625ee9c3c9ccfca6-14673518',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SELECTED_MENU_CATEGORY' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3c9cdbd6',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3c9cdbd6')) {function content_5ee9c3c9cdbd6($_smarty_tpl) {?>
<?php $_smarty_tpl->tpl_vars["APP_IMAGE_MAP"] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppIcons(), null, 0);?>

<div class="hide col-sm-12 col-xs-12 app-indicator-icon-container app-<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
">
    <div class="row" title="<?php echo vtranslate("LBL_SETTINGS",$_smarty_tpl->tpl_vars['MODULE']->value);?>
">
        <span class="app-indicator-icon fa fa-cog"></span>
    </div>
</div>
    
<?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/partials/SidebarAppMenu.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php }} ?>