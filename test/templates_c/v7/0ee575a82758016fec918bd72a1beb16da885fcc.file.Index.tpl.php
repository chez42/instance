<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:45
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Settings\TabColumnView\Index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:120405ee9c3d505e155-47591683%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ee575a82758016fec918bd72a1beb16da885fcc' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Settings\\TabColumnView\\Index.tpl',
      1 => 1589643718,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '120405ee9c3d505e155-47591683',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'SELECTED_MODULE_NAME' => 0,
    'SELECTED_TAB' => 0,
    'MODE' => 0,
    'QUALIFIED_MODULE' => 0,
    'SUPPORTED_MODULES' => 0,
    'MODULE_NAME' => 0,
    'TRANSLATED_MODULE_NAME' => 0,
    'SELECTED_MODULE_MODEL' => 0,
    'IS_TAB' => 0,
    'BLOCKS' => 0,
    'BLOCK_LABEL_KEY' => 0,
    'BLOCK_MODEL' => 0,
    'BLOCK_ID' => 0,
    'COLUMNS' => 0,
    'NUMBER_LIST' => 0,
    'NUMBER' => 0,
    'customTabData' => 0,
    'TABID' => 0,
    'SEQUENCE' => 0,
    'TABNAME' => 0,
    'TABDATA' => 0,
    'DATA' => 0,
    'MODULE' => 0,
    'HEADER_TITLE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3d5131c0',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3d5131c0')) {function content_5ee9c3d5131c0($_smarty_tpl) {?>

