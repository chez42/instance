<?php

DEFINE("TD", "TD");
DEFINE("FIDELITY", "Fidelity");
DEFINE("SCHWAB", "Schwab");
DEFINE("PERSHING", "Pershing");

class CustodianToOmni{
    private $account_number;
    private $custodian;//used for accessing the custodian database

    public function __construct(string $account_number){
        $this->account_number = $account_number;

        switch(strtoupper(PortfolioInformation_Module_Model::GetCustodianFromAccountNumber($account_number))){
            case "TD":
                $this->custodian = TD;
                break;
            case "FIDELITY":
                $this->custodian = FIDELITY;
                break;
            case "SCHWAB":
                $this->custodian = SCHWAB;
                break;
            case "PERSHING":
                $this->custodian = PERSHING;
                break;
        }
    }

    /**
     * Pull the latest positions from Custodian Omniscient and insert them into Omniscient
     */
    public function UpdatePositions(){
        switch(strtoupper($this->custodian)){
            CASE "TD":
                $tmp = new cTDQuickAccess();
                $tmp->PullPositions(array($this->account_number));
                break;
            CASE "FIDELITY":
#                $tmp = new cFidelityQuickAccess();
#                $tmp->PullPositions(array($this->account_number));
                break;
            CASE "SCHWAB":
#                $tmp = new cSchwabQuickAccess();
#                $tmp->PullPositions(array($this->account_number));
                break;
            case "PERSHING":
#                $tmp = new cPershingQuickAccess();
#                $tmp->PullPositions(array($this->account_number));
                break;
        }
    }

    public function UpdatePortfolios(){
        switch(strtoupper($this->custodian)){
            CASE "TD":
                $tmp = new cTDQuickAccess();
                $tmp->PullPortfolios(array($this->account_number));
                break;
            CASE "FIDELITY":
                $tmp = new cFidelityQuickAccess();
                $tmp->PullPortfolios(array($this->account_number));
                break;
            CASE "SCHWAB":
#                $tmp = new cSchwabQuickAccess();
#                $tmp->PullPortfolios(array($this->account_number));
                break;
            case "PERSHING":
#                $tmp = new cPershingQuickAccess();
#                $tmp->PullPositions(array($this->account_number));
                break;
        }
    }

    public function UpdateTransactions(){
        switch(strtoupper($this->custodian)){
            CASE "TD":
                $tmp = new cTDQuickAccess();
                $tmp->PullTransactions(array($this->account_number));
                break;
            CASE "FIDELITY":
                $tmp = new cFidelityQuickAccess();
                $tmp->PullTransactions(array($this->account_number));
                break;
            CASE "SCHWAB":
                $tmp = new cSchwabQuickAccess();
                $tmp->PullTransactions(array($this->account_number));
                break;
            case "PERSHING":
#                $tmp = new cPershingQuickAccess();
#                $tmp->PullPositions(array($this->account_number));
                break;
        }
    }
}