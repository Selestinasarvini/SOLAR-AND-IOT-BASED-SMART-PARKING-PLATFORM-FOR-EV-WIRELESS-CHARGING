<?php

session_start();

include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

$sql1 = "select * from 4349_user where rfid_no = '$rfid_no'";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$wallet = $row1["wallet"];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IoT</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      /* General Styling */
      
    </style>
  </head>
  <body>
    <?php
        include("header.php");
    ?>
    
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card p-3 mt-5">
                    <h2 class="text-center">Recharge Amount</h2>
                    <form method="post">
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" min="100" name="amt" class="form-control" required>
                            <input type="hidden" name="wallet" class="form-control" value="<?=$wallet?>" required>
                            <input type="hidden" name="rfid_no" class="form-control" value="<?=$rfid_no?>" required>
                        </div>   
                        <div class="form-group">
                            <input type="submit" name="add" class="form-control btn btn-success" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    </body>
</html>
<?php

if(isset($_POST['add']))
{
    $id = $_POST["rfid_no"];
    $wallet = $_POST["wallet"];
    $amt = $_POST["amt"];
    
    $total = $wallet + $amt;
    
    $sql = "update 4349_user set wallet = '$total' where rfid_no = '$rfid_no'";
    if(mysqli_query($conn, $sql))
    {
        echo '<script>alert("Payment Added Successful");window.location.replace("detail.php");</script>';
    }
    
}
