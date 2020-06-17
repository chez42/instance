<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:20
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\partials\Topbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:275875ee9c3bcc72169-87014755%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f24ed4b141a84a810554cbadbc8b947d01a7cd6' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\partials\\Topbar.tpl',
      1 => 1589643821,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '275875ee9c3bcc72169-87014755',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'APP_LIST' => 0,
    'APP_NAME' => 0,
    'APP_GROUPED_MENU' => 0,
    'moduleModel' => 0,
    'moduleName' => 0,
    'iconsarray' => 0,
    'translatedModuleLabel' => 0,
    'MODULE' => 0,
    'SELECTED_MENU_CATEGORY' => 0,
    'APP_IMAGE_MAP' => 0,
    'DASHBOARD_MODULE_MODEL' => 0,
    'USER_PRIVILEGES_MODEL' => 0,
    'HOME_MODULE_MODEL' => 0,
    'TASK_MODULE_MODEL' => 0,
    'COMMENTS_MODULE_MODEL' => 0,
    'DOCUMENTS_MODULE_MODEL' => 0,
    'USER_MODEL' => 0,
    'APP_MENU_MODEL' => 0,
    'APP_COUNT' => 0,
    'COMPANY_LOGO' => 0,
    'GLOBAL_SEARCH_VALUE' => 0,
    'QUICK_CREATE_MODULES' => 0,
    'quickCreateModule' => 0,
    'count' => 0,
    'singularLabel' => 0,
    'hideDiv' => 0,
    'MAILMANAGER_MODULE_MODEL' => 0,
    'CALENDAR_MODULE_MODEL' => 0,
    'REPORTS_MODULE_MODEL' => 0,
    'MYCTHEME_MODULE_MODEL' => 0,
    'IMAGE_DETAILS' => 0,
    'IMAGE_INFO' => 0,
    'useremail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bcf1d5d',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bcf1d5d')) {function content_5ee9c3bcf1d5d($_smarty_tpl) {?>

