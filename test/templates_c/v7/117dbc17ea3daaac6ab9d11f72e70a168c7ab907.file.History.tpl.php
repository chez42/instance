<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:23
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\dashboards\History.tpl" */ ?>
<?php /*%%SmartyHeaderCode:300165ee9c3bf2d29a1-35820087%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '117dbc17ea3daaac6ab9d11f72e70a168c7ab907' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\dashboards\\History.tpl',
      1 => 1589643820,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '300165ee9c3bf2d29a1-35820087',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'WIDGET' => 0,
    'MODULE_NAME' => 0,
    'TAB' => 0,
    'CURRENT_USER' => 0,
    'ACCESSIBLE_USERS' => 0,
    'USER_ID' => 0,
    'TYPE' => 0,
    'CURRENT_USER_ID' => 0,
    'USER_NAME' => 0,
    'COMMENTS_MODULE_MODEL' => 0,
    'HISTORY_TYPE' => 0,
    'START' => 0,
    'END' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bf35cab',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bf35cab')) {function content_5ee9c3bf35cab($_smarty_tpl) {?>
<div class="dashboardWidgetHeader clearfix">
    <div class="title">
        <div class="dashboardTitle" title="<?php echo vtranslate($_smarty_tpl->tpl_vars['WIDGET']->value->getTitle(),$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
"><?php echo vtranslate($_smarty_tpl->tpl_vars['WIDGET']->value->getTitle());?>
</div>
    </div>
    <input type="hidden" name="tab" value='<?php echo $_smarty_tpl->tpl_vars['TAB']->value;?>
'/>
    <div class="userList">
        <?php $_smarty_tpl->tpl_vars['CURRENT_USER_ID'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER']->value->getId(), null, 0);?>
        <?php if (count($_smarty_tpl->tpl_vars['ACCESSIBLE_USERS']->value)>1){?>
            <select class="select2 widgetFilter col-lg-3 reloadOnChange" name="type">
                <option value="all"  ><?php echo vtranslate('All',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</option>
                <?php  $_smarty_tpl->tpl_vars['USER_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['USER_NAME']->_loop = false;
 $_smarty_tpl->tpl_vars['USER_ID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['ACCESSIBLE_USERS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['USER_NAME']->key => $_smarty_tpl->tpl_vars['USER_NAME']->value){
$_smarty_tpl->tpl_vars['USER_NAME']->_loop = true;
 $_smarty_tpl->tpl_vars['USER_ID']->value = $_smarty_tpl->tpl_vars['USER_NAME']->key;
?>
                    <option value="<?php echo $_smarty_tpl->tpl_vars['USER_ID']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['TYPE']->value){?><?php if ($_smarty_tpl->tpl_vars['USER_ID']->value==$_smarty_tpl->tpl_vars['TYPE']->value){?>  selected <?php }?>
                    <?php }else{ ?> <?php if ($_smarty_tpl->tpl_vars['USER_ID']->value==$_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value){?>  selected <?php }?><?php }?>>
                    <?php if ($_smarty_tpl->tpl_vars['USER_ID']->value==$_smarty_tpl->tpl_vars['CURRENT_USER_ID']->value){?> 
                        <?php echo vtranslate('LBL_MINE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>

                    <?php }else{ ?>
                        <?php echo $_smarty_tpl->tpl_vars['USER_NAME']->value;?>

                    <?php }?>
                    </option>
                <?php } ?>
            </select>
            <?php }else{ ?>
                <center><?php echo vtranslate('LBL_MY',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
 <?php echo vtranslate('History',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</center>
        <?php }?>
    </div>
</div>
<div class="dashboardWidgetContent" style="padding-top:15px;">
	<?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/HistoryContents.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div>

<div class="widgeticons dashBoardWidgetFooter">
    <div class="filterContainer boxSizingBorderBox">
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-sm-12">
                <div class="col-lg-4">
                    <span><strong><?php echo vtranslate('LBL_SHOW',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong></span>
                </div>
                <div class="col-lg-7">
                        <?php if ($_smarty_tpl->tpl_vars['COMMENTS_MODULE_MODEL']->value->isPermitted('DetailView')){?>
                            <label class="radio-group cursorPointer">
                                <input type="radio" name="historyType" class="widgetFilter reloadOnChange cursorPointer" value="comments"<?php if ($_smarty_tpl->tpl_vars['HISTORY_TYPE']->value=='comments'){?>checked=""<?php }?> /> <?php echo vtranslate('LBL_COMMENTS',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>

                            </label><br>
                        <?php }?>
                        <label class="radio-group cursorPointer">
                            <input type="radio" name="historyType" class="widgetFilter reloadOnChange cursorPointer" value="updates"<?php if ($_smarty_tpl->tpl_vars['HISTORY_TYPE']->value=='updates'){?>checked=""<?php }?> /> 
                            <span><?php echo vtranslate('LBL_UPDATES',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</span>
                        </label><br>
                        <label class="radio-group cursorPointer">
                            <input type="radio" name="historyType" class="widgetFilter reloadOnChange cursorPointer" value="all" <?php if ($_smarty_tpl->tpl_vars['HISTORY_TYPE']->value=='all'){?>checked=""<?php }?> /> <?php echo vtranslate('LBL_BOTH',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>

                        </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <span class="col-lg-4">
                        <span>
                            <strong><?php echo vtranslate('LBL_SELECT_DATE_RANGE',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</strong>
                        </span>
                </span>
                <span class="col-lg-7">
                    <div class="input-daterange input-group dateRange widgetFilter" id="datepicker" name="modifiedtime">
                        <input type="text" class="input-sm form-control" name="start" value='<?php echo $_smarty_tpl->tpl_vars['START']->value;?>
' style="height:30px;"/>
                        <span class="input-group-addon">to</span>
                        <input type="text" class="input-sm form-control" name="end" value='<?php echo $_smarty_tpl->tpl_vars['END']->value;?>
'style="height:30px;"/>
                    </div>
                </span>
            </div>
        </div>
    </div>
    <div class="footerIcons pull-right">
        <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("dashboards/DashboardFooterIcons.tpl",$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('SETTING_EXIST'=>true), 0);?>

    </div>
</div>
<?php }} ?>