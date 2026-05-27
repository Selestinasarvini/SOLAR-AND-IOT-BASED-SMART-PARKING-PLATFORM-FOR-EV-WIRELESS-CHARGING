<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In</title>
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
      body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        padding: 50px 0;
      }

      /* Container and Layout */
      .signin {
        /*padding: 40px;*/
        background-color: white;
        border-radius: 10px;
        /*margin-top: 40px;*/
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }

      /* Image Styling with New Animation */
      .animated-img {
        width: 75%;
        border-radius: 50%;
        transition: transform 0.5s ease-in-out, box-shadow 0.5s ease;
      }

      .animated-img:hover {
        transform: rotate(360deg) scale(1.1);
        box-shadow: 0 0 20px rgba(255, 0, 150, 0.5);
      }

      /* Header Styling */
      h2.fw-bold {
        margin-bottom: 20px;
        color: #333;
      }

      /* Form Input Fields */
      .form-group {
        position: relative;
        margin-bottom: 20px;
      }

      .login-control {
        width: 100%;
        padding: 10px 40px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
        transition: border-color 0.3s, box-shadow 0.3s;
      }

      .login-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
      }

      .icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
        color: #007bff;
        transition: transform 0.3s, color 0.3s;
      }

      .login-control:focus + .icon {
        transform: translateY(-50%) scale(1.2);
        color: #0056b3;
      }

      .btn {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        transition: background-color 0.3s, transform 0.3s;
      }

      .btn:hover {
        background-color: #0056b3;
        transform: scale(1.05);
      }

      /* Link Styling */
      .sign-link {
        color: #007bff;
        cursor: pointer;
        transition: color 0.3s;
      }

      .sign-link:hover {
        color: #0056b3;
        text-decoration: underline;
      }

      /* Responsive Design */
      @media (max-width: 576px) {
        .animated-img {
          width: 80%;
        }

        h2.fw-bold {
          font-size: 1.5rem;
          text-align: center;
        }

        .signin {
          padding: 20px;
        }

        .btn {
          font-size: 0.9rem;
          padding: 10px;
        }
      }
    </style>
  </head>
  <body>
    <div class="container signin">
      <div class="row align-items-center">
        <div class="col-12 col-sm-6 text-center">
          <img
            src="login1.png"
            alt="Login Image"
            class="img-fluid "
          />
        </div>
        <div class="col-12 col-sm-6">
          <h2 class="fw-bold text-center">Login</h2>
          <form action="" method="post" autocomplete="off">
            <div class="form-group mx-4">
              <i class="fa-solid fa-envelope icon"></i>
              <input
                type="email"
                placeholder="Email address"
                class="login-control"
                name="email"
                required
              />
            </div>
            <div class="form-group mx-4">
              <i class="fa-solid fa-lock icon"></i>
              <input
                type="password"
                placeholder="Password"
                class="login-control"
                name="password"
                required
              />
            </div>
            <div class="sign-btn mx-4">
              <input class="btn" type="submit" name="login" value="Login" />
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php if (isset($_COOKIE["error"])): ?>
      <script>
        Swal.fire({
          title: "Error",
          text: "Email or password incorrect",
          icon: "error",
        });
      </script>
    <?php endif; ?>
  </body>
</html>

<?php

include("connection.php");

if(isset($_POST["login"]))
{
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $sql = "select * from 4339_user where email = '$email' and password = '$password'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        // $_SESSION["id"] = $row["id"];
        echo '<script>alert("Login Successful");window.location.replace("home.php");</script>';
    }
    else
    {
        echo '<script>alert("Invalid Email and Password");window.location.replace("index.php");</script>';
        echo mysqli_error($conn);
    }
}
