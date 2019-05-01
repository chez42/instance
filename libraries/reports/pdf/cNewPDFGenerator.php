<?php
require_once ('libraries/mpdf/mpdf.php');
#include_once("modules/PortfolioInformation/PortfolioInformation.php");

class cNewPDFGenerator extends MPDF{
    var $pdf_pie = array();
    var $pdf_bar = array();
    var $pdf_line = array();
    var $colors = array("#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec");
    var $user_name;
    var $account_name;
    var $nickname;
    var $portfolio_name;
    var $contact_name;
    var $logo;
    var $stylesheet;
    var $html;

    public function __construct($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=0, $orientation='P') {
        parent::mPDF($mode,$format,$default_font_size,$default_font,$mgl,$mgr,$mgt,$mgb,$mgh,$mgf,$orientation);
    }

    public function SetupPDF($record='', $module='', $accountname='', $userid=0)
    {
        if($record && $module == "Accounts")
            $values = getRecordValues(array($record), 'Accounts');
        else
            if($record && $module == "Contacts")
                $values = getRecordValues(array($record), "Contacts");

        $all_values=$values[0];
        if($all_values)
            foreach($all_values AS $k => $v)
                if($v['Portfolio Name'])
                    $this->portfolio_name = $v['Portfolio Name'][0]['disp_value'];

        if(strlen($this->portfolio_name) <= 0)
            $this->portfolio_name = $accountname;

        $this->account_name = $accountname;//$accountname is set in overview.php or whatever the 'main' file that brings us to this file is
        $this->contact_name = getContactName($record);
        $this->user_name = GetUserFirstLastNameByID($userid, true);//Reverse so it is first name then Last name
        $this->setAutoBottomMargin = 'stretch';
    }

    public function SetupHeader($logo='', $text, $inception='')
    {
        $date = date("m/d/Y");
        $viewer = new Vtiger_Viewer();
        $viewer->assign("DATE", $date);
        $viewer->assign("TEXT", $text);
        $viewer->assign("LOGO", $logo);
        $viewer->assign("INCEPTION", $inception);
        $header = $viewer->view("pdf/header.tpl", "PortfolioInformation", true);

        $this->logo = $logo;
        $this->SetHTMLHeader($header);
    }

    public function SetupFooter()
    {
        $viewer = new Vtiger_Viewer();
        $viewer->assign("PAGENO", "{PAGENO}");
        $viewer->assign("NB", "{nb}");
		$viewer->assign("LOGO", $this->logo);  // Omniver : 2016-12-07 changes for User Logo //
		
        $footer = $viewer->view("pdf/footer.tpl", "PortfolioInformation", true);
//        $footer = $viewer->view("layouts/vlayout/modules/PortfolioInformation/pdf/footer.tpl", "PortfolioInformation", true);
        $this->SetHTMLFooter ($footer);
        $this->defaultfooterline=0;

        $this->SetTextColor(44,44,44);
        $this->AliasNbPages();
        $trigger = $this->PageBreakTrigger;

        $this->AddPage();
        $this->SetFont('Arial','',10);

        $keep_table_proportions = TRUE;
    }

    public function WritePDF($stylesheetfile, $content)
    {
        $this->stylesheet = $stylesheetfile;//file_get_contents($stylesheetfile);
        $this->html = $content;
        $this->WriteHTML($this->stylesheet,1);
        $this->WriteHTML($this->html);
    }

    public function DownloadPDF($filename)
    {
        $this->Output($filename, 'D');
    }

    static public function CreateImageFile($filename, $data){
        $image = urldecode($data);
        if(strlen($image) > 0){
            list($type, $image) = explode(';', $image);
            list(, $image)      = explode(',', $image);
            $image = base64_decode($image);
            file_put_contents($filename, $image);
        }
    }

    static public function TextToImage($data){
        $image = urldecode($data);
        if(strlen($image) > 0){
            list($type, $image) = explode(';', $image);
            list(, $image)      = explode(',', $image);
            $image = base64_decode($image);
            return $image;
        }
    }
}

?>
