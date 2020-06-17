<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:28:44
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Vtiger\DetailViewFullContents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:314985ee9c62ccb0ae9-74082097%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6768144d9e58cdd9b23cab85c12939fff80ec87a' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Vtiger\\DetailViewFullContents.tpl',
      1 => 1589643770,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '314985ee9c62ccb0ae9-74082097',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'TABVIEW' => 0,
    'MODULE_NAME' => 0,
    'RECORD_STRUCTURE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c62ccc2ed',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c62ccc2ed')) {function content_5ee9c62ccc2ed($_smarty_tpl) {?>



<form id="detailView" method="POST"><?php if ($_smarty_tpl->tpl_vars['TABVIEW']->value){?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DetailViewBlockTabsView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>
<?php }else{ ?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('DetailViewBlockView.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('RECORD_STRUCTURE'=>$_smarty_tpl->tpl_vars['RECORD_STRUCTURE']->value,'MODULE_NAME'=>$_smarty_tpl->tpl_vars['MODULE_NAME']->value), 0);?>
<?php }?></form>
<?php }} ?>