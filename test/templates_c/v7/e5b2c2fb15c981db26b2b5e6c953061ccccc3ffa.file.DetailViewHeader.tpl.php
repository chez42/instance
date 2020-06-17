<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:28:44
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\DetailViewHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:293125ee9c62c84f5c8-65238849%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5b2c2fb15c981db26b2b5e6c953061ccccc3ffa' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\DetailViewHeader.tpl',
      1 => 1589643823,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '293125ee9c62c84f5c8-65238849',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c62c857fb',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c62c857fb')) {function content_5ee9c62c857fb($_smarty_tpl) {?>
<div class=" detailview-header-block sh-effect1"><div class="detailview-header"><div class="row"><?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/DetailViewHeaderTitle.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("DetailViewActions.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div></div><?php }} ?>