<?php
class Reconcile{
    public $account_number, $difference, $custodian;
    public $portfolio_value, $portfolio_date;
    public $positions_value;
    public $asset_class_history_value;

    public function __construct($account_number)
    {
        $this->account_number = $account_number;
        $this->custodian = PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($account_number);
        $id = PortfolioInformation_Module_Model::GetRecordIDFromAccountNumber($account_number);
        $t = PortfolioInformation_Record_Model::getInstanceById($id);
        $this->portfolio_value = $t->get('total_value');
        $this->portfolio_date = $t->get('stated_value_date');

        $this->positions_value = PositionInformation_Module_Model::GetTotalvalueForAccountNumberUsingPositions($account_number);
        $this->asset_class_history_value = PositionInformation_Module_Model::GetAssetClassHistoryValue($this->account_number, $this->portfolio_date);
        $this->difference = $this->portfolio_value - $this->positions_value;

#        echo $this->portfolio_value . '<br />' . $this->positions_value . '<br />' . $this->asset_class_history_value;
#        exit;
    }

    public function PullLatest(){
        self::RecalculateTD();

        $tmp = new CustodianToOmni($this->account_number);
        $tmp->UpdatePortfolios();
        $tmp->UpdatePositions();
    }

    public function RecalculateTD(){
        PortfolioInformation_Module_Model::TDBalanceCalculationsAccount($this->account_number, '2012-01-01', date("Y-m-d"), true);
    }
}

/*
DROP TABLE IF EXISTS latestPortfolios;
DROP TABLE IF EXISTS latestHistory;
DROP TABLE IF EXISTS differences;

CREATE TEMPORARY TABLE latestPortfolios
SELECT account_number, total_value
FROM vtiger_portfolioinformation p
JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
WHERE stated_value_date = '2020-11-17';

CREATE TEMPORARY TABLE latestHistory
SELECT account_number, value
FROM vtiger_asset_class_history
WHERE account_number IN (
	SELECT account_number
	FROM vtiger_portfolioinformation p
	JOIN vtiger_portfolioinformationcf cf USING (portfolioinformationid)
	WHERE stated_value_date = '2020-11-17')
AND as_of_date = '2020-11-17';

CREATE TEMPORARY TABLE differences
SELECT p.account_number, p.total_value, SUM(l.value), p.total_value - SUM(l.value) AS dif
FROM latestPortfolios p
JOIN latestHistory l ON p.account_number = l.account_number
GROUP BY p.account_number;

SELECT SUM(value)
FROM vtiger_asset_class_history
WHERE as_of_date='2020-11-17';

SELECT SUM(value)
FROM vtiger_asset_class_history_daily_users
WHERE as_of_date='2020-11-17'
AND user_id = 1;

SELECT d.*, p.origination
FROM differences d
JOIN vtiger_portfolioinformation p ON p.account_number = d.account_number
WHERE dif > 10 OR dif < -10;

#SELECT * FROM custodian_omniscient.custodian_balances_fidelity WHERE account_number='638112900';
#SELECT * FROM custodian_omniscient.custodian_balances_fidelity WHERE account_number='638112900';
*/