<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:21
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\ModuleHeader.tpl" */ ?>
<?php /*%%SmartyHeaderCode:72765ee9c3bd658ec2-17253545%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ac6a6e226392010c26ddff1bd137574ac07835c9' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\ModuleHeader.tpl',
      1 => 1589643819,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '72765ee9c3bd658ec2-17253545',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE' => 0,
    'MODULE_MODEL' => 0,
    'DEFAULT_FILTER_ID' => 0,
    'CVURL' => 0,
    'DEFAULT_FILTER_URL' => 0,
    'SELECTED_MENU_CATEGORY' => 0,
    'VIEWID' => 0,
    'CUSTOM_VIEWS' => 0,
    'FILTER_TYPES' => 0,
    'FILTERS' => 0,
    'CVNAME' => 0,
    'CVEDIT' => 0,
    'CURRENT_USER_MODEL' => 0,
    'CVEDITURL' => 0,
    'CVDUPLICATEURL' => 0,
    'CVTOGGLEURL' => 0,
    'IS_DEFAULT' => 0,
    'RECORD' => 0,
    'MODULE_BASIC_ACTIONS' => 0,
    'BASIC_ACTION' => 0,
    'MODULE_SETTING_ACTIONS' => 0,
    'MODULE_NAME' => 0,
    'SETTING' => 0,
    'FIELDS_INFO' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bd75d32',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bd75d32')) {function content_5ee9c3bd75d32($_smarty_tpl) {?>

<div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop"><div class="module-action-content clearfix <?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
-module-action-content"><div class="col-xs-<?php if ($_REQUEST['view']=='Edit'){?>12<?php }else{ ?>9<?php }?> col-lg-7 col-md-7 col-sm-7 module-breadcrumb module-breadcrumb-<?php echo $_REQUEST['view'];?>
 transitionsAllHalfSecond"><?php $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultViewName()!='List'){?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultUrl(), null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultCustomFilter(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID']->value){?><?php $_smarty_tpl->tpl_vars['CVURL'] = new Smarty_variable(("&viewname=").($_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable(($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl()).($_smarty_tpl->tpl_vars['CVURL']->value), null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrlWithAllFilter(), null, 0);?><?php }?><?php }?><a title="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
" href='<?php echo $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'><h4 class="module-title pull-left text-uppercase"> <?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </h4>&nbsp;&nbsp;</a><?php if ($_SESSION['lvs'][$_smarty_tpl->tpl_vars['MODULE']->value]['viewname']){?><?php $_smarty_tpl->tpl_vars['VIEWID'] = new Smarty_variable($_SESSION['lvs'][$_smarty_tpl->tpl_vars['MODULE']->value]['viewname'], null, 0);?><?php }?><?php if ($_smarty_tpl->tpl_vars['VIEWID']->value){?><?php  $_smarty_tpl->tpl_vars['FILTER_TYPES'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FILTER_TYPES']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['CUSTOM_VIEWS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FILTER_TYPES']->key => $_smarty_tpl->tpl_vars['FILTER_TYPES']->value){
$_smarty_tpl->tpl_vars['FILTER_TYPES']->_loop = true;
?><?php  $_smarty_tpl->tpl_vars['FILTERS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FILTERS']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['FILTER_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FILTERS']->key => $_smarty_tpl->tpl_vars['FILTERS']->value){
$_smarty_tpl->tpl_vars['FILTERS']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['FILTERS']->value->get('cvid')==$_smarty_tpl->tpl_vars['VIEWID']->value){?><?php $_smarty_tpl->tpl_vars['CVNAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->get('viewname'), null, 0);?><?php $_smarty_tpl->tpl_vars['CVEDITURL'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->getEditUrl(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVDELETE'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->isDeletable(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVEDIT'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->isEditable(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVDELETEURL'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->getDeleteUrl(), null, 0);?><?php $_smarty_tpl->tpl_vars['IS_DEFAULT'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->isDefault(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVID'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->getId(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVDUPLICATEURL'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->getDuplicateUrl(), null, 0);?><?php $_smarty_tpl->tpl_vars['CVTOGGLEURL'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->getToggleDefaultUrl(), null, 0);?><?php break 1?><?php }?><?php } ?><?php } ?><p class="current-filter-name filter-name pull-left cursorPointer" title="<?php echo $_smarty_tpl->tpl_vars['CVNAME']->value;?>
"><span class="fa fa-angle-right pull-left" aria-hidden="true"></span><a href='<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl();?>
&viewname=<?php echo $_smarty_tpl->tpl_vars['VIEWID']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['CVNAME']->value;?>
&nbsp;&nbsp;</a><?php if ($_REQUEST['view']=='List'){?><div class="module-filters filter-name listActions "><i style="margin-top:13px;" class="fa fa-angle-down dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></i><ul class="dropdown-menu popover" role="menu" style="left:auto;top:70%;padding: 3px;"><?php if ($_smarty_tpl->tpl_vars['CVEDIT']->value||$_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->isAdminUser()){?><li role="presentation" class="editFilter" data-url="<?php echo $_smarty_tpl->tpl_vars['CVEDITURL']->value;?>
"><a role="menuitem"><i class="fa fa-pencil"></i>&nbsp;<?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['CVDUPLICATEURL']->value){?><li role="presentation" class="duplicateFilter" data-url="<?php echo $_smarty_tpl->tpl_vars['CVDUPLICATEURL']->value;?>
"><a role="menuitem" ><i class="fa fa-files-o"></i>&nbsp;<?php echo vtranslate('LBL_DUPLICATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['CVTOGGLEURL']->value){?><li role="presentation" class="toggleDefault" data-url="<?php echo $_smarty_tpl->tpl_vars['CVTOGGLEURL']->value;?>
"><a role="menuitem" ><i data-check-icon="fa-check-square-o" data-uncheck-icon="fa-square-o"<?php if ($_smarty_tpl->tpl_vars['IS_DEFAULT']->value){?> class="fa fa-check-square-o" <?php }else{ ?> class="fa fa-square-o" <?php }?>></i>&nbsp;<?php echo vtranslate('LBL_DEFAULT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php }?></ul></div><?php }?></p><?php }?><?php $_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME'] = new Smarty_variable(('SINGLE_').($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['RECORD']->value&&$_REQUEST['view']=='Edit'){?><p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
">&nbsp;&nbsp;<?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
 &nbsp;&nbsp;</a></p><?php }elseif($_REQUEST['view']=='Edit'){?><p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a>&nbsp;&nbsp;<?php echo vtranslate('LBL_ADDING_NEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;&nbsp;</a></p><?php }?><?php if ($_REQUEST['view']=='Detail'){?><p class="current-filter-name filter-name pull-left"><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
">&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
 &nbsp;&nbsp;</a></p><?php }?></div><div class="col-xs-3 col-lg-5 col-md-5 col-sm-5 module-breadcrumb-<?php echo $_REQUEST['view'];?>
" style="margin:  0px; padding: 0px;"><div id="appnav" class="navbar-right"><div class="btn-group"><?php  $_smarty_tpl->tpl_vars['BASIC_ACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_BASIC_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BASIC_ACTION']->key => $_smarty_tpl->tpl_vars['BASIC_ACTION']->value){
$_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel()=='LBL_IMPORT'){?><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
" type="button" class="btn module-buttons addButton"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick="Vtiger_Import_Js.triggerImportAction('<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
')"<?php }?>><i class="material-icons">import_export</i><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></button><?php }else{ ?><button id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
" type="button" class="btn module-buttons addButton"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick='window.location.href = "<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
"'<?php }?>><i class="material-icons">add</i><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></button><?php }?><?php } ?><?php if (count($_smarty_tpl->tpl_vars['MODULE_SETTING_ACTIONS']->value)>0){?><button type="button" class="btn module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="hidden-xs hidden-sm" aria-hidden="true" title="<?php echo vtranslate('LBL_SETTINGS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
">&nbsp;<?php echo vtranslate('LBL_CUSTOMIZE','Reports');?>
</span><i class="material-icons">settings</i></button><ul class="detailViewSetting dropdown-menu pull-right animated fadeIn"><?php  $_smarty_tpl->tpl_vars['SETTING'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SETTING']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_SETTING_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SETTING']->key => $_smarty_tpl->tpl_vars['SETTING']->value){
$_smarty_tpl->tpl_vars['SETTING']->_loop = true;
?><li id="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
_listview_advancedAction_<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getLabel();?>
"><a href=<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getUrl();?>
><?php echo vtranslate($_smarty_tpl->tpl_vars['SETTING']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value,vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value));?>
</a></li><?php } ?></ul><?php }?></div></div></div></div><?php if ($_smarty_tpl->tpl_vars['FIELDS_INFO']->value!=null){?><script type="text/javascript">var uimeta = (function () {var fieldInfo = <?php echo $_smarty_tpl->tpl_vars['FIELDS_INFO']->value;?>
;return {field: {get: function (name, property) {if (name && property === undefined) {return fieldInfo[name];}if (name && property) {return fieldInfo[name][property]}},isMandatory: function (name) {if (fieldInfo[name]) {return fieldInfo[name].mandatory;}return false;},getType: function (name) {if (fieldInfo[name]) {return fieldInfo[name].type}return false;}},};})();</script><?php }?></div>
<?php }} ?>