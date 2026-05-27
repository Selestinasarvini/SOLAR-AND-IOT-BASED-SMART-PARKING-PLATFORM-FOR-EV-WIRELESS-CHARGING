<?php

include "connection.php";
$value1=$_POST['value1'];
$value2=$_POST['value2'];
$value3=$_POST['value3'];
$value4=$_POST['value4'];

        $a= json_encode(["rfid_no"=> "$value1",]);
        file_put_contents("light.json",$a);
        
        date_default_timezone_set('Asia/Kolkata');
        $timestamp = date("Y-m-d H:i:s");
        
        
        // Read the JSON file and decode it
        //  $jsonData = file_get_contents("light1.json");
        //  $stat = json_decode($jsonData, true);
         
         
        //  if (!isset($stat['robot'])) {
        //      $stat['robot'] = "off"; // Default to 'off' if the JSON file is empty or incorrect
        //  }
         
        //      $sql21 = "select * from 4339_payment where rfid_no = '$value1' order by id desc limit 1";
        //      $result21 = mysqli_query($conn, $sql21);
        //      $row21 = mysqli_fetch_assoc($result21);
        //      $vl1 = $row21["rfid_no"]; 
        //      $st = $row21["status"] ;
             
             
        //     // Toggle status if value1 > 0
        //      if ($value1=="870085970A9F" || $value1=="540051C4D213" || $value1=="F1D60202" ) {
        //           if ($st == "on") {
        //               $newStatus = "off";
        //           } else {
        //               $newStatus = "on";
        //           }

        //           // Update JSON file
        //           $a = array("robot" => $newStatus);
        //           file_put_contents("light1.json", json_encode($a));

        //           // Insert status change into the database
        //           $sql12 = "INSERT INTO 4339_payment (rfid_no, status, reading_time) 
        //                     VALUES ('$value1', '$newStatus', '$timestamp')";
        //           if (mysqli_query($conn, $sql12)) {
        //               echo "Inserted: $newStatus";
        //           } else {
        //               echo "Error inserting status change: " . mysqli_error($conn);
        //           }
        //     }
        
        
        $sql = "INSERT INTO 4349_rfid (value1,value2,value3,value4,reading_time)
        VALUES ('$value1','$value2','$value3','$value4','$timestamp')";
        $result=mysqli_query($conn,$sql);
            if ($result) {
                echo "New record created successfully";
            } 
            else {
                echo "Values Are Not Entered";
            }
        
        
        
          $sql4="SELECT * FROM 4349_rfid";
          $result4=mysqli_query($conn,$sql4);
          // print_r($result);
        
         if(mysqli_num_rows($result4)>50){
         $sql51="DELETE FROM 4349_rfid ORDER BY id ASC limit 1";
         $result51=mysqli_query($conn,$sql51);
            if($result51){
               echo "deleted Successfully";
            }
        }

?>
