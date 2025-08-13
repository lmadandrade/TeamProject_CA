<?php

session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

require_once "db.php";

$userId = (int)$_SESSION['user_id'];

// code to handle accept or declines
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // get the id of row in event_participants and convert to int
    $epId   = $_POST['ep_id'];
    $epId = (int)$epId;

    $action = $_POST['action']; // either accepted or declined

    // query to change status from penidng to accepted or declined
    if ($epId && ($action === 'accepted' || $action === 'declined')) {
    $sql = "UPDATE event_participants
        SET status = ?
        WHERE id = ?
        AND status = 'pending'
        AND user_id = ?";

    $updateStatus = $conn->prepare($sql);
    $updateStatus->bind_param("sii", $action, $epId, $userId);
    $updateStatus->execute();
    $updateStatus->close();

    header("Location: dashboard.php");
    exit;
  }
}

// --- get all the pending invitations for user ---
$sql = " SELECT
    ep.id  AS ep_id,
    e.id   AS event_id,
    e.title,
    e.event_date,
    e.location
    FROM event_participants ep
    JOIN events e ON e.id = ep.event_id
    WHERE ep.status = 'pending'
    AND ep.user_id = ?
    ORDER BY e.event_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

// store result of query
$results = $stmt->get_result();

$invites = [];
// store results in associative array
while ($result = $results->fetch_assoc()) {
    $invites[] = $result;
}

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Invitations</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Load bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- load external CSS file -->
  <link rel="stylesheet" href="../CSS/style.css" /></head>
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

    <div class="container my-4" style="max-width: 760px;">
        <h1>Pending invitations</h1>

        <!-- Code for if invites is empty -->
        <?php if (empty($invites)): ?>
            <div class="alert alert-secondary mt-3">You dont have any pending invitations.</div>
        <!-- Code for if it isnt empty -->
        <?php else: ?>
            <div class="list-group mt-3">
                <?php foreach ($invites as $inv): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1"><?php echo $inv['title']; ?></h4>
                            <div>
                                <?php echo date('F j, Y g:i A', strtotime($inv['event_date'])); ?> - <?php echo $inv['location']; ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <form method="post" class="d-inline">
                                <input type="hidden" name="ep_id" value="<?php echo $inv['ep_id']; ?>">
                                <button type="submit" name="action" value="accepted" class="btn btn-sm btn-accept">Accept</button>
                                <button type="submit" name="action" value="declined" class="btn btn-sm btn-decline">Decline</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">Eventz © 2025</div>
    </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>