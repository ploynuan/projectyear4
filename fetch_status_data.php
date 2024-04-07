   <?php 
    if(empty($_POST['stationname5'])){
        ?><P class="noinfo">Please Select Station</P><?php
    }
    elseif(isset($_POST['stationname5']))  
    {
    
        $dbname = $_POST['stationname5'];
        include_once('connect.php'); 
        ?>
        <div class="showstatus"><?php
            $query = "SELECT * FROM Devices ORDER BY id DESC LIMIT 1;";
            $query_run=mysqli_query($conn,$query);
            if(mysqli_num_rows($query_run) > 0) {
                $rows = array();
                while($row = mysqli_fetch_assoc($query_run)) {
                    $rows[] = $row;
                }
                    $lastid_status = end($rows)['id'];
                    // echo $lastid_status;
                
                foreach ($query_run as $row) {
                    ?>
                    <table class="tablestatus" style="max-width: 500px; width: 80%;background: none">
                        <tr>
                            <th style="height: 38px; width: 200px; color: #ffffff; background: none;font-weight: 100;">Mini PC NUC</th>
                            <td class="NUC" style="height: 38px;width: 200px; color: #ffffff;background-color: <?php echo ($row['NUC'] == 'Available') ? '#1ead9c;' : '#b04532'; ?>"><?php echo $row['NUC']; ?></td>
                        </tr>
                        <tr style="height: 8px;"></tr>
                        <tr>
                            <th style="height: 38px; width: 200px; color: #ffffff; background: none;font-weight: 100;">Router</th>
                            <td class="Router" style="height: 38px;width: 200px; color: #ffffff;background-color: <?php echo ($row['Router'] == 'Available') ? '#1ead9c;' : '#b04532'; ?>"><?php echo $row['Router']; ?></td>
                        </tr>
                        <tr style="height: 8px;"></tr>
                        <tr>
                            <th style="height: 38px; width: 200px; color: #ffffff; background: none;font-weight: 100;">IP Camera</th>
                            <td class="Camera" style="height: 38px;width: 200px; color: #ffffff;background-color: <?php echo ($row['Camera'] == 'Available') ? '#1ead9c;' : '#b04532'; ?>"><?php echo $row['Camera']; ?></td>
                        </tr>
                        <tr style="height: 8px;"></tr>
                        <tr>
                            <th style="height: 38px; width: 200px; color: #ffffff; background: none;font-weight: 100;">RTL-SDR Dongle</th>
                            <td class="Dongle" style="height: 38px;width: 200px; color: #ffffff;background-color: <?php echo ($row['Dongle'] == 'Available') ? '#1ead9c;' : '#b04532'; ?>"><?php echo $row['Dongle']; ?></td>
                        </tr>
                        <tr style="height: 8px;"></tr>
                        <tr>
                            <th style="height: 38px; width: 200px; color: #ffffff; background: none;font-weight: 100;">U-Blox</th>
                            <td class="Ublox"style="height: 38px; width: 200px; color: #ffffff;background-color: <?php echo ($row['Ublox'] == 'Available') ? '#1ead9c;' : '#b04532'; ?>"><?php echo $row['Ublox']; ?></td>
                        </tr>
                    </table>
                    <?php
                }
            }
            else{
                ?><P class="noinfo">No informations in this period of time</P><?php
            }  ?>
        </div> 
        <?php
        date_default_timezone_set('Asia/Bangkok');
        echo '<p id="thai-time" style="">' . date('H:i:s') . ' (UTC+7)</p>';
    }
    ?>
<style>
  td{
    border-radius: 15px 15px 15px 15px;
    text-align: center;
    border:none;
  } 
  .showstatus tr{
    font-size:15px ;
  }
  table{
    border:none;
  }

  @media (max-width: 1025px) {
    .showstatus th{
    font-size: clamp(12px,1.5vw,15px);
    }
    .showstatus td{
    font-size: clamp(12px,1.5vw,15px);
    }
  }
  
</style>
<script>
    var thaiTimeElement = document.getElementById("thai-time");

    function updateThaiTime() {
        var thaiTime = new Date().toLocaleString("en-US", {timeZone: "Asia/Bangkok", hour12: false});
        var timeParts = thaiTime.split(' ')[1];
        thaiTimeElement.textContent = timeParts+' (UTC+7)';
    }

    // Set up the initial interval
    setInterval(updateThaiTime, 1000);
</script>
<script>
    var query_s = "<?php echo $query; ?>";
    var dbname_s = "<?php echo $dbname; ?>";
    var lastid_status = "<?php echo $lastid_status; ?>";
    function checkForUpdates_s(dbname_s) {
    $.ajax({
        url: 'status_updates.php',
        type: 'GET',
        data: { lastid: lastid_status, dbname: dbname_s },
        success: function (data) {
            if (data.new_data && Array.isArray(data.new_records) && data.new_records.length > 0) {
                lastid_status = data.last_id;
                //console.log(data);

                var newRecord = data.new_records[0];

                // Update the table cells dynamically
                updateTableCell("NUC", newRecord.NUC);
                updateTableCell("Router", newRecord.Router);
                updateTableCell('Camera', newRecord.Camera);
                updateTableCell('Dongle', newRecord.Dongle);
                updateTableCell('Ublox', newRecord.Ublox);
            }
        }
    });
}

function updateTableCell(className, value) {
    var tdElement = $("td." + className);
    tdElement.text(value);
    tdElement.css("background-color", (value == 'Available') ? '#1ead9c' : '#b04532');
}


// Set up the initial interval
setInterval(function () {
    checkForUpdates_s(dbname_s);
}, 1000);

</script>
