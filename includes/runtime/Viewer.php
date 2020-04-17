<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

vimport ('~/libraries/Smarty/libs/SmartyBC.class.php');

class Vtiger_Viewer extends SmartyBC {
    
    const DEFAULTLAYOUT = 'v7';
    const DEFAULTTHEME  = 'softed';
    static $currentLayout;
    static $customLayoutPath;
    static $customLayout;
    var $tplDirs;
    
    // Turn-it on to analyze the data pushed to templates for the request.
    protected static $debugViewer = false;
    
    /**
     * log message into the file if in debug mode.
     * @param type $message
     * @param type $delimiter
     */
    protected function log($message, $delimiter="\n") {
        static $file = null;
        if ($file == null) $file = dirname(__FILE__) . '/../../logs/viewer-debug.log';
        if (self::$debugViewer) {
            file_put_contents($file, $message.$delimiter, FILE_APPEND);
        }
    }
    
    /**
     * Constructor - Sets the templateDir and compileDir for the Smarty files
     * @param <String> - $media Layout/Media name
     */
    function __construct($media='') {
        parent::__construct();
        
        $THISDIR = dirname(__FILE__);
        
        $templatesDir = '';
        $compileDir = '';
        
        
        
        if(!empty($media)) {
            self::$currentLayout = $media;
            $templatesDir = $THISDIR . '/../../layouts/'.$media;
            $compileDir = $THISDIR . '/../../test/templates_c/'.$media;
        }
        if(!$templatesDir || !file_exists($templatesDir)) {
            self::$currentLayout = self::getDefaultLayoutName();
            $templatesDir = $THISDIR . '/../../layouts/'.self::getDefaultLayoutName();
            $compileDir = $THISDIR . '/../../test/templates_c/'.self::getDefaultLayoutName();
        }
        
        if (!file_exists($compileDir)) {
            mkdir($compileDir, 0777, true);
        }
        $this->setTemplateDir(array($templatesDir));
        $this->setCompileDir($compileDir);
        
//         if(!isset($_SESSION['authenticated_user_id'])){
//             $use_def_layout = true;
//         } else{
//             $use_def_layout = false;
//         }
        
        /*$adb = PearDatabase::getInstance();
        
        if(!isset($_SESSION['authenticated_user_id'])) $use_def_layout=true;
        else{
            $forcedlayout = $adb->pquery("SELECT vtiger_users.theme FROM vtiger_users WHERE vtiger_users.id = -1;",array());
            $forcedCount =  $adb->num_rows($forcedlayout);
            
            if(!isset($forcedCount) || $forcedCount==0){
                
                $currentuserid=$_SESSION['authenticated_user_id'];
                $userlayout = $adb->pquery("SELECT vtiger_users.theme FROM vtiger_users WHERE vtiger_users.id = ?;",array($currentuserid));
                $prefCount =  $adb->num_rows($userlayout);
                
                if(!isset($prefCount) || $prefCount==0) $use_def_layout=true;
                else{
                    $sel_layout = $adb->query_result_rowdata($userlayout, 0);
                    if($sel_layout['theme'] == 'rainbow')
                        $use_def_layout=false;
                    else
                        $use_def_layout=true;
                }
            }
            else{
                $sel_layout = $adb->query_result_rowdata($forcedlayout, 0);
                if($sel_layout['theme'] == 'rainbow')
                    $use_def_layout=false;
                else
                    $use_def_layout=true;
            }
        }*/
                
        global $current_user;
        
        if(!isset($_SESSION['authenticated_user_id'])) {
            $use_def_layout = true;
        } else{
            if($current_user->theme == 'rainbow'){
                $use_def_layout = false;
            } else {
                $use_def_layout = true;
            }
        }
        
        if($use_def_layout) self::$customLayout = "v7";
        else if(!$use_def_layout)self::$customLayout = "rainbow";
        
        self::$customLayoutPath = __DIR__.'/../../layouts/'.self::$customLayout;
        
        $this->tplDirs = array(self::$customLayoutPath, $this->getTemplateDir(0));
        
        // FOR SECURITY
        // Escape all {$variable} to overcome XSS
        // We need to use {$variable nofilter} to overcome double escaping
        // TODO: Until we review the use disabled.
        //$this->registerFilter('variable', array($this, 'safeHtmlFilter'));
        
        // FOR DEBUGGING: We need to have this only once.
        static $debugViewerURI = false;
        if (self::$debugViewer && $debugViewerURI === false) {
            $debugViewerURI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (!empty($_POST)) {
                $debugViewerURI .= '?' . http_build_query($_POST);
            } else {
                $debugViewerURI = $_SERVER['REQUEST_URI'];
            }
            
            $this->log("URI: $debugViewerURI, TYPE: " . $_SERVER['REQUEST_METHOD']);
        }
    }
    
