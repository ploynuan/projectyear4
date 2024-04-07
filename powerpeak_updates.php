<?php
$dbname = $_GET['dbname'] ?? '';
include_once('connect.php');
$query = $_GET['query'] ?? '';

if (!empty($query)) {
    $newQueryResult = mysqli_query($conn, $query);

    if ($newQueryResult) {
        $newData = mysqli_fetch_assoc($newQueryResult);

        if ($newData !== null && isset($newData['Power_Average_Mean_dB'])) {
            $newData['Power_Average_Mean_dB'] = number_format($newData['Power_Average_Mean_dB'], 2);
            echo json_encode($newData['Power_Average_Mean_dB']);
        } else {
            echo json_encode(['error' => 'Invalid or null data']);
        }
    } else {
        echo json_encode(['error' => 'Query failed']);
    }
} else {
    echo json_encode(['error' => 'Query parameter not provided']);
}
?>
