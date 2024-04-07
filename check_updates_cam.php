<?php
require 'vendor/autoload.php';
use phpseclib3\Net\SFTP;

// ตั้งค่าข้อมูลเซิร์ฟเวอร์แรก
$sftp_user = "jamming2023";
$sftp_pass = "jNY6TstE";
$sftp_server = "161.246.18.204";
$sftp_port = 8030;

// ตั้งค่าข้อมูลเซิร์ฟเวอร์ที่สอง
$sftp_server2 = "161.246.18.205";
$sftp_user2 = "tts";
$sftp_pass2 = "ttsproj";
$sftp_port2 = 22;

$remoteDir = $_POST['remoteDir'];

$sftp = new SFTP($sftp_server, $sftp_port);

if ($sftp->login($sftp_user, $sftp_pass)) {
    $file_list = $sftp->nlist($remoteDir); // ใช้ $remoteDir ที่ได้จาก POST request
    $currentLastFile = end($file_list);
    // ตรวจสอบว่า $last_file มีการเปลี่ยนแปลงหรือไม่
    if ($_POST['current_c'] !== $currentLastFile) {
        echo $currentLastFile;
    } else {
        echo $_POST['current_c'];
    }
} else {
    echo "เข้าสู่ระบบผิดพลาด";
}
?>
