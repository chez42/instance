<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:57
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Calendar\partials\SidebarEssentials.tpl" */ ?>
<?php /*%%SmartyHeaderCode:219515ee9c3e1a9fb25-21347159%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a62f5819845a76b2cfc866a08169124ab323828a' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Calendar\\partials\\SidebarEssentials.tpl',
      1 => 1589643804,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '219515ee9c3e1a9fb25-21347159',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'QUICK_LINKS' => 0,
    'SIDEBARWIDGET' => 0,
    'MODULE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3e1ac277',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3e1ac277')) {function content_5ee9c3e1ac277($_smarty_tpl) {?>

<div class="col-xs-12 text-center visible-xs visible-sm visible-md" style="margin: 20px 0px;">
<div class="btn-group">
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=Calendar">
                    <i class="material-icons">event</i>
                    <span class="">My Calendar</span>
                </a>
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=SharedCalendar">
                    <i class="material-icons">share</i>
                    <span class="hidden-sm hidden-xs">Shared Calendar</span>
                </a>
<a class="btn module-buttons" href="index.php?module=Calendar&amp;view=List">
                    <i class="material-icons">list</i>
                    <span class="hidden-sm hidden-xs">List View</span>
                </a>    
</div></div>

<div class="col-xs-12 text-center visible-lg " style="margin: 20px 0px;">
<div class="btn-group">
<a data-toggle="toosltip" tippytitle data-tippy-content="My Calendar" data-placement="top" title="My Calendar" class="btn module-buttons" href="index.php?module=Calendar&amp;view=Calendar">
                    <i class="material-icons">event</i>
                </a>
<a data-toggle="toosltip" tippytitle data-tippy-content="Shared Calendar" data-placement="top" title="Shared Calendar" class="btn module-buttons" href="index.php?module=Calendar&amp;view=SharedCalendar">
                    <i class="material-icons">share</i>
                </a>
<a data-toggle="toosltip" tippytitle data-tippy-content="Calendar List" data-placement="top" title="Calendar List" class="btn module-buttons" href="index.php?module=Calendar&amp;view=List">
                    <i class="material-icons">list</i>
                </a>   

</div></div>

<?php if ($_GET['view']=='Calendar'||$_GET['view']=='SharedCalendar'){?>

<div class="sidebar-menu noprint">


    <div class="module-filters" id="module-filters">
        <div class="sidebar-container lists-menu-container">
            <?php  $_smarty_tpl->tpl_vars['SIDEBARWIDGET'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['QUICK_LINKS']->value['SIDEBARWIDGET']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->key => $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value){
$_smarty_tpl->tpl_vars['SIDEBARWIDGET']->_loop = true;
?>
            <?php if ($_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel')=='LBL_ACTIVITY_TYPES'||$_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel')=='LBL_ADDED_CALENDARS'){?>
            <div class="calendar-sidebar-tabs sidebar-widget" id="<?php echo $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel');?>
-accordion" role="tablist" aria-multiselectable="true" data-widget-name="<?php echo $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel');?>
">
                <div class="calendar-sidebar-tab">
                    <div class="sidebar-widget-header" role="tab" data-url="<?php echo $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->getUrl();?>
">
                        <div class="sidebar-header clearfix">
                            
                            <button class="btn btn-info btn-sm btn-block add-calendar-feed">
                                 <i class="material-icons" aria-hidden="true" style="float: left">add</i>
                                <?php echo vtranslate($_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel'),$_smarty_tpl->tpl_vars['MODULE']->value);?>

                            </button> 
                        </div>
                    </div>
                    <hr style="margin: 5px 0;">
                    <div class="list-menu-content">
                        <div id="<?php echo $_smarty_tpl->tpl_vars['SIDEBARWIDGET']->value->get('linklabel');?>
" class="sidebar-widget-body activitytypes" style="max-height: 100%;">
                            <div style="text-align:center;"><img src="layouts/v7/skins/images/loading.gif"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            <?php } ?>    
        </div>
    </div>
</div>
<?php }else{ ?>
    <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("partials/SidebarEssentials.tpl",'Vtiger'), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php }?><?php }} ?>