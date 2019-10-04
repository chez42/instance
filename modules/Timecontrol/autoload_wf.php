<?php
$moduleName = 'Timecontrol';

global $root_directory;

require_once("modules/Timecontrol/Timecontrol.php");
require_once(dirname(__FILE__)."/autoloader.php");

$className = '\\TimeControl\\Autoload';

$className::registerDirectory("~/modules/".$moduleName."/lib");


