<?php

session_start();
require 'config_budget.php';

if (!isset($_GET['id'])) {
    header('Location: income.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$id = (int) $_GET['id'];

// Fetch income with period
$stmt = $pdo->prepare("
    SELECT et.*, b.period
    FROM income et
    JOIN budgets b ON et.budget_id = b.id
    WHERE et.id = ? AND et.user_id = ?
");
$stmt->execute([$id, $user_id]);
$income = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$income) {
    echo "Income record not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $category = $_POST['category'] ?? '';
  $title = $_POST['title'] ?? '';
  $amount = floatval($_POST['amount'] ?? 0);
  $period = $_POST['period'] ?? date('Y-m');

  // Check if budget exists for this user and period
  $stmtBudget = $pdo->prepare("SELECT id FROM budgets WHERE user_id = ? AND period = ? LIMIT 1");
  $stmtBudget->execute([$user_id, $period]);
  $budget = $stmtBudget->fetch(PDO::FETCH_ASSOC);

  if (!$budget) {
      // Create new budget entry for this period
      $stmtInsertBudget = $pdo->prepare("INSERT INTO budgets (user_id, period) VALUES (?, ?)");
      $stmtInsertBudget->execute([$user_id, $period]);
      $budget_id = $pdo->lastInsertId();
  } else {
      $budget_id = $budget['id'];
  }

  // Update the income
  $updateStmt = $pdo->prepare("
      UPDATE income 
      SET category = ?, title = ?, amount = ?, budget_id = ? 
      WHERE id = ? AND user_id = ?
  ");
  $updateStmt->execute([$category, $title, $amount, $budget_id, $id, $user_id]);

  $_SESSION['alerts'][] = ['type' => 'success', 'message' => 'Income updated.'];
  header('Location: income.php');
  exit();
}

$firstname=$_SESSION['firstname'] ?? null;
$surname=$_SESSION['surname'] ?? null;
$email=$_SESSION['email'] ?? null;
$alerts = $_SESSION['alerts']??[];
$active_form=$_SESSION['active_form']??'';

unset($_SESSION['alerts']);
unset($_SESSION['active_form']);

if ($firstname !== null) $_SESSION['firstname'] = $firstname;
if ($surname !== null) $_SESSION['surname'] = $surname;
if ($email !== null) $_SESSION['email'] = $email;

?>

<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Income</title>

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

    <div class="in-container">
      <?php if (!empty($firstname)): ?>
        <h2>
          <?= ($firstname); ?>'s Income
        </h2>
      <?php else: ?>
        <h2>
          Your Income
        </h2>
      <?php endif; ?>
      <div class="income-error">
        <p></p>
      </div>
      <div class="income-content">
        <div class="your-income">
          <form action="" method="POST">
            <label for="category">Income Source: </label>
              <select name="category" class="income-cat" value="<?= htmlspecialchars($income['category']) ?>" required>
                <option value="">Choose Source</option>
                <option value="Salary">Salary</option>
                <option value="Freelancing">Freelance</option>
                <option value="Socials">Social Media & Sponsorships</option>
                <option value="Investment">Investments & Interest</option>
                <option value="Other">Other</option>
              </select><br>
            </label>

            <label>Income Description:
              <input type="text" name="title" value="<?= htmlspecialchars($income['title']) ?>">
            </label><br>
            <label>Income Amount:
              <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($income['amount']) ?>" required>
            </label><br>
            <label for="period">Income Month:
              <input type="month" name="period" value="<?= htmlspecialchars($income['period']) ?>">
            </label><br>
            <button type="submit" class="button" id="update-income-button">Update Income</button>
            <button class="button" id="cancel-income-update-button">
              <a href="income.php" style="text-decoration:none;color:#522B29;">Cancel</a>
            </button>
          </form>
        </div>
      </div>
    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>
