<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking Platform</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2a5bd7;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #2a5bd7 0%, #3b82f6 100%);
            --gradient-secondary: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-color);
            padding-top: 80px;
        }

        /* Modern Navbar */
        .navbar {
            background: var(--gradient-primary);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(42, 91, 215, 0.15);
            padding: 0.8rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 1rem;
        }

        .navbar-brand i {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-item {
            margin: 0 5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.8rem 1.2rem !important;
            border-radius: 10px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .nav-link i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        /* Underline animation */
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 3px;
            background: var(--secondary-color);
            border-radius: 2px;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 70%;
        }

        .nav-link.active::after {
            width: 70%;
        }

        /* Mobile menu */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-collapse {
            background: rgba(26, 32, 44, 0.95);
            border-radius: 15px;
            padding: 1rem;
            margin-top: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        @media (min-width: 992px) {
            .navbar-collapse {
                background: transparent;
                box-shadow: none;
                margin-top: 0;
                padding: 0;
            }
        }

        /* Badge for notifications */
        .nav-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--accent-color);
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="home.php">
      <i class="fas fa-parking"></i>
      <span>SmartPark EV</span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="fas fa-bars"></i>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>" href="home.php">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'slot.php' ? 'active' : ''; ?>" href="slot.php">
            <i class="fas fa-square-parking"></i>
            <span>Parking</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'detail.php' ? 'active' : ''; ?>" href="detail.php">
            <i class="fa-solid fa-circle-info"></i>
            <span>Recharge</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pay.php' ? 'active' : ''; ?>" href="pay.php">
            <i class="fas fa-credit-card"></i>
            <span>Payment</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sensor.php' ? 'active' : ''; ?>" href="sensor.php">
            <i class="fas fa-chart-line"></i>
            <span>Sensor Data</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn-logout" href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add active class based on current page
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split("/").pop();
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if(link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });

        // Logout button animation
        const logoutBtn = document.querySelector('.btn-logout');
        logoutBtn.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(239, 68, 68, 0.2)';
        });
        logoutBtn.addEventListener('mouseleave', function() {
            this.style.background = '';
        });
    });
</script>

</body>
</html>