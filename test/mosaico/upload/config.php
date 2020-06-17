<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

$config = array(BASE_URL => (array_key_exists("HTTPS", $_SERVER) ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . dirname(dirname($_SERVER["PHP_SELF"])) . "/", BASE_DIR => dirname(dirname($_SERVER["SCRIPT_FILENAME"])) . "/", UPLOADS_URL => "uploads/", UPLOADS_DIR => "uploads/", STATIC_URL => "uploads/static/", STATIC_DIR => "uploads/static/", THUMBNAILS_URL => "uploads/thumbnails/", THUMBNAILS_DIR => "uploads/thumbnails/", THUMBNAIL_WIDTH => 90, THUMBNAIL_HEIGHT => 90);

?>