<?php

session_start();
require 'config_budget.php';

$alerts = $_SESSION['alerts']??[];
$active_form=$_SESSION['active_form']??'';
unset($_SESSION['alerts'], $_SESSION['active_form']);

if (!isset($_SESSION['user_id'])) {
 /* $_SESSION['alerts'][] = [
    'type' => 'error',
    'message' => 'Please log in.'
  ];
  header('Location: index.php');
  exit();*/
} 
else {

  $user_id = $_SESSION['user_id'];

  $firstname=$_SESSION['firstname'] ?? null;
  $surname=$_SESSION['surname'] ?? null;
  $email=$_SESSION['email'] ?? null;

  /*
  $alerts = $_SESSION['alerts']??[];
  $active_form=$_SESSION['active_form']??'';

  unset($_SESSION['alerts']);
  unset($_SESSION['active_form']);*/

  if ($firstname !== null) $_SESSION['firstname'] = $firstname;
  if ($surname !== null) $_SESSION['surname'] = $surname;
  if ($email !== null) $_SESSION['email'] = $email;

}

?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>My Account</title>

    <link rel="stylesheet" href="/budget-tracker/assets/css/styles.css">

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

  </head>

  <body>

    <header>
      <a href="index.php" class="logo"><img src="/budget-tracker/assets/images/ego-no-bg.png" alt="Ofi Logo"></a>
      <nav>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
      </nav>
      <div class="user-auth">
        <?php if (!empty($firstname)): ?>
        <div class="profile-box">
          <div class="profile-circle"><?= strtoupper($firstname[0]); ?></div>
          <div class="dropdown">
            <a href="account.php">My Account</a>
            <a href="income.php">My Income</a>
            <a href="expenses.php">My Expenses</a>
            <a href="logout.php">Log Out</a>
          </div>
        </div>
        <?php else: ?>
        <button type="button" class="login-btn-modal">Log In</button>
        <?php endif; ?>
    </div>
    </header>

    <?php if (!empty($alerts)): ?>
    <div class="alert-box">
      <?php foreach($alerts as $alert): ?>
      <div class="alert <?= $alert['type']; ?>">
        <i class='bx <?= $alert['type'] === 'success' ? 'bxs-check-circle' : 'bxs-x-circle'; ?>'></i>
        <span><?=$alert['message']; ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="auth-modal <?= $active_form === 'register' ? 'show slide' : ($active_form === 'login' ? 'show' : ''); ?>">
      <button type="button" class="close-btn-modal">
        <i class='bx bxs-x-circle'></i> 
      </button>
      <div class="form-box login">
        <h2>
          Log In
        </h2>
        <form action="auth_process.php" method="POST">
          <div class="input-box">
            <input type="email" name="email" placeholder="Enter Email Address" required>
            <i class='bx bxs-envelope-alt'></i> 
          </div>
          <div class="input-box">
            <input type="password" name="password" placeholder="********" required>
            <i class='bx bxs-lock'></i> 
          </div>
          <button type="submit" name="login_btn" class="btn">
            Log In
          </button>
          <p>Don't have an account? <a href="#" class="register-link">Register Here</a></p>
        </form>
      </div>

      <div class="form-box register">
        <h2>
          Register
        </h2>
        <form action="auth_process.php" method="POST">
          <div class="input-box">
            <input type="text" name="firstname" placeholder="Enter First Name" required>
            <i class='bx bxs-user'></i>  
          </div>
          <div class="input-box">
            <input type="text" name="surname" placeholder="Enter Surname" required>
            <i class='bx bxs-user'></i>  
          </div>
          <div class="input-box">
            <input type="email" name="email" placeholder="Enter Email Address" required>
            <i class='bx bxs-envelope-alt'></i> 
          </div>
          <div class="input-box">
            <input type="password" name="password" placeholder="********" required>
            <i class='bx bxs-lock'></i> 
          </div>
          <button type="submit" name="register_btn" class="btn">
            Register
          </button>
          <p>Already have an account? <a href="#" class="login-link">Log In Here</a></p>
        </form>
      </div>
    </div>

    <div class="details">

      <h4>Details</h4>
      <a href="edit_account.php">
        <button class="acc-button-edit">
          Edit
        </button>
      </a>
      <a href="delete_account.php?id=<?= $_SESSION['user_id'] ?>" onclick="return confirm('Are you sure you want to delete your account?')">
        <button class="acc-button-delete">
          Delete
        </button>
      </a>
      <p class="first-name">
        First Name: 
        <?php if (!empty($firstname)): ?>
          <?= $firstname; ?>
        <?php endif; ?>
      </p>
      <p class="sur-name">
        Surname: 
        <?php if (!empty($surname)): ?>
          <?= $surname; ?>
        <?php endif; ?>
      </p>
      <p class="e-mail">
        Email: 
        <?php if (!empty($email)): ?>
          <?= $email; ?>
        <?php endif; ?>
      </p>

    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>