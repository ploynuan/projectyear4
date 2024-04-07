<?php 
    date_default_timezone_set('Asia/Bangkok');
    $date = date("Y-m-d");
    $date2 = date("d/m/Y");
    $year = date("Y");

    if(empty($_POST['starttime2'])&&empty($_POST['endtime2'])&&empty($_POST['stationname2'])){
        ?><P class="noinfo">Please Select Station and Datetime </P><?php
    }
    elseif(
        (isset($_POST['starttime2']) && ($_POST['starttime2'] !== '')) &&
        (isset($_POST['endtime2']) && ($_POST['endtime2'] !== '')) &&
        (isset($_POST['stationname2']) && ($_POST['stationname2'] !== '')) &&
        (strtotime($_POST['starttime2']) >  strtotime($_POST['endtime2']))      
    ) {
        echo "<script>alert('Start date must come before the end date');</script>";
    }
    elseif(
        (isset($_POST['starttime2']) && ($_POST['starttime2'] !== '')) &&
        (isset($_POST['endtime2']) && ($_POST['endtime2'] !== '')) &&
        (isset($_POST['stationname2']) && ($_POST['stationname2'] !== '')) &&
        (($_POST['starttime2']) > $date) &&  
        (($_POST['endtime2']) < $date)       
    ) {
        echo "<script>alert('Please date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime2']) && ($_POST['starttime2'] !== '')) &&
        (isset($_POST['endtime2']) && ($_POST['endtime2'] !== '')) &&
        (isset($_POST['stationname2']) && ($_POST['stationname2'] !== '')) &&
        (($_POST['starttime2']) < $date) &&  
        (($_POST['endtime2']) > $date)       
    ) {
        echo "<script>alert('Please select date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime2']) && ($_POST['starttime2'] !== '')) &&
        (isset($_POST['endtime2']) && ($_POST['endtime2'] !== '')) &&
        (isset($_POST['stationname2']) && ($_POST['stationname2'] !== '')) &&
        (($_POST['starttime2']) > $date) &&  
        (($_POST['endtime2']) > $date)       
    ) {
        echo "<script>alert('Please select date within the current dates');</script>";
    }
    elseif(
        (isset($_POST['starttime2']) && ($_POST['starttime2'] !== '')) &&
        (isset($_POST['endtime2']) && ($_POST['endtime2'] !== '')) &&
        (isset($_POST['stationname2']) && ($_POST['stationname2'] !== ''))
       
    ) {
        $starttime2=$_POST['starttime2'];
        $endtime2=$_POST['endtime2'];
        $dbname = $_POST['stationname2'];
        include_once('connect.php'); 

        $query="SELECT * FROM L1_Data WHERE Date BETWEEN '$starttime2'AND'$endtime2' AND (`RFI_Level` != 'Green')";
        $query_run=mysqli_query($conn,$query);
        if(mysqli_num_rows($query_run)>0)
        {
            ?>
            <div class="info">
                <div class="show2" style="overflow-x: auto;">
                    <table style="border: 1px solid #e8e6e2;">
                    <thead>
                        <tr>
                            <th style="height: 70px";>Date</th>
                            <th style="height: 70px";>Time</th>
                            <th style="height: 70px";>Frequency </br>(MHz)</th>
                            <th style="height: 70px";>Average Number </br>of Satellite</th>
                            <th style="height: 70px";>Average CNR</th>
                            <th style="height: 70px";>Type of Device</th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php
                    foreach($query_run as $row)
                    {
                    ?>
                        <tr>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Date'];?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Start_Time'];?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Frequency_MHz'];?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo number_format($row['Avg_Num_Sat'], 2); ?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo number_format($row['Avg_CNR'], 2); ?></td>
                        <td style="border: 1px solid #e8e6e2;"><?php echo $row['Type_of_Device'];?></td>
                        </tr>
                        <?php
                    }
                    ?></tbody>
                    </table>
                </div>
                <form method="POST" action="export.php?starttime2=<?php echo $starttime2 ?>&endtime2=<?php echo $endtime2 ?>&stationname2=<?php echo $dbname ?>">
                    <input type="submit" name="export" class="btn btn-success" value="Export">
                </form>

            </div>
            <?php  
        }
        else{
            ?><P class="noinfo">No informations in this period of time</P><?php
        }                     
    }
   
    else{
        ?><P class="noinfo">Please ensure that all options are selected </P><?php
    }
        ?>
                