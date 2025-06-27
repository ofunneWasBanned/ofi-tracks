<?php
session_start();

// Optional: get name or email from session if needed
$firstname = $_SESSION['firstname'] ?? null;

$active_form = $_SESSION['active_form'] ?? '';
unset($_SESSION['active_form']);

?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>About Ofi</title>

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
          <div class="profile-circle">
            <?= strtoupper($firstname[0]); ?>
          </div>
          <div class="dropdown">
            <a href="account.php">My Account</a>
            <a href="income.php">My Income</a>
            <a href="expenses.php">My Expenses</a>
            <a href="logout.php">Log Out</a>
          </div>
          <?php else: ?>
            <a href="index.php?login=1" class="login-btn-modal">Log In</a>
          <?php endif; ?>
        </div>
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

    <div class="how-to">
      <h4>Need help navigating Ofi? You're in the right place!</h4>
      <div class="instruction">
        <p>First, you'll need to log in. This is so that you can save your personal budgeting information to your account.</p>
        <div class="screenshot" id="prelogin">
          <img src="/budget-tracker/assets/images/prelogin.png" alt="">
        </div>
        <p>Without logging in, you will only be able to access the home, about, and contact pages.</p>
      </div>
      <div class="instruction">
        <p>Click the 'log in' button on the top right hand side, then log in, or register if you don't have an account yet.</p>
        <div class="screenshot" id="login-modal">
          <img src="/budget-tracker/assets/images/login-modal.png" alt="">
        </div>
      </div>
      <div class="instruction">
        <p>Once you log in, you'll be able to see your own personal balance.</p>
        <div class="screenshot" id="balance-june">
          <img src="/budget-tracker/assets/images/balance-june.png" alt="">
        </div>
        <p>Click on the icons to see more information on your income or spending!</p>
      </div>
      <div class="instruction">
        <p>You can add a source of income or an expense by filling in this form.</p>
        <div class="screenshot" id="add-income">
          <img src="/budget-tracker/assets/images/add-income.png" alt="">
        </div>
        <p>You can see all your incomes/expenses in the table below the form</p>
      </div>
      <div class="instruction">
        <p>You can delete entries.</p>
        <div class="screenshot" id="edit-income">
          <img src="/budget-tracker/assets/images/edit-income.png" alt="">
        </div>
        <p>And you can edit them.</p>
      </div>
      <div class="instruction">
        <p>And you can see the balance of each individual month by filtering for the month then clicking on the Ofi logo in the top left hand corner</p>
        <div class="screenshot" id="balance-march">
          <img src="/budget-tracker/assets/images/balance-march.png" alt="">
        </div>
        <p>Fun Fact: Ofi's logo is the character for money in Nsibidi, a Nigerian writing system</p>
      </div>
      <div class="instruction">
        <p>You also access your entries and your account and log out through a dropdown menu.</p>
        <div class="screenshot" id="dropdown">
          <img src="/budget-tracker/assets/images/dropdown.png" alt="">
        </div>
        <p>Just click on your initial, where the login button was earlier.</p>
      </div>  
      <div class="instruction">
        <p>You can edit your account details.</p>
        <div class="screenshot" id="acc-details">
          <img src="/budget-tracker/assets/images/acc-details.png" alt="">
        </div>
        <p>You can also delete your account, but why would you want to do that?</p>
      </div>
      <div class="instruction">
        <p>You can contact me through this form.</p>
        <div class="screenshot" id="contact">
          <img src="/budget-tracker/assets/images/contact.png" alt="">
        </div>
        <p>I'll try to respond as soon as possible!</p>
      </div>
        
    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>