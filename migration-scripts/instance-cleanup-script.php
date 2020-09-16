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
	
	$adb->pquery("delete FROM `vtiger_modtracker_detail` where vtiger_modtracker_detail.id not in (SELECT id FROM `vtiger_modtracker_basic`");
	
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
	
	$adb->pquery("delete FROM `vtiger_activity_reminder_popup` where recorid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_contact_portal_permissions` where crmid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activitycf` where activityid not in (select crmid from vtiger_crmentity)");
	
	$adb->pquery("delete FROM `vtiger_activity` where activityid not in (select crmid from vtiger_crmentity);")