<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 25.02.15 17:51
 * You must not use this file without permission.
 */
global $root_directory;
require_once($root_directory."/modules/Timecontrol/autoload_wf.php");


class Timecontrol_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View
{
    public function process(Vtiger_Request $request) {

        $datetimefield = new DateTimeField(null);
        $nowDate = $datetimefield->convertToUserTimeZone(date('Y-m-d H:i'));

        $finishDateTS = strtotime($nowDate->format('Y-m-d H:i:s'));
        $nowTime = $nowDate->format('H:i');

        $request->set('date_start', $datetimefield->convertToUserFormat(date('Y-m-d', $finishDateTS)));
        $request->set('time_start', $nowTime);

        $request->set('title', 'Timer '.rand(10000,99999));

        parent::process($request);
    }

}
