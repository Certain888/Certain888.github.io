<!DOCTYPE html>
<html>
<head>
  <title>Points Tracker</title>
  <style>
    .container {
      max-width: 400px;
      margin: 0 auto;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
    }
    input[type="submit"] {
      padding: 10px 20px;
      background-color: #4CAF50;
      color: #fff;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php
    // Function to sanitize user input
    function sanitizeInput($input) {
      $input = trim($input);
      $input = stripslashes($input);
      $input = htmlspecialchars($input);
      return $input;
    }

    // Function to check if the account file exists
    function checkAccountFile() {
      $file = 'accountinfo.txt';
      if (!file_exists($file)) {
        file_put_contents($file, '');
      }
    }

    // Function to create a new account
    function createAccount($username, $password) {
      $file = 'accountinfo.txt';
      $data = $username . ':' . $password . ':0' . PHP_EOL;
      file_put_contents($file, $data, FILE_APPEND);
      echo 'Account created successfully! You can now log in.';
    }

    // Function to validate login credentials
    function login($username, $password) {
      $file = 'accountinfo.txt';
      $accounts = file($file, FILE_IGNORE_NEW_LINES);
      foreach ($accounts as $account) {
        list($storedUsername, $storedPassword, $points) = explode(':', $account);
        if ($username === $storedUsername && $password === $storedPassword) {
          $_SESSION['username'] = $username;
          $_SESSION['points'] = $points;
          return true;
        }
      }
      return false;
    }

    // Function to send points to another user
    function sendPoints($receiver, $points) {
      $file = 'accountinfo.txt';
      $accounts = file($file, FILE_IGNORE_NEW_LINES);
      foreach ($accounts as &$account) {
        list($username, $password, &$userPoints) = explode(':', $account);
        if ($_SESSION['username'] === $username) {
          if ($userPoints >= $points) {
            $userPoints -= $points;
            $_SESSION['points'] = $userPoints;
            break;
          } else {
            echo 'Insufficient points!';
            return;
          }
        }
      }
      foreach ($accounts as &$account) {
        list($username, $password, &$userPoints) = explode(':', $account);
        if ($receiver === $username) {
          $userPoints += $points;
          file_put_contents($file, implode(PHP_EOL, $accounts));
          echo 'Points sent successfully!';
          return;
        }
      }
      echo 'Receiver not found!';
    }

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      checkAccountFile();

      if (isset($_POST['signup'])) {
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        createAccount($username, $password);
      } elseif (isset($_POST['login'])) {
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        if (login($username, $password)) {
          header('Location: dashboard.php');
          exit();
        } else {
          echo 'Invalid username or password.';
        }
      } elseif (isset($_POST['send'])) {
        $receiver = sanitizeInput($_POST['receiver']);
        $points = (int) $_POST['points'];
        sendPoints($receiver, $points);
      }
    }
    ?>
    <h1>Points Tracker</h1>
    <h2>Sign Up</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" name="signup" value="Sign Up">
    </form>
    <h2>Log In</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="submit" name="login" value="Log In">
    </form>
  </div>
</body>
</html>
