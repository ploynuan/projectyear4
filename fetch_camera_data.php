<?php
require 'vendor/autoload.php'; 
use phpseclib3\Net\SFTP;
// Include necessary files and establish a database connection
if(isset($_POST["datetime4"]))
    {
        $date_selected = $_POST["datetime4"];
        $date_selected_array = explode('-', $date_selected);
    }

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
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
}
    date_default_timezone_set('Asia/Bangkok');
        $date_c = date("Y-m-d");
        $date2_c = date("d/m/Y");
        $year = date("Y");

    if(isset($_POST['datetime4'])&&empty($_POST['stationname4'])){
        ?><P class="noinfo">Please Select Station  </P> <?php
    }
    elseif(isset($_POST['stationname4'])&&empty($_POST['datetime4'])){
        ?><P class="noinfo">Please Select datetime4 </P> <?php
    }
    elseif(empty($_POST['stationname4'])&&empty($_POST['datetime4'])){
        ?><P class="noinfo">Please Select Station and Frequency band </P> <?php
    }
    elseif(isset($_POST['datetime4'])&&isset($_POST['stationname4'])&&(($_POST['datetime4']) > $date_c)) 
    {
        echo "<script>alert('Please select end date within the current dates');</script>";
    }
    elseif(isset($_POST['datetime4'])&&isset($_POST['stationname4'])) 
    {
        $starttime = $_POST['datetime4'];
        $dbname = $_POST['stationname4'];
        $remote_dir_current_c = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Camera/" . $year . "/" . $date_c . "/";
        // echo $remote_dir_current_c;
        include_once('connect.php'); 
        ?>
        <div class="showcam">
            <?php
            $query = "SELECT * FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Red' OR `RFI_Level` = 'Yellow')";
            $query_run = mysqli_query($conn, $query);
            $results = mysqli_num_rows($query_run);
            $query2 = "SELECT * FROM L1_Data WHERE (`RFI_Level` = 'Red' OR `RFI_Level` = 'Yellow') ORDER BY id DESC LIMIT 1;";
            $query_run2 = mysqli_query($conn, $query2);
            if ($query_run2 && mysqli_num_rows($query_run2) > 0) {
                $row = mysqli_fetch_assoc($query_run2);
                $lastId = $row['id'];
                // echo $lastId;
                }?>
                
            <div class="result2"> <?php echo "About $results results";?></div>
            <?php
                
            ?>

            <div class="showpiccam">
                <?php
                $remote_dir = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/camera/" . $date_selected_array[0] . "/" . $starttime . "/";
                $remote_dir2 = "camera/";
                deleteFilesInDirectory($remote_dir2);
                if ($results > 0) {
                    $downloadResult = downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_c);
                        // JavaScript variables
                        echo '<script>';
                        echo 'var lastFile_c = "' . ($downloadResult['file_name'] ?? '') . '";';
                        echo 'var file_name_current_c = "' . ($downloadResult['file_name_current'] ?? '') . '";';
                        echo 'var remoteDir_c = "' . $remote_dir . '";';
                        echo 'var remoteDir2_c = "' . $remote_dir2 . '";';
                        echo 'var query_c = "' . $query . '";';
                        echo 'var dbname_c = "' .  $dbname . '";';
                        echo 'var starttime_c = "' .  $starttime . '";';
                        echo 'var lastid_current = "' .  $lastId . '";';
                        echo 'var status = "1";';
                        echo '</script>';
                            if ($downloadResult ) {
                                echo '<div class="showpiccam">
                                    <div class="new">
                                        <p>NEW !</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <div class="new2">
                                        <p>'.$date2_c.' </br>has new data</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <img src="' . $downloadResult['file_path'] . '" alt="Downloaded File" class="showcam">     
                                    </div>';
                            }
                        }
                    else{
                        $downloadResult = @downloadAndUploadFile($remote_dir, $sftp_user, $sftp_pass, $sftp_server, $sftp_port, $remote_dir2, $sftp_user2, $sftp_pass2, $sftp_server2, $sftp_port2, $remote_dir_current_c);
                        // JavaScript variables
                        echo '<script>';
                        echo 'var lastFile_c = "0";';
                        echo 'var file_name_current_c = "' . ($downloadResult['file_name_current'] ?? '0') . '";';
                        echo 'var remoteDir_c = "' . $remote_dir . '";';
                        echo 'var remoteDir2_c = "' . $remote_dir2 . '";';
                        echo 'var query_c = "' . $query . '";';
                        echo 'var dbname_c = "' .  $dbname . '";';
                        echo 'var starttime_c = "' .  $starttime . '";';
                        echo 'var lastid_current = "' .  $lastId . '";';
                        echo 'var lastid_c = "' .  $lastId . '";';
                        echo 'var status = "0";';
                        echo '</script>';
                        echo '<div class="showpiccam">
                                    <div class="new">
                                        <p>NEW !</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <div class="new2">
                                        <p>'.$date2_c.' </br>has new data</p>
                                        <span class="material-symbols-outlined close-icon">close</span>
                                    </div>
                                    <img src="cctv_pur.jpg" alt="Downloaded File" class="showcam">     
                                    </div>';
                    }
                        ?>
                        
                
            </div>
            <div class="showhis">
                <div class="hisdata">
                    <div class="hisdate">
                        <label for="historyDropdown">History :</label>
                        <select id="historyDropdown" style="font-size: clamp(12px, 2vw, 13px); height:30px;text-align: center;">
                            <?php
                            if ($results > 0) {
                                $rows = array();
                                while ($row = mysqli_fetch_assoc($query_run)) {
                                    $rows[] = $row;
                                }

                                $rows_reversed = array_reverse($rows);
                                $lastid = end($rows)['id'];
                                echo '<script>';
                                echo 'var lastid_c = "' .  $lastid . '";';
                                echo '</script>';

                                foreach ($rows_reversed as $row) {
                                    echo "<option value='{$row['CCTV_Time']}'>{$row['CCTV_Time']}</option>";
                                }
                            } else {
                                echo "<option value='{$row['CCTV_Time']}'>---- -- -- ---------</option>";                                        
                            }
                            ?>
                        </select>
                    </div>
                    <div class="download">
                        <button id="downloadButton" class="btn-success2">Download</button>
                    </div>
                </div>
            </div>

            <?php
    }?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $('.new').hide();
        $('.new p ').hide();
        $('.new2').hide();
        $('.new2 p ').hide();
    </script>
