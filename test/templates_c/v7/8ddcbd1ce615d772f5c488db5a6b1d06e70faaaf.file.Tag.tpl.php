<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:26:21
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\Tag.tpl" */ ?>
<?php /*%%SmartyHeaderCode:206685ee9c59d209df9-01399917%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ddcbd1ce615d772f5c488db5a6b1d06e70faaaf' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\Tag.tpl',
      1 => 1589643820,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '206685ee9c59d209df9-01399917',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ACTIVE' => 0,
    'TAG_MODEL' => 0,
    'NO_EDIT' => 0,
    'NO_DELETE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c59d22806',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c59d22806')) {function content_5ee9c59d22806($_smarty_tpl) {?>
 
 <span class="tag <?php if ($_smarty_tpl->tpl_vars['ACTIVE']->value==true){?> active <?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['TAG_MODEL']->value->getName();?>
" data-type="<?php echo $_smarty_tpl->tpl_vars['TAG_MODEL']->value->getType();?>
" data-id="<?php echo $_smarty_tpl->tpl_vars['TAG_MODEL']->value->getId();?>
" style="border-radius: 5px !important;">
    
    <span class="tagLabel display-inline-block textOverflowEllipsis" title="<?php echo $_smarty_tpl->tpl_vars['TAG_MODEL']->value->getName();?>
"><?php echo $_smarty_tpl->tpl_vars['TAG_MODEL']->value->getName();?>
</span>
    <?php if (!$_smarty_tpl->tpl_vars['NO_EDIT']->value){?>
        <i class="editTag material-icons">bookmark</i>
    <?php }?>
    <?php if (!$_smarty_tpl->tpl_vars['NO_DELETE']->value){?>
        <i class="deleteTag material-icons">close</i>
    <?php }?>
</span><?php }} ?>