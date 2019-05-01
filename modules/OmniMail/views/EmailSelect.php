<?php
class OmniMail_EmailSelect_View extends Vtiger_BasicAjax_View{
    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $viewer->view('EmailSelect.tpl', "OmniMail", false);
    }
}
?>