<script>
   var dbname_c = "<?php echo $dbname; ?>";
    var date_c = "<?php echo $starttime; ?>";
    var lastFile_c;
    var remoteDir_c;
    var lastid_c;
    var starttime_c; 
    var remoteDir2_c;
    var current_c;


    function checkForUpdates_c() {

        // console.log(date_c);
        var currentDate_c = new Date();
        currentDate_c.setHours(0, 0, 0, 0); // ตั้งเวลาให้เป็นเที่ยงคืน

        var dateStatus_c = (new Date(date_c) < currentDate_c) ? 1 : 0; // 0 =current
        var day_c = currentDate_c.getDate();
        var month_c = currentDate_c.getMonth() + 1; // เพิ่ม 1 เนื่องจาก getMonth() คืนค่าเดือนที่เริ่มจาก 0 (มกราคม = 0)
        var year_c = currentDate_c.getFullYear();

        // เติม 0 ข้างหน้าเลขวันและเดือน ถ้ามีหลักเดียว
        day_c = (day_c < 10) ? '0' + day_c : day_c;
        month_c = (month_c < 10) ? '0' + month_c : month_c;

        // console.log("Current Date:", day_c, month_c, year_c);
        //console.log("dateStatus:", dateStatus_c);
        if (dateStatus_c === 0) {
            current_c = lastFile_c;
            remoteDir_c = remoteDir_c;
            lastid_c = lastid_c;
            starttime_c =starttime_c;
            status=status;

        } else {
            remoteDir_c = "/home/jamming2023/Data/Jamming/" + dbname_c + "/L1/Camera/" + year_c + "/" + year_c + "-" + month_c + "-" + day_c + "/";
            current_c = file_name_current_c;
            lastid_c = lastid_current;
            starttime_c = year_c + "-" + month_c + "-" + day_c;
            status=status;
        
        }
        //console.log(lastid_c)
        // console.log(starttime_c)
        // console.log(lastFile_c)
        //console.log(current_c);
        //console.log(status)
        // console.log(remoteDir_c)
        $.ajax({
            type: "POST",
            url: "check_updates_cam.php",
            data: { current_c: current_c, remoteDir: remoteDir_c },
            success: function (response) {
                if (response !== current_c) {
                    //console.log(response)
                        if (response !== '..' ){
                        lastFile_c = response;
                        file_name_current_c = response;
                        //console.log('change');
                        //console.log(lastFile_c);
                        $.ajax({
                            url: 'dropdown_updates.php',
                            type: 'GET',
                            data: { lastid_c: lastid_c, dbname_c: dbname_c, starttime_c: starttime_c },
                            success: function (data) {
                                if (data.new_data) {
                                    lastid_c = data.last_id;
                                    lastid_current = data.last_id;

                                    var newOptionsHTML = "";
                                    data.new_records.forEach(function (record) {
                                        newOptionsHTML += `<option value='${record['CCTV_Time']}'>${record['CCTV_Time']}</option>`;
                                    });
                                    
                                    
                                    //console.log('file:', lastFile_c);
                                    var remoteFilePath_c = remoteDir_c + lastFile_c;
                                    //console.log('remotefile:', remoteFilePath_c);
                                    var localFilePath_c = remoteDir2_c + lastFile_c;
                                    //console.log('localfile:', localFilePath_c);
                                    //console.log(data.total_rows);
                                    var resultsupdates_c = data.total_rows; // เก็บค่าจาก data.total_rows
                                    if (dateStatus_c === 0) {
                                    $("#historyDropdown").prepend(newOptionsHTML);
                                    setTimeout(function () {
                                        if (status == '0') {
                                            var latestImagetime = data.new_records[data.new_records.length - 1]['CCTV_Time'];
                                            // ลบค่าที่มีอยู่
                                            $("#historyDropdown option").remove();

                                            // เพิ่มค่าใหม่ลงใน dropdown
                                            $("#historyDropdown").prepend(`<option value="${latestImagetime}">${latestImagetime}</option>`);
                                            //console.log(11111)
                                            status = 1
                                         }else{
                                        $("#historyDropdown").val(data.new_records[data.new_records.length - 1]['CCTV_Time']);
                                    }}, 1500);
                                    setTimeout(function () {
                                        $(".result").text("About " + resultsupdates_c + " results");
                                    }, 1500);
                                    }
                                    fetch('download_sftp_file.php?remoteFilePath=' + encodeURIComponent(remoteFilePath_c) +
                                        '&localFilePath=' + encodeURIComponent(localFilePath_c))
                                        .then(function (response) {
                                            return response.text();
                                        })
                                        .then(function (data) {
                                            //console.log('ได้รับที่อยู่รูปภาพ:', data);
                                            if (dateStatus_c === 0) {
                                                $('.new').show(); // แสดง element ที่มี class .new
                                                $('.new p').show(); // แสดง element ที่มี class .new
                                                $('.showpiccam img').attr('src', data);
                
                                                $('.new .close-icon').on('click', function() {
                                                $('.new').hide();
                                                $('.new p').hide();
                                                    });}
                                            else{
                                                $('.new2').show(); // แสดง element ที่มี class .new
                                                $('.new2 p').show(); // แสดง element ที่มี class .new
                                                 
                                                $('.new2 .close-icon').on('click', function() {
                                                $('.new2').hide();
                                                $('.new2 p').hide();
                                                    });
                                            }
                                            
                                        });

                                }
                            }
                        });
                    }
                }
            }
        });
    }

    function setupUpdateCheck_c() {
        setInterval(function () {
            // เรียกใช้ฟังก์ชันตรวจสอบอัปเดตทุก 5 วินาที
            checkForUpdates_c();
        }, 5000);
    }

    setupUpdateCheck_c();
