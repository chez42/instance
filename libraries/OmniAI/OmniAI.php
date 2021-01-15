<?php
spl_autoload_register(function ($className) {
    if (file_exists("libraries/OmniAI/$className.php")) {
        include_once "libraries/OmniAI/$className.php";
    }
});

class OmniAI{
    public function __construct(){
    }

    public function AuditTransactions($account_number){
        global $adb;

    }
}