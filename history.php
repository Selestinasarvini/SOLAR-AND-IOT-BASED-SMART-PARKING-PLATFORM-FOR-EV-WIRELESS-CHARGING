<?php
session_start();
include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

$sql1 = "SELECT * FROM 4349_user WHERE rfid_no = '$rfid_no'";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$wallet = $row1["wallet"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartPark EV | Payment History</title>
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

        /* History Card */
        .history-card {
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

        /* Table Styling */
        .history-table {
            background: white;
            border-radius: 0 0 20px 20px;
            overflow: hidden;
        }

        .history-table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        }

        .history-table thead th {
            color: white;
            font-weight: 600;
            padding: 1.2rem 1.5rem;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .history-table tbody tr {
            transition: all 0.3s ease;
        }

        .history-table tbody tr:hover {
            background-color: #f8fafc;
            transform: translateX(5px);
        }

        .history-table tbody td {
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
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .time-cell {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
        }

        .user-rfid {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .no-data h5 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #64748b;
        }

        .no-data p {
            font-size: 0.9rem;
            margin-bottom: 0;
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
            
            .history-table thead th,
            .history-table tbody td {
                padding: 0.8rem;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 5px;
            }
            
            .user-details {
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="container container-main">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="history-card">
                    <!-- Card Header -->
                    <div class="card-header-custom">
                        <h4><i class="fas fa-file-invoice"></i> Payment Transaction History</h4>
                    </div>

                    <!-- History Table -->
                    <div class="table-responsive history-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User Details</th>
                                    <th>Status</th>
                                    <th>Reading Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql12 = "SELECT * FROM 4349_payment WHERE rfid_no = '$rfid_no' ORDER BY reading_time DESC";
                                $result = mysqli_query($conn, $sql12);
                                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                // Group rows into pairs of "on" and "off"
                                $pairedRows = [];
                                for ($i = 0; $i < count($rows); $i++) {
                                    if ($i + 1 < count($rows)) {
                                        $current = $rows[$i];
                                        $next = $rows[$i + 1];

                                        // Check if current is "off" and next is "on"
                                        if ($current["status"] == "off" && $next["status"] == "on") {
                                            $pairedRows[] = [$current, $next];
                                            $i++; // Skip the next row since it's part of the pair
                                        }
                                    }
                                }

                                if (count($pairedRows) > 0) {
                                    foreach ($pairedRows as $pair) {
                                        $offRow = $pair[0];
                                        $onRow = $pair[1];
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?= strtoupper(substr($row1["name"], 0, 1)) ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <div class="user-name"><?= $row1["name"] ?></div>
                                                        <div class="user-rfid">RFID: <?= substr($rfid_no, 0, 8) ?>...</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-disconnected">
                                                    <i class="fas fa-plug-circle-xmark"></i>
                                                    Charge Disconnected
                                                </span>
                                            </td>
                                            <td class="time-cell">
                                                <div><?= date('h:i:s A', strtotime($offRow["reading_time"])) ?></div>
                                                <div class="small text-muted"><?= date('M d, Y', strtotime($offRow["reading_time"])) ?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar">
                                                        <?= strtoupper(substr($row1["name"], 0, 1)) ?>
                                                    </div>
                                                    <div class="user-details">
                                                        <div class="user-name"><?= $row1["name"] ?></div>
                                                        <div class="user-rfid">RFID: <?= substr($rfid_no, 0, 8) ?>...</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-connected">
                                                    <i class="fas fa-charging-station"></i>
                                                    Charge Connected
                                                </span>
                                            </td>
                                            <td class="time-cell">
                                                <div><?= date('h:i:s A', strtotime($onRow["reading_time"])) ?></div>
                                                <div class="small text-muted"><?= date('M d, Y', strtotime($onRow["reading_time"])) ?></div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="no-data">
                                                <i class="fas fa-file-invoice"></i>
                                                <h5>No Transaction History</h5>
                                                <p>No payment transactions found for your account.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
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