</script>

<script>
    var dbname_c = "<?php echo $dbname; ?>";
    var dateSelected_c = "<?php echo $date_selected_array[0]; ?>";
    var date_c = "<?php echo $starttime; ?>";
    var imgElement_c;
    var timer_c;
    var historyDropdown = document.getElementById('historyDropdown');

    if (historyDropdown) {
    historyDropdown.addEventListener('change', function (event) {
        var selectedOption = event.target.value;
        remoteFilePath_c = "/home/jamming2023/Data/Jamming/" + dbname_c + "/L1/camera/" + dateSelected_c + "/" + date_c + "/" + selectedOption + ".jpg"
        localFilePath_c = "camera/" + selectedOption + ".png"
        //console.log('Remote File Path:', remoteFilePath_c);
        //console.log('Local File Path:', localFilePath_c);

        fetch('download_sftp_file.php?remoteFilePath=' + encodeURIComponent(remoteFilePath_c) +
                '&localFilePath=' + encodeURIComponent(localFilePath_c))
            .then(function (response) {
                return response.text();
            })
            .then(function (data) {
                imgElement_c = document.querySelector('.showpiccam img');
                
                if (imgElement_c) {
                    $('.new').hide();
                    $('.new p').hide();
                    $('.showpiccam img').attr('src', data);
                    //console.log('Local File Path:', localFilePath_c);
                }
            });
    });
}

    document.getElementById('downloadButton').addEventListener('click', function () {
        // นิยาม imgElement ที่ระดับนี้ เพื่อให้สามารถใช้งานได้
        imgElement_c = document.querySelector('.showpiccam img');

        if (imgElement_c) {
            var currentImagePath = imgElement_c.src;
            // console.log(currentImagePath)
            var selectedOption = document.getElementById('historyDropdown').value;
            var downloadFilename = selectedOption;
            var downloadLink = document.createElement('a');
            downloadLink.href = currentImagePath;
            downloadLink.download = downloadFilename;
            downloadLink.click();
        }
    });
</script>
