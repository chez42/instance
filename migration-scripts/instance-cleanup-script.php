<?php

	include_once "includes/main/WebUI.php";
	
	global $adb;

	/*$adb->pquery('DELETE FROM vtiger_group2grouprel WHERE groupid not in (21481,11844,33968,15372,33965)', array());
	$adb->pquery('DELETE FROM vtiger_group2role where groupid not in (21481,11844,33968,15372,33965)', array());
	$adb->pquery('DELETE FROM vtiger_group2rs where groupid not in (21481,11844,33968,15372,33965)', array());
	$adb->pquery('DELETE FROM vtiger_users2group WHERE groupid not in (21481,11844,33968,15372,33965)', array());
	$adb->pquery('DELETE FROM vtiger_groups where groupid not in (21481,11844,33968,15372,33965)', array());


	$adb->pquery("delete FROM `vtiger_crmentity` where smownerid not 
	in (select id from vtiger_users) and smownerid not in (21481,11844,33968,15372,33965)");*/
	
	
	$adb->pquery("delete FROM `vtiger_account` where accountid not in (select crmid from vtiger_crmentity)");	
	$adb->pquery("delete FROM `vtiger_accountscf` where accountid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_mail_accounts` where user_id not in (select id from vtiger_users)");
	$adb->pquery("delete FROM `vtiger_modtracker_basic` where crmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_modtracker_detail` where vtiger_modtracker_detail.id not in (SELECT id FROM `vtiger_modtracker_basic`)");
	
	$adb->pquery("delete FROM `vtiger_crmentityrel` where crmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_crmentityrel` where relcrmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_portfolioinformation` where portfolioinformationid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_portfolioinformationcf` where portfolioinformationid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_transactions` where transactionsid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_transactionscf` where transactionsid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_positioninformationcf` where positioninformationid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_positioninformation` where positioninformationid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_modcomments` where modcommentsid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_modcommentscf` where modcommentsid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity_recurring_info` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity_reminder` where activity_id not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity_reminder_popup` where recordid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_contact_portal_permissions` where crmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activitycf` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_emaildetails` where emailid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_emailslookup` where crmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_emails_recipientprefs` where userid not in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `vtiger_email_access` where crmid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_email_access` where mailid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_email_track` where crmid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_email_track` where mailid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_vteemailmarketing` where vteemailmarketingid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_vteemailmarketingcf` where vteemailmarketingid not in (select crmid from vtiger_crmentity)");
	$adb->pquery("delete FROM `vtiger_vteemailmarketingrel` where vteemailmarketingid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity_reminder_popup` where recordid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_calendar_user_activitytypes` where userid not  in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `vtiger_cntactivityrel` where activityid not  in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_salesmanactivityrel` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_seactivityrel` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM vtiger_crmentity where  setype = 'ModSecurities' and crmid not in (select modsecuritiesid from vtiger_modsecurities)");
	
	$adb->pquery("delete FROM `vtiger_asset_class_history` where account_number not  in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `accountstatus` where AccountNumber not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `account_rep_codes` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `com_vtiger_workflow_activatedonce` where entity_id not in 
	(select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `consolidated_balances` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `contacts` where crmid not in (Select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `daily_user_intervals_summed` where user_id not  in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `daily_user_total_balances` where user_id not in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `vtiger_user_records` where userid not  in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `vtiger_wsapp_logs_basic` where userid not  in (select id from vtiger_users)");
	
	$adb->pquery("delete FROM `vtiger_portfolioinformation_current` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `vtiger_portfolioinformation_fees` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `vtiger_portfolioinformation_historical` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `vtiger_portfolio_daily_individual` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `vtiger_portfolio_summary` where account_number not in (select account_number from vtiger_portfolioinformation)");
	
	$adb->pquery("delete FROM `vtiger_customview` where viewname  != 'All'");
	
	$adb->pquery("DELETE FROM vtiger_cvcolumnlist WHERE cvid not in (select cvid from vtiger_customview)");
	
	$adb->pquery("DELETE FROM vtiger_cvstdfilter WHERE cvid not in (select cvid from vtiger_customview)");
	
	$adb->pquery("DELETE FROM vtiger_cvadvfilter WHERE cvid not in (select cvid from vtiger_customview)");
	
	$adb->pquery("DELETE FROM vtiger_cvadvfilter_grouping WHERE cvid not in (select cvid from vtiger_customview)");
	
	//account_value_history
	//closeme
	
	/*ALTER TABLE `vtiger_emailtemplates_view_permission` ADD CONSTRAINT `fk_emailtemplate_view_permission` FOREIGN KEY (`template_id`) REFERENCES `vtiger_emailtemplates`(`templateid`) ON DELETE CASCADE ON UPDATE RESTRICT;
	*/
	
	/*
	ALTER TABLE `vtiger_portfolioinformation` ADD CONSTRAINT `fk_portfolio_crmentity` FOREIGN KEY (`portfolioinformationid`) REFERENCES `vtiger_crmentity`(`crmid`) ON DELETE CASCADE ON UPDATE RESTRICT;
	*/
	