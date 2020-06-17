<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_SidebarWidget_View extends Vtiger_BasicAjax_View {

    public function process(Vtiger_Request $request) {
        $current_user = $cu_model = Users_Record_Model::getCurrentUserModel();
        $currentLanguage = Vtiger_Language_Handler::getLanguage();

        $adb = PearDatabase::getInstance();
        $viewer = $this->getViewer($request);
        $module = $request->get('source_module');
        $crmid = (int)$request->get('record');

        if($module == 'Events') {
            $module = 'Calendar';
        }

        $sql = 'SELECT * FROM vtiger_timecontrol INNER JOIN vtiger_crmentity ON (crmid = timecontrolid) WHERE relatedto = ? AND smownerid = ? AND timecontrolstatus = "run"';
        $result = $adb->pquery($sql, array($crmid, $current_user->id));

        $datetimefield = new DateTimeField('');

        if($adb->num_rows($result) > 0) {
            $timer = array();
            while($row = $adb->fetchByAssoc($result)) {
                $timer[] = array(
                    'start_date' => $row['date_start'],
                    'start_time' => $row['time_start'],
                    'timestamp' => strtotime($datetimefield->convertToDBTimeZone($row['date_start'].' '.$row['time_start'])->format('Y-m-d H:i:s')),
                    'relatedname' => empty($row['relatedname'])?'':$row['relatedname'],
                    'relatedurl' => 'index.php?module=' . $row['setype'] . '&view=Detail&record=' . $row['relatedto'],
                    'title' => $row['title'],
                    'timecontrolid' => $row['timecontrolid'],
                );
            }

            $viewer->assign('timer', true);
            $viewer->assign('timers', $timer);
        } else {
            $viewer->assign('timer', false);
        }

        $viewer->view("SidebarWidget.tpl", 'Timecontrol');
    }
}