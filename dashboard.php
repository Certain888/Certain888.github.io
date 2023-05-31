<!-- dashboard.php -->
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <style>
    .container {
      max-width: 400px;
      margin: 0 auto;
    }
    input[type="number"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php
    session_start();
    if (!isset($_SESSION['username']) || !isset($_SESSION['points'])) {
      header('Location: index.php');
      exit();
    }

    // Display the user's points
    echo '<h1>Welcome, ' . $_SESSION['username'] . '!</h1>';
    echo '<h2>Your Points: ' . $_SESSION['points'] . '</h2>';

    // Handle sending points
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      checkAccountFile();
      $receiver = sanitizeInput($_POST['receiver']);
      $points = (int) $_POST['points'];
      sendPoints($receiver, $points);
    }
    ?>
    <h2>Send Points</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="text" name="receiver" placeholder="Recipient Username" required>
      <input type="number" name="points" placeholder="Points" required>
      <input type="submit" name="send" value="Send">
    </form>
    <a href="logout.php">Log Out</a>
  </div>
</body>
</html>
