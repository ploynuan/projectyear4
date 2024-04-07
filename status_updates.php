<?php
$dbname = $_GET['dbname'] ?? '';
include_once('connect.php');
$lastid = isset($_GET['lastid']) ? (int)$_GET['lastid'] : 0;
$query = "SELECT * FROM Devices WHERE id > $lastid";
$query_run = mysqli_query($conn, $query);

if ($query_run) {
    $newRecords = mysqli_fetch_all($query_run, MYSQLI_ASSOC);
    if (!empty($newRecords)) {
        $latestid = end($newRecords)['id'];

        $response = array(
            'new_data' => true,
            'last_id' => $latestid,
            'new_records' => $newRecords
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        $response = array('new_data' => false);

        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    $response = array('error' => 'Query execution failed');

    header('Content-Type: application/json');
    echo json_encode($response);
}

mysqli_close($conn);
?>