<div class="container-fluid main-scroll" id="tabColumnViewContainer"><input id="selectedModuleName" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value;?>
" /><input class="selectedTab" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SELECTED_TAB']->value;?>
"><input class="selectedMode" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['MODE']->value;?>
"><input type="hidden" id="selectedModuleLabel" value="<?php echo vtranslate($_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value,$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
" /><div class="widget_header row"><label class="col-sm-2 textAlignCenter" style="padding-top: 7px;"><?php echo vtranslate('SELECT_MODULE',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</label><div class="col-sm-6"><select class="select2 col-sm-6" name="tabColumnViewModules"><option value=''><?php echo vtranslate('LBL_SELECT_OPTION',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</option><?php  $_smarty_tpl->tpl_vars['MODULE_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['MODULE_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['TRANSLATED_MODULE_NAME'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['SUPPORTED_MODULES']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['MODULE_NAME']->key => $_smarty_tpl->tpl_vars['MODULE_NAME']->value){
$_smarty_tpl->tpl_vars['MODULE_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['TRANSLATED_MODULE_NAME']->value = $_smarty_tpl->tpl_vars['MODULE_NAME']->key;
?><option value="<?php echo $_smarty_tpl->tpl_vars['MODULE_NAME']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['MODULE_NAME']->value==$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value){?> selected <?php }?>><?php echo $_smarty_tpl->tpl_vars['TRANSLATED_MODULE_NAME']->value;?>
</option><?php } ?></select></div></div><br><br><?php if ($_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value){?><div class="contents tabbable"><?php $_smarty_tpl->tpl_vars['IS_SORTABLE'] = new Smarty_variable($_smarty_tpl->tpl_vars['SELECTED_MODULE_MODEL']->value->isSortableAllowed(), null, 0);?><?php $_smarty_tpl->tpl_vars['ALL_BLOCK_LABELS'] = new Smarty_variable(array(), null, 0);?><div class="row fieldsListContainer" style="padding:1% 0"><div class="col-sm-6"><div class="row"><div class=" col-sm-3 <?php if (!$_smarty_tpl->tpl_vars['IS_TAB']->value){?>hide<?php }?> convertTab"><button class="btn btn-default addButton addTab" type="button"><i class="fa fa-plus"></i>&nbsp;&nbsp;<?php echo vtranslate('Add Tab',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button></div><div class="blockActions col-sm-5"><span><i class="fa fa-info-circle customtab-tooltip"></i>&nbsp; <?php echo vtranslate('Enable Tab View',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
&nbsp;<input style="opacity: 0;" type="checkbox"<?php if ($_smarty_tpl->tpl_vars['IS_TAB']->value){?> checked value='0' <?php }else{ ?> value='1' <?php }?> class ='cursorPointer bootstrap-switch' name="is_tab" id="is_tab"data-on-text="<?php echo vtranslate('LBL_YES',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
" data-off-text="<?php echo vtranslate('LBL_NO',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
" data-on-color="primary"  /></span></div></div></div></div><div class="row <?php if (!$_smarty_tpl->tpl_vars['IS_TAB']->value){?>hide<?php }?> convertTab"><div class="col-sm-12 tabcolumncontent"><div id="moduleBlocks"  style="margin-top:17px;"><?php  $_smarty_tpl->tpl_vars['BLOCK_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['BLOCK_MODEL']->_loop = false;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['BLOCKS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['BLOCK_MODEL']->key => $_smarty_tpl->tpl_vars['BLOCK_MODEL']->value){
$_smarty_tpl->tpl_vars['BLOCK_MODEL']->_loop = true;
 $_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value = $_smarty_tpl->tpl_vars['BLOCK_MODEL']->key;
?><?php $_smarty_tpl->tpl_vars['IS_BLOCK_SORTABLE'] = new Smarty_variable($_smarty_tpl->tpl_vars['SELECTED_MODULE_MODEL']->value->isBlockSortableAllowed($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value), null, 0);?><?php $_smarty_tpl->tpl_vars['BLOCK_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['BLOCK_MODEL']->value->get('id'), null, 0);?><?php if ($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value!='LBL_INVITE_USER_BLOCK'){?><?php $_smarty_tpl->createLocalArrayVariable('ALL_BLOCK_LABELS', null, 0);
$_smarty_tpl->tpl_vars['ALL_BLOCK_LABELS']->value[$_smarty_tpl->tpl_vars['BLOCK_ID']->value] = $_smarty_tpl->tpl_vars['BLOCK_MODEL']->value;?><?php }?><div class="<?php if ($_smarty_tpl->tpl_vars['COLUMNS']->value[$_smarty_tpl->tpl_vars['BLOCK_ID']->value]){?>nonTabModules<?php }?> tabModules" style="margin-top:17px;"><div id="block_<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
" class="col-sm-2 editFieldsTable block_<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
 marginBottom10px border1px mainBlock "data-block-id="<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
" data-sequence="<?php echo $_smarty_tpl->tpl_vars['BLOCK_MODEL']->value->get('sequence');?>
" style="background: white;margin-left:5px;"data-custom-fields-count="<?php echo $_smarty_tpl->tpl_vars['BLOCK_MODEL']->value->getCustomFieldsCount();?>
"><div class="tabColumnViewBlockHeader row"><div class="blockLabel  col-sm-8 marginLeftZero" style="word-break: break-all;padding: 5px;"title="<?php echo vtranslate($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value,$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
"><img class="cursorPointerMove" src="<?php echo vimage_path('drag.png');?>
" />&nbsp;&nbsp;<strong class="translatedBlockLabel"><?php echo substr(vtranslate($_smarty_tpl->tpl_vars['BLOCK_LABEL_KEY']->value,$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value),0,9);?>
...</strong></div><div class="col-sm-4 actions marginLeftZero" style="padding: 5px;"><div class="blockActions" style="float:left !important;" id="blockActions<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
"><span><?php $_smarty_tpl->tpl_vars['NUMBER_LIST'] = new Smarty_variable(array(2,3,4,5), null, 0);?><select id="num_of_columns_<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
" class="select2 num_of_columns" name="num_of_columns_<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
" data-block="<?php echo $_smarty_tpl->tpl_vars['BLOCK_ID']->value;?>
"  style="min-width:30px;width:45px!important;"><?php  $_smarty_tpl->tpl_vars['NUMBER'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['NUMBER']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['NUMBER_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['NUMBER']->key => $_smarty_tpl->tpl_vars['NUMBER']->value){
$_smarty_tpl->tpl_vars['NUMBER']->_loop = true;
?><option value="<?php echo $_smarty_tpl->tpl_vars['NUMBER']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['NUMBER']->value==$_smarty_tpl->tpl_vars['COLUMNS']->value[$_smarty_tpl->tpl_vars['BLOCK_ID']->value]){?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['NUMBER']->value;?>
</option><?php } ?></select></span></div></div></div><div class=" row"></div></div></div><?php } ?></div></div></div><div class=" row <?php if (!$_smarty_tpl->tpl_vars['IS_TAB']->value){?>hide<?php }?> convertTab" id="data-body"><div class="col-sm-12 tabcolumncontent"><?php  $_smarty_tpl->tpl_vars['TABDATA'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['TABDATA']->_loop = false;
 $_smarty_tpl->tpl_vars['TABID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customTabData']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['TABDATA']->key => $_smarty_tpl->tpl_vars['TABDATA']->value){
$_smarty_tpl->tpl_vars['TABDATA']->_loop = true;
 $_smarty_tpl->tpl_vars['TABID']->value = $_smarty_tpl->tpl_vars['TABDATA']->key;
?><div class="editFieldsTable col-md-2 marginBottom10px border1px ui-droppable block_<?php echo $_smarty_tpl->tpl_vars['TABID']->value;?>
" data-sequence='<?php echo $_smarty_tpl->tpl_vars['SEQUENCE']->value[$_smarty_tpl->tpl_vars['TABID']->value];?>
'style="margin-top:15px;margin-left:5px;" data-block="<?php echo $_smarty_tpl->tpl_vars['TABID']->value;?>
" ><div class="layoutBlockHeader"><div class="col-sm-12 blockLabel padding10 marginLeftZero" style="word-break: break-all;"title="<?php echo vtranslate($_smarty_tpl->tpl_vars['TABNAME']->value[$_smarty_tpl->tpl_vars['TABID']->value],$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
"><div class="row"><div class="col-sm-9"><img class="cursorPointerMove" src="<?php echo vimage_path('drag.png');?>
" />&nbsp;&nbsp;<strong class="translatedBlockLabel"> <?php echo substr(vtranslate($_smarty_tpl->tpl_vars['TABNAME']->value[$_smarty_tpl->tpl_vars['TABID']->value],$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value),0,7);?>
...</strong></div><div class="col-sm-3"><div class="pull-right "><button type="button" class="close deleteTab" data-tabid="<?php echo $_smarty_tpl->tpl_vars['TABID']->value;?>
" title="deleteTab" aria-label="Close" ><span aria-hidden="true" class="fa fa-close"></span></button></div></div></div><hr></div></div><div class="connectedSortable tabModules row" style="margin-top:17px; min-height: 200px;"><?php  $_smarty_tpl->tpl_vars['DATA'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['DATA']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['TABDATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['DATA']->key => $_smarty_tpl->tpl_vars['DATA']->value){
$_smarty_tpl->tpl_vars['DATA']->_loop = true;
?><?php if ($_smarty_tpl->tpl_vars['DATA']->value['block_id']){?><div id="block_<?php echo $_smarty_tpl->tpl_vars['DATA']->value['block_id'];?>
" class="col-sm-11 editFieldsTable block_<?php echo $_smarty_tpl->tpl_vars['DATA']->value['block_id'];?>
 marginBottom10px border1px "data-block-id="<?php echo $_smarty_tpl->tpl_vars['DATA']->value['block_id'];?>
" data-sequence="<?php echo $_smarty_tpl->tpl_vars['DATA']->value['blocksequence'];?>
" style="background: white;margin-left:5px;" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['DATA']->value['blocklabel'],$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value);?>
"><div class="tabColumnViewBlockHeader row"><div class="blockLabel col-sm-12 padding10 marginBottom10px marginLeftZero" style="word-break: break-all;"><img class="cursorPointerMove" src="<?php echo vimage_path('drag.png');?>
" />&nbsp;&nbsp;<strong class="translatedBlockLabel"><?php echo substr(vtranslate($_smarty_tpl->tpl_vars['DATA']->value['blocklabel'],$_smarty_tpl->tpl_vars['SELECTED_MODULE_NAME']->value),0,15);?>
...</strong></div></div><div class=" row"></div></div><?php }?><?php } ?></div></div><?php } ?><div class="newTabCopy hide col-md-2 marginBottom10px border1px  "  data-block="" data-sequence=""><div class="layoutBlockHeader"><div class="col-sm-12 blockLabel padding10 marginLeftZero" style="word-break: break-all;" title=''><div class="row"><div class="col-sm-9"><img class="cursorPointerMove" src="<?php echo vimage_path('drag.png');?>
" />&nbsp;&nbsp;<strong class="translatedBlockLabel"> </strong></div><div class="col-sm-3"><div class="pull-right "><button type="button" class="close deleteTab" data-tabid="" title="deleteTab" aria-label="Close" ><span aria-hidden="true" class="fa fa-close"></span></button></div></div></div><hr></div></div><div id="tabModules" class="connectedSortable row  " style="margin-top:17px; min-height: 200px;"></div><div class=" row"></div></div></div></div><div class="modal-dialog modal-content addTabModal hide"><?php ob_start();?><?php echo vtranslate('Add New Tab',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars['HEADER_TITLE'] = new Smarty_variable($_tmp1, null, 0);?><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['HEADER_TITLE']->value), 0);?>
<form class="form-horizontal addTabForm"><div class="modal-body"><div class="form-group"><label class="control-label fieldLabel col-sm-5"><span><?php echo vtranslate('Tab Name',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</span><span class="redColor">*</span></label><div class="controls col-sm-6"><input type="text" name="label" class="col-sm-3 inputElement" data-rule-required='true' style='width: 75%'/></div></div></div><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('ModalFooter.tpl','Vtiger'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</form></div></div><div class='modal-overlay-footer clearfix saveViewButton' style="opacity:0;margin-right:0px;"><div class="row clearfix"><div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '><button class="btn btn-success saveTabView" type="button" ><?php echo vtranslate('LBL_SAVE_LAYOUT',$_smarty_tpl->tpl_vars['QUALIFIED_MODULE']->value);?>
</button></div></div></div><?php }?></div>
<?php }} ?>