<?php
    require 'vendor/autoload.php'; 
    use phpseclib3\Net\SFTP;
   
    session_start(); 
    if (!isset($_SESSION["uname"])) {
        header("Location:index.php");
        die;
    }
    if(isset($_POST["sign_out"]))
    {   unset($_SESSION["uname"]);
        header("Location:index.php");
        $folderRFI = "RFI/";
        deleteFilesInDirectory($folderRFI);
        die;
    }

    // spectrum
    
    if(isset($_POST["datetime"]))
    {
        $date_selected = $_POST["datetime"];
        $date_selected_array = explode('-', $date_selected);
    }
    if(isset($_POST["datetime4"]))
    {
        $date_selected4 = $_POST["datetime4"];
        $date_selected_array4 = explode('-', $date_selected4);
    }
   
    if(isset($_POST["starttime3"]))
    {
        $date_selected2 = $_POST["starttime3"];
        $date_selected_array2 = explode('-', $date_selected2);
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    function submitForm() {
        var stationname1 = $("select[name='stationname1']").val();
        var datetime = $("input[name='datetime']").val();
        $("#spectrumresult").hide();
        $(".loader-div1").show();

        $.ajax({
            url: 'fetch_spectrum_data.php',
            type: 'POST',
            data: { stationname1: stationname1, datetime: datetime },
            success: function(response) {
                $("#spectrumresult").show();
                $(".loader-div1").hide();
                $("#spectrumresult").html(response).addClass('bounce');
            },
            error: function() {
                $(".loader-div1").hide();
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    }
    </script>

    <script>
    function submitForm2() {  
        var stationname2 = $("select[name='stationname2']").val();
        var starttime2 = $("input[name='starttime2']").val();
        var endtime2 = $("input[name='endtime2']").val();
        $("#reportResult").hide();
        $(".loader-div2").show();

        $.ajax({
            url: 'fetch_report_data.php', 
            type: 'POST',
            data: { stationname2: stationname2, starttime2: starttime2, endtime2: endtime2 },
            success: function(response) {
                $("#reportResult").show();
                $(".loader-div2").hide();
                $("#reportResult").html(response).addClass('bounce'); 
            },
            error: function() {
                $(".loader-div2").hide();
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    }
    </script>

    <script>
    function submitForm3() {
        var stationname3 = $("select[name='stationname3']").val();
        var level = $("select[name='level']").val();
        var starttime3 = $("input[name='starttime3']").val();
        var endtime3 = $("input[name='endtime3']").val();
        $("#typesResult").hide();
        $(".loader-div3").show();

        $.ajax({
            url: 'fetch_types_data.php', 
            type: 'POST',
            data: { stationname3: stationname3, level: level, starttime3: starttime3, endtime3: endtime3 },
            success: function(response) {
                $("#typesResult").show();
                $(".loader-div3").hide();
                $("#typesResult").html(response).addClass('bounce');     
            },
            error: function() {
                $(".loader-div3").hide();
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');  
            }
        });
    }
    </script>
    <script>
    function submitForm4() {
        var stationname4 = $("select[name='stationname4']").val();
        var datetime4 = $("input[name='datetime4']").val();
        $("#camResult").hide();
        $(".loader-div4").show(); // แสดง loader ก่อนที่จะทำ AJAX request

        $.ajax({
            url: 'fetch_camera_data.php',
            type: 'POST',
            data: { stationname4: stationname4, datetime4: datetime4 },
            success: function(response) {
                $("#camResult").show();
                $(".loader-div4").hide(); // ซ่อน loader เมื่อโหลดข้อมูลเสร็จสมบูรณ์
                $("#camResult").html(response).addClass('bounce');
            },
            error: function() {
                $(".loader-div4").hide(); // ซ่อน loader ในกรณีที่เกิดข้อผิดพลาด
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    }
</script>

<script>
    function submitForm5() {
        var stationname5 = $("select[name='stationname5']").val();
        $("#statusresult").hide();
        $(".loader-div5").show(); // แสดง loader ก่อนที่จะทำ AJAX request

        $.ajax({
            url: 'fetch_status_data.php',
            type: 'POST',
            data: { stationname5: stationname5},
            success: function(response) {
                $("#statusresult").show();
                $(".loader-div5").hide(); // ซ่อน loader เมื่อโหลดข้อมูลเสร็จสมบูรณ์
                $("#statusresult").html(response).addClass('bounce');
            },
            error: function() {
                $(".loader-div5").hide(); // ซ่อน loader ในกรณีที่เกิดข้อผิดพลาด
                alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            }
        });
    }
</script>

    <title>GNSS RFI Level Detection</title>
</head>

<body>
    <header class="main">
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
        <div class=container>
            <!-- <div class="circle"></div>
            <div class="circle2"></div> -->
            <div class="header-info">
                <div class="infokmitl">
                    <h1 >GNSS RFI Level Detection</h1>
                    <h3>King Mongkut's Institute of Technology Ladkrabang</h3>
                    <h3>Telecommunications and Network Engineering , Faculty of Engineering</h3>
                    <p>664C01</p>
                </div>
                <div class="imgearth">
                    <img src="earth.png" alt="">
                </div>     
            </div>
            <nav>
                <div class="nav-bar">
                    <span class="logo">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <a href="https://www.kmitl.ac.th/" target="_blank">Kmitl</a>
                    </span>
                    <i class='bx bx-menu sidebarOpen' ></i>
                    <div class="menu">
                        <div class="logo-toggle">
                            <i class='bx bx-x siderbarClose'></i>
                        </div>
                        <ul class="nav-links">
                            <li><a href="#status">Status</a></li>
                            <li><a href="#spectrum">Spectral</a></li>
                            <li><a href="#report">Report</a></li>
                            <li><a href="#types">Types</a></li>
                            <li><a href="#camera">Camera</a></li>
                            <li><a href="#aboutuspage">About us</a></li>
                            <li>
                                <form action="" method="POST">
                                <input type="submit" class="btn-signout" value="Sign out" name="sign_out">
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <?php
        function isAllowedStation($uname) {
            $config = require 'configlogin.php';
            $servername = $config['servername'];
            $dbusername = $config['dbusername'];
            $dbpassword = $config['dbpassword'];
            $dbname = $config['dbname'];
            $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch the selected station if submitted
            $selected_station = isset($_POST['stationname1']) ? $_POST['stationname1'] : '';

            $query = "SELECT sa.station FROM station_access sa
                        INNER JOIN users u ON sa.user_id = u.id
                        WHERE u.username='$uname'";

            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $station = $row["station"];
                    $station_display = str_replace('_', ' ', $station);
            
                    if ($station === $selected_station) {
                        echo "<option value='$station' selected>$station_display</option>";
                    } else {
                        echo "<option value='$station'>$station_display</option>";
                    }
                }
            }
             else {
                echo "<option value='' disabled>No stations available</option>";
            }

            $conn->close();
        }
    ?>
    <!-- status -->
    <header class="page" id="status">
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
        <div class="containerstatus" >
            <center>
            <div class="status" >
                <div class="headerpage">
                    <form id="statusForm" name="status" id="form5">
                        
                        <p class="namehead">Status Equipment</p>
                        <div class="option4">
                            <span class="station" id="stationstatus">
                                <label for="stationname">Station :</label>
                                <select name="stationname5">
                                    <?php
                                    session_start();
                                    if(empty($_POST['stationname5'])) {
                                        echo "<option value='' disables='' selected=''>Select</option>";
                                    }

                                    if (isset($_SESSION['uname'])) {
                                        $uname = $_SESSION['uname'];
                                        isAllowedStation($uname);
                                    } else {
                                        echo "Username not provided.";
                                    }
                                    ?>
                                </select>
                                </span>
                            <div id ="submit">
                                    <button class="submit3" type="button" onclick="submitForm5()">Submit</button>
                            </div>
                        </div>
                        
                    </form>
                </div>
                <div class="loader-div5">
                    <img class="loader-img" src="loaderwhite.gif" />
                </div>
                <div id=statusresult ></div>
            </div>
            </center>
        </div>
    </header>     

    <!-- spectrum -->
    <header class="page" id="spectrum">
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
        <div class="containerspec" >
        <center>
            <div class="spectrum" >
                <div class="headerpage">
                    <form id="spectrumForm" name="spectrum" id="form1">
                       
                        <p class="namehead" style = color:>Latest Spectral</p>
                        <div class="option">
                            <div class="station" id="station">
                                <label for="stationname">Station :</label>
                                <select name="stationname1">
                                    <?php
                                    session_start();
                                    if(empty($_POST['stationname1'])) {
                                        echo "<option value='' disables='' selected=''>Select</option>";
                                    }

                                    if (isset($_SESSION['uname'])) {
                                        $uname = $_SESSION['uname'];
                                        isAllowedStation($uname);
                                    } else {
                                        echo "Username not provided.";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="datetime">
                                <div id ="datetime">
                                    <label for="datetime">Date : </label>
                                    <input type="date" id="datetime" name="datetime" value="<?php if(isset($_POST['datetime'])) {echo $_POST['datetime'];}?>" style="font-size: clamp(12px, 2vw, 13px);">
                                </div>
                                <div>
                                    <button class="submit" type="button" onclick="submitForm()">Submit</button>
                                </div>
                                
                            </div>
                        </div>
                         
                    </form>
                </div>
                <div class="loader-div1">
                    <img class="loader-img" src="load.gif" />
                </div>
                <div id=spectrumresult ></div>
            </div>
        </center> 
        </div>
    </header>     

    <!-- report -->
    <header class="page" id="report">
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
            <div class="headerpage">
                <form id="reportForm" name="report" id="form2">
                    <center>
                    <p class="namehead" >Jamming Report</p>
                    <div class="option3">
                        <div class="optionspilt31">
                            <div class="station" id="stationreport">
                                <label for="stationname">Station :</label>
                                <select name="stationname2">
                                    <?php
                                    session_start();
                                    if (empty($_POST['stationname2'])) {
                                        echo "<option value='' disabled selected>Select</option>";
                                    }

                                    if (isset($_SESSION['uname'])) {
                                        $uname = $_SESSION['uname'];
                                        // Call isAllowedStation function and pass the station name as a parameter
                                        isAllowedStation($uname);
                                    } else {
                                        echo "Username not provided.";
                                    }
                                    ?>
                                </select>

                            </div>
                            <div class="startdate2">
                                <label for="datetime">Start date : </label>
                                <input type="date" id="starttime2" name="starttime2" value="<?php if(isset($_POST['starttime2'])) {echo $_POST['starttime2'];}?>"style="font-size: clamp(12px, 2vw, 13px);">
                            </div>
                        </div>
                        <div class="optionspilt32">    
                            <div class="enddate2">
                                <label for="datetime" >End date : </label>
                                <input type="date" id="endtime2" name="endtime2" value="<?php if(isset($_POST['endtime2'])) {echo $_POST['endtime2'];}?>"style="font-size: clamp(12px, 2vw, 13px);">
                            </div>
                            <div class="submit">
                                <button class="submit" type="button" onclick="submitForm2()">Submit</button>
                            </div>
                        </div>
                    </div>
                    </center>  
                </form>  
            </div>
            <div class="loader-div2">
                <img class="loader-img" src="load.gif" />
            </div>
            <div id=reportResult ></div>
            </div> 
        
    </header>  
   <!-- types -->
   <header class="page" id="types" >
   <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
   <div class="spectrum" >
        <div class="headerpage">
            <form id="typesForm" name="types" id="form3">
                <center>
                <p class="namehead">Types</p>
                <div class="option2">
                    <div class="optionspilt1">
                        <div class="station">
                            <label for="stationname">Station :</label>
                            <select name="stationname3">
                                <?php
                                    session_start();
                                    if (empty($_POST['stationname3'])) {
                                        echo "<option value='' disabled selected>Select</option>";
                                    }

                                    if (isset($_SESSION['uname'])) {
                                        $uname = $_SESSION['uname'];
                                        // Call isAllowedStation function and pass the station name as a parameter
                                        isAllowedStation($uname);
                                    } else {
                                        echo "Username not provided.";
                                    }
                                ?>
                            </select>
                        </div>
                        
                        <div class="level">
                            <label for="level">RFL Level : </label>
                            <select name="level" >
                                <?php
                                    if(!isset($level)) {
                                        echo "<option value='' disables='' selected=''>Select</option>";
                                    }
                                    else {
                                        echo "<option value='$level' selected='' hidden>$level</option>";
                                    }
                                ?>
                                <option value="Red">Red</option>
                                <option value="Yellow">Yellow</option>
                                <option value="Green">Green</option>
                                <option value="All" style=" color: #c20d0d;" >All</option>
                            </select>
                        </div>
                    </div>
                    <div class="optionspilt2">
                        <div class="startdate3">
                            <label for="datetime">Start date : </label>
                            <input type="date" id="starttime3" name="starttime3" value="<?php if(isset($_POST['starttime3'])) {echo $_POST['starttime3'];}?>"style="font-size: clamp(12px, 2vw, 13px);">
                        </div>
                        <div class="enddate3" >
                            <label for="datetime">End date : </label>
                            <input type="date" id="endtime3" name="endtime3" value="<?php if(isset($_POST['endtime3'])) {echo $_POST['endtime3'];}?>"style="font-size: clamp(12px, 2vw, 13px);">
                        </div>
                    </div>
                    <div>
                        <button class="submit" type="button" onclick="submitForm3()">Submit</button>
                    </div>
                </div>
                </center>  
                </form>  
            </div>
        </div>
            <div class="loader-div3">
                <img class="loader-img" src="load.gif" />
            </div>
            <div id=typesResult ></div>
    
    </header> 
       
    <!-- camera -->
    <header class="page" id="camera" >
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>                                
        <div class="containercam" id="camera">
            <div class="camera" >
                <div class="headerpage">
                    <form id="cameraForm" name="camera" id="form4">
                        <center>
                        <p class="namehead" style = color:>Latest Footage Camera</p>
                        <div class="option1">
                            <div class="station">
                                <label for="stationname">Station :</label>
                                <select name="stationname4">
                                    <?php
                                    session_start();
                                    if(empty($_POST['stationname4'])) {
                                        echo "<option value='' disables='' selected=''>Select</option>";
                                    }

                                    if (isset($_SESSION['uname'])) {
                                        $uname = $_SESSION['uname'];
                                        isAllowedStation($uname);
                                    } else {
                                        echo "Username not provided.";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="datetime">
                                <div id ="datetime">
                                    <label for="datetime">Date : </label>
                                    <input type="date" id="datetime" name="datetime4" value="<?php if(isset($_POST['datetime4'])) {echo $_POST['datetime4'];}?>" style="font-size: clamp(12px, 2vw, 13px);">
                                </div>
                                <div>
                                    <button class="submit3" type="button" onclick="submitForm4()">Submit</button>
                                </div>
                                
                            </div>
                        </div>
                        </center>  
                    </form>
                </div>
                <div class="loader-div4">
                    <img class="loader-img" src="load.gif" />
                </div>
                <div id=camResult ></div>
            </div>
        </div>
    </header>     
    <!-- aboutus -->
    <header class="aboutus" id="aboutuspage">
    <a class="gotop" href="#"><i class="fa-solid fa-arrow-up" id="logoontop"></i></a>
    <div class="circle3"></div>
    <div class="circle4"></div>
        <div id="aboutus" class="name">
            <div class="uni">
                <p>About us</br></p>
                <div class="box"></div>
                Department of Telecomunications</br>
                Engineering</br>
                King Mongkut's Institure of Technology</br>
                Ladkrabang,</br>
                Bangkok, Thailand, 10520</br>
                Tel : 02-329-8324</br>
                Email : telecom@kmitl.ac.th
            </div>
            <div class="member">
                Made by</br>
                Pornnapas Ngampanitchayakit</br>
                Ploynuan Chanaboon</br>
                Paniprak Kakhong</br></br>
                Advisor : Dr. Jirapoom Budtho</br>
                Co-Advisor : Prof.Dr.Pornchai Supnithi
            </div>
        </div>
    </header>
    
    <div id="imagePopup" style="display: none;">
        <div class="show4">
            <button id="closeBtn" onclick="document.getElementById('imagePopup').style.display='none'"><i class="fa-solid fa-xmark"></i></button>
            <img id="popupImage" src="">
            <a id="downloadBtn" href="" download><i class="fa-solid fa-download"></i> Download</a>
        </div>
    </div>

	
	<script>
        // navbar
        const nav = document.querySelector("nav"),
        sidebarOpen = document.querySelector(".sidebarOpen"),
        sidebarClose = document.querySelector(".sidebarClose");

        sidebarOpen.addEventListener("click", () => {
            nav.classList.add("active");
        });

        document.body.addEventListener("click", e => {
            let clickedElm = e.target;

            if (!clickedElm.classList.contains("sidebarOpen") && !clickedElm.classList.contains("menu")) {
                nav.classList.remove("active");
            }
        });

        sidebarClose.addEventListener("click", () => {
            nav.classList.remove("active");
        });

    </script>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            const currentDate = new Date();
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                const year = currentDate.getFullYear();
                const month = (currentDate.getMonth() + 1).toString().padStart(2, '0'); 
                const day = currentDate.getDate().toString().padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                input.value = formattedDate;
            });
        });
    </script>
    
</body>
</html>