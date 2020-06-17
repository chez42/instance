<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_ExportTimeSheet_Action extends Vtiger_Action_Controller {
    
    function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if(!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'Export')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }
    
    /**
     * Function is called by the controller
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request) {
        $this->ExportData($request);
    }
    
    /**
     * Function exports the data based on the mode
     * @param Vtiger_Request $request
     */
    function ExportData(Vtiger_Request $request) {
        
        $db = PearDatabase::getInstance();
        
        $moduleName = $request->getModule();
        
        $records = $this->getRecordsListFromRequest($request);
        
        $query = $this->getExportQuery($records);
        
        $result = $db->pquery($query, array($records));
        
        $translatedHeaders = $this->getHeaders();
        
        $entries = array();
        for ($j = 0; $j < $db->num_rows($result); $j++) {
            $entries[$j]["ticket_no"] =  $db->query_result($result, $j, "ticket_no");
            $entries[$j]["category"] = $db->query_result($result, $j, "category");
            
           // $entries[$j]["relatedto"] = Vtiger_Functions::getCRMRecordLabel($db->query_result($result, $j, "relatedto"));
            $entries[$j]["description"] = html_entity_decode($db->query_result($result, $j, "description"), ENT_QUOTES);
            
            $entries[$j]["smownerid"] = getUserFullName($db->query_result($result, $j, "smownerid"));
            
            $entries[$j]["ticket_status"] = $db->query_result($result, $j, "ticket_status");
            
            $entries[$j]["ticket_status_modified_by"] = $db->query_result($result, $j, "ticket_status_modified_by");
            
            $entries[$j]["timestart"] = $db->query_result($result, $j, "timestart");
            $entries[$j]["timeend"] = $db->query_result($result, $j, "timeend");
            
            $entries[$j]["totaltime"] = $db->query_result($result, $j, "totaltime");
            $entries[$j]["parent_id"] = Vtiger_Functions::getCRMRecordLabel($db->query_result($result, $j, "parent_id"));
            
            $entries[$j]["cf_3643"] = $db->query_result($result, $j, "cf_3643");
            $entries[$j]["cf_3652"] = $db->query_result($result, $j, "cf_3652");
            
        }
        
        $this->output($request, $translatedHeaders, $entries);
    }
    
    public function getHeaders() {
        //'Related Record',
        $relatedColumnFields = array('Ticket Number', 'Category', 'Description', 'Assigned To', 'Status', 'QA by', 'Timer - Start Time', 'Timer - End Time', 'Total Time', 'Client', 'Subcategory', 'Rating');
        $translatedHeaders = array_map('decode_html', $relatedColumnFields);
        return $translatedHeaders;
    }
    
    
    /**
     * Function that generates Export Query based on the mode
     * @param Vtiger_Request $request
     * @return <String> export query
     */
    function getExportQuery($records) {
        
        $listQuery = "SELECT 
        	vtiger_troubletickets.ticket_no, 
            vtiger_troubletickets.category,
            ticcrm.description,
            ticcrm.smownerid,
            vtiger_timecontrol.ticket_status,
            vtiger_timecontrol.ticket_status_modified_by,
            concat(vtiger_timecontrol.date_start, ' ',vtiger_timecontrol.time_start) as timestart,
            concat(vtiger_timecontrol.date_end,' ',vtiger_timecontrol.time_end) as timeend,
            vtiger_timecontrol.totaltime,
            vtiger_troubletickets.parent_id,
			vtiger_ticketcf.cf_3643,
            vtiger_ticketcf.cf_3652
        FROM vtiger_timecontrol
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_timecontrol.timecontrolid
        AND vtiger_crmentity.deleted = 0
        INNER JOIN vtiger_troubletickets ON vtiger_troubletickets.ticketid = vtiger_timecontrol.relatedto
		INNER JOIN vtiger_ticketcf ON vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
        INNER JOIN vtiger_crmentity as ticcrm ON ticcrm.crmid = vtiger_troubletickets.ticketid
        AND ticcrm.deleted = 0
        WHERE vtiger_timecontrol.relatedto IN (".generateQuestionMarks($records).")";
        // vtiger_timecontrol.relatedto,
        return $listQuery;
    }
    
    /**
     * Function returns the export type - This can be extended to support different file exports
     * @param Vtiger_Request $request
     * @return <String>
     */
    function getExportContentType(Vtiger_Request $request) {
        $type = $request->get('export_type');
        if(empty($type)) {
            return 'text/csv';
        }
    }
    
    /**
     * Function that create the exported file
     * @param Vtiger_Request $request
     * @param <Array> $headers - output file header
     * @param <Array> $entries - outfput file data
     */
    function output($request, $headers, $entries) {
        $moduleName = $request->get('source_module');
        $fileName = str_replace(' ','_',decode_html(vtranslate('ExportTimeSheet', $moduleName)));
        // for content disposition header comma should not be there in filename
        $fileName = str_replace(',', '_', $fileName);
        $exportType = $this->getExportContentType($request);
        
        header("Content-Disposition:attachment;filename=$fileName.csv");
        header("Content-Type:$exportType;charset=UTF-8");
        header("Expires: Mon, 31 Dec 2000 00:00:00 GMT" );
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
        header("Cache-Control: post-check=0, pre-check=0", false );
        
        $header = implode("\", \"", $headers);
        $header = "\"" .$header;
        $header .= "\"\r\n";
        echo str_replace('"', '', $header);
        
        foreach($entries as $row) {
            foreach ($row as $key => $value) {
                /* To support double quotations in CSV format
                 * To review: http://creativyst.com/Doc/Articles/CSV/CSV01.htm#EmbedBRs
                 */
                $row[$key] = str_replace('"', '""', $value);
            }
            $line = implode("\",\"",$row);
            $line = "\"" .$line;
            $line .= "\"\r\n";
            echo $line;
        }
    }
    
    function getRecordsListFromRequest(Vtiger_Request $request) {
        $cvId = $request->get('viewname');
        $module = $request->get('module');
        if(!empty($cvId) && $cvId=="undefined"){
            $sourceModule = $request->get('sourceModule');
            $cvId = CustomView_Record_Model::getAllFilterByModule($sourceModule)->getId();
        }
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        
        if(!empty($selectedIds) && $selectedIds != 'all') {
            if(!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        if($customViewModel) {
            $searchKey = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator = $request->get('operator');
            if(!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }
            
            $customViewModel->set('search_params',$request->get('search_params'));
            return $customViewModel->getRecordIds($excludedIds,$module);
        }
    }
    
}