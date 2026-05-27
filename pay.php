<?php
include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

$sql1 = "SELECT * FROM 4349_user WHERE rfid_no = '$rfid_no'";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$wallet = $row1["wallet"];

if (isset($_POST["pay"])) {
    $id = $_POST["id"];
    $cost = $_POST["cost"];
    $wallet1 = $_POST["wallet"];
    
    if ($wallet1 < $cost) {
        echo '<script>alert("Please Recharge Your Wallet Amount");window.location.replace("pay.php");</script>';
    } else {
        $v1 = $wallet1 - $cost;

        // Update wallet balance
        $sql12 = "UPDATE 4349_user SET wallet = '$v1' WHERE rfid_no = '$rfid_no'";
        if (mysqli_query($conn, $sql12)) {
            // Mark the payment as completed for the specific pair of records
            $sql13 = "UPDATE 4349_payment SET is_paid = 1 WHERE rfid_no = '$rfid_no' AND DATE(reading_time) = CURDATE() ORDER BY id DESC LIMIT 2";
            mysqli_query($conn, $sql13);

            echo '<script>alert("Payment Successful");window.location.replace("pay.php");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartPark EV | Payments</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2a5bd7;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gradient-bg: linear-gradient(135deg, #f6f9ff 0%, #f0f4ff 100%);
        }

        body {
            background: var(--gradient-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            min-height: 100vh;
        }

        .container-main {
            padding-top: 100px;
            padding-bottom: 50px;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, rgba(42, 91, 215, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 6px solid var(--primary-color);
            animation: slideInDown 0.6s ease-out;
        }

        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Payment Card */
        .payment-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.8s ease-out;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-custom h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-custom h4 i {
            color: white;
        }

        .btn-history {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-history:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        /* Table Styling */
        .payment-table {
            background: white;
            border-radius: 0 0 20px 20px;
            overflow: hidden;
        }

        .payment-table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        }

        .payment-table thead th {
            color: white;
            font-weight: 600;
            padding: 1.2rem 1.5rem;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .payment-table tbody tr {
            transition: all 0.3s ease;
        }

        .payment-table tbody tr:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }

        .payment-table tbody td {
            padding: 1.2rem 1.5rem;
            vertical-align: middle;
            border-color: #e2e8f0;
            font-weight: 500;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-connected {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-disconnected {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Payment Button */
        .btn-pay {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #34d399 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-pay:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-paid {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            cursor: default;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding-top: 80px;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .card-header-custom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .payment-table thead th,
            .payment-table tbody td {
                padding: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="container container-main">

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="payment-card">
                    <!-- Card Header -->
                    <div class="card-header-custom">
                        <h4><i class="fas fa-receipt"></i> Payment</h4>
                        <a class="btn-history" href="history.php">
                            <i class="fa-solid fa-clock-rotate-left"></i> History
                        </a>
                    </div>

                    <!-- Payment Table -->
                    <div class="table-responsive payment-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Reading Time</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get today's date
                                $current_date = date("Y-m-d");

                                // Fetch data only from the current date, ordered by ID in descending order
                                $sql12 = "SELECT * FROM 4349_payment
                                          WHERE rfid_no = '$rfid_no' 
                                          AND DATE(reading_time) = '$current_date' 
                                          ORDER BY id DESC 
                                          LIMIT 2";
                                $result = mysqli_query($conn, $sql12);

                                if (mysqli_num_rows($result) == 2) {
                                    $rows = [];
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $rows[] = $row;
                                    }

                                    // Check if the first row is "off" and the second row is "on"
                                    if ($rows[0]['status'] == 'off' && $rows[1]['status'] == 'on') {
                                        $time1 = strtotime($rows[0]['reading_time']);
                                        $time2 = strtotime($rows[1]['reading_time']);
                                        $time_diff = abs($time2 - $time1) / 60; // Difference in minutes
                                        $cost = $time_diff * 50; // Cost calculation

                                        // Check if payment has already been made for this pair
                                        $is_paid = $rows[0]['is_paid'] || $rows[1]['is_paid'];

                                        echo "<tr>
                                                <td>{$row1['name']}</td>
                                                <td>
                                                    <span class='status-badge status-disconnected'>
                                                        Charge Disconnected
                                                    </span>
                                                </td>
                                                <td>{$rows[0]['reading_time']}</td>
                                                <td rowspan='2' style='vertical-align: middle;'>";
                                        if (!$is_paid) {
                                            echo "<form method='post'>
                                                    <input type='hidden' name='id' value='" . $id . "'>
                                                    <input type='hidden' name='wallet' value='" . $wallet . "'>
                                                    <input type='hidden' name='cost' value='" . $cost . "'>
                                                    <button type='submit' name='pay' class='btn-pay'>
                                                        <i class='fas fa-lock'></i>
                                                        Pay ₹" . number_format($cost, 2) . "
                                                    </button>
                                                  </form>";
                                        } else {
                                            echo "<div class='btn-paid'>
                                                    <i class='fas fa-check-circle'></i>
                                                    Paid ₹" . number_format($cost, 2) . "
                                                  </div>";
                                        }
                                        echo "</td>
                                              </tr>
                                              <tr>
                                                <td>{$row1['name']}</td>
                                                <td>
                                                    <span class='status-badge status-connected'>
                                                        Charge Connected
                                                    </span>
                                                </td>
                                                <td>{$rows[1]['reading_time']}</td>
                                              </tr>";
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>No valid data found for today.</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No data available for today.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>