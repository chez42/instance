<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:26:35
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\MSExchange\ExtensionListLog.tpl" */ ?>
<?php /*%%SmartyHeaderCode:168795ee9c5abb867d1-29698329%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f83d836559f6454028367fa771c90473941292c0' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\MSExchange\\ExtensionListLog.tpl',
      1 => 1591885032,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '168795ee9c5abb867d1-29698329',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODAL' => 0,
    'MODULE' => 0,
    'CURRENT_USER_MODEL' => 0,
    'PAGING_MODEL' => 0,
    'TOTAL_RECORD_COUNT' => 0,
    'LISTVIEW_ENTRIES_COUNT' => 0,
    'SOURCE_MODULE' => 0,
    'DATA' => 0,
    'LOG' => 0,
    'COLSPAN_WIDTH' => 0,
    'IS_SYNC_READY' => 0,
    'MODULE_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c5abc4548',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c5abc4548')) {function content_5ee9c5abc4548($_smarty_tpl) {?>
<style>
	.listview-table tr.listViewContentHeader{
	    background: #f9f9f9;
	}
	.listview-table tr {
	    border: 1px solid #DDD;
	    border-bottom: 0px!important;
	}
	.listview-table:not(.stateContents) tr th:first-child {
	    width: inherit ! important;
	}
	.extensionContents .listview-table thead th:not(:last-child) {
	    border-right: thin solid #DDDDDD!important;
	}
	.extensionContents .listview-table thead th {
    	border-bottom: thin solid #DDDDDD!important;
	}
	
</style>
<div class="col-sm-12 col-xs-12 extensionContents">
	<?php if (!$_smarty_tpl->tpl_vars['MODAL']->value){?>
		<div class="row">
        	<div class="col-md-6">
        		<h3 class="module-title pull-left" style="margin-top:0px;"><?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 - <?php echo vtranslate('LBL_SYNC_LOG',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</h3>
			</div>
        	<div class="col-md-6">
        		<div class="ext-actions pull-right">
        			<button type="button" data-url="index.php?module=Users&parent=Settings&view=MsExchangeSettings&record=<?php echo $_smarty_tpl->tpl_vars['CURRENT_USER_MODEL']->value->id;?>
" class="settingsPage btn btn-default">
                   		<?php echo vtranslate('LBL_SYNC_SETTINGS',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                   	</button>
					<button type="button" class="revokeMSAccount btn btn-default">
                    	<?php echo vtranslate('LBL_REVOKE_ACCESS',$_smarty_tpl->tpl_vars['MODULE']->value);?>

                   	</button>
				</div>
        		
        	</div>
        </div>
    <?php }?>
   	<div class="marginTop15px">
   		<div class="row">
   			<div class="col-md-offset-4 col-md-8">
	   			<input type="hidden" name="pageStartRange" id="pageStartRange" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getRecordStartRange();?>
" /> 
		        <input type="hidden" name="pageEndRange" id="pageEndRange" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getRecordEndRange();?>
" /> 
		        <input type="hidden" name="previousPageExist" id="previousPageExist" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isPrevPageExists();?>
" /> 
		        <input type="hidden" name="nextPageExist" id="nextPageExist" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->isNextPageExists();?>
" /> 
		        <input type="hidden" name="totalCount" id="totalCount" value="<?php echo $_smarty_tpl->tpl_vars['TOTAL_RECORD_COUNT']->value;?>
" /> 
		        <input type='hidden' name="pageNumber" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->get('page');?>
" id='pageNumber'> 
		        <input type='hidden' name="pageLimit" value="<?php echo $_smarty_tpl->tpl_vars['PAGING_MODEL']->value->getPageLimit();?>
" id='pageLimit'> 
		        <input type="hidden" name="noOfEntries" value="<?php echo $_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES_COUNT']->value;?>
" id="noOfEntries"> 
		        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['SOURCE_MODULE']->value;?>
" id="source_module"> 
		        <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['MODULE']->value;?>
" name="ext-module" id="ext-module" /> 
		        <?php $_smarty_tpl->tpl_vars['RECORD_COUNT'] = new Smarty_variable($_smarty_tpl->tpl_vars['TOTAL_RECORD_COUNT']->value, null, 0);?> 
	            <?php $_smarty_tpl->tpl_vars['PAGE_NUMBER'] = new Smarty_variable($_smarty_tpl->tpl_vars['PAGING_MODEL']->value->get('page'), null, 0);?> 
	            <?php echo $_smarty_tpl->getSubTemplate (vtemplate_path("Pagination.tpl",$_smarty_tpl->tpl_vars['MODULE']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array('SHOWPAGEJUMP'=>true), 0);?>
 
    		</div>
   		</div>
   	    
        <div id="table-content" class="table-container" style="border:0px;">
        	<table id="listview-table" class="table listview-table table-bordered" align="center">
	    	    <thead>
	                <tr class="listViewContentHeader">
	                    <th rowspan="2" class="align-middle" > <?php echo vtranslate('LBL_DATE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th rowspan="2" class="align-middle"> <?php echo vtranslate('LBL_TIME',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th rowspan="2" class="align-middle"> <?php echo vtranslate('LBL_MODULE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th colspan = "3" > <?php echo vtranslate('APPTITLE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th colspan = "3" > <?php echo vtranslate($_smarty_tpl->tpl_vars['MODULE']->value,$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                </tr>
	                <tr class="listViewContentHeader">
	                    <th> <?php echo vtranslate('Created',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th> <?php echo vtranslate('LBL_UPDATED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th> <?php echo vtranslate('LBL_DELETED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
                        <th> <?php echo vtranslate('Created',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th> <?php echo vtranslate('LBL_UPDATED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                    <th> <?php echo vtranslate('LBL_DELETED',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </th>
	                </tr>
	            </thead>
	            <tbody>
	            	<?php if (count($_smarty_tpl->tpl_vars['DATA']->value)>0){?>
	            	<?php  $_smarty_tpl->tpl_vars['LOG'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['LOG']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['DATA']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['LOG']->key => $_smarty_tpl->tpl_vars['LOG']->value){
$_smarty_tpl->tpl_vars['LOG']->_loop = true;
?>
	                    <tr>
	                        <td><?php echo $_smarty_tpl->tpl_vars['LOG']->value['sync_date'];?>
 </td>
	                        <td><?php echo $_smarty_tpl->tpl_vars['LOG']->value['sync_time'];?>
 </td>
	                        <td><?php echo vtranslate($_smarty_tpl->tpl_vars['LOG']->value['module'],$_smarty_tpl->tpl_vars['LOG']->value['module']);?>
</td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['vt_create_count']>0){?> syncLogDetail extensionLink <?php }?>" data-type="vt_create" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['vt_create_count'];?>
 </a> </td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['vt_update_count']>0){?> syncLogDetail extensionLink <?php }?>" data-type="vt_update" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['vt_update_count'];?>
 </a> </td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['vt_delete_count']>0){?> syncLogDetail extensionError <?php }?>" data-type="vt_delete" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['vt_delete_count'];?>
 </a></td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['app_create_count']>0){?> syncLogDetail extensionLink <?php }?>" data-type="app_create" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['app_create_count'];?>
 </a> </td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['app_update_count']>0){?> syncLogDetail extensionLink <?php }?>" data-type="app_update" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['app_update_count'];?>
 </a> </td>
	                        <td> <a class="<?php if ($_smarty_tpl->tpl_vars['LOG']->value['app_delete_count']>0){?> syncLogDetail extensionError <?php }?>" data-type="app_delete" data-id="<?php echo $_smarty_tpl->tpl_vars['LOG']->value['id'];?>
"> <?php echo $_smarty_tpl->tpl_vars['LOG']->value['app_delete_count'];?>
 </a></td>
	                    </tr>
	                <?php } ?>
	                <?php }?>
	                <?php if ($_smarty_tpl->tpl_vars['LISTVIEW_ENTRIES_COUNT']->value=='0'){?>
	                    <tr class="emptyRecordsDiv">
	                        <?php $_smarty_tpl->tpl_vars['COLSPAN_WIDTH'] = new Smarty_variable(12, null, 0);?>
	                        <td colspan="<?php echo $_smarty_tpl->tpl_vars['COLSPAN_WIDTH']->value;?>
">
	                            <div class="emptyRecordsContent">
	                                <center> 
	                                    <?php echo vtranslate('LBL_NO');?>
 <?php echo vtranslate('LBL_SYNC_LOG',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate('LBL_FOUND');?>
. 
	                                    <?php if ($_smarty_tpl->tpl_vars['IS_SYNC_READY']->value){?>
	                                        <a href="#" class="syncNow"> <span class="blueColor"> <?php echo vtranslate('LBL_SYNC_NOW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </span></a>
	                                    <?php }else{ ?>
	                                        <a href="#" data-url="<?php echo $_smarty_tpl->tpl_vars['MODULE_MODEL']->value->getExtensionSettingsUrl($_smarty_tpl->tpl_vars['SOURCE_MODULE']->value);?>
" class="settingsPage"> <span class="blueColor"> <?php echo vtranslate('LBL_CONFIGURE',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 <?php echo vtranslate('LBL_SYNC_SETTINGS',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </span></a>
	                                    <?php }?>
	                                </center>
	                            </div>
	                        </td>
	                    </tr>
	                <?php }?>
	            </tbody>
	        </table>
        </div>
   	</div>
   	<?php if (!$_smarty_tpl->tpl_vars['MODAL']->value){?>
   		<?php if ($_smarty_tpl->tpl_vars['IS_SYNC_READY']->value){?>
   			<div class="modal-overlay-footer clearfix">
				<div class="row clearfix">
					<div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
						<button id="Contacts_basicAction_LBL_Sync_Settings" type="submit" class="btn btn-success syncNow">
		                	<i class="fa fa-refresh"></i>
	                		<span><?php echo vtranslate('LBL_SYNC_NOW',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span>
		                </button>
					</div>
				</div>
			</div>
		<?php }?>
   	<?php }?>       
        <?php }} ?>