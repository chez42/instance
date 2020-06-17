<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

define("BASE_URL", 1);
define("BASE_DIR", 2);
define("UPLOADS_URL", 3);
define("UPLOADS_DIR", 4);
define("STATIC_URL", 5);
define("STATIC_DIR", 6);
define("THUMBNAILS_URL", 7);
define("THUMBNAILS_DIR", 8);
define("THUMBNAIL_WIDTH", 9);
define("THUMBNAIL_HEIGHT", 10);
require "config.php";
require "premailer.php";
$http_response_code = 200;
$url = parse_url($_SERVER["REQUEST_URI"]);
if (array_key_exists("path", $url)) {
    $request = substr($url["path"], strlen(dirname($url["path"])));
    $request_handlers = array("/upload/" => "ProcessUploadRequest", "/img/" => "ProcessImgRequest", "/dl/" => "ProcessDlRequest");
    if (array_key_exists($request, $request_handlers)) {
        $request_handlers[$request]();
    } else {
        $http_response_code = 404;
    }
} else {
    $http_response_code = 500;
}
http_response_code($http_response_code);
/**
 * handler for upload requests
 */
function ProcessUploadRequest()
{
    global $config;
    global $http_return_code;
    $files = array();
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $dir = scandir($config[BASE_DIR] . $config[UPLOADS_DIR]);
        foreach ($dir as $file_name) {
            $file_path = $config[BASE_DIR] . $config[UPLOADS_DIR] . $file_name;
            if (is_file($file_path)) {
                $size = filesize($file_path);
                $file = array("name" => $file_name, "url" => $config[BASE_URL] . $config[UPLOADS_URL] . $file_name, "size" => $size);
                if (file_exists($config[BASE_DIR] . $config[THUMBNAILS_DIR] . $file_name)) {
                    $file["thumbnailUrl"] = $config[BASE_URL] . $config[THUMBNAILS_URL] . $file_name;
                }
                $files[] = $file;
            }
        }
    } else {
        if (!empty($_FILES)) {
            foreach ($_FILES["files"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["files"]["tmp_name"][$key];
                    $file_name = $_FILES["files"]["name"][$key];
                    $file_path = $config[BASE_DIR] . $config[UPLOADS_DIR] . $file_name;
                    if (move_uploaded_file($tmp_name, $file_path) === true) {
                        $size = filesize($file_path);
                        error_reporting(1);
                        ini_set("display_errors", "1");
                        $image = new Imagick($file_path);
                        $image->resizeImage($config[THUMBNAIL_WIDTH], $config[THUMBNAIL_HEIGHT], Imagick::FILTER_LANCZOS, 1, true);
                        $image->writeImage($config[BASE_DIR] . $config[THUMBNAILS_DIR] . $file_name);
                        $image->destroy();
                        $file = array("name" => $file_name, "url" => $config[BASE_URL] . $config[UPLOADS_URL] . $file_name, "size" => $size, "thumbnailUrl" => $config[BASE_URL] . $config[THUMBNAILS_URL] . $file_name);
                        $files[] = $file;
                    } else {
                        $http_return_code = 500;
                        return NULL;
                    }
                } else {
                    $http_return_code = 400;
                    return NULL;
                }
            }
        }
    }
    header("Content-Type: application/json; charset=utf-8");
    header("Connection: close");
    echo json_encode(array("files" => $files));
}
/**
 * handler for img requests
 */
