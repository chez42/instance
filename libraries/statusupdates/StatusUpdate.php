<?php


class StatusUpdate{
    static public function UpdateMessage($code, $message){
        global $adb;
        $query = "INSERT INTO StatusUpdate (code, message, last_update) VALUES (?, ?, NOW())
                  ON DUPLICATE KEY UPDATE message = VALUES(message), last_update = NOW()";
        $adb->pquery($query, array($code, $message), true);
    }

    static public function ReadMessage($code){
        global $adb;
        $query = "SELECT message, last_update FROM StatusUpdate WHERE code = ?";
        $result = $adb->pquery($query, array($code));
        $message = $adb->query_result($result, 0, 'message');
        $last_update = $adb->query_result($result, 0, 'last_update');
        if(strtolower($message) == 'finished'){
            return "finished";
        }else
            return $message . " - " . $last_update;
    }
}