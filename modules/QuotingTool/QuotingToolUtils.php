<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class QuotingToolUtils
 */
class QuotingToolUtils
{
    /**
     * Function to check if a given user exists (not deleted)
     * @param integer $userId - record id
     * @return bool
     */
    public static function isUserExists($userId)
    {
        global $adb;
        $query = "SELECT id FROM vtiger_users where id=? AND deleted=0";
        $result = $adb->pquery($query, array($userId));
        if ($adb->num_rows($result)) {
            return true;
        }
        return false;
    }
    /**
     * Function to check if a given record exists (not deleted)
     * @param integer $recordId - record id
     * @return bool
     */
    public static function isRecordExists($recordId)
    {
        global $adb;
        $query = "SELECT crmid FROM vtiger_crmentity where crmid=? AND deleted=0";
        $result = $adb->pquery($query, array($recordId));
        if ($adb->num_rows($result)) {
            return true;
        }
        return false;
    }
    /**
     * @link http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, 0 - strlen($haystack)) !== false;
    }
    /**
     * @link http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return $needle === "" || 0 <= ($temp = strlen($haystack) - strlen($needle)) && strpos($haystack, $needle, $temp) !== false;
    }
    /**
     * @link http://stackoverflow.com/questions/6743554/problem-slash-with-json-encode-why-and-how-solve-it
     * JSON_UNESCAPED_SLASHES only support PHP version >= 5.4
     *
     * prevent json_encode() escaping forward slashes
     * @link http://stackoverflow.com/questions/10210338/json-encode-escaping-forward-slashes
     *
     * @param $jsonString
     * @return mixed
     */
    public static function jsonUnescapedSlashes($jsonString)
    {
        return str_replace("\\/", "/", $jsonString);
    }
    /**
     * @param $arrStyle
     * @return string
     */
    public static function convertArrayToInlineStyle($arrStyle)
    {
        $style = "";
        foreach ($arrStyle as $key => $value) {
            $style .= $key . ": " . $value . ";";
        }
        return $style;
    }
    /**
     * @param $str
     * @return string
     */
    public static function removeNonAlphanumericCharacters($str)
    {
        return preg_replace("/[^A-Za-z0-9 ]/", "", $str);
    }
    /**
     * @param string $str
     * @param int $length Token Length
     * @return string
     */
    public static function generateToken($str = "", $length = 10)
    {
        $possible = self::removeNonAlphanumericCharacters($str) . "0123456789abcdefghijklmnopqrstuvwxyz";
        $token = "";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!stristr($token, $char)) {
                $token .= $char;
                $i++;
            }
        }
        return $token;
    }
}

?>