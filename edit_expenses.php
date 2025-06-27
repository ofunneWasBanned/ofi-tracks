<?php

session_start();
require 'config_budget.php';

if (!isset($_GET['id'])) {
    header('Location: expenses.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$id = (int) $_GET['id'];

// Fetch expense with period
$stmt = $pdo->prepare("
    SELECT et.*, b.period
    FROM `expense-tracker` et
    JOIN budgets b ON et.budget_id = b.id
    WHERE et.id = ? AND et.user_id = ?
");
$stmt->execute([$id, $user_id]);
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    echo "Expense record not found.";
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

  // Update the expense
  $updateStmt = $pdo->prepare("
      UPDATE `expense-tracker` 
      SET category = ?, title = ?, amount = ?, budget_id = ? 
      WHERE id = ? AND user_id = ?
  ");
  $updateStmt->execute([$category, $title, $amount, $budget_id, $id, $user_id]);

  $_SESSION['alerts'][] = ['type' => 'success', 'message' => 'Expense updated.'];
  header('Location: expenses.php');
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
    
    <title>Expenses</title>

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

    <div class="ex-container">
      <?php if (!empty($firstname)): ?>
        <h2>
          <?= ($firstname); ?>'s Expenses
        </h2>
      <?php else: ?>
        <h2>
          Your Expenses
        </h2>
      <?php endif; ?>
      <!--<div class="expense-error">
        <p></p>
      </div>-->
      <div class="expense-content">
        <div class="your-expenses">
          <form method="POST" action="">
            <label>Expense Category:
              <select name="category" class="expense-cat" value="<?= htmlspecialchars($expense['category']) ?>" required>
                <option value="">Choose Category</option>
                <option value="Food & Drinks (Groceries)">Food & Drinks (Groceries)</option>
                <option value="Food & Drinks (Eating Out)">Food & Drinks (Eating Out)</option>
                <option value="Transport (Public)">Transport (Public)</option>
                <option value="Transport (Other)">Transport (Other)</option>
                <option value="Entertainment (Subscriptions)">Entertainment (Subscriptions)</option>
                <option value="Entertainment (Performances)">Entertainment (Performances)</option>
                <option value="Entertainment (Parties)">Entertainment (Parties)</option>
                <option value="Entertainment (Other)">Entertainment (Other)</option>
                <option value="Medication & Suppelements">Medication & Supplements</option>
                <option value="Health & Wellness Facilities">Health & Wellness Facilites</option>
                <option value="Bills">Bills</option>
                <option value="Personal Shopping (Clothing & Accessories)">Personal Shopping (Clothing & Accessories)</option>
                <option value="Personal Shopping (Hair)">Personal Shopping (Hair)</option>
                <option value="Personal Shopping (Books)">Personal Shopping (Books)</option>
                <option value="Personal Shopping (Technology)">Personal Shopping (Technology)</option>
                <option value="Personal Shopping (Other)">Personal Shopping (Other)</option>
                <option value="Savings (Home)">Savings (Home)</option>
                <option value="Savings (Gifts)">Savings (Gifts)</option>
                <option value="Savings (Holiday)">Savings (Holiday)</option>
                <option value="Savings (ISA)">Savings (ISA)</option>
                <option value="Savings (Other)">Savings (Other)</option>
              </select>
            </label><br>
            <label>Expense Description:
              <input type="text" name="title" value="<?= htmlspecialchars($expense['title']) ?>">
            </label><br>
            <label>Expense Amount:
              <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($expense['amount']) ?>" required>
            </label><br>
            <label for="period">Expense Month:
              <input type="month" name="period" value="<?= htmlspecialchars($expense['period']) ?>">
            </label><br>
            <button type="submit" class="button" id="update-expense-button">Update Expense</button>
            <button class="button" id="cancel-expense-update-button">
              <a href="expenses.php" style="text-decoration:none;color:#522B29;">Cancel</a>
            </button>
          </form>
        </div>
      </div>
    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>
