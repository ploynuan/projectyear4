<?php 
$starttime2 = $_GET['starttime2'];
$endtime2 = $_GET['endtime2'];
$dbname = $_GET['stationname2'];
include_once('connect.php'); 
$output = '';
if (isset($_POST["export"]) ) 
{
    $query = "SELECT * FROM L1_Data WHERE `Date` BETWEEN '$starttime2' AND '$endtime2'";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0)
    {
        $filename = 'Report_' . $starttime2 . '_to_' . $endtime2 . '.xls';
        $output .= '
            <table>
                <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Frequency (MHz)</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Power Peak Mean (dB)</th>
                <th>Power Average Mean (dB)</th>
                <th>Number of Satellites</th>
                <th>CNR</th>
                <th>Type of Device</th>
                <th>RFI Level</th>
                </tr>
        ';
        while($row = mysqli_fetch_array($result))
        {
            $output .= '
                <tr>
                    <td>'.$row["Date"].'</td>
                    <td>'.$row["Start_Time"].'</td>
                    <td>'.$row["Frequency_MHz"].'</td>
                    <td>'.$row["Latitude"].'</td>
                    <td>'.$row["Longitude"].'</td>
                    <td>'.$row["Power_Peak_Mean_dB"].'</td>
                    <td>'.$row["Power_Average_Mean_dB"].'</td>
                    <td>'.$row["Avg_Num_Sat"].'</td>
                    <td>'.$row["Avg_CNR"].'</td>
                    <td>'.$row["Type_of_Device"].'</td>
                    <td>'.$row["RFI_Level"].'</td>
                </tr>
            ';
        }
        $output .= '</table>';
        header('Content-Type: application/xls');
        header('Content-Disposition: attachment; filename='.$filename);
        echo $output;
        }
}
// elseif (isset($_POST["export"]) && $_GET['freq2'] !== "L1_data") 
// {
//     $query = "SELECT * FROM `$values2` WHERE `Date` BETWEEN '$starttime2' AND '$endtime2'";
//     $result = mysqli_query($conn, $query);
//     if(mysqli_num_rows($result) > 0)
//     {
//         $filename = 'Report_' . $starttime2 . '_to_' . $endtime2 . '.xls';
//         $output .= '
//             <table>
//                 <tr>
//                 <th>Date</th>
//                 <th>Time</th>
//                 <th>Frequency (MHz)</th>
//                 <th>Power Peak Mean (dB)</th>
//                 <th>Power Average Mean (dB)</th>
//                 <th>Type of Device</th>
//                 <th>RFI Level</th>
//                 </tr>
//         ';
//         while($row = mysqli_fetch_array($result))
//         {
//             $output .= '
//                 <tr>
//                     <td>'.$row["Date"].'</td>
//                     <td>'.$row["Start_Time"].'</td>
//                     <td>'.$row["Frequency_MHz"].'</td>
//                     <td>'.$row["Power_Peak_Mean_dB"].'</td>
//                     <td>'.$row["Power_Average_Mean_dB"].'</td>
//                     <td>'.$row["Type_of_Device"].'</td>
//                     <td>'.$row["RFI_Level"].'</td>
//                 </tr>
//             ';
//         }
//         $output .= '</table>';
//         header('Content-Type: application/xls');
//         header('Content-Disposition: attachment; filename='.$filename);
//         echo $output;
//     }
// }
?>
