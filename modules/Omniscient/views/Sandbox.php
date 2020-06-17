<?php

class Omniscient_Sandbox_View extends Vtiger_Index_View{
    public function process(Vtiger_Request $request) {
/*        $viewer = $this->getViewer($request);
        $viewer->assign("SCRIPTS", $this->getCustomScripts($request));
        
        $viewer->view("Index.tpl", $request->getModule());*/
        include_once("include/utils/cron/cTransactionsAccess.php");
        ini_set('memory_limit', '2048M');
        $t = new cTransactionsAccess(true);
        $t->CopyAllTransactionIDsFromPCToCRM();
    }
}

?>