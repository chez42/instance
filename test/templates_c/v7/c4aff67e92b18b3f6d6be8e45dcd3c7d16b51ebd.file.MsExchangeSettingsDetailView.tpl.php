<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:37
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Users\MsExchangeSettingsDetailView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:303965ee9c3cd0f33c5-50027427%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c4aff67e92b18b3f6d6be8e45dcd3c7d16b51ebd' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Users\\MsExchangeSettingsDetailView.tpl',
      1 => 1590764870,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '303965ee9c3cd0f33c5-50027427',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_MODEL' => 0,
    'RECORD' => 0,
    'CALENDARSYNCDATA' => 0,
    'SYNCDATA' => 0,
    'CONTACTSYNCDATA' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3cd153e2',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3cd153e2')) {function content_5ee9c3cd153e2($_smarty_tpl) {?>
<form id="detailView" data-name-fields='<?php echo ZEND_JSON::encode($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getNameFields());?>
' method="POST"><div class="contents detailview-table"><div class="block"><div class="row"><div class="col-xs-12 marginTop5px"><div class=" pull-right detailViewButtoncontainer"><div class="btn-group  pull-right"><a class="btn btn-default" href="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getMsSettingsEditViewUrl();?>
">Edit</a></div></div></div></div></div><div class="block block_MS_Exchange" data-block="MS Exchange"><div class="row"><h4 class="col-xs-8">Calendar MS Exchange</h4><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)){?><div class="col-xs-4 marginTop5px"><div class=" pull-right detailViewButtoncontainer"><div class="btn-group  pull-right"><button class="btn btn-default revokeMSAccount" data-module="Calendar">Revoke Access</button></div></div></div><?php }?></div><hr><div class="blockData row"><table class="table detailview-table no-border"><tbody><tr><td class="fieldLabel alignMiddle">User Principle Name</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)){?><?php echo $_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value['impersonation_identifier'];?>
<?php }?></td></tr><tr><td class="fieldLabel alignMiddle">Sync Direction</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value['direction']=="11"){?>Sync Both Ways<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value['direction']=="10"){?>Sync from MS Exchange to CRM<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value['direction']=="01"){?>Sync from CRM to MS Exchange<?php }?></td></tr><tr><td class="fieldLabel alignMiddle">Automatic Calendar Sync</td><td class="fieldValue"><input name="automatic_calendar_sync" disabled <?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value['enable_cron']){?>checked<?php }?> type="checkbox" /></td></tr><?php if (!empty($_smarty_tpl->tpl_vars['CALENDARSYNCDATA']->value)){?><tr><td colspan="2" class="text-center"><button type="button"  class="btn btn-success syncNow" data-module="Calendar"><i class="fa fa-refresh"></i> <span>Sync Now</span></button></tr></tr><?php }?></tbody></table></div></div><br><div class="block block_MS_Exchange" data-block="MS Exchange"><div class="row"><h4 class="col-xs-8">Task MS Exchange</h4><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)){?><div class="col-xs-4 marginTop5px"><div class=" pull-right detailViewButtoncontainer"><div class="btn-group  pull-right"><button class="btn btn-default revokeMSAccount" data-module="Task" >Revoke Access</button></div></div></div><?php }?></div><hr><div class="blockData row"><table class="table detailview-table no-border"><tbody><tr><td class="fieldLabel alignMiddle">User Principle Name</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)){?><?php echo $_smarty_tpl->tpl_vars['SYNCDATA']->value['impersonation_identifier'];?>
<?php }?></td></tr><tr><td class="fieldLabel alignMiddle">Sync Direction</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)&&$_smarty_tpl->tpl_vars['SYNCDATA']->value['direction']=="11"){?>Sync Both Ways<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)&&$_smarty_tpl->tpl_vars['SYNCDATA']->value['direction']=="10"){?>Sync from MS Exchange to CRM<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)&&$_smarty_tpl->tpl_vars['SYNCDATA']->value['direction']=="01"){?>Sync from CRM to MS Exchange<?php }?></td></tr><tr><td class="fieldLabel alignMiddle">Automatic Task Sync</td><td class="fieldValue"><input name="automatic_calendar_sync" disabled <?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)&&$_smarty_tpl->tpl_vars['SYNCDATA']->value['enable_cron']){?>checked<?php }?> type="checkbox" /></td></tr><?php if (!empty($_smarty_tpl->tpl_vars['SYNCDATA']->value)){?><tr><td colspan="2" class="text-center"><button type="button"  class="btn btn-success syncNow" data-module="Task"><i class="fa fa-refresh"></i> <span>Sync Now</span></button></tr></tr><?php }?></tbody></table></div></div><br><div class="block block_MS_Exchange" data-block="MS Exchange"><div class="row"><h4 class="col-xs-8">Contact MS Exchange</h4><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)){?><div class="col-xs-4 marginTop5px"><div class=" pull-right detailViewButtoncontainer"><div class="btn-group  pull-right"><button class="btn btn-default revokeMSAccount" data-module="Contacts">Revoke Access</button></div></div></div><?php }?></div><hr><div class="blockData row"><table class="table detailview-table no-border"><tbody><tr><td class="fieldLabel alignMiddle">User Principle Name</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)){?><?php echo $_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value['impersonation_identifier'];?>
<?php }?></td></tr><tr><td class="fieldLabel alignMiddle">Sync Direction</td><td class="fieldValue"><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value['direction']=="11"){?>Sync Both Ways<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value['direction']=="10"){?>Sync from MS Exchange to CRM<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)&&$_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value['direction']=="01"){?>Sync from CRM to MS Exchange<?php }?></td></tr><?php if (!empty($_smarty_tpl->tpl_vars['CONTACTSYNCDATA']->value)){?><tr><td colspan="2" class="text-center"><button type="button"  class="btn btn-success syncNow" data-module="Contacts"><i class="fa fa-refresh"></i> <span>Sync Now</span></button></tr></tr><?php }?></tbody></table></div></div><br><?php }} ?>