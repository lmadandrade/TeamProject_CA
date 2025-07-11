<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "event_planner");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message
$loginMessage = "";

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $loginMessage = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginMessage = "Invalid email format.";
    } else {
        // Find user by email
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['name'];
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

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Planner Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>
  <div class="login-container">
    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<p style="color: green; text-align: center;">Registration successful. Please log in.</p>';
    }

    if (!empty($loginMessage)) {
        echo '<p style="color: red; text-align: center;">' . htmlspecialchars($loginMessage) . '</p>';
    }
    ?>

    <h2>Login to Event Planner</h2>

    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>

    <button class="invitation-btn">Sign in with Invitation</button>

    <div class="links">
      <a href="forgot-password.php">Forgot password?</a>
      <a href="register.php">Register here</a>
    </div>
  </div>
</body>
</html>
