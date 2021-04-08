<?php
error_reporting(0);
set_time_limit(0);
ignore_user_abort(true);
$file_title = __DIR__ . "/title.txt";
$icecastHost = "localhost";
$icecastPort = "8000";
$icecastMount = "live.mp3";
$icecastAdmin = "admin";
$icecastAdminPass = "admin password";
$url = "https://site.com";
exec('ps -ef | grep -v grep | grep ' . __FILE__ . '  2>&1', $processes);
if ((isset($processes[1])) and (stripos($processes[1], __FILE__) !== false)) {
    echo "A copy of the script is already running!" . PHP_EOL;
} else {
    function updateMetadata($string, $server, $port, $mountpoint, $admin, $pass, $url)
    {
        $string = preg_replace("/ /", "%20", $string);
        $currentsonguri = urlencode($string);
        $currentsonguri = preg_replace("/\%2520/", "%20", $currentsonguri);
        $fp = fsockopen($server, $port, $errno, $errstr, 3);
        if ($fp) {
            fputs($fp, "GET /admin/metadata.xsl?mount=/" . urlencode($mountpoint) . "&mode=updinfo&charset=UTF-8&artist=&song=" . $currentsonguri . "&url=" . $url . " HTTP/1.1\n");
            fputs($fp, "Authorization: Basic " . base64_encode($admin . ":" . $pass) . "\r\n\r\n");
            fputs($fp, "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:87.0)\n\n");
            fclose($fp);
        }
    }
    $titleout = $titlenew = "";
    while (true) {
        $title_arr = file($file_title);
        $titlenew = trim($title_arr[0]);
        if ($titleout !== $titlenew) {
            echo $titlenew . PHP_EOL;
            $titleout = $titlenew;
            echo $titleout . PHP_EOL;;
            updateMetadata($titleout, $icecastHost, $icecastPort, $icecastMount, $icecastAdmin, $icecastAdminPass, $url);
        }
        sleep(10);
    }
}
?>