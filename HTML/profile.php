<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

// Fetch user data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newPassword = $_POST['password'] ?? '';
  // hash for security
  $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);

  // update
  $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
  $stmt->bind_param("si", $hashedNewPassword, $userId);

  if ($stmt->execute()) {
    header("Location: dashboard.php");
    exit;
  } else {
    echo "Error updating profile: " . $stmt->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Load bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- load external CSS file -->
  <link rel="stylesheet" href="../CSS/style.css" /></head>

</head>
<body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
        <a class="navbar-brand" href="dashboard.php"><strong>Eventz</strong></a>
        
        <!-- hamerburger menu toggler for small screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <!-- left navigation -->
            <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="create_event.php">Create Event</a></li>
            <li class="nav-item"><a class="nav-link" href="invitations.php">Invitations</a></li>
            </ul>
            <!-- right navigation -->
            <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
        </div>
    </nav>

  <div class="profile-wrapper">
    <h2>User Profile</h2>

    <form action="profile.php" method="POST" class="profile-form">
      <h4>Profile Information</h4>

      <div>
        <label for="fullname">Full Name:</label>
        <input type="text" class="profile-input" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['name']); ?>" disabled />
      </div>

      <div>
        <label for="email">Email Address:</label>
        <input type="email" class="profile-input" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled />
      </div>

      <div>
        <label for="password">Password:</label>
        <input type="password" class="profile-input" name="password" placeholder="Enter new password" minlength="8"required />
      </div>

      <div class="form-actions">
        <button type="submit" class="save-btn">Save Changes</button>
        <a href="dashboard.php" class="cancel-btn">Cancel</a>
      </div>

      <div class="delete-account">
        <a href="delete_account.php" style="color: red;">Delete Account</a>
      </div>
    </form>
  </div>

  <footer class="footer">
    <div class="container">Eventz © 2025</div>
  </footer>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
