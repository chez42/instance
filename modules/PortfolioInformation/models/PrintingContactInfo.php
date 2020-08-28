<?php


class PrintingContactInfo{
    protected $logo, $title, $prepared_for, $prepared_by, $prepared_date;
    protected $record;
    public function __construct($record){
        $current_user = Users_Record_Model::getCurrentUserModel();
        $this->logo = PortfolioInformation_Module_Model::GetLogo();//Set the logo
        $this->prepared_for = PortfolioInformation_Module_Model::GetPreparedForNameByRecordID($record);
#        $prepared_by = PortfolioInformation_Module_Model::GetPreparedByFormattedByRecordID($record);//Returns a string from the database already formatted
        $prepared_by = PortfolioInformation_Module_Model::GetPreparedByFormattedByUserID($current_user->get("id"));

        if(empty($prepared_by) || strlen(trim($prepared_by)) == 0){
            $this->prepared_by = array("pre_formatted" => 0, "content" => $current_user->get("first_name") . " " . $current_user->get("last_name"));//Need to fill in information due to the fact nothing has been setup.  Prepared by logged in user presumably?
        }else{
            $this->prepared_by = array("pre_formatted" => 1, "content" => $prepared_by);
        }
        $this->record = $record;
        $this->prepared_date = Date("F j, Y");
    }

    public function GetLogo(){return $this->logo;}
    public function GetTitle(){return $this->title;}
    public function GetPreparedFor(){return $this->prepared_for;}
    public function GetPreparedBy(){return $this->prepared_by;}
    public function SetTitle($title){$this->title = $title;}
    public function GetPreparedDate(){return $this->prepared_date;}
    public function SetLogo($logo){$this->logo = $logo;}
}

/**
 * Class FormattedContactInfo
 * This class returns the formatted template files for each given block section.  Logo, Prepared For, etc.  The parent class is able to return the
 * raw data for further configuration options
 */
class FormattedContactInfo extends PrintingContactInfo{
    protected $viewer;

    public function __construct($record){
        parent::__construct($record);
        $this->viewer = new Vtiger_Viewer();
    }

    public function GetFormattedLogo(){
        $this->viewer->assign("LOGO", $this->logo);
        $output = $this->viewer->view('Reports/logo.tpl', 'PortfolioInformation', true);
        return $output;
    }

    public function GetFormattedPreparedFor(){
        $this->viewer->assign("PREPARED_FOR", $this->prepared_for);
        $output = $this->viewer->view('Reports/prepared_for.tpl', 'PortfolioInformation', true);
        return $output;
    }

    public function GetFormattedPreparedBy(){
        $this->viewer->assign("PREPARED_BY", $this->prepared_by);
        $output = $this->viewer->view('Reports/prepared_by.tpl', 'PortfolioInformation', true);
        return $output;
    }

    public function GetFormattedTitle(){
        $this->viewer->assign("REPORT_TITLE", $this->title);
        $output = $this->viewer->view('Reports/report_title.tpl', 'PortfolioInformation', true);
        return $output;
    }

    public function GetFormattedPreparedDate(){
        $this->viewer->assign("PREPARED_DATE", $this->prepared_date);
        $output = $this->viewer->view('Reports/prepared_date.tpl', 'PortfolioInformation', true);
        return $output;
    }

}