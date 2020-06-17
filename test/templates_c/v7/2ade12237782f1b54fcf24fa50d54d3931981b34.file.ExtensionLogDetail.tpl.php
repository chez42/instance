<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:28:36
         compiled from "D:\xampp\htdocs\omni-live\layouts\v7\modules\Vtiger\ExtensionLogDetail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28105ee9c624063b04-19459767%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2ade12237782f1b54fcf24fa50d54d3931981b34' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\v7\\modules\\Vtiger\\ExtensionLogDetail.tpl',
      1 => 1589643771,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28105ee9c624063b04-19459767',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'TYPE' => 0,
    'MODULE' => 0,
    'TITLE' => 0,
    'SOURCE_MODULE' => 0,
    'LOG_ID' => 0,
    'DATA' => 0,
    'LOG' => 0,
    'RECORD_LINK' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c6240bb98',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c6240bb98')) {function content_5ee9c6240bb98($_smarty_tpl) {?>

<div id="detailviewhtml">
    <div class='fc-overlay-modal modal-content' style="height:100vh;">
        <div class='overlayHeader'>
            <?php ob_start();?><?php echo vtranslate($_smarty_tpl->tpl_vars['TYPE']->value,$_smarty_tpl->tpl_vars['MODULE']->value,vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value));?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["TITLE"] = new Smarty_variable($_tmp1, null, 0);?>
            <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("ModalHeader.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('TITLE'=>$_smarty_tpl->tpl_vars['TITLE']->value), 0);?>

        </div>

        <div class='modal-body'>
            <div class="row">
                <div class="col-sm-8 col-xs-8"></div>
                <div class="col-sm-4 col-xs-4">
                    <a id="downloadCsv" href="index.php?module=<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
&view=ExportExtensionLog&logid=<?php echo $_smarty_tpl->tpl_vars['LOG_ID']->value;?>
&type=<?php echo $_smarty_tpl->tpl_vars['TYPE']->value;?>
" type="button" class="btn addButton btn-default downloadCsv pull-right">
                        <span class="fa fa-download" aria-hidden="true"></span> <?php echo vtranslate('LBL_DOWNLOAD_AS_CSV',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                    </a>
                </div>
            </div>
            <br>
            <div class='datacontent' style="max-height: 450px">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th> <?php echo vtranslate('LBL_SOURCE_MODULE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
                                    <th> <?php echo vtranslate('LBL_RECORD_NAME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
                                        <?php if ($_smarty_tpl->tpl_vars['TYPE']->value=='vt_skip'||$_smarty_tpl->tpl_vars['TYPE']->value=='app_skip'){?>
                                        <th class="remove"> <?php echo vtranslate('LBL_REASON',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
                                        <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  $_smarty_tpl->tpl_vars['LOG'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LOG']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['DATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LOG']->key => $_smarty_tpl->tpl_vars['LOG']->value){
$_smarty_tpl->tpl_vars['LOG']->_loop = true;
?>
                                    <?php if ($_smarty_tpl->tpl_vars['TYPE']->value!='vt_delete'&&$_smarty_tpl->tpl_vars['TYPE']->value!='app_delete'){?>
                                        <?php $_smarty_tpl->tpl_vars['RECORD_LINK'] = new Smarty_variable($_smarty_tpl->tpl_vars['LOG']->value['link'], null, 0);?>
                                    <?php }?>
                                    <tr>
                                        <td> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['module'];?>
 </td>
                                        <td>
                                            <?php if (!empty($_smarty_tpl->tpl_vars['RECORD_LINK']->value)){?>
                                                <a class="extensionLink" href="<?php echo $_smarty_tpl->tpl_vars['RECORD_LINK']->value;?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['LOG']->value['name'];?>
</a>
                                            <?php }else{ ?>
                                                <?php echo $_smarty_tpl->tpl_vars['LOG']->value['name'];?>

                                            <?php }?>
                                        </td>
                                        <?php if ($_smarty_tpl->tpl_vars['LOG']->value['error']){?>
                                            <td> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['error'];?>
 </td>
                                        <?php }?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php }} ?>