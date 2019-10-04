<?php
class CustomStyler {
     
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($moduleName, $eventType) {
        
        $adb = PearDatabase::getInstance();
        
        if($eventType == 'module.postinstall') {
            $this->addLinks($adb);
            $this->customStylerTables($adb);
            $this->defaultEntries($adb);
            
        } else if($eventType == 'module.disabled') {
            
            $this->removeLinks($adb);
            
        } else if($eventType == 'module.enabled') {
            
            $this->addLinks($adb);
            $this->customStylerTables($adb);
            $this->defaultEntries($adb);
            
        }
        else if($eventType == 'module.preuninstall') {
            
            $this->removeLinks($adb);
            
        }
        else if($eventType == 'module.preupdate') {}
        else if($eventType == 'module.postupdate') {}
    }
    
    function removeLinks($adb) {
        
		$tab_id = 0;
		
        $linkurl = 'modules/CustomStyler/js/tinycolor-0.9.15.min.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'CustomStyler', $linkurl);
        }
        
        $linkurl = 'modules/CustomStyler/js/pick-a-color.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'CustomStyler', $linkurl);
        }
        
        $linkurl = 'modules/CustomStyler/js/MYCStyler.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'HEADERSCRIPT', 'CustomStyler', $linkurl);
        }
        
        $linkurl = 'modules/CustomStyler/css/pick-a-color-1.2.3.min.css';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result)){
            Vtiger_Link::deleteLink($tab_id, 'HEADERCSS', 'CustomStylerCSS', $linkurl);
        }
        
    }
    
    function addLinks($adb) {
        
        $tab_id =0;
        
        $linkurl = 'modules/CustomStyler/js/tinycolor-0.9.15.min.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'CustomStyler', $linkurl, '', '0', '', '', '');
        }
		
        $linkurl = 'modules/CustomStyler/js/pick-a-color.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'CustomStyler',
                $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'modules/CustomStyler/js/MYCStyler.js';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'HEADERSCRIPT', 'CustomStyler',
                $linkurl, '', '0', '', '', '');
        }
        
        $linkurl = 'modules/CustomStyler/css/pick-a-color-1.2.3.min.css';
        $result = $adb->pquery("select * from vtiger_links where linkurl = ?",array($linkurl));
        if($adb->num_rows($result) < 1){
            Vtiger_Link::addLink($tab_id, 'HEADERCSS', 'CustomStylerCSS',
                $linkurl, '', '0', '', '', '');
        }
    
	}
	
	function customStylerTables($adb){
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_customstyler (
        stylerid INT(19) NOT NULL AUTO_INCREMENT ,
        theme_name VARCHAR(200) NULL ,
        font_name VARCHAR(200) NULL ,
        font_zoom VARCHAR(200) NULL ,
        topbar_color VARCHAR(200) NULL ,
        topbar_font_color VARCHAR(200) NULL ,
        menu_style VARCHAR(200) NULL ,
        menu_color VARCHAR(200) NULL ,
        menu_font_color VARCHAR(200) NULL ,
        container_color VARCHAR(200) NULL ,
        isapplied VARCHAR(200) NULL ,
        border_radius VARCHAR(200) NULL ,
        field_labels_color VARCHAR(200) NULL,
        field_labels_font_color VARCHAR(200) NULL,
        field_value_color VARCHAR(200) NULL,
        field_value_font_color VARCHAR(200) NULL,
        field_border_color VARCHAR(200) NULL,
        owner INT(19) NULL,
        type VARCHAR(200) NULL ,
        custom VARCHAR(200) NULL ,
        PRIMARY KEY (stylerid));");
	    
	    $adb->pquery("CREATE TABLE IF NOT EXISTS vtiger_customstyler_current_user_style (
        style INT(11) NOT NULL ,
        userid INT(11) NOT NULL );");
	    
	}
	
	function defaultEntries($adb){
	    
	    $styles = array(
	        "default" => array(
	            "stylerid" => 1,
	            "theme-name" => "Default"
	        ),
	        "style-myc-1" => array(
	            "stylerid" => 2,
	            "theme-name" => "Style #1",
	            "font-name" => "Roboto Condensed",
	            "font-zoom" => "0",
	            "topbar-color" => "#f5f5f5",
	            "menu-color" => "#ffffff",
	            "container-color" => "#fafafa",
	            "isApplied" => false,
	            "border-radius" => "0",
	            "topbar-font-color" => "#6b6b6b",
	            "menu-font-color" => "#6b6b6b",
	            "menu-active-font-color"=> "#000000",
	            "custom"=> "#3f3f3f"
	        ),
	        "style-myc-2" => array(
	            "stylerid" => 3,
	            "theme-name" => "Style #2",
	            "font-name" => "Roboto Condensed",
	            "font-zoom" => "0",
	            "topbar-color" => "#262626",
	            "topbar-font-color" => "#ffffff",
	            "menu-style" => "top-menu-dropdown",
	            "menu-color" => "#000000",
	            "menu-font-color" => "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "container-color" => "#383838",
	            "border-radius" => "5",
	            "isApplied" => true,
	            "custom"=> "#ffffff"
	        ),
	        "style-myc-3" => array(
	            "stylerid" => 4,
	            "theme-name" => "Style #3",
	            "font-name" => "Roboto Condensed",
	            "font-zoom" => "0",
	            "topbar-color" => "#090d2b",
	            "topbar-font-color" => "#ffffff",
	            "menu-style" => "sidebar-menu",
	            "menu-color" => "#d0d4db",
	            "menu-font-color" => "#2f2f2f",
	            "menu-active-font-color"=> "#000000",
	            "container-color" => "#FFFFFF",
	            "border-radius" => "10",
	            "isApplied"=> true,
	            "custom"=> "#404040"
	        ),
	        "style-myc-4"=> array(
	            "stylerid" => 5,
	            "theme-name"=> "Style #4",
	            "font-name"=> "Rubik",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#3572b6",
	            "topbar-font-color"=> "#ffffff",
	            "menu-style"=> "top-menu-dropdown",
	            "menu-color"=> "#26a846",
	            "menu-font-color"=> "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "container-color"=> "#ffffdb",
	            "border-radius"=> "25",
	            "isApplied"=> true,
	            "custom"=> "#404037"
	        ),
	        "style-myc-5" => array(
	            "stylerid" => 6,
	            "theme-name"=> "Style #5",
	            "font-name"=> "Roboto Condensed",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#FFFFFF",
	            "topbar-font-color"=> "#000000",
	            "menu-style"=> "sidebar-menu",
	            "menu-color"=> "#323c48",
	            "menu-font-color"=> "#fcfcfc",
	            "menu-active-font-color"=> "#000000",
	            "container-color"=> "#FFFFFF",
	            "border-radius"=> "5",
	            "isApplied"=> true,
	            "custom"=> "#404040"
	        ),
	        "style-myc-6" => array(
	            "stylerid" => 7,
	            "theme-name"=> "Style #6",
	            "font-name"=> "Oswald",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#300032",
	            "menu-color"=> "#c43235",
	            "container-color"=> "#e6e6e8",
	            "isApplied"=> false,
	            "border-radius"=> "5",
	            "menu-font-color"=> "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "topbar-font-color"=> "#ffffff",
	            "custom"=> "#3a3a3a"
	        ),
	        "style-myc-7" => array(
	            "stylerid" => 8,
	            "theme-name" => "Style #7",
	            "font-name"=> "Montserrat",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#ffffff",
	            "menu-color"=> "#594d4d",
	            "container-color"=> "#f2f2f2",
	            "isApplied"=> false,
	            "border-radius"=> "25",
	            "menu-font-color"=> "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "topbar-font-color"=> "#828282",
	            "custom"=> "#3d3d3d"
	        ),
	        "style-myc-8" => array(
	            "stylerid" => 9,
	            "theme-name"=> "Style #8",
	            "font-name"=> "Work Sans",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#215ca0",
	            "topbar-font-color"=> "#ffffff",
	            "menu-color"=> "#032e61",
	            "menu-font-color"=> "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "container-color"=> "#f4f4f4",
	            "isApplied"=> true,
	            "border-radius"=> "5",
	            "menu-style"=> "sidebar-menu",
	            "custom"=> "#3d3d3d"
	        ),
	        "style-myc-9" => array(
	            "stylerid" => 10,
	            "theme-name"=> "Style #9",
	            "font-name"=> "Lato",
	            "font-zoom"=> "0",
	            "topbar-color"=> "#5c566b",
	            "topbar-font-color"=> "#ffffff",
	            "menu-color"=> "#dc6254",
	            "menu-font-color"=> "#ffffff",
	            "menu-active-font-color"=> "#000000",
	            "container-color"=> "#f5f5f5",
	            "isApplied"=> true,
	            "custom"=> "#3d3d3d"
	        ),
	    );
	    
	    $colName = '';
	    $val = '';
	    $upval = '';
	    foreach($styles as $key => $style){
	        $count = count($style);
	        $n = 0;
	        foreach($style as $stylecol => $colVal){
	            if($n>0 && $n<$count){
	                $colName.=', ';
	                $val .= ', ';
	                $upval .= ', ';
	            }
	            
	            $colName .= strtolower(str_replace('-','_',$stylecol));
	            $val .= '"'.$colVal.'"';
	            $upval .= strtolower(str_replace('-','_',$stylecol)) .' = '. '"'.$colVal.'"';
	            $n++;
	            
	        }
	        $columns = $colName;
	        $allVal = $val;
	        $updateVal = $upval;
	        
	        unset($colName);
	        unset($val);
	        unset($upval);
	        
	        $adb->pquery('INSERT INTO vtiger_customstyler('.$columns.', type) VALUES ('.$allVal.', "default")
            ON DUPLICATE KEY UPDATE '.$updateVal.'');
	    }
	    
	}
    
}