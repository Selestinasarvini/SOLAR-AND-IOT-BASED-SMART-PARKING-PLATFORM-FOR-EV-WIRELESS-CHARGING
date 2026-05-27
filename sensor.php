<?php
session_start();
include("connection.php");

$sql1 = "SELECT * FROM 4349_rfid order by id desc limit 1";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
$value2 = $row1['value2'];
$value3 = $row1['value3'];
$value4 = $row1['value4'];
$time = $row1['reading_time'];

// Calculate power (P = V * I)
$power = $value2 * $value3;
$power_kw = $power / 1000;

// Calculate estimated charging time (assuming 60kWh battery)
$battery_capacity = 60; // kWh
if ($power_kw > 0) {
    $estimated_time_hours = $battery_capacity / $power_kw;
    $hours = floor($estimated_time_hours);
    $minutes = round(($estimated_time_hours - $hours) * 60);
} else {
    $hours = 0;
    $minutes = 0;
}

// Get historical data for charts
$sql_history = "SELECT reading_time, value2 as voltage, value3 as current, value4 as units FROM 4349_rfid ORDER BY reading_time DESC LIMIT 10";
$result_history = mysqli_query($conn, $sql_history);
$history_data = [];
while ($row = mysqli_fetch_assoc($result_history)) {
    $history_data[] = $row;
}
$history_data = array_reverse($history_data); // Oldest first for chart
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SmartPark EV | Sensor Analytics</title>
    <meta http-equiv="refresh" content="10">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 6px solid var(--primary-color);
            box-shadow: var(--card-shadow);
            animation: slideInDown 0.6s ease-out;
        }

        .page-header h4 {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #64748b;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            font-weight: 600;
            font-size: 0.9rem;
            animation: pulse 2s infinite;
            margin-top: 0.5rem;
        }

        .live-indicator::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: inline-block;
        }

        /* Sensor Cards Grid */
        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .sensor-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border-top: 5px solid var(--primary-color);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        .sensor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .sensor-card.voltage {
            border-top-color: #8b5cf6;
        }

        .sensor-card.current {
            border-top-color: var(--accent-color);
        }

        .sensor-card.power {
            border-top-color: var(--danger-color);
        }

        .sensor-card.units {
            border-top-color: var(--secondary-color);
        }

        .sensor-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .sensor-card.voltage .sensor-icon {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(167, 139, 250, 0.05) 100%);
            color: #8b5cf6;
        }

        .sensor-card.current .sensor-icon {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.05) 100%);
            color: var(--accent-color);
        }

        .sensor-card.power .sensor-icon {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(252, 165, 165, 0.05) 100%);
            color: var(--danger-color);
        }

        .sensor-card.units .sensor-icon {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
            color: var(--secondary-color);
        }

        .sensor-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .sensor-label {
            font-size: 1rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sensor-unit {
            font-size: 1rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .sensor-status {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .status-normal {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--accent-color);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Last Reading Card */
        .reading-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2.5rem;
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .reading-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .reading-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .reading-header h3 i {
            color: var(--primary-color);
            background: rgba(42, 91, 215, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        .reading-time {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 1rem;
        }

        .reading-date {
            color: #64748b;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Charts Container */
        .charts-container {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2.5rem;
            animation: fadeIn 1s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .charts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .charts-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .charts-header h3 i {
            color: var(--primary-color);
            background: rgba(42, 91, 215, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        .chart-wrapper {
            height: 300px;
            margin-bottom: 2rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            animation: fadeInUp 0.5s ease-out;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        }

        .stat-card:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, var(--accent-color) 0%, #fbbf24 100%);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, var(--danger-color) 0%, #fca5a5 100%);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #34d399 100%);
        }

        .stat-content h4 {
            font-size: 1rem;
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

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-action {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
        }

        .btn-history {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            color: white;
        }

        .btn-export {
            background: linear-gradient(135deg, var(--accent-color) 0%, #fbbf24 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            color: white;
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

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding-top: 20px;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .reading-card, .charts-container {
                padding: 1.5rem;
            }
            
            .sensor-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
            
            .chart-wrapper {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container container-main">
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between">
            <h4><i class="fas fa-chart-line me-2"></i>Sensor Analytics Dashboard</h4>
            <a href="sensorhistory.php" class="btn-action btn-history">
                <i class="fas fa-history"></i>
                View Sensor History
            </a>
        </div>

        <!-- Sensor Cards Grid -->
        <div class="sensor-grid">
            <div class="sensor-card voltage">
                <div class="sensor-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="sensor-label">Voltage</div>
                <div class="sensor-value"><?= $value2 ?></div>
                <div class="sensor-unit">Volts (V)</div>

            </div>

            <div class="sensor-card current">
                <div class="sensor-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="sensor-label">Current</div>
                <div class="sensor-value"><?= $value3 ?></div>
                <div class="sensor-unit">Amperes (A)</div>
            </div>

            <div class="sensor-card units">
                <div class="sensor-icon">
                    <i class="fas fa-battery-full"></i>
                </div>
                <div class="sensor-label">Units Consumed</div>
                <div class="sensor-value"><?= $value4 ?></div>
                <div class="sensor-unit">Kilowatt-hours (kWh)</div>

            </div>
        </div>

        <!-- Last Reading Card -->
        <div class="reading-card">
            <div class="reading-header">
                <h3><i class="fas fa-clock"></i> Last Reading Time</h3>
            </div>
            <div class="reading-time">
                <?= date('h:i:s A', strtotime($time)) ?>
            </div>
            <div class="reading-date">
                <?= date('F j, Y', strtotime($time)) ?>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>