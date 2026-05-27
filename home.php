<?php
session_start();
include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

$sql = "SELECT * FROM 4349_user where rfid_no='$rfid_no'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$name = $row['name'];
$car = $row['car'];
$carno = $row['car_no'];
$wallet = $row['wallet'];

$sql1 = "SELECT * FROM 4349_payment where rfid_no='$rfid_no' order by id desc";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$status = $row1['status'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartPark EV | Dashboard</title>
    <meta http-equiv="refresh" content="5">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2a5bd7;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --gradient-bg: linear-gradient(135deg, #f6f9ff 0%, #f0f4ff 100%);
        }

        body {
            background: var(--gradient-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
            min-height: 100vh;
        }

        .container-main {
            padding-top: 30px;
            padding-bottom: 50px;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, rgba(42, 91, 215, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            border-left: 6px solid var(--primary-color);
            box-shadow: var(--card-shadow);
            animation: slideInDown 0.6s ease-out;
        }

        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            animation: fadeInUp 0.5s ease-out;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(42, 91, 215, 0.15);
        }

        .stat-card.wallet {
            border-top-color: var(--secondary-color);
        }

        .stat-card.vehicle {
            border-top-color: var(--accent-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .stat-card .stat-icon {
            background: linear-gradient(135deg, rgba(42, 91, 215, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            color: var(--primary-color);
        }

        .stat-card.wallet .stat-icon {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
            color: var(--secondary-color);
        }

        .stat-card.vehicle .stat-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.05) 100%);
            color: var(--accent-color);
        }

        .stat-content h3 {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stat-content p {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        /* Main Details Card */
        .details-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .details-card .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .details-card .card-title i {
            color: var(--primary-color);
            background: rgba(42, 91, 215, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        .detail-group {
            margin-bottom: 1.8rem;
        }

        .detail-group label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-group label i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .detail-value {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            padding: 0.8rem 1.2rem;
            background: #f8fafc;
            border-radius: 12px;
            border-left: 4px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .detail-value:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .detail-value i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding-top: 20px;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .details-card {
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Live Indicator */
        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 0.9rem;
            animation: pulse 2s infinite;
        }

        .live-indicator::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: inline-block;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container container-main">
        <!-- Main Details Card -->
        <div class="details-card">
            <h3 class="card-title">
                <i class="fas fa-id-card"></i>
                User & Vehicle Details
            </h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-group">
                        <label><i class="fas fa-user-tag"></i> Owner Name</label>
                        <div class="detail-value">
                            <i class="fas fa-user"></i>
                            <span><?= htmlspecialchars($name) ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-group">
                        <label><i class="fas fa-credit-card"></i> RFID Number</label>
                        <div class="detail-value">
                            <i class="fas fa-id-card"></i>
                            <span><?= htmlspecialchars($rfid_no) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="detail-group">
                        <label><i class="fas fa-car"></i> Vehicle Model</label>
                        <div class="detail-value">
                            <i class="fas fa-car-side"></i>
                            <span><?= htmlspecialchars($car) ?></span>
                        </div>
                    </div>
                    
                    <div class="detail-group">
                        <label><i class="fas fa-hashtag"></i> Registration Number</label>
                        <div class="detail-value">
                            <i class="fas fa-plate"></i>
                            <span><?= htmlspecialchars($carno) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="detail-group">
                        <label><i class="fas fa-car"></i> Wallet Balance</label>
                        <div class="detail-value">
                            <i class="fas fa-car-side"></i>
                            <span>₹ <?= number_format($wallet, 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>