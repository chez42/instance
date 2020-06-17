<?php

class Portfolios extends CRMEntity {
	var $table_name = "vtiger_portfolios";
	
        function GetSecurityInfoBySecurityID($securityID)
        {
            global $adb;
            $query = "SELECT * FROM vtiger_securities s
                      LEFT JOIN vtiger_security_types st ON st.security_type_id = s.security_type_id
                      WHERE s.security_id=?";
            $result = $adb->pquery($query, array($securityID));
            return $result;
        }
        
	function GetAllGroupIDsForUser($userID){
		global $adb;
		$query = "SELECT groupid FROM vtiger_users2group WHERE userid={$userID}";
		$result = $adb->pquery($query, array());
		return $result;
	}
        
	function GetUserIDsFromGroupID($groupID, $tabID)
	{
//            $members = getGroupMembers($groupID);
//            foreach($members['users'] AS $a => $b)
//                echo "A: {$b}<br />";
		global $adb;
		$query = "SELECT userid FROM vtiger_tmp_read_group_sharing_per WHERE sharedgroupid={$groupID} AND tabid={$tabID}";
//		echo $query;
		$result = $adb->pquery($query, array());
		return $result;
	}
	
	function GetAllAccountIDsForUser($userID){
		global $adb;
		$query = "SELECT crmid FROM vtiger_crmentity WHERE smownerid={$userID} AND setype='Accounts'";
		$result = $adb->pquery($query, array());
		return $result;
	}
	
	function GetAllContactIDsForUser($userID){
		global $adb;
		$query = "SELECT crmid FROM vtiger_crmentity WHERE smownerid={$userID} AND setype='Contacts'";
		$result = $adb->pquery($query, array());
		return $result;
	}
	
        function GetAllSSNFromAccountsArray($accounts)
        {
            global $adb;

            $accounts = "'" . implode("','", $accounts) . "'";
            if($accounts == "''")
                return 0;
            $query = "SELECT * FROM vtiger_contactdetails LEFT JOIN vtiger_contactscf 
                      ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
                      WHERE vtiger_contactdetails.accountid IN ($accounts)";
                      
//            echo $query . "<br />";
            $result = $adb->pquery($query, array());
            
            $ssns = array();
            if($adb->num_rows($result) > 0)
                for($x = 0; $x < $adb->num_rows($result); $x++)
                    $ssns[$x] = $adb->query_result($result, $x, "ssn");
            else
                return 0;
            
            return $ssns;
        }
        
        function GetAllPortfoliosBySecurityID($securityID, $conditions="null")
        {
            global $adb;
            $query = "SELECT * FROM vtiger_portfolio_securities ps
                      LEFT JOIN vtiger_portfolios p ON p.portfolio_id = ps.portfolio_security_portfolio_id
                      LEFT JOIN vtiger_securities s ON ps.portfolio_security_security_id = s.security_id
                      WHERE portfolio_security_security_id = {$securityID} {$conditions}";
//                      echo $query . "<br /><br />";
            $result = $adb->pquery($query, array());
            return $result;
        }
        
        function GetAllAccountsAssignedToGroup($groupID, $includeContacts = false)//Do we want the contacts as well
        {
            global $adb;
            $contacts = array();
            if($includeContacts)
            {
                $query = "SELECT crmid FROM vtiger_crmentity WHERE setype='Contacts' AND smownerid={$groupID}";
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) >  0)
                    foreach($result AS $k => $v)
                        $contacts[] = $v["crmid"];

                if($contacts)
                    $contacts = "'" . implode("','", $contacts) . "'";
                
                $query = "SELECT accountid FROM vtiger_contactdetails WHERE contactid IN ($contacts) AND accountid != 0";
                unset($contacts);
                $contacts = array();
                $result = $adb->pquery($query, array());
                if($adb->num_rows($result) > 0)
                    foreach($result AS $k => $v)
                        $contacts[] = $v["accountid"];
            }
//            echo "NUMBER OF CONTACTS: " . sizeof($contacts) . "<br />";
            if($contacts)
                $contacts = "'" . implode("','", $contacts) . "'";
