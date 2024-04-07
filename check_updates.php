<?php
require 'vendor/autoload.php';
use phpseclib3\Net\SFTP;
use phpseclib3\Exception\ConnectionClosedException;

// ตั้งค่าข้อมูลเซิร์ฟเวอร์แรก
$sftp_user = "jamming2023";
$sftp_pass = "jNY6TstE";
$sftp_server = "161.246.18.204";
$sftp_port = 8030;



// ตรวจสอบว่าคีย์ "remoteDir" และ "current" มีอยู่ในข้อมูล POST หรือไม่
if (isset($_POST['remoteDir'], $_POST['current'])) {
    $remoteDir = $_POST['remoteDir'];

    try {
        // ลองเชื่อมต่อ SSH กับเซิร์ฟเวอร์แรก
        $sftp = new SFTP($sftp_server, $sftp_port);
        if ($sftp->login($sftp_user, $sftp_pass)) {
            $file_list = $sftp->nlist($remoteDir);

            // ตรวจสอบว่าเป็นอาร์เรย์และมีสมาชิก
            if (is_array($file_list) && count($file_list) > 0) {
                $currentLastFile = end($file_list);

                // ตรวจสอบว่าคีย์ "current" ต่างจากไฟล์ล่าสุดหรือไม่
                if ($_POST['current'] !== $currentLastFile) {
                    echo $currentLastFile;
                } else {
                    echo $_POST['current'];
                }
            } else {
                echo "0";
            }
        } else {
            echo "เข้าสู่ระบบผิดพลาด";
        }
    } catch (RuntimeException $e) {
        // จัดการข้อผิดพลาดเมื่อมี RuntimeException
        echo "0";
    }
} else {
    echo "คีย์ 'remoteDir' หรือ 'current' ไม่พบในข้อมูล POST";
}
?>
