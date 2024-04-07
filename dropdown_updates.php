<?php
$dbname = $_GET['dbname_c'] ?? '';
include_once('connect.php');
$starttime = $_GET['starttime_c'] ?? '';
// Get the last id from the AJAX request
$lastid = isset($_GET['lastid_c']) ? mysqli_real_escape_string($conn, $_GET['lastid_c']) : 0;

// Query เมื่อมีข้อมูลที่มีค่ามากกว่า lastid ที่รับมา
$query = "SELECT * FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Red' OR `RFI_Level` = 'Yellow') AND id > $lastid";
$query_run = mysqli_query($conn, $query);

if ($query_run) {
    $newRecords = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
    $countQuery = "SELECT COUNT(*) as total_rows FROM L1_Data WHERE Date = '$starttime' AND (`RFI_Level` = 'Red' OR `RFI_Level` = 'Yellow')";
    $countQuery_run = mysqli_query($conn, $countQuery);
    if ($countQuery_run) {
        $countResult = mysqli_fetch_assoc($countQuery_run);
        $totalRows = $countResult['total_rows'];
    }

    if (!empty($newRecords)) {
        $latestid = end($newRecords)['id'];
        $response = array(
            'new_data' => true,
            'last_id' => $latestid,
            'new_records' => $newRecords,
            'total_rows' => $totalRows // เพิ่มบรรทัดนี้เพื่อนับแถวทั้งหมด
        );

        // Output the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; // เพื่อป้องกันการทำงานต่อ
    } else {
        $response = array('new_data' => false);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; // เพื่อป้องกันการทำงานต่อ
    }
} else {
    $response = array('error' => 'Query execution failed');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // เพื่อป้องกันการทำงานต่อ
}

mysqli_close($conn);
?>