<?php echo $_smarty_tpl->getSubTemplate ("layouts/rainbow/modules/Vtiger/Header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php $_smarty_tpl->tpl_vars["APP_IMAGE_MAP"] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppIcons(), null, 0);?><style>.dropdown-submenu {position: relative;}.dropdown-submenu .dropdown-menu {top: 0;left: 100%;margin-top: -1px;}.mycFsAppMenu{position: fixed;top: 0;height: 100vh;width: 100vw;background-color: rgba(0,0,0,.6);z-index: 99999;padding: 10vw;overflow: auto;display: none;}.mycFsAppMenu-applink{padding: 20px;color: white;}.mycFsAppMenu-appicon{font-size: 80px;padding: 30px;border-radius: 100%;background-color: white;color: black;}.mycFsAppMenu-appname{font-size: 20px;margin-top: 10px;}</style><div class="mycFsAppMenu"><div class="row"><?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/partials/ModuleIcons.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php $_smarty_tpl->tpl_vars['APP_GROUPED_MENU'] = new Smarty_variable(Settings_MenuEditor_Module_Model::getAllVisibleModules(), null, 0);?><?php $_smarty_tpl->tpl_vars['APP_LIST'] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppMenuList(), null, 0);?><?php  $_smarty_tpl->tpl_vars['APP_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_NAME']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_NAME']->key => $_smarty_tpl->tpl_vars['APP_NAME']->value){
$_smarty_tpl->tpl_vars['APP_NAME']->_loop = true;
?><?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?><?php $_smarty_tpl->tpl_vars['translatedModuleLabel'] = new Smarty_variable(vtranslate($_smarty_tpl->tpl_vars['moduleModel']->value->get('label'),$_smarty_tpl->tpl_vars['moduleName']->value), null, 0);?><div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 text-center mycFsAppMenu-applink"><a href="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
"><span class="mycFsAppMenu-appicon"><i class="material-icons module-icon" ><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp1];?>
</i></span><div class="clearfix"></div><br><span class="mycFsAppMenu-appname"><?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
</span></a></div><?php } ?><?php } ?></div></div><nav class="navbar navbar-default navbar-fixed-top app-fixed-navbar"><div class="container-fluid global-nav"><div class="row"><div class="col-lg-3 col-md-5 col-sm-4 col-xs-8 paddingRight0 app-navigator-container"><div class="row"><div id="appnavigator" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 cursorPointer app-switcher-container hidden-lg hidden-md" data-app-class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Home'||!$_smarty_tpl->tpl_vars['MODULE']->value){?>ti-dashboard<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['APP_IMAGE_MAP']->value[$_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value];?>
<?php }?>"><div class="row app-navigator"><span class="app-icon fa fa-bars"></span></div></div><div class="dropdown col-lg-2 hidden-sm hidden-xs"><button class="btn btn-fask btn-lg" type="button" id="dropdownMenuButtonDesk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">menu</i></button><div class="dropdown-menu fask" aria-labelledby="dropdownMenuButtonDesk"><div class="bluredBackground"></div><ul class="faskfirst"><li class="nav-small-cap hide">APPS</li><?php $_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL'] = new Smarty_variable(Users_Privileges_Model::getCurrentUserPrivilegesModel(), null, 0);?><?php $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Home'), null, 0);?><?php $_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Dashboard'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL']->value->getId())){?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Home"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="<?php echo $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL']->value->getDefaultUrl();?>
" ><i class="material-icons">dashboard</i><span class="hide-menu" style="text-transform: uppercase"><?php echo vtranslate('LBL_DASHBOARD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </span></a></li><?php }?><?php $_smarty_tpl->tpl_vars['TASK_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Task'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['TASK_MODULE_MODEL']->value->getId())){?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Task"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="index.php?module=Task&view=List" ><i class="fa fa-tasks" aria-hidden="true"></i><span class="hide-menu"> <?php echo vtranslate('Task');?>
</span></a></li><?php }?><?php $_smarty_tpl->tpl_vars['COMMENTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('ModComments'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['COMMENTS_MODULE_MODEL']->value->getId())){?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="ModComments"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="index.php?module=ModComments&view=List" ><i class="fa fa-comments-o" aria-hidden="true"></i><span class="hide-menu"> <?php echo vtranslate('ModComments');?>
</span></a></li><?php }?><?php $_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Documents'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL']->value->getId())){?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Documents"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="index.php?module=Documents&view=List" ><i class="app-icon-list material-icons">file_download</i><span class="hide-menu"> <?php echo vtranslate('Documents');?>
</span></a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?><?php if (vtlib_isModuleActive('ExtensionStore')){?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="ExtensionStore"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore" ><i class="app-icon-list material-icons">shopping_cart</i><span class="hide-menu"> <?php echo vtranslate('LBL_EXTENSION_STORE','Settings:Vtiger');?>
</span></a></li><?php }?><?php }?><hr/><?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?><li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="index.php?module=Vtiger&parent=Settings&view=Index" ><span class="module-icon"><i class="material-icons">settings</i></span><span class="hide-menu">  <?php echo vtranslate('LBL_CRM_SETTINGS','Vtiger');?>
</span></a></li><li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="index.php?module=Users&parent=Settings&view=List" ><span class="module-icon"><i class="material-icons">contacts</i></span><span class="hide-menu">   <?php echo vtranslate('LBL_MANAGE_USERS','Vtiger');?>
</span></a></li><?php }else{ ?><li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Users"){?>active<?php }?>"><a class=" waves-effect waves-dark" href="index.php?module=Users&view=Settings" ><i class="material-icons">settings</i><span class="hide-menu" style="text-transform: uppercase"> <?php echo vtranslate('LBL_SETTINGS','Settings:Vtiger');?>
</span></a></li><?php }?></ul><ul class="fasksecond"><?php $_smarty_tpl->tpl_vars['APP_GROUPED_MENU'] = new Smarty_variable(Settings_MenuEditor_Module_Model::getAllVisibleModules(), null, 0);?><?php $_smarty_tpl->tpl_vars['APP_LIST'] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppMenuList(), null, 0);?><?php $_smarty_tpl->tpl_vars['APP_COUNT'] = new Smarty_variable(count($_smarty_tpl->tpl_vars['APP_LIST']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Home"){?><?php $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY'] = new Smarty_variable('Dashboard', null, 0);?><?php }?><?php  $_smarty_tpl->tpl_vars['APP_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_NAME']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_NAME']->key => $_smarty_tpl->tpl_vars['APP_NAME']->value){
$_smarty_tpl->tpl_vars['APP_NAME']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['APP_NAME']->value=='ANALYTICS'){?> <?php continue 1?> <?php }?><?php if (count($_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value])>0){?><?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?><?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?><?php break 1?><?php }?><?php } ?><?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/partials/ModuleIcons.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<li class="with-childs <?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>active<?php }?>" style="width:<?php echo 100/$_smarty_tpl->tpl_vars['APP_COUNT']->value;?>
% !important;"><a class="has-arrow waves-effect waves-dark " ><i class="app-icon-list fa <?php echo $_smarty_tpl->tpl_vars['APP_IMAGE_MAP']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value];?>
" ></i><span class="hide-menu"><?php echo vtranslate(($_smarty_tpl->tpl_vars['APP_NAME']->value));?>
</span></a><ul style="padding-left:6px;padding-top:15px;"><?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?><?php $_smarty_tpl->tpl_vars['translatedModuleLabel'] = new Smarty_variable(vtranslate($_smarty_tpl->tpl_vars['moduleModel']->value->get('label'),$_smarty_tpl->tpl_vars['moduleName']->value), null, 0);?><li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
" ><?php if ($_smarty_tpl->tpl_vars['moduleName']->value=='PortfolioInformation'){?><i class="fa fa-line-chart" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='Connection'){?><i class="fa fa-users" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='ModComments'){?><i class="fa fa-comments-o" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='RingCentral'){?><i class="fa fa-phone-square" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='Task'){?><i class="fa fa-tasks" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='Timecontrol'){?><i class="fa fa-hourglass" aria-hidden="true"></i><?php }elseif($_smarty_tpl->tpl_vars['moduleName']->value=='EmailTemplates'||$_smarty_tpl->tpl_vars['moduleName']->value=='CalendarTemplate'){?><i class="fa fa-fast-forward" aria-hidden="true"></i><?php }else{ ?><i class="material-icons module-icon" ><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp2];?>
</i><?php }?><span class="hide-menu"> <?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
</span></a></li><?php } ?></ul></li><?php }?><?php } ?><li class="nav-small-cap hide">TOOLS & SETTINGS</li><?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?><?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?><?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?><?php break 1?><?php }?><?php } ?></div></div><div class="logo-container col-lg-8 col-md-8 col-sm-8 col-xs-8"><div class="row"><a href="index.php" class="company-logo"><img src="<?php echo $_smarty_tpl->tpl_vars['COMPANY_LOGO']->value->get('imagepath');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['COMPANY_LOGO']->value->get('alt');?>
"/></a></div></div></div></div><div id="navbar" class="col-sm-6 col-md-3 col-lg-3 collapse navbar-collapse navbar-right global-actions"><ul class="nav navbar-nav"><li><div class="search-links-container hidden-sm"><div class="search-link hidden-xs"><span class="ti-search" aria-hidden="true"></span><input class="keyword-input" type="text" placeholder="<?php echo vtranslate('LBL_TYPE_SEARCH');?>
" value="<?php echo $_smarty_tpl->tpl_vars['GLOBAL_SEARCH_VALUE']->value;?>
"><span id="adv-search" title="Advanced Search" class="adv-search ti-arrow-circle-down pull-right cursorPointer" aria-hidden="true"></span></div></div></li><li><div class="dropdown"><div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><a href="#" id="menubar_quickCreate" class="qc-button" title="<?php echo vtranslate('LBL_QUICK_CREATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" aria-hidden="true"><i class="material-icons">add</i></a></div><ul class="dropdown-menu animated fadeIn" role="menu" aria-labelledby="dropdownMenu1" style="width:650px;"><li class="title" style="padding: 5px 0 0 15px;"><h4><strong><?php echo vtranslate('LBL_QUICK_CREATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></h4></li><hr/><li id="quickCreateModules" style="padding: 0 5px;"><div class="col-lg-12" style="padding-bottom:15px;"><?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['QUICK_CREATE_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?><?php if ($_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('CreateView')||$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('EditView')){?><?php $_smarty_tpl->tpl_vars['quickCreateModule'] = new Smarty_variable($_smarty_tpl->tpl_vars['moduleModel']->value->isQuickCreateSupported(), null, 0);?><?php $_smarty_tpl->tpl_vars['singularLabel'] = new Smarty_variable($_smarty_tpl->tpl_vars['moduleModel']->value->getSingularLabelKey(), null, 0);?><?php ob_start();?><?php echo !$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('CreateView')&&$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('EditView');?>
<?php $_tmp3=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['hideDiv'] = new Smarty_variable($_tmp3, null, 0);?><?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/partials/ModuleIcons.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
<?php if ($_smarty_tpl->tpl_vars['quickCreateModule']->value=='1'){?><?php if ($_smarty_tpl->tpl_vars['count']->value%3==0){?><div class="row"><?php }?><?php if ($_smarty_tpl->tpl_vars['singularLabel']->value=='SINGLE_Calendar'){?><?php $_smarty_tpl->tpl_vars['singularLabel'] = new Smarty_variable('LBL_TASK', null, 0);?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?>create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide <?php }else{ ?> col-lg-4<?php }?>"><a id="menubar_quickCreate_Events" class="quickCreateModule" data-name="Events"data-url="index.php?module=Events&view=QuickCreateAjax" href="javascript:void(0)"><i class="material-icons pull-left">event</i><span class="quick-create-module"><?php echo vtranslate('LBL_EVENT',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</span></a></div><?php }elseif($_smarty_tpl->tpl_vars['singularLabel']->value=='SINGLE_Documents'){?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?>create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide<?php }else{ ?>col-lg-4<?php }?> dropdown"><a id="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" class="quickCreateModuleSubmenu dropdown-toggle" data-name="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" data-toggle="dropdown"data-url="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getQuickCreateUrl();?>
" href="javascript:void(0)"><i class="material-icons pull-left"><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp4=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp4];?>
</i><span class="quick-create-module"><?php echo vtranslate($_smarty_tpl->tpl_vars['singularLabel']->value,$_smarty_tpl->tpl_vars['moduleName']->value);?>
<i class="fa fa-caret-down quickcreateMoreDropdownAction"></i></span></a><ul class="dropdown-menu quickcreateMoreDropdown" aria-labelledby="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
"><li class="dropdown-header"><i class="material-icons">file_upload</i> <?php echo vtranslate('LBL_FILE_UPLOAD',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</li><li id="VtigerAction"><a href="javascript:Documents_Index_Js.uploadTo('Vtiger')"><i class="fa fa-desktop"> </i>  <?php echo vtranslate('LBL_FROM_COMPUTER','Documents');?>
</a></li><li class="dropdown-header"><i class="ti-link"></i> <?php echo vtranslate('LBL_LINK_EXTERNAL_DOCUMENT',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</li><li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="material-icons">link</i>&nbsp;&nbsp; <?php ob_start();?><?php echo vtranslate('LBL_FILE_URL',$_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp5=ob_get_clean();?><?php echo vtranslate('LBL_FROM_SERVICE',$_smarty_tpl->tpl_vars['moduleName']->value,$_tmp5);?>
</a></li><li role="separator" class="divider"></li><li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="ti-file"></i> <?php ob_start();?><?php echo vtranslate('SINGLE_Documents',$_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp6=ob_get_clean();?><?php echo vtranslate('LBL_CREATE_NEW',$_smarty_tpl->tpl_vars['moduleName']->value,$_tmp6);?>
</a></li></ul></div><?php }else{ ?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?> create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide <?php }else{ ?> col-lg-4 <?php }?> <?php if ($_smarty_tpl->tpl_vars['moduleModel']->value->getName()=='Campaigns'||$_smarty_tpl->tpl_vars['moduleModel']->value->getName()=='ProjectTask'||$_smarty_tpl->tpl_vars['moduleModel']->value->getName()=='ProjectMilestone'){?> hide <?php }?>"><a id="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" class="quickCreateModule" data-name="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
"data-url="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getQuickCreateUrl();?>
" href="javascript:void(0)"><i class="material-icons pull-left"><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp7=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp7];?>
</i><span class="quick-create-module"><?php echo vtranslate($_smarty_tpl->tpl_vars['singularLabel']->value,$_smarty_tpl->tpl_vars['moduleName']->value);?>
</span></a></div><?php }?><?php if ($_smarty_tpl->tpl_vars['count']->value%3==2){?></div><br><?php }?><?php if (!$_smarty_tpl->tpl_vars['hideDiv']->value){?><?php $_smarty_tpl->tpl_vars['count'] = new Smarty_variable($_smarty_tpl->tpl_vars['count']->value+1, null, 0);?><?php }?><?php }?><?php }?><?php } ?></div></li></ul></div></li><?php $_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL'] = new Smarty_variable(Users_Privileges_Model::getCurrentUserPrivilegesModel(), null, 0);?><?php $_smarty_tpl->tpl_vars['MAILMANAGER_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('MailManager'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['MAILMANAGER_MODULE_MODEL']->value->getId())){?><li><div style="margin:-5px !important;"><a href="index.php?module=MailManager&view=List" target = "_blank" class="vicon"  title="<?php echo vtranslate('MailManager');?>
" aria-hidden="true"><i class="vicon-mailmanager"></i></a></div></li><?php }?><?php $_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Calendar'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getId())){?><li><div><a href="index.php?module=Calendar&view=<?php echo $_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getDefaultViewName();?>
" title="<?php echo vtranslate('Calendar','Calendar');?>
" aria-hidden="true"><i class="material-icons">event</i></a></div></li><?php }?><?php $_smarty_tpl->tpl_vars['REPORTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Reports'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['REPORTS_MODULE_MODEL']->value->getId())){?><li><div><a href="index.php?module=Reports&view=List" title="<?php echo vtranslate('Reports','Reports');?>
" aria-hidden="true"><i class="material-icons">show_chart</i></a></div></li><?php }?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getId())){?><li><div><a href="#" class="taskManagement" title="<?php echo vtranslate('Tasks','Vtiger');?>
" aria-hidden="true"><i style="line-height: 40px;" class="fa fa-tasks" aria-hidden="true"></i></a></div></li><?php }?><?php $_smarty_tpl->tpl_vars['MYCTHEME_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('OmniThemeManager'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['MYCTHEME_MODULE_MODEL']->value->getId())){?><li><div><a href="#" class="themeStyler" title="Theme Styler" aria-hidden="true"><i class="material-icons">brush</i></a></div></li><?php }?><li class="dropdown"><div><?php $_smarty_tpl->tpl_vars['IMAGE_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getImageDetails(), null, 0);?><?php $_smarty_tpl->tpl_vars['IMAGE_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value['imagename'], null, 0);?><?php if (empty($_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value)){?><a href="#" class="userName dropdown-toggle " data-toggle="dropdown" role="button" title="<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name');?>
 <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name');?>
(<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('user_name');?>
)"><i class="material-icons">perm_identity</i><span class="link-text-xs-only hidden-lg hidden-md hidden-sm"><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
</span></a><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['IMAGE_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['IMAGE_INFO']->key => $_smarty_tpl->tpl_vars['IMAGE_INFO']->value){
$_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = true;
?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
<?php $_tmp8=ob_get_clean();?><?php if (!empty($_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'])&&!empty($_tmp8)){?><a href="#" class="userName dropdown-toggle" data-toggle="dropdown" role="button" title="<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name');?>
 <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name');?>
(<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('user_name');?>
)"><img style="width: 30px;border-radius: 50%;padding: 7px 0px;" src="<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'];?>
_<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
"></a><?php }?><?php } ?><?php }?><div class="dropdown-menu logout-content animated flipInY" role="menu"><div class="row"><div class="col-lg-12 col-sm-12" style="padding:10px;"><div class="profile-container col-lg-5 col-sm-5"><?php $_smarty_tpl->tpl_vars['IMAGE_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getImageDetails(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value!=''&&$_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value[0]!=''&&$_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value[0]['path']==''){?><i class='material-icons'>perm_identity</i><?php }else{ ?><?php  $_smarty_tpl->tpl_vars['IMAGE_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['IMAGE_INFO']->key => $_smarty_tpl->tpl_vars['IMAGE_INFO']->value){
$_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = true;
?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
<?php $_tmp9=ob_get_clean();?><?php if (!empty($_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'])&&!empty($_tmp9)){?><img src="<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'];?>
_<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
"><?php }?><?php } ?><?php }?></div><div class="col-lg-7 col-sm-7"><h5><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name');?>
 <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name');?>
</h5><h6 class="textOverflowEllipsis" title='<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('user_name');?>
'><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('user_name');?>
 | <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getUserRoleName();?>
</h6><?php $_smarty_tpl->tpl_vars['useremail'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->get('email1'), null, 0);?><h6 class="textOverflowEllipsis" title='<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('email');?>
'><?php echo $_smarty_tpl->tpl_vars['useremail']->value;?>
</h6></div><hr style="margin: 10px 0 !important"><div class="col-lg-12 col-sm-12"><ul class="dropdown-user"><li role="separator" class="divider"></li><li><a id="menubar_item_right_LBL_MY_PREFERENCES" href="<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getPreferenceDetailViewUrl();?>
"><i class="material-icons">settings</i> <?php echo vtranslate('LBL_MY_PREFERENCES');?>
</a></li><li><a id="menubar_item_right_LBL_SIGN_OUT" href="index.php?module=Users&action=Logout"><i class="material-icons">power_settings_new</i> <?php echo vtranslate('LBL_SIGN_OUT');?>
</a></li></ul></div></div></div></div></div></li></ul></div><div class="col-xs-4 visible-xs padding0px quickTopButtons"><div class="dropdown btn-group pull-right"><button class="btn dropdown-toggle" style="background-color: transparent;padding: 12px;color: #fff;margin-top: -1px;margin-bottom:0px;border: none;" data-toggle="dropdown" aria-expanded="true"><a href="#" id="menubar_quickCreate_mobile" class="qc-button" title="<?php echo vtranslate('LBL_QUICK_CREATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" aria-hidden="true"><i class="material-icons">add</i>&nbsp;<span class="caret"></span></a></button><ul class="dropdown-menu"><li class="title" style="padding: 5px 0 0 15px;"><h4><strong><?php echo vtranslate('LBL_QUICK_CREATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</strong></h4></li><hr/><li id="quickCreateModules_mobile" style="padding: 0 8px;"><div class="col-xs-12 padding0px" style="padding-bottom:15px;"><?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['QUICK_CREATE_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?><?php if ($_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('CreateView')||$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('EditView')){?><?php $_smarty_tpl->tpl_vars['quickCreateModule'] = new Smarty_variable($_smarty_tpl->tpl_vars['moduleModel']->value->isQuickCreateSupported(), null, 0);?><?php $_smarty_tpl->tpl_vars['singularLabel'] = new Smarty_variable($_smarty_tpl->tpl_vars['moduleModel']->value->getSingularLabelKey(), null, 0);?><?php ob_start();?><?php echo !$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('CreateView')&&$_smarty_tpl->tpl_vars['moduleModel']->value->isPermitted('EditView');?>
<?php $_tmp10=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['hideDiv'] = new Smarty_variable($_tmp10, null, 0);?><?php if ($_smarty_tpl->tpl_vars['quickCreateModule']->value=='1'){?><?php if ($_smarty_tpl->tpl_vars['singularLabel']->value=='SINGLE_Calendar'){?><?php $_smarty_tpl->tpl_vars['singularLabel'] = new Smarty_variable('LBL_TASK', null, 0);?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?>create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide<?php }else{ ?>col-xs-12<?php }?>"><a id="menubar_quickCreate_Events" class="quickCreateModule" data-name="Events"data-url="index.php?module=Events&view=QuickCreateAjax" href="javascript:void(0)"><i class="material-icons pull-left">event</i><span class="quick-create-module"><?php echo vtranslate('LBL_EVENT',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</span></a></div><?php }elseif($_smarty_tpl->tpl_vars['singularLabel']->value=='SINGLE_Documents'){?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?>create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide<?php }else{ ?>col-xs-12<?php }?> dropdown"><a id="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" class="quickCreateModuleSubmenu dropdown-toggle" data-name="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" data-toggle="dropdown"data-url="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getQuickCreateUrl();?>
" href="javascript:void(0)"><i class="material-icons pull-left"><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp11=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp11];?>
</i><span class="quick-create-module"><?php echo vtranslate($_smarty_tpl->tpl_vars['singularLabel']->value,$_smarty_tpl->tpl_vars['moduleName']->value);?>
<i class="fa fa-caret-down quickcreateMoreDropdownAction"></i></span></a><ul class="dropdown-menu quickcreateMoreDropdown" aria-labelledby="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
"><li class="dropdown-header"><i class="ti-upload"></i> <?php echo vtranslate('LBL_FILE_UPLOAD',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</li><li id="VtigerAction"><a href="javascript:Documents_Index_Js.uploadTo('Vtiger')"><i class="fa fa-desktop"> </i>  <?php echo vtranslate('LBL_FROM_COMPUTER','Documents');?>
</a></li><li class="dropdown-header"><i class="ti-link"></i> <?php echo vtranslate('LBL_LINK_EXTERNAL_DOCUMENT',$_smarty_tpl->tpl_vars['moduleName']->value);?>
</li><li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="ti-link"></i>&nbsp;&nbsp; <?php ob_start();?><?php echo vtranslate('LBL_FILE_URL',$_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp12=ob_get_clean();?><?php echo vtranslate('LBL_FROM_SERVICE',$_smarty_tpl->tpl_vars['moduleName']->value,$_tmp12);?>
</a></li><li role="separator" class="divider"></li><li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="ti-file"></i> <?php ob_start();?><?php echo vtranslate('SINGLE_Documents',$_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp13=ob_get_clean();?><?php echo vtranslate('LBL_CREATE_NEW',$_smarty_tpl->tpl_vars['moduleName']->value,$_tmp13);?>
</a></li></ul></div><?php }else{ ?><div class="<?php if ($_smarty_tpl->tpl_vars['hideDiv']->value){?>create_restricted_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
 hide<?php }else{ ?>col-xs-12<?php }?>"><a id="menubar_quickCreate_<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
" class="quickCreateModule" data-name="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getName();?>
"data-url="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getQuickCreateUrl();?>
" href="javascript:void(0)"><i class="material-icons pull-left"><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp14=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp14];?>
</i><span class="quick-create-module"><?php echo vtranslate($_smarty_tpl->tpl_vars['singularLabel']->value,$_smarty_tpl->tpl_vars['moduleName']->value);?>
</span></a></div><?php }?><?php if (!$_smarty_tpl->tpl_vars['hideDiv']->value){?><?php $_smarty_tpl->tpl_vars['count'] = new Smarty_variable($_smarty_tpl->tpl_vars['count']->value+1, null, 0);?><?php }?><?php }?><?php }?><?php } ?></div></li></ul></div><div class="dropdown btn-group pull-right"><button style="background-color: transparent;padding: 12px;color: #fff;margin-top: -1px;margin-bottom:0px;border: none;border-right: 1px solid #fff; border-radius: 0px; " class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="material-icons">settings</i>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu"><div class="clearfix"></div><?php $_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL'] = new Smarty_variable(Users_Privileges_Model::getCurrentUserPrivilegesModel(), null, 0);?><?php $_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Calendar'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getId())){?><li><a href="index.php?module=Calendar&view=<?php echo $_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getDefaultViewName();?>
" title="<?php echo vtranslate('Calendar','Calendar');?>
" aria-hidden="true"><i class="material-icons">event</i>&nbsp;<?php echo vtranslate('Calendar','Calendar');?>
</a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['CALENDAR_MODULE_MODEL']->value->getId())){?><li><a class="taskManagement" href="#" title="<?php echo vtranslate('Task','Task');?>
" aria-hidden="true"><i class="material-icons">card_travel</i>&nbsp;<?php echo vtranslate('Task','Task');?>
</a></li><?php }?><?php $_smarty_tpl->tpl_vars['MYCTHEME_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('OmniThemeManager'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['MYCTHEME_MODULE_MODEL']->value->getId())){?><li><div><a href="#" class="themeStyler" title="Theme Styler" aria-hidden="true"><i class="material-icons">brush</i>&nbsp;Theme Styler</a></div></li><?php }?><?php $_smarty_tpl->tpl_vars['REPORTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Reports'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['REPORTS_MODULE_MODEL']->value->getId())){?><li><a href="index.php?module=Reports&view=List" title="<?php echo vtranslate('Reports','Reports');?>
" aria-hidden="true"><i class="material-icons">pie_chart</i>&nbsp;<?php echo vtranslate('Reports','Reports');?>
</a></li><?php }?><li class="divider"></li><li class="dropdown-header"><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('first_name');?>
 <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('last_name');?>
<br/><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->get('user_name');?>
 | <?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getUserRoleName();?>
</li><li class="divider"></li><li><a id="menubar_item_right_LBL_MY_PREFERENCES" href="<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getPreferenceDetailViewUrl();?>
"><i class="material-icons">settings</i>&nbsp;<?php echo vtranslate('LBL_MY_PREFERENCES');?>
</a></li><li><a id="menubar_item_right_LBL_SIGN_OUT" href="index.php?module=Users&action=Logout"><i class="material-icons">power_settings_new</i>&nbsp;<?php echo vtranslate('LBL_SIGN_OUT');?>
</a></li></ul></div></div></div></div><?php }} ?>