//            echo "CONTACTS: {$contacts}<br />";
            $query = "SELECT crmid FROM vtiger_crmentity WHERE (setype='Accounts') AND (smownerid={$groupID} OR crmid IN ({$contacts}))";
            $result = $adb->pquery($query, array());
            $accounts = array();
            if($adb->num_rows($result) > 0)
            {
                foreach($result AS $k => $v)
                    $accounts[] = $v["crmid"];
//                echo "NUMBER OF ACCOUNTS: " . sizeof($accounts) . "<br />";
                return $accounts;
            }
            return null;
        }
        
	function GetAllPortfolioIDsForUser($userID, $searchtype=null, $searchcontent=null, $extraConditions=null, $order=null, $direction="ASC")
	{
		global $adb;
//		$currentuser = $userID;
                global $current_user;

                if(!$direction)
                    $direction = "ASC";
                
                $ssns = array();
		$userids = array();
		$accounts = array();
                $ssnarray = array();//Added in to get SSN's from accounts that are group assigned
		//echo $currentuser;//Show the current logged in user
                
		$count = 0;
		$groupcount = 0;
//                echo "ORDER IN: {$order}<br />";
                $has_access = HasRoleAccess($current_user->id);

                if(!is_admin($current_user) && !$has_access)
		{
			$result = $this->GetAllGroupIDsForUser($current_user->id);
//			echo "Getting group ID for user #: " . $currentuser . "<br />";
			$groups = $adb->num_rows($result);
//			echo "NUMBER OF GROUPS THE USER IS IN: {$groups}<br />";
			$userids[0]=$current_user->id;
			for($x = 0; $x < $groups; $x++)
			{
				$groupID = $adb->query_result($result, $x, "groupid");
//                                echo "GROUPID: {$groupID}<br />";
				$groupResult = $this->GetUserIDsFromGroupID($groupID, 4);
				$groupResult_num = $adb->num_rows($groupResult);//Get the number of user ID's assigned to the group
//                                echo "NUMBER OF USERS IN GROUP: {$groupResult_num}<br />";
                                $tmp = $this->GetAllAccountsAssignedToGroup($groupID, true);
                                
//                                echo "ACCOUNTS: " . SeparateArrayWithCommasAndSingleQuotes($tmp) . "<br />";
                                if($tmp != null)
                                    $accounts = array_merge($accounts, $tmp);
                                
                                $ssnarray = $this->GetAllSSNFromAccountsArray($accounts);
                                $t = 0;
                                if($ssnarray)
                                    $ssnarray = "'" . implode("','", $ssnarray) . "'";//Implode the portfolio_ids for use in the query.
//                                echo "SSNS: {$ssnarray}<br />";
//				echo "Number of users in group: " . $groupResult_num . "<br />";
//				$groupResult_num+=1;//Add one because we always get the USER's ID
//				$userids[0]=$currentuser;//Set the first userid to the logged in user
				for($y = 0; $y < $groupResult_num; $y++)
				{
					$userids[$groupcount] = $adb->query_result($groupResult, $y, "userid");
//                                        echo "User ID: " . $userids[$groupcount] . "<br />";
//					echo "User being added to group id array: " . $userids[$groupcount] . "<br />";
					$groupcount++;
				}
			}
                        if(!in_array($current_user->id,$userids))//If the current user is NOT in the user ID list for some reason
                            $userids[] = $current_user->id;
                        
			$userids = array_unique($userids);//Remove any duplicate users
//			echo "Size of userids is " . sizeof($userids) . "<br />";
			for($x = 0; $x < sizeof($userids); $x++)
			{
				$result = $this->GetAllContactIDsForUser($userids[$x]);
				$num_rows = $adb->num_rows($result);
//				echo "NUMBER OF CONTACTS BELONGING TO USER {$userids[$x]}: {$num_rows}<br />";
				for($y = 0; $y < $num_rows; $y++)
				{
					$id = $adb->query_result($result, $y, crmid);
					$r = $this->GetPersonalInfoFromContactID($id);
					$ssns[$count] = $adb->query_result($r, 0, "ssn");
					$count++;
				}
			}
                        
		}
		
//		echo "Size of matching SSN's " . sizeof($ssns) . "<br />";
//		$conditionString = "WHERE (";
                $separated = SeparateArrayWithCommasAndSingleQuotes($ssns);
                if(!$ssnarray)
                    $ssnarray = "''";
                $ssnarray .= ", " . $separated;
		$conditionString = "WHERE (portfolio_tax_id IN ($ssnarray) ";//This was set to *AND*, not sure why but causes people to not see portfolios if it is AND
/*		for($x = 0; $x < sizeof($ssns); $x++)
		{
//                    if($ssns[$x] != '')
                    {
                        $conditionString = $conditionString . "portfolio_tax_id='{$ssns[$x]}'";
                        if($x != sizeof($ssns)-1)
                                $conditionString = $conditionString . " OR ";
                    }
		}*/
                
		$conditionString .= ") ";
                $conditionString .= $extraConditions;
		if(is_admin($current_user) || $role == "CONCERT Support")//No conditions for the admin, he's like pokeman and gets em all
			$conditionString = "";//"WHERE portfolio_tax_id!=''";
//		echo $conditionString . "<br />";
                if(!$order)
                    $order = "portfolio_first_name";
		if($searchtype != null)
			if($searchcontent == null)//Nothing was typed in for search text
                        {
                                $order = $searchtype;
				$result = $this->GetAllPortfolios("{$conditionString}",$order, $direction);
                        }
			else
                        {
                                if(is_admin($current_user) || $role == "CONCERT Support")
                                    $result = $this->GetAllPortfolios("WHERE {$searchtype} REGEXP '{$searchcontent}'",$order, $direction);
                                else
                                    $result = $this->GetAllPortfolios("{$conditionString} AND {$searchtype} REGEXP '{$searchcontent}'",$order, $direction);
                        }
		else
			$result = $this->GetAllPortfolios("{$conditionString}",$order, $direction);
		
		return $result;
	}

	function GetAllPortfolios($conditions=null,$order=null, $direction=null){
		global $adb;
                if(!$order)
                    $order = "portfolio_account_number";
                if(!$direction)
                    $direction = "ASC";
                
//                echo "DIRECTION: {$direction}<br />";
                $query = "SELECT * FROM v_UserAccountPortfolioV2 {$conditions} GROUP BY portfolio_account_number ORDER BY {$order} {$direction}";//Group by requires to avoid doubles/triples of certain accounts
//		echo $query;
                $result = $adb->pquery($query, array());
		return $result;
	}
	
	function GetPersonalInfoFromAccountID($id){//Get personal info from the given account ID
		global $adb;
		$query = "SELECT * FROM vtiger_contactdetails LEFT JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid WHERE vtiger_contactdetails.accountid={$id}";
		$result = $adb->pquery($query, array());
		return $result;
	}
	
	function GetPersonalInfoFromPortfolioID($id){
		global $adb;
		$query = "SELECT portfolio_tax_id FROM vtiger_portfolios WHERE portfolio_id={$id}";//Get the SSN
		$result = $adb->pquery($query, array());
		$ssn = $adb->query_result($result, 0, "portfolio_tax_id");
		return $this->GetPersonalInfoFromSSN($ssn);//Get the personal information with the SSN we just retrieved
	}
	
        public function GetPortfolioInfoFromAccountNumber($portfolio_account_number)
        {
            global $adb;
            $query = "SELECT * FROM vtiger_portfolios p
                      LEFT JOIN vtiger_pc_account_custom ac ON ac.account_number = p.portfolio_account_number
                      WHERE portfolio_account_number = ?";
            $result = $adb->pquery($query, array($portfolio_account_number));
            return $result;
        }
        
	function GetAccountInfoFromAccountID($id){
		global $adb;
		$query = "SELECT * FROM vtiger_account LEFT JOIN vtiger_accountscf ON vtiger_account.accountid = vtiger_accountscf.accountid WHERE vtiger_account.accountid AND vtiger_accountscf.accountid={$id}";
		$result = $adb->pquery($query, array());
		return $result;//Return all account information
	}
	
	function GetPortfolioInfoFromPortfolioID($id){
		global $adb;
		$query = "SELECT * FROM vtiger_portfolios WHERE portfolio_id={$id}";
		$result = $adb->pquery($query, array());
		return $result;//Return all portfolio information
	}
	
	function GetAllAccountsFromSSN($ssn){
		global $adb;
		$query = "SELECT * FROM vtiger_portfolios WHERE portfolio_tax_id='{$ssn}'";
//                echo $query;
		$result = $adb->pquery($query, array());
		return $result;
	}
	
	function GetPersonalInfoFromContactID($id){
		global $adb;
		$query = "SELECT * FROM vtiger_contactdetails LEFT JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid WHERE vtiger_contactscf.contactid AND vtiger_contactdetails.contactid={$id}";
		$result = $adb->pquery($query, array());
		return $result;
	}
	
	private function GetPersonalInfoFromSSN($ssn){
		global $adb;
//		$query = "SELECT * FROM vtiger_contactdetails LEFT JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid WHERE ssn='{$ssn}'";
		$query = "SELECT * FROM vtiger_contactdetails cd
                          LEFT JOIN vtiger_contactscf cf ON cd.contactid = cf.contactid 
                          LEFT JOIN vtiger_crmentity e ON e.crmid = cd.contactid
                          WHERE ssn='{$ssn}' AND e.deleted = 0";
//                echo "QUERY: {$query}<br />";
		$result = $adb->pquery($query, array());
		return $result;//Return all personal information from the two tables
	}
}
?>