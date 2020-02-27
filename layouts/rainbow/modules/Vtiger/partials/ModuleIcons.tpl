{assign var=iconsarrayTemp value=[

	'expenses'=>'monetization_on', 
	'potentials'=>'attach_money',
	'marketing'=>'thumb_up',
	'leads'=>'thumb_up',
	'accounts'=>'business',
	'sales'=>'attach_money',
	'smsnotifier'=>'sms', 'services'=>'format_list_bulleted',
	'pricebooks'=>'library_books',
	'salesorder'=>'attach_money',
	'purchaseorder'=>'attach_money',
	'vendors'=>'local_shipping',
	'faq'=>'help',
	'helpdesk'=>'headset',
	'assets'=>'settings',
	'project'=>'card_travel',
	'projecttask'=>'check_box',
	'projectmilestone'=>'card_travel',
	'mailmanager'=>'email',
	'documents'=>'file_download', 'calendar'=>'event',
	'emails'=>'email',
	'reports'=>'show_chart',
	'servicecontracts'=>'content_paste',
	'contacts'=>'contacts',
	'campaigns'=>'notifications',
	'quotes'=>'description',
	'invoice'=>'description',
	'emailtemplates'=>'subtitles',
	'pbxmanager'=>'perm_phone_msg',
	'rss'=>'rss_feed',
	'recyclebin'=>'delete_forever',
	'products'=>'inbox',
	'portal'=>'web',
	'inventory'=>'assignment',
	'support'=>'headset',
	'tools'=>'business_center',
	'mycthemeswitcher'=>'folder', 
	'chat'=>'chat', 
	'mobilecall'=>'call', 
	'call'=>'call', 
	'meeting'=>'people' 

]}

{assign var=APP_LIST value=Vtiger_MenuStructure_Model::getAppMenuList()}
{assign var=APP_GROUPED_MENU value=Settings_MenuEditor_Module_Model::getAllVisibleModules()}
{foreach item=APP_NAME from=$APP_LIST}						
	{foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
		{if !isset($iconsarrayTemp[{strtolower($moduleName)}]) }
			{append var='iconsarrayTemp' value='folder' index="{strtolower($moduleName)}"}			
		{/if}
	{/foreach}
{/foreach}

{assign var=iconsarray value=$iconsarrayTemp  scope='global'}