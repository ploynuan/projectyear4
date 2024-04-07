<?php
require 'vendor/autoload.php';

use phpseclib3\Net\SFTP;
$config = require 'config.php';

$sftp_user = $config['sftp_user'];
$sftp_pass = $config['sftp_pass'];
$sftp_server = $config['sftp_server'];
$sftp_port = $config['sftp_port'];

// ตั้งค่าข้อมูลเซิร์ฟเวอร์ที่สอง
$sftp_user2 = $config['sftp_user2'];
$sftp_pass2 = $config['sftp_pass2'];
$sftp_server2 = $config['sftp_server2'];
$sftp_port2 = $config['sftp_port2'];

// Remote and local file paths
$remote_dir = $_GET['remoteFilePath'];
$local= $_GET['remoteDir2'];

// Connect to the SFTP server
$sftp = new SFTP($sftp_server, $sftp_port);
if ($sftp->login($sftp_user, $sftp_pass)) {
    $sftp2 = new SFTP($sftp_server2);

    // เชื่อมต่อกับเซิร์ฟเวอร์ 2
    if (!$sftp2->login($sftp_user2, $sftp_pass2)) {
        die("เข้าสู่ระบบผิดพลาด");
    }
    // ดาวน์โหลดไฟล์
    if ($sftp->get($remoteFilePath, $localFilePath)) {
        echo  $localFilePath;
} else {
    echo 'Failed to download file';
}
    $sftp->disconnect();
}
?>
