<?php
require 'vendor/autoload.php'; 
use phpseclib3\Net\SFTP;
// Include necessary files and establish a database connection
if(isset($_POST["datetime"]))
    {
        $date_selected = $_POST["datetime"];
        $date_selected_array = explode('-', $date_selected);
    }

// ตั้งค่าข้อมูลเซิร์ฟเวอร์แรก
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

// ฟังก์ชันสำหรับดาวน์โหลดและอัปโหลดไฟล์
function downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current) {
    $sftp = new SFTP($sftp_server, $sftp_port);

    if ($sftp->login($sftp_user, $sftp_pass)) {
        $file_list = $sftp->nlist($remote_dir);

        if (is_array($file_list) && count($file_list) > 0) {
            $last_file = end($file_list);

            $remote_dir_current_files = $sftp->nlist($remote_dir_current);

            if (is_array($remote_dir_current_files) && count($remote_dir_current_files) > 0) {
                $last_file_current = end($remote_dir_current_files);
            } else {
                $last_file_current = 0;
            }

            $remote_file_path = $remote_dir . $last_file;
            $sftp2 = new SFTP($sftp_server2, $sftp_port2);

            if (!$sftp2->login($sftp_user2, $sftp_pass2)) {
                die("Login to server 2 failed");
            }

            $remoteFilePath = $remote_file_path;
            $localFilePath = $remote_dir2 . $last_file;
            
            if ($sftp->get($remoteFilePath, $localFilePath)) {
                return ['file_path' => $localFilePath, 'file_name' => $last_file, 'file_name_current' => $last_file_current];
            } else {
                
                return ['file_name_current' => $last_file_current];
            }
        } else {
            $remote_dir_current_files = $sftp->nlist($remote_dir_current);
            if (is_array($remote_dir_current_files) && count($remote_dir_current_files) > 0) {
                $last_file_current = end($remote_dir_current_files);
                return ['file_name_current' => $last_file_current];
            } else {
                $last_file_current = 0;
                return ['file_name_current' => $last_file_current];
            }
        }
    }
}




