<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:45
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\ModalHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:98895ee9c3d5177849-44629054%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e3da5dee0bd02fd6340439e301fc14e3dd166dc7' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\ModalHeader.tpl',
      1 => 1589643819,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '98895ee9c3d5177849-44629054',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'TITLE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3d517ae5',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3d517ae5')) {function content_5ee9c3d517ae5($_smarty_tpl) {?>
<div class="modal-header"><div class="clearfix"><div class="pull-right " ><button type="button" class="btn btn-danger" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class='ti-close'></span></button></div><h4 class="pull-left"><?php echo $_smarty_tpl->tpl_vars['TITLE']->value;?>
</h4></div></div>    <?php }} ?>