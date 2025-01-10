<?php
include '../../Include/database.php';

$query = "SELECT service_name FROM services";
$result = $conn->query($query);

$service_types = [];
while ($row = $result->fetch_assoc()) {
    $service_types[] = $row;
}

echo json_encode($service_types);
?>
