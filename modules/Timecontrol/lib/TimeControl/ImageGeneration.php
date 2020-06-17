<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 26.03.15 11:59
 * You must not use this file without permission.
 */
namespace TimeControl;

class ImageGeneration
{

    public static function generateImage($user_id) {
        if(!function_exists('imagestring')) {
           return;
        }

        $filepath = vglobal('root_directory').'modules/Timecontrol/tmp/user_'.$user_id.'.png';

        $adb = \PearDatabase::getInstance();
        $sql = 'SELECT COUNT(*) as num FROM vtiger_timecontrol INNER JOIN vtiger_crmentity ON (crmid = timecontrolid AND deleted = 0) WHERE smownerid = ? AND timecontrolstatus = "run"';
        $result = $adb->pquery($sql, array($user_id));

        $im = imagecreatetruecolor(30, 14);

        $bg = imagecolorallocate($im, 0, 0, 0);
        imagefill($im,0,0,$bg);
        imagecolortransparent($im,$bg);

        // White background and blue text
        $textcolor = imagecolorallocate($im, 255, 255, 255);

        // Write the string at the top left
        imagestring($im, 2, 5, 0, $adb->query_result($result, 0, 'num'), $textcolor);

        imagepng($im, $filepath);
        //unlink($filepath);

    }

}
