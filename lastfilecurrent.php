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


// ตรวจสอบว่าคีย์ "remoteDir" และ "current" มีอยู่ในข้อมูล POST หรือไม่
if (isset($_POST['remoteDir'])) {
    $remoteDir = $_POST['remoteDir'];

    $sftp = new SFTP($sftp_server, $sftp_port);

    if ($sftp->login($sftp_user, $sftp_pass)) {
        $file_list = $sftp->nlist($remoteDir);

        // ตรวจสอบว่าเป็นอาร์เรย์และมีสมาชิก
        if (is_array($file_list) && count($file_list) > 0) {
            $currentLastFile = end($file_list);
                echo $currentLastFile;
        } else {
            echo "0";
        }
    } else {
        echo "เข้าสู่ระบบผิดพลาด";
    }
} else {
    echo "คีย์ 'remoteDir' หรือ 'current' ไม่พบในข้อมูล POST";
}
?>
