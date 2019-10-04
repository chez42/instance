<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_GetFilters_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod("getFilters");
    }
    
    public function checkPermission(Vtiger_Request $request)
    {
    }
    
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get("mode");
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    
    public function getFilters(Vtiger_Request $request){
     
        $filterModule = $request->get('filterModule');
        $filters = array();
        if($filterModule){
            $filters[] = CustomView_Record_Model::getAll($filterModule);
        }
        $data = array();
       
        foreach($filters as $filter){
            foreach($filter as $key => $filterData){
                $count = VTEEmailMarketing_Record_Model::getCountRecordFilter($filterData->get('cvid'), $filterModule);
                
                $userName ='';
                if($filterData->get('userid'))
                    $userName = getUserFullName($filterData->get('userid'));
                
                $data[] = array(
                    "cvid" => $filterData->get('cvid'),
                    "name_filter" => vtranslate($filterData->get('viewname'), $filterModule),
                    "module" => vtranslate($filterData->get('entitytype'),$filterData->get('entitytype')),
                    "name" => $userName,
                    "count" => $count,
                );
                
            }
        }
       
        $response = new Vtiger_Response();
        $response->setResult($data);
        $response->emit();
       
    }
   
}

?>