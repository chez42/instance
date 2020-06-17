<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:28:44
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\DetailViewActions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:72635ee9c62c9042d4-55206285%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'da19b62dd8879239befcb1662b12d5ad79f37f25' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\DetailViewActions.tpl',
      1 => 1589643822,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '72635ee9c62c9042d4-55206285',
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
    'RECORD' => 0,
    'MODULE_BASIC_ACTIONS' => 0,
    'BASIC_ACTION' => 0,
    'MODULE_SETTING_ACTIONS' => 0,
    'MODULE_NAME' => 0,
    'SETTING' => 0,
    'STARRED' => 0,
    'DETAILVIEW_LINKS' => 0,
    'DETAIL_VIEW_BASIC_LINK' => 0,
    'DETAIL_VIEW_LINK' => 0,
    'NO_PAGINATION' => 0,
    'PREVIOUS_RECORD_URL' => 0,
    'NEXT_RECORD_URL' => 0,
    'FIELDS_INFO' => 0,
    'TIMECONTROL_ENABLE' => 0,
    'TIMECONTROLID' => 0,
    'SHOW_WATCH' => 0,
    'WATCH_COUNTER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c62ca6a79',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c62ca6a79')) {function content_5ee9c62ca6a79($_smarty_tpl) {?>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 detailViewButtoncontainer"><div class="pull-right"><!-- bread svv<div class="module-breadcrumb module-breadcrumb-<?php echo $_REQUEST['view'];?>
 transitionsAllHalfSecond"><?php $_smarty_tpl->tpl_vars['MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultViewName()!='List'){?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultUrl(), null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getDefaultCustomFilter(), null, 0);?><?php if ($_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID']->value){?><?php $_smarty_tpl->tpl_vars['CVURL'] = new Smarty_variable(("&viewname=").($_smarty_tpl->tpl_vars['DEFAULT_FILTER_ID']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable(($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl()).($_smarty_tpl->tpl_vars['CVURL']->value), null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL'] = new Smarty_variable($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrlWithAllFilter(), null, 0);?><?php }?><?php }?><a title="<?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
