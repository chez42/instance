<?php

class Documents_GenerateDocumentLink_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $records = $request->get('record');
        $html = '';
        $trackURL='';
        foreach($records as $key=>$record){
            
            $options = array(
                'handler_path' => 'modules/Documents/handlers/DocumentViewer.php',
                'handler_class' => 'Documents_DocumentViewer_Handler',
                'handler_function' => 'documentview',
                'handler_data' => array(
                    'documentId' => $record,
                )
            );
            
            $trackURL = Vtiger_ShortURL_Helper::generateURL($options);
            
            $documentName = Vtiger_Functions::getCRMRecordLabel($record);
            
            $html .= "<a href='".$trackURL."'>".$documentName."</a>";
            $html.='<br>';
           
        }
        $result = array('html'=>$html,'url'=>$trackURL);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
    
}