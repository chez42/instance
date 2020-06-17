<?php
class OmniThemeManager_List_View extends Vtiger_Index_View {
	public function process(Vtiger_Request $request) {
        		
		global $adb;  
		
		$viewer = $this->getViewer($request);
		
		$checkPass = $this->checkMYCSetupRequirements($request);
		
		if(!$checkPass) return true;
        		
		$avlayouts=$this->getAvailableLayouts();
		
		$avlayouts[]=array(
        	"name"=>"vlayout",
        	"label"=>"Default",
        	"version"=>"-",
        	"author"=>"vTiger", 
        	"layoutuid"=>"default",
        );        	
        
        $currentuserid=$_SESSION['authenticated_user_id'];	
		$userlayout = $adb->pquery("SELECT * FROM vtiger_mycthemeswitcher_userlayouts WHERE userid=?;",array($currentuserid));
		$prefCount =  $adb->num_rows($userlayout);
						
		if(!isset($prefCount) || $prefCount==0) $curr_layout="default";	
		else{ 
			$sel_layout = $adb->query_result_rowdata($userlayout, 0); 
			$curr_layout=$sel_layout['layoutuid'];
		}


				$focedlayout = $adb->pquery("SELECT * FROM vtiger_mycthemeswitcher_userlayouts WHERE userid=-1;",array());
				$focedCount =  $adb->num_rows($focedlayout);
								
				if(!isset($focedCount) || $focedCount==0) $viewer->assign('FORCED_LAYOUTUID', false);	
				else{ 
					$f_layout = $adb->query_result_rowdata($focedlayout, 0); 
					$viewer->assign('FORCED_LAYOUTUID', $f_layout['layoutuid']);	
				}

	            $viewer->assign('SELECTED_LAYOUTUID', $curr_layout);	
                $viewer->assign('AVAILABLE_LAYOUTS', $avlayouts);                
                $viewer->view('List.tpl', $request->getModule());
        }
        
        public function checkMYCSetupRequirements($request){
	        
	        global $root_directory;
	        
	        $mode = "";
	        $mode = $request->get("mode");
	        
			//CHECK FOR PHP-ZIP
			$check["php_zip"] = (class_exists('ZipArchive') ? true : false);
			
	        //CHECK FOR FILES AND FOLDERS PERMISSION
			$dir_files_list = array(
				"includes/runtime/Viewer.php",
				"includes/runtime/Controller.php",
				"includes/runtime/JavaScript.php",
				"includes/runtime/Theme.php",
				"modules/CustomView/views/EditAjax.php",
				"layouts",	
			);
			
			$check["dir_files_permissions"]=array();
			$permission_error = false;
			
			if($mode=="finalize"){
				
				require_once("modules/OmniThemeManager/OmniThemeManager.php");
				$mts = new OmniThemeManager();
				
				$mts->setupUpdateDb();
				
				header("Location: index.php?module=OmniThemeManager&view=List");
				return true;
			}
			
			else return true;
        }
        
        public function getAvailableLayouts(){
	        global $adb;
	        
	        $modVersionRes = $adb->pquery("SELECT version FROM vtiger_tab WHERE name='OmniThemeManager';",array());
	        $modVersion = $adb->query_result_rowdata($modVersionRes, 0);
			$result = $adb->pquery("SELECT * FROM vtiger_mycthemeswitcher_layouts;",array());
			$rowCount =  $adb->num_rows($result);
			
			$layouts = array();	
			
	        for($i=0; $i<$rowCount; $i++) {
	            $layout = $adb->query_result_rowdata($result, $i);
	            
	            $ekey = html_entity_decode($layout['layoutinfo'], ENT_QUOTES, "UTF-8");
	            
	            $layoutinfo = json_decode($ekey,JSON_FORCE_OBJECT);
	            
	            if(isset($layoutinfo) && is_array($layoutinfo) && count($layoutinfo)>0){	            	
	            	$layoutinfo['layoutuid']=$layout['layoutuid'];
	            	$layouts[$layout['layoutuid']]=$layoutinfo;    
	            }
	            
	                  
	            
	        }
	        
	        return $layouts;
        }
        
         
 }