" href='<?php echo $_smarty_tpl->tpl_vars['DEFAULT_FILTER_URL']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'><h4 class="module-title pull-left "> <?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </h4></a><?php if ($_SESSION['lvs'][$_smarty_tpl->tpl_vars['MODULE']->value]['viewname']){?><?php $_smarty_tpl->tpl_vars['VIEWID'] = new Smarty_variable($_SESSION['lvs'][$_smarty_tpl->tpl_vars['MODULE']->value]['viewname'], null, 0);?><?php }?><?php if ($_smarty_tpl->tpl_vars['VIEWID']->value){?><?php  $_smarty_tpl->tpl_vars['FILTER_TYPES'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FILTER_TYPES']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['CUSTOM_VIEWS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FILTER_TYPES']->key => $_smarty_tpl->tpl_vars['FILTER_TYPES']->value){
$_smarty_tpl->tpl_vars['FILTER_TYPES']->_loop = true;
?><?php  $_smarty_tpl->tpl_vars['FILTERS'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['FILTERS']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['FILTER_TYPES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['FILTERS']->key => $_smarty_tpl->tpl_vars['FILTERS']->value){
$_smarty_tpl->tpl_vars['FILTERS']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['FILTERS']->value->get('cvid')==$_smarty_tpl->tpl_vars['VIEWID']->value){?><?php $_smarty_tpl->tpl_vars['CVNAME'] = new Smarty_variable($_smarty_tpl->tpl_vars['FILTERS']->value->get('viewname'), null, 0);?><?php break 1?><?php }?><?php } ?><?php } ?><p class="current-filter-name filter-name pull-left cursorPointer" title="<?php echo $_smarty_tpl->tpl_vars['CVNAME']->value;?>
"><span class="ti-angle-right pull-left" style="margin-left: 10px;" aria-hidden="true"></span><a href='<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getListViewUrl();?>
&viewname=<?php echo $_smarty_tpl->tpl_vars['VIEWID']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['CVNAME']->value;?>
&nbsp;&nbsp;</a> </p><?php }?><?php $_smarty_tpl->tpl_vars['SINGLE_MODULE_NAME'] = new Smarty_variable(('SINGLE_').($_smarty_tpl->tpl_vars['MODULE']->value), null, 0);?><?php if ($_smarty_tpl->tpl_vars['RECORD']->value&&$_REQUEST['view']=='Edit'){?><p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
">&nbsp;&nbsp;<?php echo vtranslate('LBL_EDITING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 : <?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
 &nbsp;&nbsp;</a></p><?php }elseif($_REQUEST['view']=='Edit'){?><p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a>&nbsp;&nbsp;<?php echo vtranslate('LBL_ADDING_NEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
&nbsp;&nbsp;</a></p><?php }?><?php if ($_REQUEST['view']=='Detail'){?><p class="current-filter-name filter-name pull-left"><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
">&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->get('label');?>
 &nbsp;&nbsp;</a></p><?php }?></div>bread svv --></div><div class="pull-right btn-toolbar"><!-- svv <div class="btn-group pull-left"><?php  $_smarty_tpl->tpl_vars['BASIC_ACTION'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_BASIC_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BASIC_ACTION']->key => $_smarty_tpl->tpl_vars['BASIC_ACTION']->value){
$_smarty_tpl->tpl_vars['BASIC_ACTION']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel()=='LBL_IMPORT'){?><button tippytitle data-toggle="toolstip" data-placement="top" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
" type="button" class="btn btn-primary addButton"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick="Vtiger_Import_Js.triggerImportAction('<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
')"<?php }?>><div aria-hidden="true"><i class="material-icons">import_export</i></div></button><?php }else{ ?><button tippytitle data-toggle="toolstip" data-placement="top" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE']->value);?>
" id="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
_listView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getLabel());?>
" type="button" class="btn btn-primary addButton"<?php if (stripos($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),'javascript:')===0){?>onclick='<?php echo substr($_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl(),strlen("javascript:"));?>
;'<?php }else{ ?>onclick='window.location.href = "<?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
"'<?php }?>><div aria-hidden="true"><i class="material-icons><?php echo $_smarty_tpl->tpl_vars['BASIC_ACTION']->value->getIcon();?>
</i></div></button><?php }?><?php } ?><?php if (count($_smarty_tpl->tpl_vars['MODULE_SETTING_ACTIONS']->value)>0){?><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" data-toggle="tooltisp" tippytitle data-placement="top" title="<?php echo vtranslate('LBL_CUSTOMIZE','Reports');?>
" aria-expanded="false"><span aria-hidden="true" title="<?php echo vtranslate('LBL_SETTINGS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><i class="material-icons">settings</i></span>&nbsp; <span class="caret"></span></button><ul class="detailViewSetting dropdown-menu pull-right animated fadeIn"><?php  $_smarty_tpl->tpl_vars['SETTING'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SETTING']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['MODULE_SETTING_ACTIONS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SETTING']->key => $_smarty_tpl->tpl_vars['SETTING']->value){
$_smarty_tpl->tpl_vars['SETTING']->_loop = true;
?><li id="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
_listview_advancedAction_<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getLabel();?>
"><a href=<?php echo $_smarty_tpl->tpl_vars['SETTING']->value->getUrl();?>
><?php echo vtranslate($_smarty_tpl->tpl_vars['SETTING']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value,vtranslate($_smarty_tpl->tpl_vars['MODULE_NAME']->value,$_smarty_tpl->tpl_vars['MODULE_NAME']->value));?>
</a></li><?php } ?></ul><?php }?></div>----><div class="btn-group"><?php $_smarty_tpl->tpl_vars['STARRED'] = new Smarty_variable($_smarty_tpl->tpl_vars['RECORD']->value->get('starred'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isStarredEnabled()){?><button class="btn btn-primary markStar <?php if ($_smarty_tpl->tpl_vars['STARRED']->value){?> active <?php }?>" id="starToggle"><div class='starredStatus' title="<?php echo vtranslate('LBL_STARRED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><div class='unfollowMessage'><i class="material-icons">star</i> &nbsp;<?php echo vtranslate('LBL_UNFOLLOW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div><div class='followMessage'><i class="material-icons active">star_border</i> <span class="hidden-xs"><?php echo vtranslate('LBL_FOLLOWING',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span></div></div><div class='unstarredStatus' title="<?php echo vtranslate('LBL_NOT_STARRED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><?php echo vtranslate('LBL_FOLLOW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</div></button><?php }?><?php  $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['DETAILVIEW_LINKS']->value['DETAILVIEWBASIC']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->key => $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value){
$_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->_loop = true;
?><button data-toggle="toosltip" data-placement="top" tippytitle data-tippy-content="<?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
" class="btn btn-primary" id="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
_detailView_basicAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel());?>
"<?php if ($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->isPageLoadLink()){?>onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'"<?php }else{ ?>onclick="<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getUrl();?>
"<?php }?><?php if ($_smarty_tpl->tpl_vars['MODULE_NAME']->value=='Documents'&&$_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel()=='LBL_VIEW_FILE'){?>data-filelocationtype="<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->get('filelocationtype');?>
" data-filename="<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->get('filename');?>
" ><i class="material-icons">zoom_in</i><?php }elseif(Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel())=="LBL_EDIT"){?>><i class="fa fa-pencil"></i><?php }elseif(Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel())=="LBL_SEND_EMAIL"){?>><i class="fa fa-envelope"></i><?php }elseif($_smarty_tpl->tpl_vars['MODULE_NAME']->value=='Project'){?><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><?php }elseif($_smarty_tpl->tpl_vars['MODULE_NAME']->value=='Leads'){?><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><?php }elseif($_smarty_tpl->tpl_vars['MODULE_NAME']->value=='Potentials'){?><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><?php }else{ ?><span class="hidden-sm hidden-xs"><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_BASIC_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span><?php }?></button><?php } ?><?php if (count($_smarty_tpl->tpl_vars['DETAILVIEW_LINKS']->value['DETAILVIEW'])>0){?><button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);"><i class="material-icons">list</i> <span class="hidden-sm hidden-xs"><?php echo vtranslate('LBL_MORE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span> &nbsp;&nbsp;<i class="caret"></i></button><ul class="dropdown-menu dropdown-menu-right animated fadeIn"><?php  $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['DETAILVIEW_LINKS']->value['DETAILVIEW']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->key => $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value){
$_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getLabel()==''){?><li class="divider"></li><?php }else{ ?><li id="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
_detailView_moreAction_<?php echo Vtiger_Util_Helper::replaceSpaceWithUnderScores($_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getLabel());?>
"><?php if (strstr($_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getUrl(),"javascript")){?><a href='<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getUrl();?>
'><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</a><?php }else{ ?><a href='<?php echo $_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
' ><?php echo vtranslate($_smarty_tpl->tpl_vars['DETAIL_VIEW_LINK']->value->getLabel(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</a><?php }?></li><?php }?><?php } ?></ul><?php }?></div><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['NO_PAGINATION']->value;?>
<?php $_tmp1=ob_get_clean();?><?php if (!$_tmp1){?><div class="button-group pull-right"><button class="btn btn-secondary" id="detailViewPreviousRecordButton" <?php if (empty($_smarty_tpl->tpl_vars['PREVIOUS_RECORD_URL']->value)){?> disabled="disabled" <?php }else{ ?> onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['PREVIOUS_RECORD_URL']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'" <?php }?> ><i class="material-icons">keyboard_arrow_left</i></button><button class="btn btn-secondary " id="detailViewNextRecordButton"<?php if (empty($_smarty_tpl->tpl_vars['NEXT_RECORD_URL']->value)){?> disabled="disabled" <?php }else{ ?> onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['NEXT_RECORD_URL']->value;?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
'" <?php }?>><i class="material-icons">keyboard_arrow_right</i></button></div><?php }?></div><input type="hidden" name="record_id" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['FIELDS_INFO']->value!=null){?><script type="text/javascript">var uimeta = (function () {var fieldInfo = <?php echo $_smarty_tpl->tpl_vars['FIELDS_INFO']->value;?>
;return {field: {get: function (name, property) {if (name && property === undefined) {return fieldInfo[name];}if (name && property) {return fieldInfo[name][property]}},isMandatory: function (name) {if (fieldInfo[name]) {return fieldInfo[name].mandatory;}return false;},getType: function (name) {if (fieldInfo[name]) {return fieldInfo[name].type}return false;}},};})();</script><?php }?></div><?php if ($_smarty_tpl->tpl_vars['TIMECONTROL_ENABLE']->value){?><div  class="col-lg-3 pull-right" style="margin-top:10px;"><div class="summaryWidgetContainer"><form action="index.php" method="post" name="QuickTimer<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
"><input type="hidden" name="module" value="Timecontrol"><input type="hidden" name="record" value="<?php echo $_smarty_tpl->tpl_vars['TIMECONTROLID']->value;?>
"><input type="hidden" name="modeTC" value="continueTimetracker"><input type="hidden" name="mode" value="edit"><input type="hidden" name="ticketId" value="<?php echo $_smarty_tpl->tpl_vars['RECORD']->value->getId();?>
"><?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value=='started'){?><input type="hidden" name="action" value="Finish"><?php }?><?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value=='halted'){?><input type="hidden" name="isDuplicate" value="true"><input type="hidden" name="view" value="Edit"><?php }?><input type="hidden" name="stop_watch" value="<?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value=='halted'){?>0<?php }else{ ?>1<?php }?>"><div class="widget_header clearfix"><div class="pull-right"><button class="btn pull-right btn-success ticketHalted startTimer <?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value!='halted'){?>hide<?php }?>" type="submit"><strong><?php echo vtranslate('Start','Timecontrol');?>
</strong></button><button class="btn pull-right btn-danger ticketStart <?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value=='halted'){?>hide<?php }?> stopTimer" type="submit"><strong><?php echo vtranslate('LBL_WATCH_STOP','Timecontrol');?>
</strong></button></div><div class="pull-right" style="font-size: 150%;font-weight: bold;margin-right: 10px;"><div class="ticketHalted <?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value!='halted'){?>hide<?php }?>"><img src="modules/Timecontrol/images/clock-red-small.png" id="clock_image" style="vertical-align: middle; margin-right:5px; width:30px;"><span id="clock_display" style="vertical-align: middle">00:00</span></div><div class="ticketStart <?php if ($_smarty_tpl->tpl_vars['SHOW_WATCH']->value!='started'){?>hide<?php }?>"><img src="modules/Timecontrol/images/clock-green-small.png" id="clock_image" style="vertical-align: middle; margin-right:5px; width:30px;"><span id="clock_display_hours" style="vertical-align: middle;">00</span><span id="clock_display_separator" style="vertical-align: middle;">:</span><span id="clock_display_minutes" style="vertical-align: middle;">00</span><span id="clock_display_separator2" style="vertical-align: middle;">:</span><span id="clock_display_seconds" style="vertical-align: middle;">00</span><input id="clock_counter" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['WATCH_COUNTER']->value;?>
"><script type="text/javascript">var tc_clock;$(document).ready(function() {clearInterval(tc_clock);updateClock(true);tc_clock = setInterval("updateClock()", 1000);});</script></div></div></div></form></div></div><?php }?><?php }} ?>