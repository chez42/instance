<?php
class cWeight{
    private $account_number;
    private $contact_id;
    private $household_id;

    public function __construct($account_number){
        global $adb;
        $this->account_number = $account_number;

        $query = "SELECT contact_link FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($this->account_number));
        if($adb->num_rows($result) > 0)
            $this->contact_id = $adb->query_result($result, 0, 'contact_link');

        $query = "SELECT household_account FROM vtiger_portfolioinformation WHERE account_number = ?";
        $result = $adb->pquery($query, array($this->account_number));
        if($adb->num_rows($result) > 0)
            $this->household_id = $adb->query_result($result, 0, 'household_account');
    }

    public function UpdatePortfolioWeight(){
        global $adb;
        $query = "UPDATE vtiger_portfolioinformation por
                  JOIN vtiger_portfolioinformationcf porcf USING (portfolioinformationid)
                  JOIN vtiger_positioninformation pos ON pos.account_number = por.account_number
                  SET pos.weight = pos.current_value / por.total_value * 100
                  WHERE por.account_number = ?";
        $adb->pquery($query, array($this->account_number));
    }

    /**
     * GET AND SET CONTACT VALUES
     * @throws Exception
     */
    public function UpdateContactWeightAndValue(){
        global $adb;

        $query = "SELECT SUM(total_value) AS val FROM vtiger_portfolioinformation WHERE contact_link = ?";
        $result = $adb->pquery($query, array($this->contact_id));
        if($adb->num_rows($result) > 0){
            $contact_total = $adb->query_result($result, 0, 'val');

            /**Update the contact total value for all portfolios belonging to it**/
            $query = "UPDATE vtiger_contactscf cf
	                  SET cf.contact_total = ?
	                  WHERE cf.contactid = ?";
            $adb->pquery($query, array($contact_total, $this->contact_id));

            $query = "UPDATE vtiger_positioninformation pos
	                  JOIN vtiger_positioninformationcf poscf USING (positioninformationid)
	                  SET poscf.contact_weight = pos.current_value / ? * 100
	                  WHERE pos.account_number = ?";
            $adb->pquery($query, array($contact_total, $this->account_number));
        }
    }

    /**
     * GET AND SET HOUSEHOLD VALUES
     */
    public function UpdateHouseholdWeightAndValue(){
        global $adb;
        $query = "SELECT SUM(total_value) AS val
                  FROM vtiger_portfolioinformation p 
                  WHERE p.household_account = ?";
        $result = $adb->pquery($query, array($this->household_id));

        if($adb->num_rows($result) > 0){
            $household_total = $adb->query_result($result, 0, 'val');

            /**Update the household total value for all portfolios belonging to it**/
            $query = "UPDATE vtiger_accountscf cf 
                      SET cf.household_total = ?
                      WHERE cf.accountid = ?";
            $adb->pquery($query, array($household_total, $this->household_id));

            $query = "UPDATE vtiger_positioninformation pos
	                  JOIN vtiger_positioninformationcf poscf USING (positioninformationid)
	                  SET poscf.household_weight = pos.current_value / ? * 100
	                  WHERE pos.account_number = ?";
            $adb->pquery($query, array($household_total, $this->account_number));
        }
    }
}