function deleteFilesInDirectory($directory) {
    $files = glob($directory . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}
    date_default_timezone_set('Asia/Bangkok');
    $date = date("Y-m-d");
    $date2 = date("d/m/Y");
    $year = date("Y");

    if(isset($_POST['datetime'])&&empty($_POST['stationname1'])){
        ?><P class="noinfo">Please Select Station  </P> <?php
    }
    elseif(isset($_POST['stationname1'])&&empty($_POST['datetime'])){
        ?><P class="noinfo">Please Select Datetime </P> <?php
    }
    elseif(empty($_POST['stationname1'])&&empty($_POST['datetime'])){
        ?><P class="noinfo">Please Select Station and Frequency band </P> <?php
    }
    elseif(isset($_POST['datetime'])&&isset($_POST['stationname1'])&&(($_POST['datetime']) > $date)) 
    { 
        echo "<script>alert('Please select end date within the current dates');</script>";
    }
    elseif(isset($_POST['datetime'])&&isset($_POST['stationname1'])) 
    {   
        $starttime=$_POST['datetime'];
        $dbname = $_POST['stationname1'];
        $remote_dir_current_r = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Red/" . $year . "/" . $date . "/";
        $remote_dir_current_y = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Yellow/" . $year . "/" . $date . "/";
        $remote_dir_current_g = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Green/" . $year . "/" . $date . "/";
        
        include_once('connect.php'); 
        ?>
        <div id="resultContainer" class="showspec">
            <div class=rfi1>
                <div class="rfi"><?php
                    $query = "SELECT * FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Red' OR `RFI_Level` = 'Red (Intentional)') ORDER BY id DESC LIMIT 1;";
                    $query_run=mysqli_query($conn,$query);
                    $remote_dir = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Red/" . $date_selected_array[0] . "/" . $starttime . "/";
                    $remote_dir2 = "downloads_R/";
                            echo '<script>';
                            echo 'var date = "' . $starttime . '";';
                            echo '</script>';
                    deleteFilesInDirectory($remote_dir2);
                    if(mysqli_num_rows($query_run)>0)
                    {   
                        $results = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
                        $json_data = json_encode($results);
                        foreach ($results as $row) {
                            
                            $downloadResult = downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_r);
                            // JavaScript variables
                            echo '<script>';
                            echo 'var lastFile = "' . $downloadResult['file_name'] . '";';
                            echo 'var file_name_current = "' . $downloadResult['file_name_current'] . '";';
                            echo 'var remoteDir_r = "' . $remote_dir . '";';
                            echo 'var remoteDir2 = "' . $remote_dir2 . '";';
                            echo 'var query_r = "' . $query . '";';
                            echo 'var dbname = "' .  $dbname . '";';
                            echo '</script>';

                            if ($downloadResult) {
                               
                                echo ' <div class="rfi_r">
                                            <div class="new4" id="new4_r">
                                                <p>NEW !</p>
                                                <span class="material-symbols-outlined close-icon">close</span>
                                            </div>
                                            <img id="downloadedImage" src="' . $downloadResult['file_path'] . '" alt="Downloaded File" width="300">
                                        </div>';
                                echo '<div class="value">';
                                echo '<span class="typerfi_r">Red</span>';
                                echo '<span class="powervalue_r" id="powerPeak">Average power : ' . number_format($row['Power_Average_Mean_dB'], 2) . ' dB</span>';
                                echo '</div>';
                                echo '<div id="alerttext_r"style="font-weight: 600;color:#F7F5F1;"><p>'.$date2.' has new data</p></div>';
                            } 
                        }
                    } else {
                        
                        
                            $downloadResult = @downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_r);
                            // JavaScript variables
                            echo '<script>';
                            echo 'var file_name_current = "' . ($downloadResult['file_name_current'] ?? '0') . '";';
                            echo 'var lastFile = "0";';
                            echo 'var remoteDir_r = "' . $remote_dir . '";';
                            echo 'var remoteDir2 = "' . $remote_dir2 . '";';
                            echo 'var query_r = "' . $query . '";';
                            echo 'var dbname = "' .  $dbname . '";';
                            echo '</script>';
                            echo '<div class="rfi_r">
                                    <div class="new4" id="new4_r">
                                        <p>NEW !</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <img id="downloadedImage_r" src="r_n.jpg" width="300">
                                </div>';
                        echo '<div class="value">';
                        echo '<span class="typerfi_r">Red</span>';
                        echo '<span class="powervalue_r"id="powerPeak">Average power : --.-- dB</span>';
                        echo '</div>';
                        echo '<div id="alerttext_r"style="font-weight: 600;color:#F7F5F1;"><p>'.$date2.' has new data</p></div>';
                    }
                    ?>
                </div>
                <div class="rfi" id="rfi_y"><?php
                    $query = "SELECT * FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Yellow' OR `RFI_Level` = 'Yellow (Intentional)') ORDER BY id DESC LIMIT 1;";
                    $query_run=mysqli_query($conn,$query);
                    $remote_dir = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Yellow/" . $date_selected_array[0] . "/" . $starttime . "/";
                    $remote_dir2 = "downloads_Y/";
                    deleteFilesInDirectory($remote_dir2);
                    if(mysqli_num_rows($query_run)>0)
                    {   
                        
                        $results = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
                        $json_data = json_encode($results);
                        foreach ($results as $row) {
                            
                            $downloadResult = downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_y);
                            // JavaScript variables
                            echo '<script>';
                            echo 'var lastFile_y = "' . $downloadResult['file_name'] . '";';
                            echo 'var file_name_current_y = "' . $downloadResult['file_name_current'] . '";';
                            echo 'var remoteDir_y = "' . $remote_dir . '";';
                            echo 'var remoteDir2_y = "' . $remote_dir2 . '";';
                            echo 'var query_y = "' . $query . '";';
                            echo '</script>';

                            if ($downloadResult) {
                                echo ' <div class="rfi_y">
                                            <div class="new4" id="new4_y">
                                                <p>NEW !</p>
                                                <span class="material-symbols-outlined close-icon">close</span>
                                            </div>
                                            <img id="downloadedImage2" src="' . $downloadResult['file_path'] . '" alt="Downloaded File" width="300">
                                        </div>';
                                echo '<div class="value">';
                                echo '<span class="typerfi_y">Yellow</span>';
                                echo '<span class="powervalue_y" id="powerPeak2">Average power : ' . number_format($row['Power_Average_Mean_dB'], 2) . ' dB</span>';
                                echo '</div>';
                                echo '<div id="alerttext_y"style="font-weight: 600;color:#F7F5F1;"><p>'.$date2.' has new data</p></div>';

                            } 
                        }
                    } else {
                            $downloadResult = @downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_y);
                            // JavaScript variables
                            // echo $downloadResult['file_name_current'] ;
                            echo '<script>';
                            echo 'var lastFile_y = "0";';
                            echo 'var file_name_current_y = "' . ($downloadResult['file_name_current'] ?? '0') . '";';
                            echo 'var remoteDir_y = "' . $remote_dir . '";';
                            echo 'var remoteDir2_y = "' . $remote_dir2 . '";';
                            echo 'var query_y = "' . $query . '";';
                            echo '</script>';
                            echo '<div class="rfi_y">
                                    <div class="new4" id="new4_y">
                                        <p>NEW !</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <img id="downloadedImage_y" src="y_n.jpg" width="300">
                                </div>';
                        echo '<div class="value">';
                        echo '<span class="typerfi_y">Yellow</span>';
                        echo '<span class="powervalue_y"id="powerPeak2">Average power : --.-- dB</span>';
                        echo '</div>';
                        echo '<div id="alerttext_y"style="font-weight: 600;color:#F7F5F1;"><p>'.$date2.' has new data</p></div>';
                    }
                    ?>
                </div>
                <div class="rfi" id="rfi_g"><?php
                    $query = "SELECT * FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Green') ORDER BY id DESC LIMIT 1;";
                    $query_run=mysqli_query($conn,$query);
                    $remote_dir = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Spectrum/Green/" . $date_selected_array[0] . "/" . $starttime . "/";
                    $remote_dir2 = "downloads_G/"; 
                    deleteFilesInDirectory($remote_dir2);
                    if(mysqli_num_rows($query_run)>0)
                    {   
                        $results = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
                        $json_data = json_encode($results);
                        foreach ($results as $row) {
                            $downloadResult = downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_g);
                            // JavaScript variables
                            echo '<script>';
                            echo 'var lastFile_g = "' . $downloadResult['file_name'] . '";';
                            echo 'var file_name_current_g = "' . $downloadResult['file_name_current'] . '";';
                            echo 'var remoteDir_g = "' . $remote_dir . '";';
                            echo 'var remoteDir2_g = "' . $remote_dir2 . '";';
                            echo 'var query_g = "' . $query . '";';
                            echo '</script>';

                            if ($downloadResult) {
                                echo ' <div class="rfi_g">
                                            <div class="new4" id="new4_g">
                                                <p>NEW !</p>
                                                <span class="material-symbols-outlined close-icon">close</span>
                                            </div>
                                            <img id="downloadedImage3" src="' . $downloadResult['file_path'] . '" alt="Downloaded File" width="300">
                                        </div>';
                                echo '<div class="value">';
                                echo '<span class="typerfi_g">Green</span>';
                                echo '<span class="powervalue_g" id="powerPeak3">Average power : ' . number_format($row['Power_Average_Mean_dB'], 2) . ' dB</span>';
                                echo '</div>';
                                echo '<div id="alerttext_g" style="font-weight: 600;color:#F7F5F1;"><p>'.$date2.' has new data</p></div>';
                            } 
                        }
                    } else {
                        
                            $downloadResult = @downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_g);
                            // JavaScript variables
                            // echo $downloadResult['file_name_current'] ;
                            echo '<script>';
                            echo 'var lastFile_g = "0";';
                            echo 'var file_name_current_g = "' . ($downloadResult['file_name_current'] ?? '0') . '";';
                            echo 'var remoteDir_g = "' . $remote_dir . '";';
                            echo 'var remoteDir2_g = "' . $remote_dir2 . '";';
                            echo 'var query_g = "' . $query . '";';
                            echo '</script>';
                            echo '<div class="rfi_g">
                                    <div class="new4" id="new4_g">
                                        <p>NEW !</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <img id="downloadedImage_g" src="g_n.jpg" width="300">
                                </div>';
                        echo '<div class="value">';
                        echo '<span class="typerfi_g">Green</span>';
                        echo '<span class="powervalue_g"id="powerPeak3">Average power : --.-- dB</span>';
                        echo '</div>';
                        echo '<div><p id="alerttext_g"style="font-weight: 600;color:#F7F5F1;">'.$date2.' has new data</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>                            
        <?php
    }?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $('#new4_r').hide();
    $('#new4_y').hide();
    $('#new4_g').hide();
    // $('#alerttext_g').hide();
    // $('#alerttext_y').hide();
    // $('#alerttext_r').hide();

