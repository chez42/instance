<?php /* Smarty version Smarty-3.1.7, created on 2020-06-17 07:18:21
         compiled from "D:\xampp\htdocs\omni-live\layouts\rainbow\modules\Vtiger\partials\SidebarAppMenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2875ee9c3bd431207-86694347%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2bcf92fa454e14cdbf5c659042f9260658f1fefb' => 
    array (
      0 => 'D:\\xampp\\htdocs\\omni-live\\layouts\\rainbow\\modules\\Vtiger\\partials\\SidebarAppMenu.tpl',
      1 => 1589643821,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2875ee9c3bd431207-86694347',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'USER_MODEL' => 0,
    'IMAGE_DETAILS' => 0,
    'IMAGE_INFO' => 0,
    'GLOBAL_SEARCH_VALUE' => 0,
    'COMPANY_LOGO' => 0,
    'DASHBOARD_MODULE_MODEL' => 0,
    'USER_PRIVILEGES_MODEL' => 0,
    'MODULE' => 0,
    'HOME_MODULE_MODEL' => 0,
    'APP_LIST' => 0,
    'APP_NAME' => 0,
    'APP_GROUPED_MENU' => 0,
    'APP_MENU_MODEL' => 0,
    'SELECTED_MENU_CATEGORY' => 0,
    'APP_IMAGE_MAP' => 0,
    'moduleModel' => 0,
    'moduleName' => 0,
    'iconsarray' => 0,
    'translatedModuleLabel' => 0,
    'DOCUMENTS_MODULE_MODEL' => 0,
    'TASK_MODULE_MODEL' => 0,
    'COMMENTS_MODULE_MODEL' => 0,
    'FIRST_MENU_MODEL' => 0,
    'MAILMANAGER_MODULE_MODEL' => 0,
    'EMAILTEMPLATES_MODULE_MODEL' => 0,
    'RECYCLEBIN_MODULE_MODEL' => 0,
    'RSS_MODULE_MODEL' => 0,
    'PORTAL_MODULE_MODEL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5ee9c3bd5d38f',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5ee9c3bd5d38f')) {function content_5ee9c3bd5d38f($_smarty_tpl) {?>



<aside class="left-sidebar hidden-lg hidden-md" style="" id="parent">
<div class="user-panel">
		 
        <div class="image hide">
         <?php $_smarty_tpl->tpl_vars['IMAGE_DETAILS'] = new Smarty_variable($_smarty_tpl->tpl_vars['USER_MODEL']->value->getImageDetails(), null, 0);?>
												<?php if ($_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value!=''&&$_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value[0]!=''&&$_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value[0]['path']==''){?>
										
									
									<span class="link-text-xs-only hidden-lg hidden-md hidden-sm"><?php echo $_smarty_tpl->tpl_vars['USER_MODEL']->value->getName();?>
</span>
												<?php }else{ ?>
													<?php  $_smarty_tpl->tpl_vars['IMAGE_INFO'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['IMAGE_DETAILS']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['IMAGE_INFO']->key => $_smarty_tpl->tpl_vars['IMAGE_INFO']->value){
$_smarty_tpl->tpl_vars['IMAGE_INFO']->_loop = true;
?>
														<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
<?php $_tmp1=ob_get_clean();?><?php if (!empty($_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'])&&!empty($_tmp1)){?>
															<img style="width: 30px;border-radius: 50%;
    padding: 7px 0px;" src="<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['path'];?>
_<?php echo $_smarty_tpl->tpl_vars['IMAGE_INFO']->value['orgname'];?>
">
														<?php }?>
													<?php } ?>
												<?php }?>
        </div>



</div>
	  
	  
            <!-- Sidebar scroll-->

<!-- search mobile-->

<div class="col-xs-12 visible-sm visible-xs" id ="searchmobile">
<div class="search-links-container">
				 <div class="search-link">
						<span class="ti-search" aria-hidden="true"></span>
						<input class="mobile-search-key keyword-input" type="text" placeholder="<?php echo vtranslate('LBL_TYPE_SEARCH');?>
" value="<?php echo $_smarty_tpl->tpl_vars['GLOBAL_SEARCH_VALUE']->value;?>
">
					</div>
				</div>
				</div>  
<!--/ search mobile-->

<div class="clearfix"></div>

				<div class="scroll-sidebar " >

                <!-- Sidebar navigation-->
                <nav class="sidebar-nav active" style="padding-bottom: 50px">
                    <ul id="sidebarnav" class="in mini-sidebar">
	                    
	                    <li class="sidebar-logo hide">
	                    	<img src="<?php echo $_smarty_tpl->tpl_vars['COMPANY_LOGO']->value->get('imagepath');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['COMPANY_LOGO']->value->get('alt');?>
"/>
	                    </li>
                        
                        <li class="nav-small-cap hide">APPS</li>
                        <!-- <li class="active"> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="material-icons">dashboard</i><span class="hide-menu">Dashboard <span class="label label-rouded label-themecolor pull-right">4</span></span></a>
                            <ul aria-expanded="true" class="collapse in">
                                <li class="active"><a href="index.html" class="active"><i class="fa fa-dashboard"></i> Minimal </a></li>
                                <li><a href="index2.html">Analytical</a></li>
                                <li><a href="index3.html">Demographical</a></li>
                                <li><a href="index4.html">Modern</a></li>
                            </ul>
                        </li>
                        -->
                        
                        <?php $_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL'] = new Smarty_variable(Users_Privileges_Model::getCurrentUserPrivilegesModel(), null, 0);?>
						<?php $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Home'), null, 0);?>
						<?php $_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Dashboard'), null, 0);?>
			
							<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL']->value->getId())){?>
								<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Home"){?>active<?php }?>"> <a class=" waves-effect waves-dark" href="<?php echo $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL']->value->getDefaultUrl();?>
" ><i class="material-icons">dashboard</i><span class="hide-menu" style="text-transform: uppercase"><?php echo vtranslate('LBL_DASHBOARD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
 </span></a>
                        </li>
							<?php }?>
							<?php $_smarty_tpl->tpl_vars['APP_GROUPED_MENU'] = new Smarty_variable(Settings_MenuEditor_Module_Model::getAllVisibleModules(), null, 0);?>
							<?php $_smarty_tpl->tpl_vars['APP_LIST'] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppMenuList(), null, 0);?>
							
							<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Home"){?>
							<?php $_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY'] = new Smarty_variable('Dashboard', null, 0);?>
							<?php }?>
							
							<?php  $_smarty_tpl->tpl_vars['APP_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_NAME']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_NAME']->key => $_smarty_tpl->tpl_vars['APP_NAME']->value){
$_smarty_tpl->tpl_vars['APP_NAME']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['APP_NAME']->value=='ANALYTICS'){?> <?php continue 1?><?php }?>
								<?php if (count($_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value])>0){?>
									
										<?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?>
											<?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?>
											<?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?>
												<?php break 1?>
											<?php }?>
										<?php } ?>
										
										<?php echo $_smarty_tpl->getSubTemplate (myclayout_path("modules/Vtiger/partials/ModuleIcons.tpl"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

									
								<li class="with-childs <?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>active<?php }?>"> <a class="has-arrow waves-effect waves-dark " href="#" aria-expanded="<?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>true<?php }else{ ?>false<?php }?>">
								<i class="app-icon-list fa <?php echo $_smarty_tpl->tpl_vars['APP_IMAGE_MAP']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value];?>
" ></i><span class="hide-menu"><?php echo vtranslate(($_smarty_tpl->tpl_vars['APP_NAME']->value));?>
</span></a>
                            
                            <ul aria-expanded="<?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>true<?php }else{ ?>false<?php }?>" class="collapse <?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>in<?php }?>" style="padding-left:0px;padding-top:4px;">
	                            <?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?>
	                            <?php $_smarty_tpl->tpl_vars['translatedModuleLabel'] = new Smarty_variable(vtranslate($_smarty_tpl->tpl_vars['moduleModel']->value->get('label'),$_smarty_tpl->tpl_vars['moduleName']->value), null, 0);?>
								
                                <li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
" >
									<i class="material-icons module-icon" ><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp2=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp2];?>
</i>
								<span class="hide-menu"> <?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
</span></a></li>
                                <?php } ?>
                            </ul>
                            
                        </li>
                        
											
								<?php }?>
							<?php } ?>
                        
                        <li class="nav-small-cap hide">TOOLS & SETTINGS</li>
                                               
                       
						
						<?php $_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Documents'), null, 0);?>
						<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL']->value->getId())){?>
							
							<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Documents"){?>active<?php }?>"> <a class=" waves-effect waves-dark" href="index.php?module=Documents&view=List" ><i class="app-icon-list material-icons">file_download</i><span class="hide-menu"> <?php echo vtranslate('Documents');?>
</span></a>
                        </li>
						<?php }?>
						<?php $_smarty_tpl->tpl_vars['TASK_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Task'), null, 0);?>
						<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['TASK_MODULE_MODEL']->value->getId())){?>
							<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Task"){?>active<?php }?>"> 
								<a class=" waves-effect waves-dark" href="index.php?module=Task&view=List" >
									<i class="fa fa-tasks" aria-hidden="true"></i>
									<span class="hide-menu"> <?php echo vtranslate('Task');?>
</span>
								</a>
                        	</li>
						<?php }?>
						<?php $_smarty_tpl->tpl_vars['COMMENTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('ModComments'), null, 0);?>
						<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['COMMENTS_MODULE_MODEL']->value->getId())){?>
							<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="ModComments"){?>active<?php }?>"> 
								<a class=" waves-effect waves-dark" href="index.php?module=ModComments&view=List" >
									<i class="fa fa-comments-o" aria-hidden="true"></i>
									<span class="hide-menu"> <?php echo vtranslate('ModComments');?>
</span>
								</a>
                        	</li>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>
							<?php if (vtlib_isModuleActive('ExtensionStore')){?>
								
								<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="ExtensionStore"){?>active<?php }?>"> <a class=" waves-effect waves-dark" href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore" ><i class="app-icon-list material-icons">shopping_cart</i><span class="hide-menu"> <?php echo vtranslate('LBL_EXTENSION_STORE','Settings:Vtiger');?>
</span></a>
                        </li>
							<?php }?>
						<?php }?>
			                      
			                      
			                      
			                      
			                      
			                      
			                      
			                      
				<?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?>
					<?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?>
						<?php break 1?>
					<?php }?>
				<?php } ?>
			
			
			<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>
			
			<li class="with-childs <?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>active<?php }?>"> <a class="has-arrow waves-effect waves-dark " href="#" aria-expanded="<?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>true<?php }else{ ?>false<?php }?>"><i class="app-icon-list material-icons">settings</i><span class="hide-menu"> <?php echo vtranslate('LBL_SETTINGS','Settings:Vtiger');?>
</span></a>
                            
                            <ul  style="padding-left:6px;padding-top:4px;" aria-expanded="<?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>true<?php }else{ ?>false<?php }?>" class="collapse <?php if ($_smarty_tpl->tpl_vars['SELECTED_MENU_CATEGORY']->value==$_smarty_tpl->tpl_vars['APP_NAME']->value){?>in<?php }?>">
	                           
	                            <li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="index.php?module=Vtiger&parent=Settings&view=Index" ><span class="module-icon"><i class="material-icons">settings</i></span><span class="hide-menu">  <?php echo vtranslate('LBL_CRM_SETTINGS','Vtiger');?>
</span></a></li>
					
								<li><a class="waves-effect waves-dark <?php if ($_smarty_tpl->tpl_vars['MODULE']->value==$_smarty_tpl->tpl_vars['moduleName']->value){?>active<?php }?>" href="index.php?module=Users&parent=Settings&view=List" ><span class="module-icon"><i class="material-icons">contacts</i></span><span class="hide-menu">   <?php echo vtranslate('LBL_MANAGE_USERS','Vtiger');?>
</span></a></li>
					
						
	                            
                            </ul>
			</li>  
			
			<?php }else{ ?>
				
				<li class="<?php if ($_smarty_tpl->tpl_vars['MODULE']->value=="Users"){?>active<?php }?>"> <a class=" waves-effect waves-dark" href="index.php?module=Users&view=Settings" ><i class="material-icons">settings</i><span class="hide-menu" style="text-transform: uppercase"> <?php echo vtranslate('LBL_SETTINGS','Settings:Vtiger');?>
</span></a>
                        </li>
                        
			<?php }?>
			
                       
                    </ul>
                </nav>
        </aside>




<div class="app-menu hide" id="app-menu">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-xs-2 cursorPointer app-switcher-container">
				<div class="row app-navigator">
					<span id="menu-toggle-action" class="app-icon ti-close"></span>
				</div>
			</div>
		</div>
		<?php $_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL'] = new Smarty_variable(Users_Privileges_Model::getCurrentUserPrivilegesModel(), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Home'), null, 0);?>
		<?php $_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Dashboard'), null, 0);?>
		<div class="app-list row">
			<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DASHBOARD_MODULE_MODEL']->value->getId())){?>
				<div class="menu-item app-item dropdown-toggle" data-default-url="<?php echo $_smarty_tpl->tpl_vars['HOME_MODULE_MODEL']->value->getDefaultUrl();?>
">
					<div class="menu-items-wrapper">
						<span class="app-icon-list"><i class="material-icons">dashboard</i></span>
						<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('LBL_DASHBOARD',$_smarty_tpl->tpl_vars['MODULE']->value);?>
</span>
					</div>
				</div>
			<?php }?>
			<?php $_smarty_tpl->tpl_vars['APP_GROUPED_MENU'] = new Smarty_variable(Settings_MenuEditor_Module_Model::getAllVisibleModules(), null, 0);?>
			<?php $_smarty_tpl->tpl_vars['APP_LIST'] = new Smarty_variable(Vtiger_MenuStructure_Model::getAppMenuList(), null, 0);?>
			<?php  $_smarty_tpl->tpl_vars['APP_NAME'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_NAME']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_LIST']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_NAME']->key => $_smarty_tpl->tpl_vars['APP_NAME']->value){
$_smarty_tpl->tpl_vars['APP_NAME']->_loop = true;
?>
				<?php if ($_smarty_tpl->tpl_vars['APP_NAME']->value=='ANALYTICS'){?> <?php continue 1?><?php }?>
				<?php if (count($_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value])>0){?>
					<div class="dropdown app-modules-dropdown-container">
						<?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?>
							<?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?>
							<?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?>
								<?php break 1?>
							<?php }?>
						<?php } ?>
						<div class="menu-item app-item dropdown-toggle app-item-color-<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
" data-app-name="<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="<?php echo $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
">
							<div class="menu-items-wrapper app-menu-items-wrapper">
								<span class="app-icon-list fa <?php echo $_smarty_tpl->tpl_vars['APP_IMAGE_MAP']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value];?>
"></span>
								<span class="app-name textOverflowEllipsis"> <?php echo vtranslate(($_smarty_tpl->tpl_vars['APP_NAME']->value));?>
</span>
								<span class="ti-angle-right pull-right"></span>
							</div>
						</div>
						<ul class="dropdown-menu app-modules-dropdown" aria-labelledby="<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
_modules_dropdownMenu">
							<li class="visible-sm visible-xs app-item-color-<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
">
								<span style="color:white">
									<span class="app-icon-list fa <?php echo $_smarty_tpl->tpl_vars['APP_IMAGE_MAP']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value];?>
"></span>
									<span class="app-name textOverflowEllipsis"> <?php echo vtranslate(($_smarty_tpl->tpl_vars['APP_NAME']->value));?>
</span>
								</span>
							</li>
							
							<?php  $_smarty_tpl->tpl_vars['moduleModel'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['moduleModel']->_loop = false;
 $_smarty_tpl->tpl_vars['moduleName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['moduleModel']->key => $_smarty_tpl->tpl_vars['moduleModel']->value){
$_smarty_tpl->tpl_vars['moduleModel']->_loop = true;
 $_smarty_tpl->tpl_vars['moduleName']->value = $_smarty_tpl->tpl_vars['moduleModel']->key;
?>
								<?php $_smarty_tpl->tpl_vars['translatedModuleLabel'] = new Smarty_variable(vtranslate($_smarty_tpl->tpl_vars['moduleModel']->value->get('label'),$_smarty_tpl->tpl_vars['moduleName']->value), null, 0);?>
								<li>
									<a href="<?php echo $_smarty_tpl->tpl_vars['moduleModel']->value->getDefaultUrl();?>
&app=<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
" title="<?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
">
									
										<span class="module-icon">
											<i class="material-icons"><?php ob_start();?><?php echo strtolower($_smarty_tpl->tpl_vars['moduleName']->value);?>
<?php $_tmp3=ob_get_clean();?><?php echo $_smarty_tpl->tpl_vars['iconsarray']->value[$_tmp3];?>
</i>
										</span>
										<span class="module-name textOverflowEllipsis"><?php echo $_smarty_tpl->tpl_vars['translatedModuleLabel']->value;?>
</span>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				<?php }?>
			<?php } ?>
			<div class="app-list-divider"></div>
			<?php $_smarty_tpl->tpl_vars['MAILMANAGER_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('MailManager'), null, 0);?>
			<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['MAILMANAGER_MODULE_MODEL']->value->getId())){?>
				<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=MailManager&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list"><i class="maerial-icons">email</i></span>
						<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('MailManager');?>
</span>
					</div>
				</div>
			<?php }?>
			<?php $_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Documents'), null, 0);?>
			<?php if ($_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['DOCUMENTS_MODULE_MODEL']->value->getId())){?>
				<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=Documents&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list"><i class="material-icons">file_download</i></span>
						<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('Documents');?>
</span>
					</div>
				</div>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>
				<?php if (vtlib_isModuleActive('ExtensionStore')){?>
					<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore">
						<div class="menu-items-wrapper">
							<span class="app-icon-list"><i class="material-icons">shopping_cart</i></span>
							<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('LBL_EXTENSION_STORE','Settings:Vtiger');?>
</span>
						</div>
					</div>
				<?php }?>
			<?php }?>
			<div class="dropdown app-modules-dropdown-container dropdown-compact">
				<?php  $_smarty_tpl->tpl_vars['APP_MENU_MODEL'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['APP_GROUPED_MENU']->value[$_smarty_tpl->tpl_vars['APP_NAME']->value]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->key => $_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){
$_smarty_tpl->tpl_vars['APP_MENU_MODEL']->_loop = true;
?>
					<?php $_smarty_tpl->tpl_vars['FIRST_MENU_MODEL'] = new Smarty_variable($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value, null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['APP_MENU_MODEL']->value){?>
						<?php break 1?>
					<?php }?>
				<?php } ?>
				<div class="menu-item app-item dropdown-toggle app-item-misc" data-app-name="TOOLS" id="TOOLS_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<div class="menu-items-wrapper app-menu-items-wrapper">
						<span class="app-icon-list ti-more-alt"></span>
						<span class="app-name textOverflowEllipsis"> <?php echo vtranslate("LBL_MORE");?>
</span>
						<span class="ti-angle-right pull-right"></span>
					</div>
				</div>
				<ul class="dropdown-menu app-modules-dropdown dropdown-modules-compact" aria-labelledby="<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
_modules_dropdownMenu" data-height="0.34">
					<?php $_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('EmailTemplates'), null, 0);?>
					
					<li class="visible-sm visible-xs app-item-misc">
								<span style="color:white">
									<span class="app-icon-list ti-more-alt"></span>
									<span class="app-name textOverflowEllipsis"> <?php echo vtranslate("LBL_MORE");?>
</span>
								</span>
					</li>
							
					<?php if ($_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL']->value&&$_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL']->value->getId())){?>
						<li>
							<a href="<?php echo $_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL']->value->getDefaultUrl();?>
">
								<span class="module-icon"><i class="fa fa-fast-forward" aria-hidden="true"></i></span>
								<span class="module-name textOverflowEllipsis"> <?php echo vtranslate($_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL']->value->getName(),$_smarty_tpl->tpl_vars['EMAILTEMPLATES_MODULE_MODEL']->value->getName());?>
</span>
							</a>
						</li>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars['RECYCLEBIN_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('RecycleBin'), null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['RECYCLEBIN_MODULE_MODEL']->value&&$_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['RECYCLEBIN_MODULE_MODEL']->value->getId())){?>
						<li>
							<a href="<?php echo $_smarty_tpl->tpl_vars['RECYCLEBIN_MODULE_MODEL']->value->getDefaultUrl();?>
">
								<span class="module-icon"><i class="material-icons">delete_forever</i></span>
								<span class="module-name textOverflowEllipsis"> <?php echo vtranslate('Recycle Bin');?>
</span>
							</a>
						</li>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars['RSS_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Rss'), null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['RSS_MODULE_MODEL']->value&&$_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['RSS_MODULE_MODEL']->value->getId())){?>
						<li>
							<a href="index.php?module=Rss&view=List">
								<span class="module-icon"><i class="material-icons">rss_feed</i></span>
								<span class="module-name textOverflowEllipsis"><?php echo vtranslate($_smarty_tpl->tpl_vars['RSS_MODULE_MODEL']->value->getName(),$_smarty_tpl->tpl_vars['RSS_MODULE_MODEL']->value->getName());?>
</span>
							</a>
						</li>
					<?php }?>
					<?php $_smarty_tpl->tpl_vars['PORTAL_MODULE_MODEL'] = new Smarty_variable(Vtiger_Module_Model::getInstance('Portal'), null, 0);?>
					<?php if ($_smarty_tpl->tpl_vars['PORTAL_MODULE_MODEL']->value&&$_smarty_tpl->tpl_vars['USER_PRIVILEGES_MODEL']->value->hasModulePermission($_smarty_tpl->tpl_vars['PORTAL_MODULE_MODEL']->value->getId())){?>
						<li>
							<a href="index.php?module=Portal&view=List">
								<span class="module-icon"><i class="material-icons">desktop_windows</i></span>
								<span class="module-name textOverflowEllipsis"> <?php echo vtranslate('Portal');?>
</span>
							</a>
						</li>
					<?php }?>
				</ul>
			</div>
			<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>
				<div class="dropdown app-modules-dropdown-container dropdown-compact">
					<div class="menu-item app-item dropdown-toggle app-item-misc" data-app-name="SETTINGS" id="TOOLS_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>index.php?module=Vtiger&parent=Settings&view=Index<?php }else{ ?>index.php?module=Users&view=Settings<?php }?>">
						<div class="menu-items-wrapper app-menu-items-wrapper">
							<span class="app-icon-list"><i class="material-icons">settings</i></span>
							<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('LBL_SETTINGS','Settings:Vtiger');?>
</span>
							<?php if ($_smarty_tpl->tpl_vars['USER_MODEL']->value->isAdminUser()){?>
								<span class="ti-angle-right pull-right"></span>
							<?php }?>
						</div>
					</div>
					<ul class="dropdown-menu app-modules-dropdown dropdown-modules-compact" aria-labelledby="<?php echo $_smarty_tpl->tpl_vars['APP_NAME']->value;?>
_modules_dropdownMenu" data-height="0.27">
						<li class="visible-sm visible-xs app-item-misc">
								<span style="color:white">
									<span class="app-icon-list"><i class="material-icons">settings</i></span>
									<span class="app-name textOverflowEllipsis"> <?php echo vtranslate('LBL_SETTINGS','Settings:Vtiger');?>
</span>
								</span>
						</li>
						<li>
							<a href="?module=Vtiger&parent=Settings&view=Index">
								<span class="module-icon"><i class="material-icons">settings</i></span>
								<span class="module-name textOverflowEllipsis"> <?php echo vtranslate('LBL_CRM_SETTINGS','Vtiger');?>
</span>
							</a>
						</li>
						<li>
							<a href="?module=Users&parent=Settings&view=List">
								<span class="module-icon"><i class="material-icons">contacts</i></span>
								<span class="module-name textOverflowEllipsis"> <?php echo vtranslate('LBL_MANAGE_USERS','Vtiger');?>
</span>
							</a>
						</li>
					</ul>
				</div>
			<?php }?>
		</div>
	</div>
</div>
<?php }} ?>