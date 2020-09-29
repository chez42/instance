<?php
$Vtiger_Utils_Log = true;

//chdir('../');

include_once 'includes/main/WebUI.php';

$ids = '27339,34287,34461,34577,34624';

$ticket = $adb->pquery("SELECT * FROM vtiger_troubletickets 
INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid 
AND vtiger_crmentity.deleted = 0
WHERE vtiger_crmentity.smownerid IN (" . $ids . ")");

if($adb->num_rows($ticket)){
    
    for($i=0;$i<$adb->num_rows($ticket);$i++){
		
        $ticketId = $adb->query_result($ticket, $i, 'ticketid');
        
		$financial_advisor = $adb->query_result($ticket, $i, 'financial_advisor');
        
        $adb->pquery("UPDATE vtiger_crmentity
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_senotesrel.crmid = ? AND vtiger_crmentity.deleted = 0",array($financial_advisor, $ticketId));
        
        $adb->pquery("UPDATE vtiger_crmentity
	    INNER JOIN vtiger_modcomments ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to =?",array($financial_advisor, $ticketId));
        
    }
    
}

$contacts = $adb->pquery("SELECT * FROM vtiger_contactdetails
INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.smownerid IN (" . $ids . ")");

if($adb->num_rows($contacts)){
    
    for($c=0;$c<$adb->num_rows($contacts);$c++){
        $conId = $adb->query_result($contacts, $c, 'contactid');
        $assUser = $adb->query_result($contacts, $c, 'smownerid');
        
        $adb->pquery("UPDATE vtiger_crmentity
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_senotesrel.crmid = ? AND vtiger_crmentity.deleted = 0",array($assUser, $conId));
        
        $adb->pquery("UPDATE vtiger_crmentity
	    INNER JOIN vtiger_modcomments ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to =?",array($assUser, $conId));
        
    }
    
}

$accounts = $adb->pquery("SELECT * FROM vtiger_account 
INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid
WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.smownerid IN (" . $ids . ")");

if($adb->num_rows($accounts)){
    
    for($a=0;$a<$adb->num_rows($accounts);$a++){
        $accId = $adb->query_result($accounts, $a, 'accountid');
        $assUser = $adb->query_result($accounts, $a, 'smownerid');
        
        $adb->pquery("UPDATE vtiger_crmentity
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_senotesrel.crmid = ? AND vtiger_crmentity.deleted = 0",array($assUser, $accId));
        
        $adb->pquery("UPDATE vtiger_crmentity
	    INNER JOIN vtiger_modcomments ON vtiger_modcomments.modcommentsid = vtiger_crmentity.crmid
        SET vtiger_crmentity.smownerid = ?
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_modcomments.related_to =?",array($assUser, $accId));
        
    }
    
}
exit;