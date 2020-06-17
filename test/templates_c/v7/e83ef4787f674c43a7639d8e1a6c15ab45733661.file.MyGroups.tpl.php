<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:34
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Users\uitypes\MyGroups.tpl" */ ?>
<?php /*%%SmartyHeaderCode:237575ee9c3ca478834-62681165%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e83ef4787f674c43a7639d8e1a6c15ab45733661' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Users\\uitypes\\MyGroups.tpl',
      1 => 1589643816,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '237575ee9c3ca478834-62681165',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'RECORD' => 0,
    'GROUPS' => 0,
    'Index' => 0,
    'GROUP' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3ca489e1',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3ca489e1')) {function content_5ee9c3ca489e1($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars['GROUPS'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECORD']->value->getRelatedGroupsInformation(), null, 0);?>
<?php if (count($_smarty_tpl->tpl_vars['GROUPS']->value)>0){?>
</div>
<table style="width:100%!important;margin:10px!important;">
	<tr>
		<th>#</th>
            <th>Group Name</th>
            <th>Description</th>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['GROUP'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['GROUP']->_loop = false;
 $_smarty_tpl->tpl_vars['Index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['GROUPS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['GROUP']->key => $_smarty_tpl->tpl_vars['GROUP']->value){
$_smarty_tpl->tpl_vars['GROUP']->_loop = true;
 $_smarty_tpl->tpl_vars['Index']->value = $_smarty_tpl->tpl_vars['GROUP']->key;
?>
		<tr>
			<td><?php echo $_smarty_tpl->tpl_vars['Index']->value+1;?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['GROUP']->value['name'];?>
</td>
			<td><?php echo $_smarty_tpl->tpl_vars['GROUP']->value['description'];?>
</td>
		</tr>
	<?php } ?>
</table>
<?php }?>
<?php }} ?>