function ProcessImgRequest()
{
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $method = $_GET["method"];
        $params = explode(",", $_GET["params"]);
        $width = (int) $params[0];
        $height = (int) $params[1];
        if ($method == "placeholder") {
            $image = new Imagick();
            $image->newImage($width, $height, "#707070");
            $image->setImageFormat("png");
            $x = 0;
            $y = 0;
            $size = 40;
            $draw = new ImagickDraw();
            while ($y < $height) {
                $draw->setFillColor("#808080");
                $points = array(array("x" => $x, "y" => $y), array("x" => $x + $size, "y" => $y), array("x" => $x + $size * 2, "y" => $y + $size), array("x" => $x + $size * 2, "y" => $y + $size * 2));
                $draw->polygon($points);
                $points = array(array("x" => $x, "y" => $y + $size), array("x" => $x + $size, "y" => $y + $size * 2), array("x" => $x, "y" => $y + $size * 2));
                $draw->polygon($points);
                $x += $size * 2;
                if ($width < $x) {
                    $x = 0;
                    $y += $size * 2;
                }
            }
            $draw->setFillColor("#B0B0B0");
            $draw->setFontSize($width / 5);
            $draw->setFontWeight(800);
            $draw->setGravity(Imagick::GRAVITY_CENTER);
            $draw->annotation(0, 0, $width . " x " . $height);
            $image->drawImage($draw);
            header("Content-type: image/png");
            echo $image;
        } else {
            $file_name = $_GET["src"];
            $path_parts = pathinfo($file_name);
            switch ($path_parts["extension"]) {
                case "png":
                    $mime_type = "image/png";
                    break;
                case "gif":
                    $mime_type = "image/gif";
                    break;
                default:
                    $mime_type = "image/jpeg";
                    break;
            }
            $file_name = $path_parts["basename"];
            $image = ResizeImage($file_name, $method, $width, $height);
            header("Content-type: " . $mime_type);
            echo $image;
        }
    }
}
/**
 * handler for dl requests
 */
function ProcessDlRequest()
{
    global $config;
    global $http_return_code;
    $premailer = Premailer::html($_POST["html"], true, "hpricot", $config[BASE_URL]);
    $html = $premailer["html"];
    $matches = array();
    $num_full_pattern_matches = preg_match_all("#<img.*?src=\"([^\"]*?\\/[^/]*\\.[^\"]+)#i", $html, $matches);
    for ($i = 0; $i < $num_full_pattern_matches; $i++) {
        if (stripos($matches[1][$i], "/img?src=") !== false) {
            $src_matches = array();
            if (preg_match("#/img\\?src=(.*)&amp;method=(.*)&amp;params=(.*)#i", $matches[1][$i], $src_matches) !== false) {
                $file_name = urldecode($src_matches[1]);
                $file_name = substr($file_name, strlen($config[BASE_URL] . $config[UPLOADS_URL]));
                $method = urldecode($src_matches[2]);
                $params = urldecode($src_matches[3]);
                $params = explode(",", $params);
                $width = (int) $params[0];
                $height = (int) $params[1];
                $static_file_name = $method . "_" . $width . "x" . $height . "_" . $file_name;
                $html = str_ireplace($matches[1][$i], $config[BASE_URL] . $config[STATIC_URL] . urlencode($static_file_name), $html);
                $image = ResizeImage($file_name, $method, $width, $height);
                $image->writeImage($config[BASE_DIR] . $config[STATIC_DIR] . $static_file_name);
            }
        }
    }
    switch ($_POST["action"]) {
        case "download":
            echo "            <!-- Provides extra visual weight and identifies the primary action in a set of buttons -->\n            <div id=\"output_template\" style=\"margin: 0 auto;width: 660px;\">\n                ";
            echo $html;
            echo "            </div>\n\n            ";
            break;
        case "email":
            $to = $_POST["rcpt"];
            $subject = $_POST["subject"];
            $headers = array();
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/html; charset=iso-8859-1";
            $headers[] = "To: " . $to;
            $headers[] = "Subject: " . $subject;
            $headers = implode("\r\n", $headers);
            if (mail($to, $subject, $html, $headers) === false) {
                $http_return_code = 500;
                return NULL;
            }
            break;
    }
}
/**
 * function to resize images using resize or cover methods
 */
function ResizeImage($file_name, $method, $width, $height)
{
    global $config;
    $image = new Imagick($config[BASE_DIR] . $config[UPLOADS_DIR] . $file_name);
    if ($method == "resize") {
        $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
    } else {
        $image_geometry = $image->getImageGeometry();
        $width_ratio = $image_geometry["width"] / $width;
        $height_ratio = $image_geometry["height"] / $height;
        $resize_width = $width;
        $resize_height = $height;
        if ($height_ratio < $width_ratio) {
            $resize_width = 0;
        } else {
            $resize_height = 0;
        }
        $image->resizeImage($resize_width, $resize_height, Imagick::FILTER_LANCZOS, 1);
        $image_geometry = $image->getImageGeometry();
        $x = ($image_geometry["width"] - $width) / 2;
        $y = ($image_geometry["height"] - $height) / 2;
        $image->cropImage($width, $height, $x, $y);
    }
    return $image;
}

?>