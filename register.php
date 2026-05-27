<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Your existing CSS styles */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding: 50px 0;
        }
        img.img-fluid {
            width: 70%;
            transition: transform 0.3s ease, opacity 0.5s ease;
            margin-bottom: 20px;
        }
        img.img-fluid:hover {
            transform: scale(1.1);
            opacity: 0.9;
        }
        .signin {
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            margin-top: 60px;
        }
        h2.fw-bold {
            margin-bottom: 30px;
            color: #333;
            font-size: 1.8rem;
            text-align: center;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-group input {
            width: 100%;
            padding: 15px 45px;
            border-radius: 5px;
            border: 1px solid #ccc;
            outline: none;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.3rem;
            color: #007bff;
            transition: all 0.3s ease;
        }
        .form-group input:hover {
            border-color: #0056b3;
        }
        .form-group input:focus + .icon {
            transform: translateY(-50%) scale(1.2);
            color: #0056b3;
        }
        .sign-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.3s;
        }
        .sign-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .checkbox {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .checkbox .span {
            color: #007bff;
            cursor: pointer;
        }
        .checkbox .span:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            img.img-fluid {
                width: 90%;
                margin-bottom: 30px;
            }
            .signin {
                padding: 40px;
                background-color: white;
                border-radius: 8px;
                margin: 0px;
            }
            h2.fw-bold {
                font-size: 1.5rem;
                text-align: center;
            }
            .form-group input {
                width: 100%;
                padding-left: 40px;
            }
            .sign-btn {
                width: 100%;
            }
            body {
                background-color: #f8f9fa;
                font-family: Arial, sans-serif;
                padding: 0px;
            }
        }
    </style>
</head>
<body>
    <div class="container signin">
        <div class="row align-items-center">
            <div class="col-12 col-sm-6 text-center">
                <img src="images/login.png" alt="Login Image" class="img-fluid rounded-circle">
            </div>
            <div class="col-12 col-sm-6">
                <h2 class="fw-bold text-start">Sign Up!</h2>
                <form action="verify.php" method="post">
                    <div class="form-group">
                        <i class="fa-solid fa-user icon"></i> 
                        <input type="text" placeholder="Your username" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <i class="fa-regular fa-envelope icon"></i>
                        <input type="email" placeholder="Email address" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <i class="fa-solid fa-phone icon"></i>
                        <input type="text" placeholder="Phone number" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <i class="fa-solid fa-lock icon"></i>
                        <input type="password" placeholder="Password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <i class="fa-solid fa-house icon"></i>
                        <input type="text" placeholder="Address" name="address" class="form-control" required>
                    </div>
                    <div class="checkbox">
                        <input type="checkbox" name="terms" required> Please accept the <span class="span">Terms and conditions</span>
                    </div>
                    <input class="sign-btn" type="submit" name="register" value="Sign up">
                </form>
                <p class="mt-3">Already have an account? <a href="index.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