    function safeHtmlFilter($content, $smarty) {
        //return htmlspecialchars($content,ENT_QUOTES,UTF-8);
        // NOTE: to_html is being used as data-extraction depends on this
        // We shall improve this as it plays role across the product.
        return to_html($content);
    }
    
    /**
     * Function to get the current layout name
     * @return <String> - Current layout name if not empty, otherwise Default layout name
     */
    public static function getLayoutName() {
        if(!empty(self::$currentLayout)) {
            return self::$currentLayout;
        }
        return self::getDefaultLayoutName();
    }
    
    public static function getCustomLayoutPath($relative=false) {
        if(!$relative) return self::$customLayoutPath;
        else return "layouts/".self::$customLayout;
    }
    
    public static function getCustomLayoutPathFile($file) {
        if(!$relative) return self::$customLayoutPath."/".$file;
        else return "layouts/".self::$customLayout."/".$file;
    }
    
    
    /**
     * Function to return for default layout name
     * @return <String> - Default Layout Name
     */
    public static function getDefaultLayoutName(){
        return self::DEFAULTLAYOUT;
    }
    
    /**
     * Function to get the module specific template path for a given template
     * @param <String> $templateName
     * @param <String> $moduleName
     * @return <String> - Module specific template path if exists, otherwise default template path for the given template name
     */
    public function getTemplatePath($templateName, $moduleName='') {
        $moduleName = str_replace(':', '/', $moduleName);
        
        $completeFilePathTheme = $this->tplDirs[0]. DIRECTORY_SEPARATOR . "modules/$moduleName/$templateName";
        $completeFilePath = $this->tplDirs[1]. DIRECTORY_SEPARATOR . "modules/$moduleName/$templateName";
        
        if(!empty($moduleName) && !empty(self::$customLayout) && file_exists($completeFilePathTheme)) {
            return $this->tplDirs[0]."/modules/$moduleName/$templateName";
        }
        elseif(!empty($moduleName) && file_exists($completeFilePath)) {
            return $this->tplDirs[1]."/modules/$moduleName/$templateName";
        } else {
            // Fall back lookup on actual module, in case where parent module doesn't contain actual module within in (directory structure)
            if(strpos($moduleName, '/') > 0) {
                $moduleHierarchyParts = explode('/', $moduleName);
                $actualModuleName = $moduleHierarchyParts[count($moduleHierarchyParts)-1];
                $baseModuleName = $moduleHierarchyParts[0];
                $fallBackOrder = array (
                    "$actualModuleName",
                    "$baseModuleName/Vtiger"
                );
                
                foreach($fallBackOrder as $fallBackModuleName) {
                    $intermediateFallBackFileName = 'modules/'. $fallBackModuleName .'/'.$templateName;
                    $intermediateFallBackFilePath = $this->tplDirs[1]. DIRECTORY_SEPARATOR . $intermediateFallBackFileName;
                    
                    $intermediateFallBackFileNameTheme = 'modules/'. $fallBackModuleName .'/'.$templateName;
                    $intermediateFallBackFilePathTheme = $this->tplDirs[0]. DIRECTORY_SEPARATOR . $intermediateFallBackFileNameTheme;
                    
                    if(file_exists($intermediateFallBackFilePathTheme)) {
                        return $this->tplDirs[0]."/".$intermediateFallBackFileNameTheme;
                    }
                    if(file_exists($intermediateFallBackFilePath)) {
                        return $this->tplDirs[1]."/".$intermediateFallBackFileName;
                    }
                }
            }
            if(file_exists($this->tplDirs[0]."/modules/Vtiger/$templateName"))
                return $this->tplDirs[0]."/modules/Vtiger/$templateName";
                
                else return $this->tplDirs[1]."/modules/Vtiger/$templateName";
        }
        
    }
    
    
    
