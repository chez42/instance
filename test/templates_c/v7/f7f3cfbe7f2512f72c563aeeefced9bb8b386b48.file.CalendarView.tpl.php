<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:57
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Calendar\CalendarView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:257405ee9c3e1acdbd1-02408456%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f7f3cfbe7f2512f72c563aeeefced9bb8b386b48' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Calendar\\CalendarView.tpl',
      1 => 1589643615,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '257405ee9c3e1acdbd1-02408456',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'CURRENT_USER' => 0,
    'CURRENT_USER_MODEL' => 0,
    'LEFTPANELHIDE' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3e1aed6c',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3e1aed6c')) {function content_5ee9c3e1aed6c($_smarty_tpl) {?>

<link type="text/css" rel="stylesheet" href="layouts/v7/skins/marketing/style.css" media="print" /><link type="text/css" rel="stylesheet" href="layouts/v7/lib/jquery/fullcalendar/print2.css" media="print" /><input type="hidden" id="currentView" value="<?php echo $_REQUEST['view'];?>
" /><input type="hidden" id="start_day" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('dayoftheweek');?>
" /><input type="hidden" id="activity_view" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('activity_view');?>
" /><input type="hidden" id="time_format" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('hour_format');?>
" /><input type="hidden" id="start_hour" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('start_hour');?>
" /><input type="hidden" id="date_format" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('date_format');?>
" /><input type="hidden" id="hideCompletedEventTodo" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('hidecompletedevents');?>
"><input type="hidden" id="show_allhours" value="<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER']->value->get('showallhours');?>
" /><style media="print">.noprint { display:none; }#mycalendar {margin-top:-70px;min-height:500px !important;}a[href]:after { content: none !important; }.vicon-meeting{ display : none; }</style><div id="mycalendar" class="calendarview col-lg-12"><?php $_smarty_tpl->tpl_vars['LEFTPANELHIDE'] = new Smarty_variable($_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->get('leftpanelhide'), null, 0);?><div class="essentials-toggle noprint" title="<?php echo vtranslate('LBL_LEFT_PANEL_SHOW_HIDE','Vtiger');?>
"><span class="essentials-toggle-marker fa <?php if ($_smarty_tpl->tpl_vars['LEFTPANELHIDE']->value=='1'){?>fa-chevron-right<?php }else{ ?>fa-chevron-left<?php }?> cursorPointer"></span></div></div>
<?php }} ?>