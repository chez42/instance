<?php

class OmniMail_Module_Model extends Vtiger_Module_Model{
    public function getDefaultUrl() {
//        return 'index.php?module='.$this->get('name').'&view=List'.'" target="new"';
        global $current_user;
        
        return $this->getMailUrl() . "libraries/OmniMail/index.php?uid=".$current_user->id.'" target="new"';
//        return 'http://www.advisorviewdev.com/vcrm2/libraries/OmniMail/index.php?uid='.$current_user->id.'" target="new"';
//        return 'index.php?module='.$this->get('name').'&view='.$this->getDefaultViewName();
    }
    public function getMailUrl(){
        $url = "";
        if ($_SERVER["HTTPS"] == "on")
            $url = "https://";
        else
            $url = "http://";

        $url .= $_SERVER["SERVER_NAME"];

        //get public directory structure eg "/top/second/third" 
        $public_directory = dirname($_SERVER['PHP_SELF']); 
        //place each directory into array 
        $directory_array = explode('/', $public_directory); 
        //get highest or top level in array of directory strings 
        $public_base = max($directory_array); 

        $url .= "/" . $public_base . "/";
        
        return $url;
    }
}

?>