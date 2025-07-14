<?php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "event_planner");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $loginMessage = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loginMessage = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

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

    <h2>Event Planner</h2>

    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">LOGIN</button>
    </form>

    <button class="invitation-btn">Sign in with Invitation</button>

    <div class="links">
      <a href="register.php">Register</a>
      <a href="forgot-password.php">Forgot Password</a>
    </div>
  </div>
</body>
</html>