</script>
<script>
    $(document).ready(function () {
        // Define global variables
        
        function checkForUpdates(remoteDir2, powerPeakElement, ImgElement,ImgElement_n) {
            //console.log(date);

            var currentDate = new Date();
            currentDate.setHours(0, 0, 0, 0); // ตั้งเวลาให้เป็นเที่ยงคืน

            var dateStatus = (new Date(date) < currentDate) ? 1 : 0;

            //console.log(currentDate);
            //console.log("dateStatus:", dateStatus);

            // ดึงข้อมูลวัน, เดือน, ปี จาก currentDate
            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1; // เพิ่ม 1 เนื่องจาก getMonth() คืนค่าเดือนที่เริ่มจาก 0 (มกราคม = 0)
            var year = currentDate.getFullYear();

            // เติม 0 ข้างหน้าเลขวันและเดือน ถ้ามีหลักเดียว
            day = (day < 10) ? '0' + day : day;
            month = (month < 10) ? '0' + month : month;

            //console.log("Current Date:", day, month, year);
            //console.log("dateStatus:", dateStatus);


            var current;
            var remoteDir;
            var query;
            if (ImgElement === 'downloadedImage') {
               
                if (dateStatus === 0) {
                    current = lastFile;
                    remoteDir = remoteDir_r;
                    query = query_r;
                    //console.log(query);
                    //console.log(remoteDir)
                } else if (dateStatus === 1) {
                    remoteDir = "/home/jamming2023/Data/Jamming/" + dbname + "/L1/Spectrum/Red/" + year + "/" + year + "-" + month + "-" + day + "/";
                    query = `SELECT * FROM L1_Data WHERE Date = '${year}-${month}-${day}' AND (RFI_Level = 'Red' OR RFI_Level = 'Red (Intentional)') ORDER BY id DESC LIMIT 1;`;
                    current = file_name_current;
                    if (current === '..') {
                        current = '0';  
                    }
                    //console.log(query);
                    //console.log(current)
                }
            } else if (ImgElement === 'downloadedImage2') {
                
                if (dateStatus === 0) {
                    current = lastFile_y;
                    remoteDir = remoteDir_y;
                    query = query_y;
                    //console.log(query);
                    //console.log(remoteDir)
                }  else if (dateStatus === 1) {
                    remoteDir = "/home/jamming2023/Data/Jamming/" + dbname + "/L1/Spectrum/Yellow/" + year + "/" + year + "-" + month + "-" + day + "/";
                    query = `SELECT * FROM L1_Data WHERE Date = '${year}-${month}-${day}' AND (RFI_Level = 'Yellow' OR RFI_Level = 'Yellow (Intentional)') ORDER BY id DESC LIMIT 1;`;
                    current = file_name_current_y;
                    if (current === '..') {
                        current = '0';  
                    }
                    //console.log(query);
                    //console.log(current)
                }
            } else if (ImgElement === 'downloadedImage3') {
                
                if (dateStatus === 0) {
                    current = lastFile_g;
                    remoteDir = remoteDir_g;
                    query = query_g;
                    //console.log(query);
                    //console.log(remoteDir)
                }  else if (dateStatus === 1) {
                    remoteDir = "/home/jamming2023/Data/Jamming/" + dbname + "/L1/Spectrum/Green/" + year + "/" + year + "-" + month + "-" + day + "/";
                    query = `SELECT * FROM L1_Data WHERE Date = '${year}-${month}-${day}' AND (RFI_Level = 'Green') ORDER BY id DESC LIMIT 1;`;
                    current = file_name_current_g;
                    if (current === '..') {
                        current = '0';  
                    }
                    //console.log(query);
                    //console.log(current)
                }
            }
    
            //console.log(current)

            $.ajax({
                type: "POST",
                url: "check_updates.php",
                data: { current: current, remoteDir: remoteDir },
                async: true,
                success: function (response) {
                    if (response !== current) {
                        if (response !== '..' && response !== '0'){
                        var targetLastFile;
                        if (ImgElement === 'downloadedImage') {
                            lastFile = response;
                            file_name_current = response;
                            targetLastFile = lastFile;
                            //console.log('r',lastFile)
                        } else if (ImgElement === 'downloadedImage2') {
                            lastFile_y = response;
                            file_name_current_y = response;
                            targetLastFile = lastFile_y;
                            //console.log('y',lastFile_y)
                        } else if (ImgElement === 'downloadedImage3') {
                            lastFile_g = response;
                            file_name_current_g = response;
                            targetLastFile = lastFile_g;
                            //console.log('g',lastFile_g)
                        }
                        $.ajax({
                            url: 'powerpeak_updates.php',
                            type: 'GET',
                            data: { query: query, dbname: dbname },
                            async: true,
                            success: function (response) {
                                var powerPeakValue = response.replace(/"/g, ''); // Remove all double quotes
                                //console.log('Power mean Value:', powerPeakValue);

                                //console.log('File Path:', targetLastFile);
                                var remoteFilePath = remoteDir + targetLastFile;
                                //console.log('Remote File Path:', remoteFilePath);
                                var localFilePath = remoteDir2 + targetLastFile;
                                //console.log('Local File Path:', localFilePath);


                                fetch('download_sftp_file.php?remoteFilePath=' + encodeURIComponent(remoteFilePath) +
                                    '&localFilePath=' + encodeURIComponent(localFilePath))
                                    .then(function (response) {
                                        return response.text();
                                    })
                                    .then(function (data) {
                                        //console.log('Received Image Path:', data);
                                        if (ImgElement === 'downloadedImage') {
                                            if (dateStatus === 0) {
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'r_n.jpg') {
                                                    $('#new4_r').show();
                                                    $('#' + ImgElement_n).attr('src', data);        
                                                    $('#new4_r .close-icon').on('click', function() {
                                                    $('#new4_r').hide();                                                    
                                                        });
                                                    //console.log(1)
                                                } else {
                                                    //console.log(0)
                                                    $('#new4_r').show();
                                                    $('#' + ImgElement).attr('src', data);
                                                    $('#' + ImgElement_n).attr('src', data);
                                                    $('#new4_r .close-icon').on('click', function() {
                                                    $('#new4_r').hide();                                                
                                                        });
                                                }
                                            }
                                            else{
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'r_n.jpg') {
                                                    $('#alerttext_r').css('color', 'red').addClass('bounce');                                                               
                                                    //console.log(1)
                                                } else {
                                                    //console.log(0)
                                                    $('#alerttext_r').css('color', 'red').addClass('bounce');  
                                                }
                                            }
    
                                        } else if (ImgElement === 'downloadedImage2') {
                                            if (dateStatus === 0) {                                            
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'y_n.jpg') {
                                                    $('#new4_y').show();
                                                    $('#' + ImgElement_n).attr('src', data);                                                    
                                                    $('#new4_y .close-icon').on('click', function() {
                                                    $('#new4_y').hide();                                                
                                                        });
                                                    //console.log(1)
                                                } else {
                                                    //console.log(0)
                                                    $('#new4_y').show();
                                                    $('#' + ImgElement).attr('src', data);
                                                    $('#' + ImgElement_n).attr('src', data);                                                    
                                                    $('#new4_y .close-icon').on('click', function() {
                                                    $('#new4_y').hide();                                                    
                                                        });
                                                }
                                            }
                                            else{
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'y_n.jpg') {
                                                    $('#alerttext_y').css('color', 'red').addClass('bounce');                                                               
                                                    //console.log(1)
                                                } else {
                                                    //console.log(0)
                                                    $('#alerttext_y').css('color', 'red').addClass('bounce');  
                                                }
                                            }

                                        } else if (ImgElement === 'downloadedImage3') {
                                            if (dateStatus === 0) {
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'g_n.jpg') {
                                                    $('#new4_g').show();
                                                    $('#' + ImgElement_n).attr('src', data);                                                    
                                                    $('#new4_g .close-icon').on('click', function() {
                                                    $('#new4_g').hide();
                                                        });
                                                    //console.log(1)
                                                } else {                                                
                                                    $('#new4_g').show();
                                                    $('#' + ImgElement).attr('src', data);
                                                    $('#' + ImgElement_n).attr('src', data);                                                    
                                                    $('#new4_g .close-icon').on('click', function() {
                                                    $('#new4_g').hide(); 
                                                    //console.log(0)
                                                        });
                                                }
                                            }
                                            else{
                                                if ($('#' + ImgElement_n).css('display') !== 'none' && $('#' + ImgElement_n).attr('src') == 'g_n.jpg') {
                                                    $('#alerttext_g').css('color', 'red').addClass('bounce');                                                               
                                                    //console.log(1)
                                                } else {
                                                    //console.log(0)
                                                    $('#alerttext_g').css('color', 'red').addClass('bounce');  
                                                }
                                            }
                                        }
                                    });
                                    if (dateStatus === 0) {
                                    // Set a delay of 1 second before updating the #powerPeak element
                                    setTimeout(function () {
                                        $('#' + powerPeakElement).text('Average mean: ' + powerPeakValue + ' dB');
                                    }, 1000);
                                    }
                            }
                        });
                    }
                    }
                }
            });
        }

        function setupUpdateCheck(remoteDir2, powerPeakElement, ImgElement,ImgElement_n) {
            setInterval(function () {
                checkForUpdates(remoteDir2, powerPeakElement, ImgElement,ImgElement_n);
            }, 5000);
        }

        // red
        setupUpdateCheck(remoteDir2, 'powerPeak', 'downloadedImage','downloadedImage_r');
        // yellow
        setupUpdateCheck(remoteDir2_y, 'powerPeak2', 'downloadedImage2','downloadedImage_y');
        // Green
        setupUpdateCheck(remoteDir2_g, 'powerPeak3', 'downloadedImage3','downloadedImage_g');
    });

    
</script>
