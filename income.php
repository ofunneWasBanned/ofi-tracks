<?php

session_start();
require 'config_budget.php';

if (!isset($_SESSION['user_id'])) {
  $_SESSION['alerts'][] = [
    'type' => 'error',
    'message' => 'Please log in.'
  ];
  header('Location: index.php');
  exit();
} else {

  $user_id = $_SESSION['user_id'];

  // Get selected period from dropdown (or default to showing all)
  $selectedPeriod = $_GET['period'] ?? '';

  // Fetch all available periods for dropdown
  $stmtPeriods = $pdo->prepare("
    SELECT DISTINCT period 
    FROM budgets 
    WHERE user_id = :user_id 
    ORDER BY period DESC
  ");
  $stmtPeriods->execute(['user_id' => $user_id]);
  $availablePeriods = $stmtPeriods->fetchAll(PDO::FETCH_COLUMN);

  // Prepare SQL to fetch incomes
  $sql = "
    SELECT income.*, budgets.period 
    FROM income 
    JOIN budgets ON income.budget_id = budgets.id 
    WHERE income.user_id = :user_id
  ";

  $params = ['user_id' => $user_id];

  if (!empty($selectedPeriod)) {
    $sql .= " AND budgets.period = :period";
    $params['period'] = $selectedPeriod;
  }

  $sql .= " ORDER BY income.id DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Restore session values
  $firstname = $_SESSION['firstname'] ?? null;
  $surname = $_SESSION['surname'] ?? null;
  $email = $_SESSION['email'] ?? null;

  $alerts = $_SESSION['alerts'] ?? [];
  $active_form = $_SESSION['active_form'] ?? '';

  unset($_SESSION['alerts'], $_SESSION['active_form']);

  // Keep session values alive
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
    
    <title>My Income | Ofi</title>

    <link rel="stylesheet" href="/budget-tracker/assets/css/styles.css">

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

  </head>

  <body>

    <header>
      <a href="index.php?period=<?= urlencode($selectedPeriod ?: date('Y-m')) ?>" class="logo">
        <img src="/budget-tracker/assets/images/ego-no-bg.png" alt="Ofi Logo">
      </a>
      <nav>
        <a href="index.php?period=<?= urlencode($selectedPeriod ?: date('Y-m')) ?>">Home</a>
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
      <!--<div class="income-error">
        <p></p>
      </div>-->
      <div class="income-content">
        <div class="your-income">
          <form action="add_income.php" method="POST">
            <label for="category">Income Source: </label>
            <select name="category" class="income-cat">
              <option value="">Choose Source</option>
              <option value="Salary">Salary</option>
              <option value="Freelancing">Freelance</option>
              <option value="Socials">Social Media & Sponsorships</option>
              <option value="Investment">Investments & Interest</option>
              <option value="Other">Other</option>
            </select><br>

            <label for="title">Income Description: </label>
            <input type="text" name="title" placeholder="Enter Income Description" class="income-input"><br>

            <label for="amount">Income Amount: </label>
            <input type="number" name="amount" placeholder="Enter Income Amount" min="0" step="0.01" class="income-amount"><br>

            <label for="period">Income Month:</label>
            <input type="month" name="period" id="inc-period" value="<?= isset($income['period']) ? htmlspecialchars($income['period']) : date('Y-m') ?>"><br>

            <button class="button" id="income-button">Add Income</button>
          </form>
        </div>
      </div>

      <form method="GET" action="income.php" class="filter-month">
        <label for="period">Filter by Month:</label>
        <select name="period" id="filt-period" onchange="this.form.submit()">
          <option value="">-- Show All --</option>
          <?php foreach ($availablePeriods as $period): ?>
            <option value="<?= $period ?>" <?= $period === $selectedPeriod ? 'selected' : '' ?>>
              <?= date('F Y', strtotime($period)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>

      <table class="in-table-content">
        <h2 style="margin-bottom: 2rem;">Income Details</h2>
        <thead>
          <tr><th>Category</th><th>Description</th><th>Amount</th><th>Month</th><th>Actions</th></tr>
        </thead>
        <tbody class="in-table-tr-content" id="in-table-data">
          <?php foreach ($entries as $income): ?>
            <tr>
              <td class="cat"><?= htmlspecialchars($income['category']) ?></td>
              <td class="tit"><?= htmlspecialchars($income['title']) ?></td>
              <td class="amt">£<?= number_format($income['amount'], 2) ?></td>
              <td class="mon"><?= date('F Y', strtotime($income['period'])) ?></td>
              <td class="act">
                <a href="edit_income.php?id=<?= $income['id'] ?>" class="in-button edit">Edit</a>
                <a href="delete_income.php?id=<?= $income['id'] ?>" style="text-decoration:none;" onclick="return confirm('Are you sure you want to delete this source of income?')" class="in-button delete" >Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>