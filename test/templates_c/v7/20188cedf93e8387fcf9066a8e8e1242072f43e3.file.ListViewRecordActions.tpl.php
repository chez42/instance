<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:26:21
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Calendar\ListViewRecordActions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:132985ee9c59d8fd9e9-10454317%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '20188cedf93e8387fcf9066a8e8e1242072f43e3' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Calendar\\ListViewRecordActions.tpl',
      1 => 1589643805,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '132985ee9c59d8fd9e9-10454317',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SEARCH_MODE_RESULTS' => 0,
    'LISTVIEW_ENTRY' => 0,
    'QUICK_PREVIEW_ENABLED' => 0,
    'MODULE' => 0,
    'MODULE_MODEL' => 0,
    'STARRED' => 0,
    'IS_MODULE_EDITABLE' => 0,
    'EDIT_VIEW_URL' => 0,
    'IS_CREATE_PERMITTED' => 0,
    'SELECTED_MENU_CATEGORY' => 0,
    'RECORD_ACTIONS' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c59d96369',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c59d96369')) {function content_5ee9c59d96369($_smarty_tpl) {?>

<div class="table-actions calendar-table-actions"><?php if (!$_smarty_tpl->tpl_vars['SEARCH_MODE_RESULTS']->value){?><span class="input" ><input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" class="listViewEntriesCheckBox"/></span><?php }?><?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('starred')=='Yes'){?><?php $_smarty_tpl->tpl_vars['STARRED'] = new Smarty_variable(true, null, 0);?><?php }else{ ?><?php $_smarty_tpl->tpl_vars['STARRED'] = new Smarty_variable(false, null, 0);?><?php }?><?php if ($_smarty_tpl->tpl_vars['QUICK_PREVIEW_ENABLED']->value=='true'){?><span class="quickView icon action" title="<?php echo vtranslate('LBL_QUICK_VIEW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
"><i class="material-icons">zoom_in</i></span><?php }?><?php if ($_smarty_tpl->tpl_vars['MODULE_MODEL']->value->isStarredEnabled()){?><span class="markStar icon action " title="<?php if ($_smarty_tpl->tpl_vars['STARRED']->value){?> <?php echo vtranslate('LBL_STARRED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php }else{ ?> <?php echo vtranslate('LBL_NOT_STARRED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
<?php }?>"><i class="material-icons"><?php if ($_smarty_tpl->tpl_vars['STARRED']->value){?> star <?php }else{ ?> star_border<?php }?></i></span><?php }?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getEditViewUrl();?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['EDIT_VIEW_URL'] = new Smarty_variable($_tmp1, null, 0);?><?php if ($_smarty_tpl->tpl_vars['IS_MODULE_EDITABLE']->value&&$_smarty_tpl->tpl_vars['EDIT_VIEW_URL']->value&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('taskstatus')!=vtranslate('Held',$_smarty_tpl->tpl_vars['MODULE']->value)&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('taskstatus')!=vtranslate('Completed',$_smarty_tpl->tpl_vars['MODULE']->value)){?><span class="icon action" title="<?php echo vtranslate('LBL_MARK_AS_HELD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
" onclick="Calendar_Calendar_Js.markAsHeld('<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
');"><i class="material-icons">bookmark</i></span><?php }?><?php if ($_smarty_tpl->tpl_vars['IS_CREATE_PERMITTED']->value&&$_smarty_tpl->tpl_vars['EDIT_VIEW_URL']->value&&$_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->get('taskstatus')==vtranslate('Held',$_smarty_tpl->tpl_vars['MODULE']->value)){?><span class="icon action" title="<?php echo vtranslate('LBL_HOLD_FOLLOWUP_ON',"Events");?>
" onclick="Calendar_Calendar_Js.holdFollowUp('<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
');"><i class="material-icons">book</i></span><?php }?><span class="more dropdown action"><span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="material-icons icon">info_outline</i></span><ul class="dropdown-menu"><li><a data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" href="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getFullDetailViewUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
"><?php echo vtranslate('LBL_DETAILS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php if ($_smarty_tpl->tpl_vars['RECORD_ACTIONS']->value){?><?php if ($_smarty_tpl->tpl_vars['RECORD_ACTIONS']->value['edit']){?><li><a data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" href="javascript:void(0);" data-url="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getEditViewUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value;?>
" name="editlink"><?php echo vtranslate('LBL_EDIT',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php }?><?php if ($_smarty_tpl->tpl_vars['RECORD_ACTIONS']->value['delete']){?><li><a data-id="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRY']->value->getId();?>
" href="javascript:void(0);" class="deleteRecordButton"><?php echo vtranslate('LBL_DELETE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</a></li><?php }?><?php }?></ul></span><div class="btn-group inline-save hide"><button class="button btn-success btn-small save" name="save"><i class="material-icons">check</i></button><button class="button btn-danger btn-small cancel" name="Cancel"><i class="material-icons">close</i></button></div></div><?php }} ?>