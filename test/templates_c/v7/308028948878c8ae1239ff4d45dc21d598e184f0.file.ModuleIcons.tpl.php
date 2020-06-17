<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:21
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\partials\ModuleIcons.tpl" */ ?>
<?php /*%%SmartyHeaderCode:60095ee9c3bd0662d8-33615655%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '308028948878c8ae1239ff4d45dc21d598e184f0' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\partials\\ModuleIcons.tpl',
      1 => 1589643821,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '60095ee9c3bd0662d8-33615655',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'APP_LIST' => 0,
    'APP_NAME' => 0,
    'APP_GROUPED_MENU' => 0,
    'moduleName' => 0,
    'iconsarrayTemp' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bd0ad95',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bd0ad95')) {function content_5ee9c3bd0ad95($_smarty_tpl) {?><?php $_smarty_tpl->tpl_vars['iconsarrayTemp'] = new Smarty_variable(array('expenses'=>'monetization_on','potentials'=>'attach_money','marketing'=>'thumb_up','leads'=>'thumb_up','accounts'=>'business','sales'=>'attach_money','smsnotifier'=>'sms','services'=>'format_list_bulleted','pricebooks'=>'library_books','salesorder'=>'attach_money','purchaseorder'=>'attach_money','vendors'=>'local_shipping','faq'=>'help','helpdesk'=>'headset','assets'=>'settings','project'=>'card_travel','projecttask'=>'check_box','projectmilestone'=>'card_travel','mailmanager'=>'email','documents'=>'file_download','calendar'=>'event','emails'=>'email','reports'=>'show_chart','servicecontracts'=>'content_paste','contacts'=>'contacts','campaigns'=>'notifications','quotes'=>'description','invoice'=>'description','emailtemplates'=>'subtitles','pbxmanager'=>'perm_phone_msg','rss'=>'rss_feed','recyclebin'=>'delete_forever','products'=>'inbox','portal'=>'web','inventory'=>'assignment','support'=>'headset','tools'=>'business_center','mycthemeswitcher'=>'folder','chat'=>'chat','mobilecall'=>'call','call'=>'call','meeting'=>'people'), null, 0);?>

<?php $_smarty_tpl->tpl_vars['APP_LIST'] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppMenuList(), null, 0);?>
<?php $_smarty_tpl->tpl_vars['APP_GROUPED_MENU'] = new Smarty_variable(Settings_MenuEditor_Module_Model::getAllVisibleModules(), null, 0);?>
<?php  $_smarty_tpl->tpl_vars['APP_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_NAME']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_NAME']->key => $_smarty_tpl->tpl_vars['APP_NAME']->value){
$_smarty_tpl->tpl_vars['APP_NAME']->_loop = true;
?>						
	<?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?>
		<?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp1=ob_get_clean();?><?php if (!isset($_smarty_tpl->tpl_vars['iconsarrayTemp']->value[$_tmp1])){?>
			<?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp2=ob_get_clean();?><?php $_smarty_tpl->createLocalArrayVariable('iconsarrayTemp', null, 0);
$_smarty_tpl->tpl_vars['iconsarrayTemp']->value[$_tmp2] = 'folder';?>			
		<?php }?>
	<?php } ?>
<?php } ?>

<?php $_smarty_tpl->tpl_vars['iconsarray'] = new Smarty_variable($_smarty_tpl->tpl_vars['iconsarrayTemp']->value, null, 3);
$_ptr = $_smarty_tpl->parent; while ($_ptr != null) {$_ptr->tpl_vars['iconsarray'] = clone $_smarty_tpl->tpl_vars['iconsarray']; $_ptr = $_ptr->parent; }
Smarty::$global_tpl_vars['iconsarray'] = clone $_smarty_tpl->tpl_vars['iconsarray'];?><?php }} ?>