    public function getTemplatePathOld($templateName, $moduleName='') {
        $moduleName = str_replace(':', '/', $moduleName);
        
        foreach($this->tplDirs as $tplDir){
            
            $completeFilePath = $tplDir. DIRECTORY_SEPARATOR . "modules/$moduleName/$templateName";
            
            if(!empty($moduleName) && file_exists($completeFilePath)) {
                return $tplDir."/modules/$moduleName/$templateName";
            } else {
                // Fall back lookup on actual module, in case where parent module doesn't contain actual module within in (directory structure)
                if(strpos($moduleName, '/') > 0) {
                    $moduleHierarchyParts = explode('/', $moduleName);
                    $actualModuleName = $moduleHierarchyParts[count($moduleHierarchyParts)-1];
                    $baseModuleName = $moduleHierarchyParts[0];
                    $fallBackOrder = array (
                        "$actualModuleName",
                        "$baseModuleName/Vtiger"
                    );
                    
                    foreach($fallBackOrder as $fallBackModuleName) {
                        $intermediateFallBackFileName = 'modules/'. $fallBackModuleName .'/'.$templateName;
                        $intermediateFallBackFilePath = $tplDir. DIRECTORY_SEPARATOR . $intermediateFallBackFileName;
                        if(file_exists($intermediateFallBackFilePath)) {
                            return $tplDir."/".$intermediateFallBackFileName;
                        }
                    }
                }
                if(file_exists($tplDir."/modules/Vtiger/$templateName") || end($this->tplDirs) == $tplDir)
                    return $tplDir."/modules/Vtiger/$templateName";
            }
            
        }
    }
    
    /** @Override */
    public function assign($tpl_var, $value = null, $nocache = false) {
        // Reject unexpected value assignments.
        if ($tpl_var == 'SELECTED_MENU_CATEGORY') {
            if ($val && preg_match("/[^a-zA-Z0-9_-]/", $val, $m)) {
                return;
            }
        }
        return parent::assign($tpl_var, $value, $nocache);
    }
    
    /**
     * Function to display/fetch the smarty file contents
     * @param <String> $templateName
     * @param <String> $moduleName
     * @param <Boolean> $fetch
     * @return html data
     */
    public function view($templateName, $moduleName='', $fetch=false) {
        $templatePath = $this->getTemplatePath($templateName, $moduleName);
        $templateFound = $this->templateExists($templatePath);
        
        // Logging
        if (self::$debugViewer) {
            $templatePathToLog = $templatePath;
            $qualifiedModuleName = str_replace(':', '/', $moduleName);
            // In case we found a fallback template, log both lookup and target template resolved to.
            if (!empty($moduleName) && strpos($templatePath, "modules/$qualifiedModuleName/") !== 0) {
                $templatePathToLog = "modules/$qualifiedModuleName/$templateName > $templatePath";
            }
            $this->log("VIEW: $templatePathToLog, FOUND: " . ($templateFound? "1" : "0"));
            foreach ($this->tpl_vars as $key => $smarty_variable) {
                // Determine type of value being pased.
                $valueType = 'literal';
                if (is_object($smarty_variable->value)) $valueType = get_class($smarty_variable->value);
                else if (is_array($smarty_variable->value)) $valueType = 'array';
                $this->log(sprintf("DATA: %s, TYPE: %s", $key, $valueType));
            }
        }
        // END
        
        if ($templateFound) {
            if($fetch) {
                return $this->fetch($templatePath);
            } else {
                $this->display($templatePath);
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Static function to get the Instance of the Class Object
     * @param <String> $media Layout/Media
     * @return Vtiger_Viewer instance
     */
    static function getInstance($media='') {
        $instance = new self($media);
        return $instance;
    }
    
}

function vtemplate_path($templateName, $moduleName='') {
    $viewerInstance = Vtiger_Viewer::getInstance();
    $args = func_get_args();
    return call_user_func_array(array($viewerInstance, 'getTemplatePath'), $args);
}

function myclayout_path($templateName) {
    $viewerInstance = Vtiger_Viewer::getInstance();
    $args = func_get_args();
    return call_user_func_array(array($viewerInstance, 'getCustomLayoutPathFile'), $args);
}

/**
 * Generated cache friendly resource URL linked with version of Vtiger
 */
function vresource_url($url) {
    global $vtiger_current_version;
    if (stripos($url, '://') === false) {
        $url = $url .'?v='.$vtiger_current_version;
    }
    return $url;
}

function getPurifiedSmartyParameters($param){
    return htmlentities($_REQUEST[$param]);
}
