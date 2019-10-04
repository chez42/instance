<?php
/***********************************************************************************
 * Copyright 2014 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM TimeControl extension.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';
require_once('autoload_wf.php');

class Timecontrol extends Vtiger_CRMEntity {
	
	public static $now_on_resume=true;
	var $USE_RTE = 'true';
	var $sumup_HelpDesk = true;
	var $sumup_ProjectTask = true;
	
	var $table_name = 'vtiger_timecontrol';
	var $table_index= 'timecontrolid';

	var $customFieldTable = Array('vtiger_timecontrolcf', 'timecontrolid');
	var $related_tables = Array('vtiger_timecontrolcf'=>array('timecontrolid','vtiger_timecontrol', 'timecontrolid'));

	var $tab_name = Array('vtiger_crmentity', 'vtiger_timecontrol', 'vtiger_timecontrolcf');

	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_timecontrol' => 'timecontrolid',
		'vtiger_timecontrolcf'=>'timecontrolid');

	var $list_fields = Array (
	'Timecontrol Number' => array('timecontrol', 'timecontrolnr'),
    'Title'=> Array('timecontrol', 'title'),
    'Date Start' => array('timecontrol', 'date_start'),
    'Time Start' => array('timecontrol', 'time_start'),
    'Total Time' => array('timecontrol', 'totaltime'),
	'Description' => Array('crmentity','description'),
	'Assigned To' => Array('crmentity','smownerid')
	);
	
	var $list_fields_name = Array (
	'Timecontrol Number' => 'timecontrolnr',
    'Title'=> 'title',
    'Date Start' => 'date_start',
    'Time Start' => 'time_start',
    'Total Time' => 'totaltime',
	'Description' => 'description',
		'Assigned To' => 'assigned_user_id'
	);

	var $list_link_field = 'timecontrolnr';

	var $search_fields = Array(
		'Timecontrol Number' => array('timecontrol', 'timecontrolnr'),
		'Title'=> Array('timecontrol', 'title')
	);
	var $search_fields_name = Array (
		'Timecontrol Number' => 'timecontrolnr',
		'Title'=> 'title'
	);

	var $popup_fields = Array ('timecontrolnr');

	var $def_basicsearch_col = 'timecontrolnr';

	var $def_detailview_recname = 'title';

	var $mandatory_fields = Array('createdtime', 'modifiedtime', 'timecontrolnr', 'date_start', 'time_start');

	var $default_order_by = 'date_start';
	var $default_sort_order='DESC';

	function vtlib_handler($moduleName, $eventType) {
		global $adb;
 		if($eventType == 'module.postinstall') {

 		    $this->setModuleSeqNumber('configure', $moduleName, 'TIME-BILLING-', '000001');
 			self::addTSRelations();
 			self::setSummaryFields();
            $this->initialize_module();
            $this->AddHeaderLink();
             
		} else if($eventType == 'module.disabled') {
		    
             $this->disable_module();
             
		} else if($eventType == 'module.preuninstall') {
             
		    $this->disable_module();
		    
		} else if($eventType == 'module.preupdate') {

		} else if($eventType == 'module.postupdate') {
            
		    $this->initialize_module();
            $this->AddHeaderLink();
             
		}else if($eventType == 'module.enabled') {
		   
		    $this->initialize_module();
		    $this->AddHeaderLink();
		    
		}
 	}

	static function addTSRelations($dorel=true) {
		$Vtiger_Utils_Log = true;
		include_once('vtlib/Vtiger/Module.php');

		$module = Vtiger_Module::getInstance('Timecontrol');

		$cfgTCMods = array('HelpDesk');
		$adb = \PearDatabase::getInstance();
		$fld = Vtiger_Field::getInstance('relatedto',$module);
        foreach ($cfgTCMods as $tcmod) {
            $sql = 'SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?';
            $result = $adb->pquery($sql, array(getTabid('Timecontrol'), getTabid($tcmod)));
            if($adb->num_rows($result) == 0) {
			    $rtcModule = Vtiger_Module::getInstance($tcmod);
			    $rtcModule->setRelatedList($module, 'Timecontrol', Array('ADD'), 'get_dependents_list',$fld->id);
			    //$rtcModule->addLink('DETAILVIEWBASIC', 'Timecontrol', 'index.php?module=Timecontrol&action=EditView&relatedto=$RECORD$','modules/Timecontrol/images/stopwatch.gif');
            }
		}
	}

	static function setSummaryFields() {
		global $adb;
		$Vtiger_Utils_Log = true;
		include_once('vtlib/Vtiger/Module.php');
		$module = Vtiger_Module::getInstance('Timecontrol');
		$sumfields = array('timecontrolnr','totaltime','relatedto','date_end','time_end','date_start','time_start','title');
		foreach ($sumfields as $fldname) {
			$fld = Vtiger_Field::getInstance($fldname,$module);
			$adb->query('update vtiger_field set summaryfield=1 where fieldid='.$fld->id);
		}
	}

	function save_module($module) {
		global $log;
		$this->updateTimesheetTotalTime();
		$this->updateRelatedEntities($this->id);

        \TimeControl\ImageGeneration::generateImage($this->column_fields['assigned_user_id']);

		if (!empty($this->column_fields['relatedto'])) {
			$relmod=getSalesEntityType($this->column_fields['relatedto']);
			$seqfld=$this->getModuleSequenceField($relmod);
			$relm = CRMEntity::getInstance($relmod);
			$relm->retrieve_entity_info($this->column_fields['relatedto'], $relmod);
			$enum=$relm->column_fields[$seqfld['column']];
			$ename=getEntityName($relmod, array($this->column_fields['relatedto']));
			$ename=decode_html($ename[$this->column_fields['relatedto']]);
			$this->db->query("update vtiger_timecontrol set relatednum='$enum', relatedname='$ename' where timecontrolid=".$this->id);

		}
	}
	
	function updateTimesheetTotalTime() {
		global $adb;
		if (!empty($this->column_fields['date_end']) && !empty($this->column_fields['time_end'])) {
			$query = "select date_start, time_start, date_end, time_end from vtiger_timecontrol where timecontrolid={$this->id}";
			$res = $adb->query($query);
			$strtdate = $adb->query_result($res, 0, 'date_start');
			$strttime = $adb->query_result($res, 0, 'time_start');
			$start = new DateTime("$strtdate $strttime");
			
			$enddate = $adb->query_result($res, 0, 'date_end');
			$endtime = $adb->query_result($res, 0, 'time_end');
			$end = new DateTime("$enddate $endtime");
			
			$totaltime = new DateTime(date_diff($start, $end) -> format("%H:%i:%s"));
			$query = "update vtiger_timecontrol set totaltime='{$totaltime->format('H:i:s')}' where timecontrolid={$this->id}";
			$adb->query($query);
			
			$hour = $totaltime->format('H');
			$min =  $totaltime->format('i');
			
			$decMin = array('01'=>'.02','02'=>'.03','03'=>'.05','04'=>'.07','05'=>'.08','06'=>'.10','07'=>'.12','08'=>'.13','09'=>'.15','10'=>'.17','11'=>'.18'
						,'12'=>'.20','13'=>'.22','14'=>'.23','15'=>'.25','16'=>'.27','17'=>'.28','18'=>'.30','19'=>'.32','20'=>'.33','21'=>'.35','22'=>'.37','23'=>'.38',
						'24'=>'.40','25'=>'.42','26'=>'.43','27'=>'.45','28'=>'.47','29'=>'.48','30'=>'.50','31'=>'.52','32'=>'.53','33'=>'.55','34'=>'.57','35'=>'.58',
						'36'=>'.60','37'=>'.62','38'=>'.63','39'=>'.65','40'=>'.67','41'=>'.68','42'=>'.70','43'=>'.72','44'=>'.73','45'=>'.75','46'=>'.77','47'=>'.78',
						'48'=>'.80','49'=>'.82','50'=>'.83','51'=>'.85','52'=>'.87','53'=>'.88','54'=>'.90','55'=>'.92','56'=>'.93','57'=>'.95','58'=>'.97',
						'59'=>'.98','60'=>'.1');
			
			$decimalTime = $hour.$decMin[$min]; 
			 
			$adb->pquery("UPDATE vtiger_timecontrolcf SET cf_1170 = ? WHERE timecontrolid = ?",array($decimalTime,$this->id));
			
			self::update_totalday_control($this->id);
		}
		if (!empty($this->column_fields['totaltime']) && (empty($this->column_fields['date_end']) && empty($this->column_fields['time_end']))) {
			$totaltime = $this->column_fields['totaltime'];
			if (strpos($this->column_fields['totaltime'], ':')) { 
				$tt = explode(':', $this->column_fields['totaltime']);
				$this->column_fields['totaltime'] = $tt[0]*60+$tt[1];
			}
			$query = "select date_start, time_start, date_end, time_end from vtiger_timecontrol where timecontrolid={$this->id}";
			$res = $adb->query($query);
			$date = $adb->query_result($res, 0, 'date_start');
			$time = $adb->query_result($res, 0, 'time_start');
			list($year, $month, $day) = explode('-', $date);
			list($hour, $minute, $seconds) = explode(':', $time);
			$endtime = mktime($hour, $minute+$this->column_fields['totaltime'], $seconds, $month, $day, $year);
			$datetimefield = new DateTimeField(date('Y-m-d', $endtime));
			$this->column_fields['date_end'] = $datetimefield->getDisplayDate();
			$this->column_fields['time_end'] = date('H:i:s', $endtime);
			$query = "update vtiger_timecontrol set totaltime='{$totaltime}', date_end='".date('Y-m-d', $endtime)."', time_end='{$this->column_fields['time_end']}' where timecontrolid={$this->id}";
			$adb->query($query);
			self::update_totalday_control($this->id);
		}
		
	}
	
	public static function update_totalday_control($tcid) {
		global $adb,$log;
		if (self::totalday_control_installed()) {
			$tcdat=$adb->query("select date_start, smownerid
					from vtiger_timecontrol
					inner join vtiger_crmentity on crmid=timecontrolid
					where crmid=".$tcid);

			$workdate=$adb->query_result($tcdat,0,'date_start');
			$user    =$adb->query_result($tcdat,0,'smownerid');
			$tctot=$adb->query("select coalesce(sum(time_to_sec(totaltime))/3600,0) as totnum, coalesce(sec_to_time(sum(time_to_sec(totaltime))),0) as tottime
					from vtiger_timecontrol
					inner join vtiger_crmentity on crmid=timecontrolid
					where date_start='$workdate' and smownerid=$user and deleted=0");

			$totnum=$adb->query_result($tctot,0,'totnum');
			$tottim=$adb->query_result($tctot,0,'tottime');

			$adb->query("update vtiger_timecontrol
					inner join vtiger_crmentity on crmid=timecontrolid
					set totaldayhours=$totnum,totaldaytime='$tottim'
					where date_start='$workdate' and smownerid=$user ");
			
		}
	
	}
	
	public static function totalday_control_installed() {
		global $adb;
		$cnacc=$adb->getColumnNames('vtiger_timecontrol');
		if (in_array('totaldaytime', $cnacc)
		and in_array('totaldayhours', $cnacc)) return true;
		return false;
	}
	
	function updateRelatedEntities($tcid) {
		global $adb;
		$relid=$adb->query_result($adb->query("select relatedto from vtiger_timecontrol where timecontrolid=$tcid"),0,0);
		if (empty($relid)) return true;
		if ($this->sumup_HelpDesk and getSalesEntityType($relid)=='HelpDesk') {
			$query = "select round(sum(time_to_sec(totaltime))/3600) as stt
			from vtiger_timecontrol
			inner join vtiger_crmentity on crmid=timecontrolid
			where relatedto=$relid and deleted=0";
			$res = $adb->query($query);
			$stt = $adb->query_result($res, 0, 'stt');
			$query = "update vtiger_troubletickets set hours='$stt' where ticketid=$relid";
			$adb->query($query);
		}
		if ($this->sumup_ProjectTask and getSalesEntityType($relid)=='ProjectTask') {
			$query = "select sec_to_time(sum(time_to_sec(totaltime))) as stt
			from vtiger_timecontrol
			inner join vtiger_crmentity on crmid=timecontrolid
			where relatedto=$relid and deleted=0";
			$res = $adb->query($query);
			$stt = $adb->query_result($res, 0, 'stt');
			$query = "update vtiger_projecttask set projecttaskhours='$stt' where projecttaskid=$relid";
			$adb->query($query);
		}
	}

    public function cleanDetailViewWidgets() {
        $adb = \PearDatabase::getInstance();

		$sql = 'SELECT * FROM vtiger_links WHERE linkurl LIKE ?';
		$result = $adb->pquery($sql, array("%module=Timecontrol&view=AccountingWidget%"));

		if($adb->num_rows($result) > 1) {
			$sql = 'DELETE FROM vtiger_links WHERE linkurl LIKE ? LIMIT '.($adb->num_rows($result) - 1);
			$adb->pquery($sql, array("%module=Timecontrol&view=AccountingWidget%"), true);
		}


    }


	function trash($module,$record) {
		global $adb;
		parent::trash($module,$record);
		self::update_totalday_control($record);
		$this->updateRelatedEntities($record);

	}

	private function getModuleSequenceField($module) {
		global $adb, $log;
		$log->debug("Entering function getModuleSequenceFieldName ($module)...");
		$field = null;
		if (!empty($module)) {
			$seqColRes = $adb->pquery("SELECT fieldname, fieldlabel, columnname FROM vtiger_field WHERE uitype=? AND tabid=? and vtiger_field.presence in (0,2)", array('4', getTabid($module)));
			if($adb->num_rows($seqColRes) > 0) {
				$fieldname = $adb->query_result($seqColRes,0,'fieldname');
				$columnname = $adb->query_result($seqColRes,0,'columnname');
				$fieldlabel = $adb->query_result($seqColRes,0,'fieldlabel');
				$field = array();
				$field['name'] = $fieldname;
				$field['column'] = $columnname;
				$field['label'] = $fieldlabel;
			}
		}
		$log->debug("Exiting getModuleSequenceFieldName...");
		return $field;
	}


    function checkDB() {
        require_once(dirname(__FILE__).'/checkDB.php');
    }

    public function RemoveHeaderLink() {
   		$adb = PearDatabase::getInstance();

   		$sql = "DELETE FROM vtiger_links WHERE linktype = 'HEADERSCRIPT' AND linklabel = '".get_class($this)."JS'";
   		$adb->query($sql);

   	}
   	public function AddHeaderLink() {
   		$adb = PearDatabase::getInstance();

   		$this->removeHeaderLink();

   		$moduleModel = Vtiger_Module_Model::getInstance(get_class($this));
   		
   		require_once('vtlib/Vtiger/Module.php');
   		$link_module = Vtiger_Module::getInstance(get_class($this));
   		$link_module->addLink('HEADERSCRIPT',get_class($this)."JS","modules/".get_class($this)."/views/resources/frontend.js?v=".$moduleModel->version."&", "", "1");

        $sql = 'DELETE FROM vtiger_links WHERE linkurl LIKE ?';
		$adb->pquery($sql, array('&module=Timecontrol&view=AccountingWidget&'));

   	}

    public function ActivateEvent() {
        $adb = \PearDatabase::getInstance();

        $em = new VTEventsManager($adb);

        $em->registerHandler('vtiger.entity.aftersave', 'modules/Timecontrol/EventHandler.php', 'TimecontrolEventHandler');

        $sql = 'UPDATE vtiger_eventhandlers SET is_active = 1 WHERE handler_path = "modules/Timecontrol/EventHandler.php"';
        $adb->pquery($sql, array());
    }
    
    public function DeactivateEvent() {
        $adb = \PearDatabase::getInstance();
        $sql = 'UPDATE vtiger_eventhandlers SET is_active = 0 WHERE handler_path = "modules/Timecontrol/EventHandler.php"';
        $adb->pquery($sql, array());
    }

    public function initialize_module() {
        ob_start();
        $this->checkDB();

        $this->ActivateEvent();

        ob_end_clean();
    }

	public function disable_module() {
		$this->RemoveHeaderLink();
        $this->DeactivateEvent();
	}

	
	function get_reporting($id, $cur_tab_id, $rel_tab_id, $actions=false) {
	    
	    global $log, $singlepane_view,$currentModule,$current_user;
	    
	    $log->debug("Entering get_reporting(".$id.") method ...");
	    $this_module = $currentModule;
	    
	    $related_module = vtlib_getModuleNameById($rel_tab_id);
	    require_once("modules/$related_module/$related_module.php");
	    $other = new $related_module();
	    vtlib_setup_modulevars($related_module, $other);
	    $singular_modname = vtlib_toSingular($related_module);
	    
	    $parenttab = getParentTab();
	    
	    if($singlepane_view == 'true')
	        $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
            
            $button = '';
            
            if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'account_id','readwrite') == '0') {
                if(is_string($actions)) $actions = explode(',', strtoupper($actions));
                if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                    $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
                }
                if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                    $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
   	                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
   	                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
                }
            }
            
            global $adb;
            
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
                'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
            
            $query = "SELECT vtiger_reporting.*,
			vtiger_crmentity.crmid,
                        vtiger_crmentity.smownerid,
			case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
			FROM vtiger_reporting
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_reporting.reportingid
            INNER JOIN vtiger_timecontrol ON vtiger_timecontrol.reporting_id = vtiger_reporting.reportingid
			LEFT JOIN vtiger_groups	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 and vtiger_timecontrol.timecontrolid = '$id'";
            
            $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);
            
            if($return_value == null) $return_value = Array();
            $return_value['CUSTOM_BUTTON'] = $button;
            
            $log->debug("Exiting get_reporting method ...");
            return $return_value;
    }
	
}