<?php
session_start(); // start the session so we can store user info later

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "event_planner");

if ($conn->connect_error) {
    // show error if DB doesn't connect
    die("Connection failed: " . $conn->connect_error);
}

// default value for login feedback
$loginMessage = "";

// Check if user submitted the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // grab and clean up the values
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // make sure fields aren't empty
    if (empty($email) || empty($password)) {
        $loginMessage = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // very basic email format check
        $loginMessage = "Invalid email format.";
    } else {
        // find the user in database
        $stmt = $conn->prepare("SELECT id, name, password, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // get the result to see if user exists
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // verify the password using built-in method
            if (password_verify($password, $user['password'])) {
                // everything checks out → store user data
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                // redirect to dashboard after login
                header("Location: dashboard.php");
                exit;
            } else {
                $loginMessage = "Incorrect password.";
            }
        } else {
            $loginMessage = "User not found.";
        }

        $stmt->close();
    }

    // close the connection
    $conn->close();
}
?>

<!-- login page HTML starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Planner Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- external styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

    <nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.html"><strong>Eventz</strong></a>
        
        <!-- hamerburger menu toggler for small screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
        <!-- left navigation -->
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
        </ul>
        <!-- right navigation -->
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        </ul>
        </div>
    </div>
    </nav>
  <main>
  <div class="login-container">

    <?php
    // if user just finished registration
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<p style="color: green; text-align: center;">Registration successful. Please log in.</p>';
    }

    // show errors during login attempt
    if (!empty($loginMessage)) {
        echo '<p style="color: red; text-align: center;">' . htmlspecialchars($loginMessage) . '</p>';
    }
    ?>

    <h2>Event Planner</h2>

    <!-- login form goes here -->
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">LOGIN</button>
    </form>

    <!-- maybe I'll hook this up later 
    <!-- <button class="invitation-btn">Sign in with Invitation</button> -->

    <!-- links for users who forgot or want to register -->
    <div class="links">
        <a href="register.php">Register</a>
        <a href="forgot-password.php">Forgot Password</a>
    </div>




  </div>
  </main>

  <footer class="footer">
    <div class="container">
      Eventz © 2025
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
