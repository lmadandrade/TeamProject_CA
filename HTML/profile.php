<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

// Fetch user data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($_POST['fullname']);
  $default_reminder = $_POST['default_reminder'];
  $default_color = $_POST['default_color'];

  $stmt = $conn->prepare("UPDATE users SET name = ?, default_reminder = ?, default_color = ? WHERE id = ?");
  $stmt->bind_param("sssi", $fullname, $default_reminder, $default_color, $userId);

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
  <!-- Adjust the path below if needed based on your folder structure -->
  <link rel="stylesheet" href="../CSS/style.css" />
</head>
<body>

  <div class="profile-wrapper">
    <h2>User Profile</h2>

    <form action="profile.php" method="POST" class="profile-form">
      <h4>Profile Information</h4>

      <div>
        <label for="fullname">Full Name:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['name']); ?>" required />
      </div>

      <div>
        <label for="email">Email Address:</label>
        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled />
      </div>

      <div>
        <label for="default_reminder">Default Reminder:</label>
        <select id="default_reminder" name="default_reminder">
          <option value="24hr" <?php if ($user['default_reminder'] === '24hr') echo 'selected'; ?>>24 hours before</option>
          <option value="1hr" <?php if ($user['default_reminder'] === '1hr') echo 'selected'; ?>>1 hour before</option>
          <option value="custom" <?php if ($user['default_reminder'] === 'custom') echo 'selected'; ?>>Custom</option>
        </select>
      </div>

      <div>
        <label for="default_color">Default Event Color:</label>
        <select id="default_color" name="default_color">
          <option value="Blue" <?php if ($user['default_color'] === 'Blue') echo 'selected'; ?>>Blue</option>
          <option value="Yellow" <?php if ($user['default_color'] === 'Yellow') echo 'selected'; ?>>Yellow</option>
          <option value="Green" <?php if ($user['default_color'] === 'Green') echo 'selected'; ?>>Green</option>
        </select>
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

</body>
</html>
