<?php
/**
 * Created by PhpStorm.
 * User: ryansandnes
 * Date: 2019-02-04
 * Time: 11:38 AM
 */

class cPhantom
{
    public function __construct()
    {
    }

    public function ConfirmUserAndPassword($uname, $pword){
        global $adb;
        $query = "SELECT username, password FROM vtiger_phantomjssettings";
        $result = $adb->pquery($query, array());
        if($adb->num_rows($result) > 0){
            $vt_uname = $adb->query_result($result, 0, 'username');
            $vt_pword = $adb->query_result($result, 0, 'password');
            if($uname === $vt_uname && $pword === $vt_pword){
                return 1;
            }
        }
    }

    public function __destruct()
    {
    }
}