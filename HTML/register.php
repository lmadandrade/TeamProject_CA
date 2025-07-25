<?php
// Show errors while working on the project
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// needed to track sessions later
session_start();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // open database connection
    $conn = new mysqli("localhost", "root", "", "event_planner");

    // if connection fails, show error and stop
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // get all the form values safely
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // check if any field is empty
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required.");
    }

    // quick check for email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // make sure passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // if user is already registered
    if ($stmt->num_rows > 0) {
        die("Email already registered.");
    }

    $stmt->close();

    // encrypt the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

    // if saved, redirect to login with success message
    if ($stmt->execute()) {
        header("Location: login.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<!-- This part only runs when page loads first time -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register - Event Planner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- load external CSS file -->
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>
  <div class="login-container">
    <h2>Create your account</h2>

    <!-- registration form -->
    <form method="POST" action="register.php">
      <input type="text" name="fullname" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />

      <button type="submit">Register</button>
    </form>

    <div class="links">
      <a href="login.php">Already have an account? Login here</a>
    </div>
  </div>
</body>
</html>
