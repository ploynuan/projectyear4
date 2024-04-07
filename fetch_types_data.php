<?php 
    require 'vendor/autoload.php'; 
    use phpseclib3\Net\SFTP;
    // Include necessary files and establish a database connection
    if(isset($_POST["starttime3"]))
    {
        $date_selected2 = $_POST["starttime3"];
        $date_selected_array2 = explode('-', $date_selected2);
    }
    
    $config = require 'config.php';

    $sftp_user = $config['sftp_user'];
    $sftp_pass = $config['sftp_pass'];
    $sftp_server = $config['sftp_server'];
    $sftp_port = $config['sftp_port'];
    
    
    function deleteFilesInDirectory($directory) {
        $files = glob($directory . '*'); 
        foreach($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }
    date_default_timezone_set('Asia/Bangkok');
    $date = date("Y-m-d");
    $date2 = date("d/m/Y");
    $year = date("Y");
    
    if(empty($_POST['starttime3'])&&empty($_POST['endtime3'])&&empty($_POST['stationname3'])&&empty($_POST['level'])){
        ?><P class="noinfo">Please Select Station Level and Datetime </P><?php
    }
    elseif(
        (isset($_POST['starttime3']) && ($_POST['starttime3'] !== '')) &&
        (isset($_POST['endtime3']) && ($_POST['endtime3'] !== '')) &&
        (isset($_POST['stationname3']) && ($_POST['stationname3'] !== '')) &&
        (isset($_POST['level'])&&($_POST['level']!==''))&&
        (strtotime($_POST['starttime3']) >  strtotime($_POST['endtime3'])) 
    ) {   
        echo "<script>alert('Start date must come before the end date');</script>";
    }
    elseif(
        (isset($_POST['starttime3']) && ($_POST['starttime3'] !== '')) &&
        (isset($_POST['endtime3']) && ($_POST['endtime3'] !== '')) &&
        (isset($_POST['stationname3']) && ($_POST['stationname3'] !== '')) &&
        (isset($_POST['level'])&&($_POST['level']!==''))&&
        (($_POST['starttime3']) > $date) &&  
        (($_POST['endtime3']) < $date)    
    ) {   
        echo "<script>alert('Please select start date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime3']) && ($_POST['starttime3'] !== '')) &&
        (isset($_POST['endtime3']) && ($_POST['endtime3'] !== '')) &&
        (isset($_POST['stationname3']) && ($_POST['stationname3'] !== '')) &&
        (isset($_POST['level'])&&($_POST['level']!==''))&&
        (($_POST['starttime3']) < $date) &&  
        (($_POST['endtime3']) > $date)       
    ) {   
        echo "<script>alert('Please select date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime3']) && ($_POST['starttime3'] !== '')) &&
        (isset($_POST['endtime3']) && ($_POST['endtime3'] !== '')) &&
        (isset($_POST['stationname3']) && ($_POST['stationname3'] !== '')) &&
        (isset($_POST['level'])&&($_POST['level']!==''))&&
        (($_POST['starttime3']) > $date) &&  
        (($_POST['endtime3']) > $date)       
    ) {   
        echo "<script>alert('Please select date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime3']) && ($_POST['starttime3'] !== '')) &&
        (isset($_POST['endtime3']) && ($_POST['endtime3'] !== '')) &&
        (isset($_POST['stationname3']) && ($_POST['stationname3'] !== '')) &&
        (isset($_POST['level'])&&($_POST['level']!==''))
    ) {   
        
        $starttime3=$_POST['starttime3'];
        $endtime3=$_POST['endtime3'];
        $dbname = $_POST['stationname3'];
        $level3=$_POST['level'];
        include_once('connect.php'); 
        if($_POST['level'] !== "All"){
            $query = "SELECT * FROM L1_Data WHERE Date BETWEEN '$starttime3' AND '$endtime3' AND (`RFI_Level` = '$level3' OR `RFI_Level` = '$level3 (Unintentional)')";
        }
        elseif ($_POST['level'] == "All") {
            $query = "SELECT * FROM L1_Data WHERE Date BETWEEN '$starttime3' AND '$endtime3'";
        }
        $query_run=mysqli_query($conn,$query);
        $results = mysqli_num_rows($query_run);
        if(mysqli_num_rows($query_run)>0)
        {   
            ?><div class="result"> <?php echo "About $results results";?></div>
            <div class="show3" style="overflow-x: auto;">
                
                <table>
                <thead>
                    <tr>
                        <th class="date-header" style="height: 70px";>Date</th>
                        <th style="height: 70px";>Start Time</th>
                        <th style="height: 70px";>End Time</th>
                        <th style="height: 70px";>Total Time (Sec)</th>
                        <th style="height: 70px";>RFI Level</th>
                        <th class="spectrum-header"style="height: 70px";>CCTV</th>
                    </tr>
                    </thead>
                    <tbody>
                <?php
                $sftp = new SFTP($sftp_server, $sftp_port);
                if (!$sftp->login($sftp_user, $sftp_pass)) {
                    exit('Login Failed');
                } 
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $folderRFI = "RFI/";
                    deleteFilesInDirectory($folderRFI);                 
                    foreach($query_run as $row)
                    {
                    ?>
                        <tr>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Date'];?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Start_Time'];?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['End_Time'];?></td>
                        <td style="border: 1px solid #e8e6e2;"  data-image-time="<?php echo $row['CCTV_Time'];?>"><?php echo number_format($row['Total_Time'], 2); ?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['RFI_Level'];
                            $rfiLevel = $row['RFI_Level'];
                                if (strpos($rfiLevel, ' ') !== false) {
                                    $rfiParts = explode(' ', $rfiLevel); 
                                    $rfi = $rfiParts[0];
                                } else {
                                    $rfi = $rfiLevel; 
                                } 
                            ?>
                        </td>
                        <td style="border: 1px solid #e8e6e2;">
                            <?php
                                $dbname = str_replace(' ', '_', $dbname);
                                $remote_file_path = "/home/jamming2023/Data/Jamming/" . $dbname . "/L1/Camera/" . $date_selected_array2[0] . "/" . $row['Date'] . "/" . $row['CCTV_Time'] . ".jpg"; 
                                // echo  $remote_file_path;
                                if ($sftp->file_exists($remote_file_path)) {
                                    ?><a href="javascript:void(0);" class="showImageBtn point-cursor" ?>
                                            <i class="fa-solid fa-image"></i>
                                        </a><?php
                                     }
                                else {
                                    echo '<p>-</p>';
                                } 
                            ?>
                        </td>
                        </tr>
                        <?php
                    }
                }
                ?></tbody>
                </table>
        </div><?php
            }
        
        else
        {
            
            ?><div class="result"> <?php echo "About $results results";?></div>
            <P class="noinfo">No information in this period time</P><?php
        }                     
    }
    
    else{
        ?><P class="noinfo">Please ensure that all options are selected </P><?php
    }
    ?>
    
    <script>
        var dbname_t = "<?php echo $dbname; ?>";
        var dateSelected_t = "<?php echo $date_selected_array2[0]; ?>";

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('fa-image')) {
                var date_t = event.target.closest('tr').querySelector('td:nth-child(1)').innerText;
                var startTime = event.target.closest('tr').querySelector('td:nth-child(2)').innerText;
                var endTime = event.target.closest('tr').querySelector('td:nth-child(3)').innerText;
                var rfi = event.target.closest('tr').querySelector('td:nth-child(5)').innerText;
                var imageTime = event.target.closest('tr').querySelector('td[data-image-time]').getAttribute('data-image-time');
        
           // console.log("Image Time: " + imageTime);

                var remoteFilePath, localFilePath;
                if (rfi.includes(' ')) {
                    var rfiParts = rfi.split(' ');
                    rfi = rfiParts[0];}
                else {}

                remoteFilePath = "/home/jamming2023/Data/Jamming/" + dbname_t + "/L1/Camera/" + dateSelected_t + "/" + date_t + "/" + imageTime + ".jpg"
                localFilePath = "RFI/" + imageTime + ".jpg"

                //.log('Remote File Path:', remoteFilePath);
                //console.log('Local File Path:', localFilePath);

                fetch('download_sftp_file.php?remoteFilePath=' + encodeURIComponent(remoteFilePath) + 
                '&localFilePath=' + encodeURIComponent(localFilePath))
                    .then(function(response) {
                        return response.text();
                    })
                    .then(function(data) {
                        //console.log('Received imagePath:', data);
                        const imagePopup = document.getElementById("imagePopup");
                        const popupImage = document.getElementById("popupImage");
                        const downloadBtn = document.getElementById("downloadBtn");
                        const closeBtn = document.getElementById("closeBtn");

                        // Assuming imagePath is the URL to the image
                        popupImage.src = data;
                        downloadBtn.setAttribute("href", data);
                        imagePopup.style.display = "block";

                        closeBtn.addEventListener("click", function() {
                            imagePopup.style.display = "none";
                        });
                    })
                    .catch(function(error) {
                        //console.error('Error fetching data:', error);
                    });
            }
        });
    </script>
    



   

