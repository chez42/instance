<?php
class OmniThemeManager {
	
	var $layoutinfo;
	
	public function __construct(){
		$this->layoutinfo = array(
            	"name"=>"rainbow",
            	"label"=>"Rainbow",
            	"version"=>"1.0.0",
            	"author"=>"MakeYourCloud", 
            	"zipname"=>"MYC_RAINBOW_SRC.zip",
            	"mycpid"=>83541,
        );
    }
	
	function vtlib_handler($module_name, $event_type){
	
		$module = Vtiger_Module::getInstance($module_name);
	
		if($event_type == 'module.postinstall')
		{
			$this->setupUpdateDb();
			
		}
		else if($event_type == 'module.disabled')
		{
		}
		else if($event_type == 'module.enabled')
		{
			$this->setupUpdateDb();
		}
		else if($event_type == 'module.preuninstall')
		{
			$this->restoreOriginalFiles();
		}
		else if($event_type == 'module.preupdate')
		{
			// TODO Handle actions before this module is updated.
		}
		else if($event_type == 'module.postupdate')
		{
			
			$this->setupUpdateDb();
           
		}
	}
	
	
	public function setupUpdateDb(){
			global $adb;
			
			$adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_mycthemeswitcher_userlayouts (
                    userid INT(10) NOT NULL,
                    layoutuid TEXT NOT NULL,
                    PRIMARY KEY (userid)
                  ) ENGINE=InnoDB;",array());
            
                  
            $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_mycthemeswitcher_layouts (
                    layoutuid TEXT NOT NULL,
                    layoutinfo TEXT NOT NULL
                  ) ENGINE=InnoDB;",array());
             
            if(function_exists('mcrypt_encrypt')) {
             
	            $newLayoutInfo = $this->layoutinfo;           
	           
	            $ec1=$newLayoutInfo['name'];
	            
	            $ec2= json_encode($newLayoutInfo,JSON_FORCE_OBJECT);
	            
				$adb->pquery("DELETE FROM vtiger_mycthemeswitcher_layouts WHERE layoutuid = ?;",array($ec1)); 
				$adb->pquery("INSERT INTO vtiger_mycthemeswitcher_layouts (layoutuid,layoutinfo) VALUES (?,?);",array($ec1,$ec2)); 

			}
			
	}      
    
       
   
}