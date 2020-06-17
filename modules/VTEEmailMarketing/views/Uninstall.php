<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Class VTEEmailMarketing_Uninstall_View
 */
class VTEEmailMarketing_Uninstall_View extends Settings_Vtiger_Index_View
{
    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $module = Vtiger_Module::getInstance($moduleName);
        echo "<div class=\"container-fluid\">";
        if (!$module) {
            echo "<div class=\"widget_header row-fluid\"><h3>" . vtranslate("Invalid module") . "</h3></div>";
            echo "<hr>";
        } else {
            echo "<div class=\"widget_header row-fluid\"><h3>" . $module->label . "</h3></div>";
            echo "<hr>";
            $module->delete();
            $this->cleanDatabase($moduleName);
            $this->cleanFolder($moduleName);
            $this->cleanLanguage($moduleName);
            echo "Module was uninstalled.";
        }
        echo "<br>";
        echo "Back to <a href=\"index.php?module=ModuleManager&parent=Settings&view=List\">" . vtranslate("ModuleManager") . "</a>";
        echo "</div>";
    }
    /**
     * @param $moduleName
     */
    public function cleanDatabase($moduleName)
    {
        global $adb;
        $tabId = getTabid($moduleName);
        echo "&nbsp;&nbsp;- Delete vtiger_vteemailmarketingcf table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketingcf");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing_schedule table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing_schedule");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing_emailtemplate table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing_emailtemplate");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing_unsubcribes table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing_unsubcribes");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing_status table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing_status");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketing_status_seq table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketing_status_seq");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vtiger_vteemailmarketingrel table.";
        $result = $adb->pquery("DROP TABLE vtiger_vteemailmarketingrel");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete record related_list.";
        $result = $adb->pquery("DELETE FROM vtiger_relatedlists WHERE related_tabid = ?", array($tabId));
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>";
        $sql = "DROP TABLE `vteemailmarketing_settings`;";
        $result = $adb->pquery($sql, array());
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Delete vteemailmarketing_settings table.";
    }
    /**
     * @param $moduleName
     */
    public function cleanFolder($moduleName)
    {
        echo "&nbsp;&nbsp;- Remove " . $moduleName . " template folder";
        $result = $this->removeFolder("layouts/v7/modules/" . $moduleName);
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>";
        echo "&nbsp;&nbsp;- Remove " . $moduleName . " module folder";
        $result = $this->removeFolder("modules/" . $moduleName);
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>&nbsp;&nbsp;- Remove Mosaico Folder";
        $result = $this->removeFolder("test/mosaico");
        echo $result ? " - DONE" : " - <b>ERROR</b>";
        echo "<br>";
    }
    /**
     * @param $path
     * @return bool
     */
    public function removeFolder($path)
    {
        if (!isFileAccessible($path) || !is_dir($path)) {
            return false;
        }
        if (!is_writeable($path)) {
            chmod($path, 511);
        }
        $handle = opendir($path);
        while ($tmp = readdir($handle)) {
            if ($tmp == ".." || $tmp == ".") {
                continue;
            }
            $tmpPath = $path . DS . $tmp;
            if (is_file($tmpPath)) {
                if (!is_writeable($tmpPath)) {
                    chmod($tmpPath, 438);
                }
                unlink($tmpPath);
            } else {
                if (is_dir($tmpPath)) {
                    if (!is_writeable($tmpPath)) {
                        chmod($tmpPath, 511);
                    }
                    $this->removeFolder($tmpPath);
                }
            }
        }
        closedir($handle);
        rmdir($path);
        return !is_dir($path);
    }
    /**
     * @param $moduleName
     */
    public function cleanLanguage($moduleName)
    {
        $files = glob("languages/*/" . $moduleName . ".php");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    /**
     * @link http://stackoverflow.com/questions/7288029/php-delete-directory-that-is-not-empty
     * @param $dir
     */
    public function rmdir_recursive($dir)
    {
        foreach (scandir($dir) as $file) {
            if ("." === $file || ".." === $file) {
                continue;
            }
            $tmpFile = (string) $dir . "/" . $file;
            if (is_dir($tmpFile)) {
                $this->rmdir_recursive($tmpFile);
            } else {
                unlink($tmpFile);
            }
        }
        rmdir($dir);
    }
}

?>