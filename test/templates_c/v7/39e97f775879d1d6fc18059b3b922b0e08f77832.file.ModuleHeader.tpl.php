<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:33
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Settings\Vtiger\ModuleHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2835ee9c3c9d4e6d5-38087555%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39e97f775879d1d6fc18059b3b922b0e08f77832' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Settings\\Vtiger\\ModuleHeader.tpl',
      1 => 1589643815,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2835ee9c3c9d4e6d5-38087555',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'USER_MODEL' => 0,
    'MODULE' => 0,
    'VIEW' => 0,
    'ACTIVE_BLOCK' => 0,
    'QUALIFIED_MODULE' => 0,
    'MODULE_MODEL' => 0,
    'ALLOWED_MODULES' => 0,
    'URL' => 0,
    'PAGETITLE' => 0,
    'RECORD' => 0,
    'CUSTOM_VIEWS' => 0,
    'GROUP_LABEL' => 0,
    'GROUP_CUSTOM_VIEWS' => 0,
    'CUSTOM_VIEW' => 0,
    'CUSTOME_VIEW_RECORD_MODEL' => 0,
    'MEMBERS' => 0,
    'MEMBER_LIST' => 0,
    'VIEWID' => 0,
    'CURRENT_TAG' => 0,
    'VIEWNAME' => 0,
    'LISTVIEW_URL' => 0,
    'SELECTED_MENU_CATEGORY' => 0,
    'CURRENT_USER_MODEL' => 0,
    'SHARED_MEMBER_COUNT' => 0,
    'LIST_STATUS' => 0,
    'IS_DEFAULT' => 0,
    'CUSTOM_VIEWS_NAMES' => 0,
    'MODULE_BASIC_ACTIONS' => 0,
    'BASIC_ACTION' => 0,
    'LISTVIEW_LINKS' => 0,
    'QUALIFIEDMODULE' => 0,
    'SETTING' => 0,
    'RESTRICTED_MODULE_LIST' => 0,
    'LISTVIEW_BASICACTION' => 0,
    'FIELDS_INFO' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3ca03061',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3ca03061')) {function content_5ee9c3ca03061($_smarty_tpl) {?>

<div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop"><div class="module-action-content clearfix"><div class="col-lg-7 col-md-7"><?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?><a title="<?php echo vtranslate('Home',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" href='index.php?module=Vtiger&parent=Settings&view=Index'><h4 class="module-title pull-left text-uppercase"><?php echo vtranslate('LBL_HOME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </h4></a>&nbsp;<span class="ti-angle-right pull-left <?php if ($_smarty_tpl->tpl_vars['VIEW']->value=='Index'&&$_smarty_tpl->tpl_vars['MODULE']->value=='Vtiger'){?> hide <?php }?>" aria-hidden="true" style="padding-top: 12px;padding-left: 5px;"></span><?php }?><?php if ($_smarty_tpl->tpl_vars['MODULE']->value!='Vtiger'||$_REQUEST['view']!='Index'){?><?php if ($_smarty_tpl->tpl_vars['ACTIVE_BLOCK']->value['block']){?><span class="current-filter-name filter-name pull-left"><?php echo vtranslate($_smarty_tpl->tpl_vars['ACTIVE_BLOCK']->value['block'],$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;</span><?php }?><?php if ($_smarty_tpl->tpl_vars['MODULE']->value!='Vtiger'){?><?php $_smarty_tpl->tpl_vars['ALLOWED_MODULES'] = new Smarty_variable(explode(",",'Users,Profiles,Groups,Roles,Webforms,Workflows'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value&&in_array($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['ALLOWED_MODULES']->value)){?><?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Webforms'){?><?php $_smarty_tpl->tpl_vars['URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl(), null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultUrl(), null, 0);?><?php }?><?php if (strpos($_smarty_tpl->tpl_vars['URL']->value,'parent')==''){?><?php $_smarty_tpl->tpl_vars['URL'] = new Smarty_variable((($_smarty_tpl->tpl_vars['URL']->value).('&parent=')).($_REQUEST['parent']), null, 0);?><?php }?><?php }?><span class="current-filter-name settingModuleName filter-name pull-left"><?php if ($_REQUEST['view']=='Calendar'){?><?php if ($_REQUEST['mode']=='Edit'){?><a href="<?php echo ((((("index.php?module=").($_REQUEST['module'])).('&parent=')).($_REQUEST['parent'])).('&view=')).($_REQUEST['view']);?>
"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp1=ob_get_clean();?><?php echo vtranslate($_tmp1,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</a>&nbsp;<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;<?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp2=ob_get_clean();?><?php echo vtranslate($_tmp2,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<?php echo vtranslate('LBL_OF',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
<?php }else{ ?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp3=ob_get_clean();?><?php echo vtranslate($_tmp3,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
<?php }?><?php }elseif($_REQUEST['view']!='List'&&$_REQUEST['module']=='Users'){?><?php if ($_REQUEST['view']=='PreferenceEdit'){?><a href="<?php echo ((((("index.php?module=").($_REQUEST['module'])).('&parent=')).($_REQUEST['parent'])).('&view=PreferenceDetail&record=')).($_REQUEST['record']);?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['ACTIVE_BLOCK']->value['block'],$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;</a><span class="ti-angle-right" aria-hidden="true"></span>&nbsp;<?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;<?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
<?php }elseif($_REQUEST['view']=='Edit'||$_REQUEST['view']=='Detail'){?><a href="<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
"><?php if ($_REQUEST['extensionModule']){?><?php echo $_REQUEST['extensionModule'];?>
<?php }else{ ?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp4=ob_get_clean();?><?php echo vtranslate($_tmp4,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?>&nbsp;</a><span class="ti-angle-right" aria-hidden="true"></span>&nbsp;<?php if ($_REQUEST['view']=='Edit'){?><?php if ($_smarty_tpl->tpl_vars['RECORD']->value){?><?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getName();?>
<?php }else{ ?><?php echo vtranslate('LBL_ADDING_NEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getName();?>
<?php }?><?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
<?php }?><?php }elseif($_smarty_tpl->tpl_vars['URL']->value&&strpos($_smarty_tpl->tpl_vars['URL']->value,$_REQUEST['view'])==''){?><a href="<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
"><?php if ($_REQUEST['extensionModule']){?><?php echo $_REQUEST['extensionModule'];?>
<?php }else{ ?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp5=ob_get_clean();?><?php echo vtranslate($_tmp5,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?></a>&nbsp;<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;<?php if ($_smarty_tpl->tpl_vars['RECORD']->value){?><?php if ($_REQUEST['view']=='Edit'){?><?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 :&nbsp;<?php }?><?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getName();?>
<?php }?><?php }else{ ?>&nbsp;<?php if ($_REQUEST['extensionModule']){?><?php echo $_REQUEST['extensionModule'];?>
<?php }else{ ?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp6=ob_get_clean();?><?php echo vtranslate($_tmp6,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?><?php }?></span><?php }else{ ?><?php if ($_REQUEST['view']=='TaxIndex'){?><?php $_smarty_tpl->tpl_vars['SELECTED_MODULE'] = new Smarty_variable('LBL_TAX_MANAGEMENT', null, 0);?><?php }elseif($_REQUEST['view']=='TermsAndConditionsEdit'){?><?php $_smarty_tpl->tpl_vars['SELECTED_MODULE'] = new Smarty_variable('LBL_TERMS_AND_CONDITIONS', null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['SELECTED_MODULE'] = new Smarty_variable($_smarty_tpl->tpl_vars['ACTIVE_BLOCK']->value['menu'], null, 0);?><?php }?><span class="current-filter-name filter-name pull-left" style='width:50%;'><span class="display-inline-block"><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['PAGETITLE']->value;?>
<?php $_tmp7=ob_get_clean();?><?php echo vtranslate($_tmp7,$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span></span><?php }?><?php }?></div><div class="col-lg-5 col-md-5 pull-right"><div id="appnav" class="navbar-right"><div class="btn-group"><?php if ($_REQUEST['view']=='List'&&$_REQUEST['module']=='Users'){?><div class="btn-group listViewMassActions " role="group"><button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown"><img class="filterImage" src="layouts/v7/skins/images/filter.png" style="height: 13px; margin-right: 2px; vertical-align: middle;"><span id="selected"> <?php echo vtranslate('LBL_MORE','Vtiger');?>
</span>&nbsp;<span class="caret"></span></button><ul class="dropdown-menu filter-menu" style="min-width:250px;"><div class="module-filters" id="module-filters"><div class="sidebar-container lists-menu-container"><div class="sidebar-header clearfix"><h5 class="pull-left"><?php echo vtranslate('LBL_LISTS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h5><button id="createFilter" data-url="<?php echo CustomView_Record_Model::getCreateViewUrl($_smarty_tpl->tpl_vars['MODULE']->value);?>
" class="btn btn-sm btn-info pull-right sidebar-btn" title="<?php echo vtranslate('LBL_CREATE_LIST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><div class="ti-plus" aria-hidden="true"></div></button></div><hr><div><input class="search-list"  type="hidden" placeholder="<?php echo vtranslate('LBL_SEARCH_FOR_LIST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></div><div class="menu-scroller scrollContainer" style="position:relative; top:0; left:0;height: 450px;"><div class="list-menu-content"><?php $_smarty_tpl->tpl_vars["CUSTOM_VIEW_NAMES"] = new Smarty_variable(array(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEWS']->value&&count($_smarty_tpl->tpl_vars['CUSTOM_VIEWS']->value)>0){?><?php  $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->_loop = false;
 $_smarty_tpl->tpl_vars['GROUP_LABEL'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['CUSTOM_VIEWS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->key => $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->value){
$_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->_loop = true;
 $_smarty_tpl->tpl_vars['GROUP_LABEL']->value = $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->key;
?><?php if ($_smarty_tpl->tpl_vars['GROUP_LABEL']->value!='Mine'&&$_smarty_tpl->tpl_vars['GROUP_LABEL']->value!='Shared'){?><?php continue 1?><?php }?><div class="list-group" id="<?php if ($_smarty_tpl->tpl_vars['GROUP_LABEL']->value=='Mine'){?>myList<?php }else{ ?>sharedList<?php }?>"><h6 class="lists-header <?php if (count($_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->value)<=0){?> hide <?php }?>" ><?php if ($_smarty_tpl->tpl_vars['GROUP_LABEL']->value=='Mine'){?><?php echo vtranslate('LBL_MY_LIST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }else{ ?><?php echo vtranslate('LBL_SHARED_LIST',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?></h6><input type="hidden" name="allCvId" value="<?php echo CustomView_Record_Model::getAllFilterByModule($_smarty_tpl->tpl_vars['MODULE']->value)->get('cvid');?>
" /><ul class="lists-menu" style="list-style: none;"><?php $_smarty_tpl->tpl_vars['count'] = new Smarty_variable(0, null, 0);?><?php $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['LISTVIEW_URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl(), null, 0);?><?php  $_smarty_tpl->tpl_vars["CUSTOM_VIEW"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["CUSTOM_VIEW"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['GROUP_CUSTOM_VIEWS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["customView"]['iteration']=0;
foreach ($_from as $_smarty_tpl->tpl_vars["CUSTOM_VIEW"]->key => $_smarty_tpl->tpl_vars["CUSTOM_VIEW"]->value){
$_smarty_tpl->tpl_vars["CUSTOM_VIEW"]->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["customView"]['iteration']++;
?><?php $_smarty_tpl->tpl_vars['IS_DEFAULT'] = new Smarty_variable($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isDefault(), null, 0);?><?php $_smarty_tpl->tpl_vars["CUSTOME_VIEW_RECORD_MODEL"] = new Smarty_variable(CustomView_Record_Model::getInstanceById($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getId()), null, 0);?><?php $_smarty_tpl->tpl_vars["MEMBERS"] = new Smarty_variable($_smarty_tpl->tpl_vars['CUSTOME_VIEW_RECORD_MODEL']->value->getMembers(), null, 0);?><?php $_smarty_tpl->tpl_vars["LIST_STATUS"] = new Smarty_variable($_smarty_tpl->tpl_vars['CUSTOME_VIEW_RECORD_MODEL']->value->get('status'), null, 0);?><?php  $_smarty_tpl->tpl_vars["MEMBER_LIST"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["MEMBER_LIST"]->_loop = false;
 $_smarty_tpl->tpl_vars['GROUP_LABEL'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['MEMBERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["MEMBER_LIST"]->key => $_smarty_tpl->tpl_vars["MEMBER_LIST"]->value){
$_smarty_tpl->tpl_vars["MEMBER_LIST"]->_loop = true;
 $_smarty_tpl->tpl_vars['GROUP_LABEL']->value = $_smarty_tpl->tpl_vars["MEMBER_LIST"]->key;
?><?php if (count($_smarty_tpl->tpl_vars['MEMBER_LIST']->value)>0){?><?php $_smarty_tpl->tpl_vars["SHARED_MEMBER_COUNT"] = new Smarty_variable(1, null, 0);?><?php }?><?php } ?><li style="font-size:12px;" class='listViewFilter <?php if ($_smarty_tpl->tpl_vars['VIEWID']->value==$_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getId()&&($_smarty_tpl->tpl_vars['CURRENT_TAG']->value=='')){?> active <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['customView']['iteration']>10){?> <?php $_smarty_tpl->tpl_vars['count'] = new Smarty_variable(1, null, 0);?> <?php }?> <?php }elseif($_smarty_tpl->getVariable('smarty')->value['foreach']['customView']['iteration']>10){?> filterHidden hide<?php }?> '><?php ob_start();?><?php echo vtranslate($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->get('viewname'),$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php $_tmp8=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['VIEWNAME'] = new Smarty_variable($_tmp8, null, 0);?><?php $_smarty_tpl->createLocalArrayVariable("CUSTOM_VIEW_NAMES", null, 0);
$_smarty_tpl->tpl_vars["CUSTOM_VIEW_NAMES"]->value[] = $_smarty_tpl->tpl_vars['VIEWNAME']->value;?><a class="filterName listViewFilterElipsis" href="<?php echo (((($_smarty_tpl->tpl_vars['LISTVIEW_URL']->value).('&viewname=')).($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getId())).('&app=')).($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value);?>
" oncontextmenu="return false;" data-filter-id="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getId();?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['VIEWNAME']->value, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['VIEWNAME']->value, ENT_QUOTES, 'UTF-8', true);?>
</a><div class="pull-right"><span class="js-popover-container" style="cursor:pointer;"><span  class="fa fa-angle-down" rel="popover" data-toggle="popover" aria-expanded="true"<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isMine()&&$_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->get('viewname')!='All'){?>data-deletable="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isDeletable()&&$_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->get('viewname')!='All'){?>true<?php }else{ ?>false<?php }?>"data-editable="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isEditable()){?>true<?php }else{ ?>false<?php }?>"<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isEditable()||$_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->isAdminUser()){?>data-editurl="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getEditUrl();?>
<?php }?>"<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isDeletable()){?><?php if ($_smarty_tpl->tpl_vars['SHARED_MEMBER_COUNT']->value==1||$_smarty_tpl->tpl_vars['LIST_STATUS']->value==3){?> data-shared="1"<?php }?>data-deleteurl="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getDeleteUrl();?>
"<?php }?><?php }?>toggleClass="fa <?php if ($_smarty_tpl->tpl_vars['IS_DEFAULT']->value){?>fa-check-square-o<?php }else{ ?>fa-square-o<?php }?>"data-filter-id="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getId();?>
"data-is-default="<?php echo $_smarty_tpl->tpl_vars['IS_DEFAULT']->value;?>
" data-defaulttoggle="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getToggleDefaultUrl();?>
"data-default="<?php echo $_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->getDuplicateUrl();?>
"data-isMine="<?php if ($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->isMine()&&($_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->get('viewname')!='LBL_ACTIVE_USERS'&&$_smarty_tpl->tpl_vars['CUSTOM_VIEW']->value->get('viewname')!='LBL_INACTIVE_USERS')){?>true<?php }else{ ?>false<?php }?>"></span></span></div></li><?php } ?></ul></div><?php } ?><input type="hidden" id='allFilterNames'  value='<?php echo Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($_smarty_tpl->tpl_vars['CUSTOM_VIEWS_NAMES']->value));?>
'/><div id="filterActionPopoverHtml"><ul class="listmenu hide" role="menu"><li role="presentation" class="editFilter"><a role="menuitem"><i class="fa fa-pencil"></i>&nbsp;<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><li role="presentation" class="deleteFilter"><a role="menuitem"><i class="fa fa-trash"></i>&nbsp;<?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><li role="presentation" class="duplicateFilter"><a role="menuitem" ><i class="fa fa-files-o"></i>&nbsp;<?php echo vtranslate('LBL_DUPLICATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><li role="presentation" class="toggleDefault"><a role="menuitem" ><i data-check-icon="fa-check-square-o" data-uncheck-icon="fa-square-o"></i>&nbsp;<?php echo vtranslate('LBL_DEFAULT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li></ul></div><?php }?><div class="list-group hide noLists"><h6 class="lists-header"><center> <?php echo vtranslate('LBL_NO');?>
 <?php echo vtranslate('LBL_LISTS');?>
 <?php echo vtranslate('LBL_FOUND');?>
 ... </center></h6></div></div></div></div></div></ul></div><?php }?><?php  $_smarty_tpl->tpl_vars['BASIC_ACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_BASIC_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BASIC_ACTION']->key => $_smarty_tpl->tpl_vars['BASIC_ACTION']->value){
$_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel()=='LBL_IMPORT'){?><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
" type="button" class="btn addButton module-buttons"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick="Vtiger_Import_Js.triggerImportAction('<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
')"<?php }?>><div class="fa <?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getIcon();?>
" aria-hidden="true"></div>&nbsp;&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button><?php }else{ ?><button type="button" class="btn addButton module-buttons"id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick='window.location.href="<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
"'<?php }?>><div class="fa <?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getIcon();?>
" aria-hidden="true"></div>&nbsp;&nbsp;<?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</button><?php }?><?php } ?><?php if (count($_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWSETTING'])>0){?><?php if (empty($_smarty_tpl->tpl_vars['QUALIFIEDMODULE']->value)){?><?php $_smarty_tpl->tpl_vars['QUALIFIEDMODULE'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE']->value, null, 0);?><?php }?><div class="settingsIcon"><button type="button" class="btn module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="ti-settings" aria-hidden="true" title="<?php echo vtranslate('LBL_SETTINGS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"></span>&nbsp; <span class="caret"></span></button><ul class="detailViewSetting dropdown-menu animated flipInY"><?php  $_smarty_tpl->tpl_vars['SETTING'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SETTING']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWSETTING']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SETTING']->key => $_smarty_tpl->tpl_vars['SETTING']->value){
$_smarty_tpl->tpl_vars['SETTING']->_loop = true;
?><li id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_setings_lisview_advancedAction_<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getLabel();?>
"><a href="javascript:void(0);" onclick="<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getUrl();?>
;"><?php echo vtranslate($_smarty_tpl->tpl_vars['SETTING']->value->getLabel(),$_smarty_tpl->tpl_vars['QUALIFIEDMODULE']->value);?>
</a></li><?php } ?></ul></div><?php }?><?php $_smarty_tpl->tpl_vars['RESTRICTED_MODULE_LIST'] = new Smarty_variable(array('Users','EmailTemplates'), null, 0);?><?php if (count($_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC'])>0&&!in_array($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['RESTRICTED_MODULE_LIST']->value)){?><?php if (empty($_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value)){?><?php $_smarty_tpl->tpl_vars['QUALIFIED_MODULE'] = new Smarty_variable(('Settings:').($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php }?><?php  $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['LISTVIEW_LINKS']->value['LISTVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->key => $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value){
$_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Users'){?> <?php $_smarty_tpl->tpl_vars['LANGMODULE'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE']->value, null, 0);?> <?php }?><button class="btn module-buttons"id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel());?>
"<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Workflows'){?>onclick='Settings_Workflows_List_Js.triggerCreate("<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
&mode=V7Edit")'<?php }else{ ?><?php if (stripos($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick='window.location.href = "<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getUrl();?>
"'<?php }?><?php }?>><?php if ($_smarty_tpl->tpl_vars['MODULE']->value=='Tags'){?><i class="ti-plus"></i>&nbsp;&nbsp;<?php echo vtranslate('LBL_ADD_TAG',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getIcon()){?><i class="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getIcon();?>
"></i>&nbsp;&nbsp;<?php }?><?php echo vtranslate($_smarty_tpl->tpl_vars['LISTVIEW_BASICACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php }?></button><?php } ?><?php }?></div></div></div></div><?php if ($_smarty_tpl->tpl_vars['FIELDS_INFO']->value!=null){?><script type="text/javascript">var uimeta = (function () {var fieldInfo = <?php echo $_smarty_tpl->tpl_vars['FIELDS_INFO']->value;?>
;return {field: {get: function (name, property) {if (name && property === undefined) {return fieldInfo[name];}if (name && property) {return fieldInfo[name][property]}},isMandatory: function (name) {if (fieldInfo[name]) {return fieldInfo[name].mandatory;}return false;},getType: function (name) {if (fieldInfo[name]) {return fieldInfo[name].type}return false;}},};})();</script><?php }?></div>
<?php }} ?>