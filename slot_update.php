<?php
include("connection.php");

$status = $_POST['status']; // on / off

$a = array("robot" => $status);
file_put_contents("light3.json", json_encode($a));

$sql1 = "SELECT * FROM 4349_rfid ORDER BY id DESC LIMIT 1";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$value1 = $row1['value1'];

date_default_timezone_set('Asia/Kolkata');
$timestamp = date("Y-m-d H:i:s");

if ($value1=="1" || $value1=="0002409830" || $value1=="F1D60202") {
    mysqli_query($conn,
      "INSERT INTO 4349_payment (rfid_no, status, reading_time)
       VALUES ('$value1', '$status', '$timestamp')"
    );
}

echo "success";