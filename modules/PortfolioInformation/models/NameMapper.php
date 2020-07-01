<?php

class NameMapper{
    public function __construct(){

    }

    public function RenamePortfoliosBasedOnLinkedContact(array $account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        $params = array();
        $params[] = $account_numbers;
        $params[] = "System Generated";

        $query = "UPDATE vtiger_contactdetails cd 
                  JOIN vtiger_portfolioinformation p ON p.contact_link = cd.contactid
                  SET p.first_name = cd.firstname, p.last_name = cd.lastname
                  WHERE p.account_number IN ({$questions})
                  AND p.first_name = ?";
        $adb->pquery($query, $params, true);
    }
}