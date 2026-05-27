<?php
session_start();
include("connection.php");

$json = file_get_contents("light.json");
$data = json_decode($json, true);
$rfid_no = $data['rfid_no'];

// $rfid_no=DA700D7D;

$sql1 = "SELECT * FROM 4349_user WHERE rfid_no = '$rfid_no'";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);

// Get charging status if available
$charging_sql = "SELECT status, charging_level FROM 4349_payment WHERE rfid_no = '$rfid_no' ORDER BY id DESC LIMIT 1";
$charging_result = mysqli_query($conn, $charging_sql);
$charging_data = mysqli_fetch_assoc($charging_result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartPark EV | Vehicle Details</title>
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

        /* Details Card */
        .details-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .card-title-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(42, 91, 215, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--primary-color);
        }

        .card-title-text h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .card-title-text p {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Vehicle Details Section */
        .vehicle-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            background: #f8fafc;
            border-radius: 15px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .detail-item:hover {
            background: #f1f5f9;
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .detail-item.wallet {
            border-left-color: var(--secondary-color);
        }

        .detail-item.owner {
            border-left-color: var(--accent-color);
        }

        .detail-item.rfid {
            border-left-color: #8b5cf6;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-label i {
            font-size: 1rem;
        }

        .detail-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        .detail-subtext {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-top: 0.5rem;
        }

        /* Charging Status */
        .charging-status {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .charging-status h5 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .charging-progress {
            height: 10px;
            background: #e2e8f0;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .charging-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary-color) 0%, #34d399 100%);
            border-radius: 5px;
            width: <?= isset($charging_data['charging_level']) ? $charging_data['charging_level'] . '%' : '0%' ?>;
            transition: width 1s ease;
        }

        .charging-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #64748b;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-action {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-recharge {
            background: linear-gradient(135deg, var(--accent-color) 0%, #fbbf24 100%);
            color: white;
        }

        .btn-charge {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #34d399 100%);
            color: white;
        }

        .btn-history {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            color: white;
        }

        /* Vehicle Image Placeholder */
        .vehicle-image {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 20px;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 300px;
        }

        .vehicle-icon {
            font-size: 5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .vehicle-model {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .vehicle-type {
            color: #64748b;
            font-size: 1rem;
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

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container container-main">

        <!-- Main Details Card -->
        <div class="details-card">
            <!-- Card Title -->
            <div class="card-title-section">
                <div class="card-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="card-title-text">
                    <h3>Vehicle Information</h3>
                    <p>RFID: <?= htmlspecialchars($rfid_no) ?></p>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Vehicle Details -->
                <div class="col-lg-12">
                    <!-- Vehicle Details Grid -->
                    <div class="vehicle-details-grid">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-car"></i>
                                Vehicle Model
                            </div>
                            <div class="detail-value">
                                <?= htmlspecialchars($row1["car"]) ?>
                            </div>
                            <div class="detail-subtext">
                                Electric Vehicle
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-hashtag"></i>
                                Registration Number
                            </div>
                            <div class="detail-value">
                                <?= htmlspecialchars($row1["car_no"]) ?>
                            </div>
                            <div class="detail-subtext">
                                Registered Vehicle
                            </div>
                        </div>

                        <div class="detail-item owner">
                            <div class="detail-label">
                                <i class="fas fa-user-tie"></i>
                                Owner Name
                            </div>
                            <div class="detail-value">
                                <?= htmlspecialchars($row1["name"]) ?>
                            </div>
                            <div class="detail-subtext">
                                Primary Account Holder
                            </div>
                        </div>

                        <div class="detail-item wallet">
                            <div class="detail-label">
                                <i class="fas fa-wallet"></i>
                                Wallet Balance
                            </div>
                            <div class="detail-value">
                                ₹<?= number_format($row1["wallet"], 2) ?>
                            </div>
                            <div class="detail-subtext">
                                Available for parking & charging
                            </div>
                        </div>
                    </div>

                    <!-- Charging Status -->
                    <?php if(isset($charging_data['status'])): ?>
                    <div class="charging-status">
                        <h5>
                            <i class="fas fa-bolt"></i>
                            Current Charging Status
                        </h5>
                        <div class="charging-progress">
                            <div class="charging-progress-bar" id="chargingProgress"></div>
                        </div>
                        <div class="charging-info">
                            <span>Battery Level</span>
                            <span id="chargingLevel"><?= isset($charging_data['charging_level']) ? $charging_data['charging_level'] . '%' : 'Not Charging' ?></span>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-<?= $charging_data['status'] == 'charging' ? 'success' : 'warning' ?> p-2">
                                <i class="fas fa-<?= $charging_data['status'] == 'charging' ? 'charging-station' : 'pause-circle' ?> me-1"></i>
                                <?= ucfirst($charging_data['status']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="recharge.php" class="btn btn-action btn-recharge">
                            <i class="fas fa-coins"></i>
                            Recharge Wallet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate charging progress bar
            const progressBar = document.getElementById('chargingProgress');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = progressBar.style.width;
                }, 100);
            }

            // Add hover effects to detail items
            const detailItems = document.querySelectorAll('.detail-item');
            detailItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // SweetAlert for recharge button
            const rechargeBtn = document.querySelector('.btn-recharge');
            if (rechargeBtn) {
                rechargeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            }

            // Simulate live charging updates
            const chargingLevel = document.getElementById('chargingLevel');
            if (chargingLevel && chargingLevel.textContent.includes('%')) {
                setInterval(() => {
                    // This would normally come from an API
                    // Simulating live update
                    const currentLevel = parseInt(chargingLevel.textContent);
                    if (currentLevel < 100) {
                        // chargingLevel.textContent = (currentLevel + 1) + '%';
                        // progressBar.style.width = (currentLevel + 1) + '%';
                    }
                }, 10000); // Update every 10 seconds
            }
        });
    </script>
</body>
</html>