<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:19:23
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Settings\Vtiger\Index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:85785ee9c3fb244e11-66855387%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cecb39b04c26cb4abde2113d94e5e5758c87fd5f' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Settings\\Vtiger\\Index.tpl',
      1 => 1589643815,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '85785ee9c3fb244e11-66855387',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'USERS_COUNT' => 0,
    'ACTIVE_WORKFLOWS' => 0,
    'ACTIVE_MODULES' => 0,
    'SETTINGS_SHORTCUTS' => 0,
    'COUNTER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3fb2937b',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3fb2937b')) {function content_5ee9c3fb2937b($_smarty_tpl) {?>
<div class="settingsIndexPage col-lg-12 col-md-12 col-sm-12"><div><h4><?php echo vtranslate('LBL_SUMMARY',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4></div><hr><div class="row"><span class="col-lg-4 col-md-4 col-sm-4 col-xs-12 settingsSummary"><a href="index.php?module=Users&parent=Settings&view=List"><span class="summaryCount"><?php echo $_smarty_tpl->tpl_vars['USERS_COUNT']->value;?>
</span><p class="summaryText" style="margin-top:20px;"><?php echo vtranslate('LBL_ACTIVE_USERS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p></a></span><span class="col-lg-4 col-md-4 col-sm-4 col-xs-12 settingsSummary"><a href="index.php?module=Workflows&parent=Settings&view=List&parentblock=LBL_AUTOMATION"><span class="summaryCount"><?php echo $_smarty_tpl->tpl_vars['ACTIVE_WORKFLOWS']->value;?>
</span><p class="summaryText" style="margin-top:20px;"><?php echo vtranslate('LBL_WORKFLOWS_ACTIVE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p></a></span><span class="col-lg-4 col-md-4 col-sm-4 col-xs-12 settingsSummary"><a href="index.php?module=ModuleManager&parent=Settings&view=List"><span class="summaryCount"><?php echo $_smarty_tpl->tpl_vars['ACTIVE_MODULES']->value;?>
</span><p class="summaryText" style="margin-top:20px;"><?php echo vtranslate('LBL_MODULES',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</p></a></span></div><br><br>&nbsp;<h4><?php echo vtranslate('LBL_SETTINGS_SHORTCUTS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h4><hr><div id="settingsShortCutsContainer" style="min-height: 500px;"><div class="col-lg-12"><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(0, null, 0);?><?php  $_smarty_tpl->tpl_vars['SETTINGS_SHORTCUT'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SETTINGS_SHORTCUT']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['SETTINGS_SHORTCUTS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SETTINGS_SHORTCUT']->key => $_smarty_tpl->tpl_vars['SETTINGS_SHORTCUT']->value){
$_smarty_tpl->tpl_vars['SETTINGS_SHORTCUT']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['COUNTER']->value==3){?></div><div class="col-lg-12"><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable(1, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['COUNTER'] = new Smarty_variable($_smarty_tpl->tpl_vars['COUNTER']->value+1, null, 0);?><?php }?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SettingsShortCut.tpl',$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php } ?></div></div></div><?php }} ?>