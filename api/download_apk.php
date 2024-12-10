<?php
require('../conect.php');
require_once(__DIR__ . '/../logger/logger.php');

$db = new mysqli($SERVER, $USER, $PASS, $DB);
$sql = "SELECT version FROM version_apk";
$version = $db->query($sql)->fetch_object()->version;
// File to be downloaded (change this to your file's path)
$file_path = __DIR__ . "/../versions/version-$version.apk";

// Function to set appropriate headers for file download
function downloadFile($file_path)
{
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "File not found.";
    }
}

// Call the downloadFile function to initiate the download
downloadFile($file_path);

?>