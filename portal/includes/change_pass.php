<?php

	include_once('config.php'); 
	
	if(isset($_REQUEST['fun']) && $_REQUEST['fun'] == 'changepassword'){
		global $adb;
		$customer_id = $_SESSION['ID'];
		
		$newpw = trim($_REQUEST['new_password']);
		
		$confirmpw = trim($_REQUEST['confirm_password']);
		
		if($customer_id && $newpw == $confirmpw && $newpw != ''){
			
			$sql = "update vtiger_portalinfo set user_password=? where id=? ";
			$result = $adb->pquery($sql, array($newpw, $customer_id));
			
			if($result){
				$errormsg = 'true';//'MSG_PASSWORD_CHANGED';
			}else {
				$errormsg = 'false';//'LBL_ENTER_VALID_USER';	
			}
		}else {
			$errormsg = 'false';//'LBL_ENTER_VALID_USER';	
		}
		
		echo $errormsg;
	}