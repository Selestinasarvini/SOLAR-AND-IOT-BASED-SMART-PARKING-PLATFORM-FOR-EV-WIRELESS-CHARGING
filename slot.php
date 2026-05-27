 <?php
session_start();
include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

$sql1 = "SELECT * FROM 4349_rfid order by id desc limit 1";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$value2=$row1['value2'];
$value3=$row1['value3'];
$time=$row1['reading_time'];

$sql1 = "SELECT * FROM 4349_payment where rfid_no='$rfid_no' AND reading_time >= NOW() - INTERVAL 5 MINUTE ORDER BY id DESC";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$status=$row1['status'];

if($status === "on"){
    $bgcolor = "slot-boxcon";
}elseif($status === "off"){
    $bgcolor = "slot-boxdis";
}else{
    $bgcolor = "slot-boxnone";
}

if($status === "on"){
    $bgcolor1 = "statuscon";
    $text ="Connected";
}elseif($status === "off"){
    $bgcolor1 = "statusdis";
    $text ="Disconnected";
}else{
    $bgcolor1 = "statusnone";
    $text ="Not Connect";
}

//  if ($value1=="$0005337929" || $value1=="$0002409830" ) {
// disabled

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="refresh" content="5">
    <title>SmartPark EV | Slots</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* General Styling */
        body {
            background: var(--gradient-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            min-height: 100vh;
        }
        .container-main {
            padding-top: 30px;
            padding-bottom: 50px;
            display:flex;
            justify-content:center;
        }
        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        
        .container {
  text-align: center;
}
.slot_back{
    width:300px;
    background-color:white;
    display:flex;
    flex-direction:column;
    align-items:center;
}

/* Slot box */
.slot-box {
  background-color:gray;
  width: 260px;
  height: 350px;
  position: relative;
  margin-bottom: 30px;
  border-radius:20px;
}
.slot-boxcon{
    background-color:#73ff6d;
    /*background-color:#07b800;*/
    /*background-color:#E62727;*/
    color: #fff;
}
.slot-boxdis{
    background-color:#fd6f72;
    color: #fff;
}
.slot-boxnone{
    color: #fff;
}
/* Corner design */
.corner {
  /*border-radius:16px 0 0 0;*/
  width: 45px;
  height: 45px;
  position: absolute;
  border: 2px solid #000;
}

.tl { top: 0; left: 0; border-right: none; border-bottom: none; border-radius:20px 0 0 0;}
.tr { top: 0; right: 0; border-left: none; border-bottom: none; border-radius:0 20px 0 0;}
.bl { bottom: 0; left: 0; border-right: none; border-top: none; border-radius:0 0 0 20px;}
.br { bottom: 0; right: 0; border-left: none; border-top: none; border-radius:0 0 20px 0;}

.slot-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-weight: bold;
}

/* Status */
.status {
  width:90%;
  /*border: 2px solid #000;*/
  padding: 11px;
  margin-bottom: 15px;
  font-weight: bold;
}
.statuscon{
     background-color:#07b800;
    /*background-color:#E62727;*/
    /*background-color:#73ff6d;*/
    color: #fff;
}
.statusdis{
     /*background-color:#07b800;*/
    background-color:#E62727;
    /*background-color:#fd6f72;*/
    color: #fff;
}
.statusnone{
    background-color:#86807a;
    color: #fff;
}

/* Buttons */
.btn-group {
  display: flex;
  gap: 10px;
  justify-content: center;
}

.btn-control {
  padding: 8px 14px;
  border: 2px solid #000;
  /*background: #fff;*/
  cursor: pointer;
  font-weight: bold;
}
.btn-on{
    background: #07b800;
    color: #fff;
    border: 2px solid #07b800;
}
.btn-on:hover {
  background: #058000;
  color: #fff;
  border: 2px solid #058000;
}
.btn-off{
    background: #E62727;
    color: #fff;
    border: 2px solid #E62727;
}
.btn-off:hover {
  background: #c01616;
  color: #fff;
  border: 2px solid #c01616;
}

        
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    

  <div class="container container-main">
      <div class="slot_back">
             <!-- Camera / Slot Box -->
    <div class="slot-box <?=$bgcolor?>">
      <span class="corner tl"></span>
      <span class="corner tr"></span>
      <span class="corner bl"></span>
      <span class="corner br"></span>

      <p class="slot-text">Your Parking Slot</p>
    </div>

    <!-- Status -->
    <div id="status" class="status <?=$bgcolor1?>"><?= $text?></div>
    
    <!-- Buttons -->
    <div class="btn-group">
    <form method="post" action="" class="control-buttons">
        <button type="submit" name="on" class="btn-control btn-on">
            <i class="fas fa-power-off"></i> Connect 
        </button>
        <button type="submit" name="off" class="btn-control btn-off">
            <i class="fas fa-power-off"></i> Disconnect
        </button>
    </form>
    </div>
      </div>
  </div>




<?php
    if (isset($_POST["on"])) {
        
    
        
        $sql1 = "SELECT * FROM 4349_rfid order by id desc limit 1";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $value1=$row1['value1'];
        $value2=$row1['value2'];
        $value3=$row1['value3'];
        
        date_default_timezone_set('Asia/Kolkata');
        $timestamp = date("Y-m-d H:i:s");
             if ($value1=="$0008754954" || $value1=="$0005358802" || $value1=="F1D60202" ) {
                 
                     $a = array("robot" => "on");
        $a = json_encode($a);
        file_put_contents("light3.json", $a);
                 
                      $newStatus = "on";
                  // Insert status change into the database
                  $sql12 = "INSERT INTO 4349_payment (rfid_no, status, reading_time) 
                            VALUES ('$value1', '$newStatus', '$timestamp')";
                  if (mysqli_query($conn, $sql12)) {
                      
                  } else {
                      echo "Error inserting status change: " . mysqli_error($conn);
                  }
            }
      
    }
    if (isset($_POST["off"])) {
        
        
        $sql1 = "SELECT * FROM 4349_rfid order by id desc limit 1";
        $result1 = mysqli_query($conn, $sql1);
        $row1 = mysqli_fetch_assoc($result1);
        $value1=$row1['value1'];
        $value2=$row1['value2'];
        $value3=$row1['value3'];
        
        date_default_timezone_set('Asia/Kolkata');
        $timestamp = date("Y-m-d H:i:s");
             
             
            // Toggle status if value1 > 0
             if ($value1=="$0008754954" || $value1=="$0005358802" || $value1=="F1D60202" ) {
                 
                 $a = array("robot" => "off");
        $a = json_encode($a);
        file_put_contents("light3.json", $a);
        
                      $newStatus = "off";
                  // Insert status change into the database
                  $sql12 = "INSERT INTO 4349_payment (rfid_no, status, reading_time) 
                            VALUES ('$value1', '$newStatus', '$timestamp')";
                  if (mysqli_query($conn, $sql12)) {

                  } else {
                      echo "Error inserting status change: " . mysqli_error($conn);
                  }
            }
    }
?>
</body>
</html>

