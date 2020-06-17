<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

header("Content-type: text/css");
if ($handle = opendir("../../../../test/QuotingTool/resources/font")) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $fileSplit = explode(".", $entry);
            list($nameFont, $fileType) = $fileSplit;
            if (strtoupper($fileType) == "TTF" || strtoupper($fileType) == "WOFF") {
                $format = "truetype";
            } else {
                if (strtoupper($fileType) == "OTF") {
                    $format = "opentype";
                } else {
                    if (strtoupper($fileType) == "EOT") {
                        $format = "embedded-opentype";
                    } else {
                        if (strtoupper($fileType) == "SGV") {
                            $format = "svg";
                        }
                    }
                }
            }
            echo "@font-face {\r\n            font-family: '" . $nameFont . "';\r\n            src: url('../../../../test/QuotingTool/resources/font/" . $entry . "');\r\n            src: url('../../../../test/QuotingTool/resources/font/" . $entry . "') format('" . $format . "');\r\n            }";
        }
    }
    closedir($handle);
}

?>