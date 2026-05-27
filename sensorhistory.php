<?php
include("connection.php");

// Fetch all records from the database with pagination
$records_per_page = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records
$count_sql = "SELECT COUNT(*) as total FROM 4349_rfid";
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Fetch paginated records
$sql = "SELECT * FROM 4349_rfid ORDER BY id DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);

// Calculate statistics
$stats_sql = "SELECT 
    AVG(value2) as avg_voltage,
    AVG(value3) as avg_current,
    MAX(value2) as max_voltage,
    MAX(value3) as max_current,
    SUM(value4) as total_units,
    MIN(reading_time) as first_reading,
    MAX(reading_time) as last_reading
    FROM 4349_rfid";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

// Get daily consumption
$daily_sql = "SELECT DATE(reading_time) as date, 
              AVG(value2) as avg_voltage,
              AVG(value3) as avg_current,
              SUM(value4) as daily_units
              FROM 4349_rfid 
              GROUP BY DATE(reading_time) 
              ORDER BY date DESC LIMIT 7";
$daily_result = mysqli_query($conn, $daily_sql);
$daily_data = [];
while($row = mysqli_fetch_assoc($daily_result)) {
    $daily_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartPark EV | Sensor History</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2a5bd7;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --danger-color: #ef4444;
            --purple-color: #8b5cf6;
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

        /* Statistics Cards */
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
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
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
            background: linear-gradient(135deg, var(--purple-color) 0%, #a78bfa 100%);
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

        .stat-card:nth-child(5) .stat-icon {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
        }

        .stat-card:nth-child(6) .stat-icon {
            background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
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

        .stat-unit {
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* History Table Card */
        .history-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--card-shadow);
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2.5rem;
        }

        .card-header-custom {
            background: transparent;
            border-bottom: 2px solid #f1f5f9;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .card-header-custom h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .card-header-custom h4 i {
            color: var(--primary-color);
            background: rgba(42, 91, 215, 0.1);
            padding: 10px;
            border-radius: 12px;
        }

        /* Table Styling */
        .history-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
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

        .history-table thead th i {
            margin-right: 8px;
            font-size: 1rem;
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

        .value-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .voltage-cell {
            color: var(--purple-color);
        }

        .current-cell {
            color: var(--accent-color);
        }

        .units-cell {
            color: var(--secondary-color);
        }

        .time-cell {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #64748b;
            font-weight: 500;
        }

        .pagination .page-link {
            border: none;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border: none;
        }

        .pagination .page-link:hover {
            background: rgba(42, 91, 215, 0.1);
            color: var(--primary-color);
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

        .btn-back {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
        }

        .btn-export {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #34d399 100%);
            color: white;
        }

        .btn-refresh {
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

        /* Responsive */
        @media (max-width: 768px) {
            .container-main {
                padding-top: 20px;
            }
            
            .page-header {
                padding: 1.5rem;
            }
            
            .history-card, .charts-container {
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .history-table thead th,
            .history-table tbody td {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
            
            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
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

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .no-data h4 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #64748b;
        }

        .no-data p {
            font-size: 1rem;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container container-main">



        <!-- History Table Card -->
        <div class="history-card">
            <!-- Card Header -->
            <div class="card-header-custom">
                <h4><i class="fas fa-table"></i> Sensor Readings History</h4>
                <div class="d-flex gap-2">
                    <a href="sensor.php" class="btn-action btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
            <!-- Table -->
            <div class="table-responsive history-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag"></i> </th>
                            <th><i class="fas fa-bolt voltage-cell"></i> Voltage</th>
                            <th><i class="fas fa-bolt current-cell"></i> Current</th>
                            <th><i class="fas fa-battery-full units-cell"></i> Units</th>
                            <th><i class="fas fa-clock"></i> Reading Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = $offset + 1;
                        while($row = mysqli_fetch_assoc($result)): 
                            $power = $row['value2'] * $row['value3'];
                            $power_kw = $power / 1000;
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $i ?></td>
                            <td class="value-cell voltage-cell"><?= number_format($row['value2'], 2) ?> V</td>
                            <td class="value-cell current-cell"><?= number_format($row['value3'], 2) ?> A</td>
                            <td class="value-cell units-cell"><?= number_format($row['value4'], 2) ?> kWh</td>
                            <td class="time-cell">
                                <div><?= date('h:i:s A', strtotime($row['reading_time'])) ?></div>
                                <div class="small text-muted"><?= date('M d, Y', strtotime($row['reading_time'])) ?></div>
                            </td>
                        </tr>
                        <?php $i++; endwhile; ?>
                    </tbody>
                </table>
            </div>

            
            <?php else: ?>
            <div class="no-data">
                <i class="fas fa-database"></i>
                <h4>No Sensor Data Available</h4>
                <p>No sensor readings have been recorded yet.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>