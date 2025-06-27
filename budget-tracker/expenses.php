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

  // Prepare SQL to fetch expenses
  $sql = "
    SELECT `expense-tracker`.*, budgets.period 
    FROM `expense-tracker` 
    JOIN budgets ON `expense-tracker`.budget_id = budgets.id 
    WHERE `expense-tracker`.user_id = :user_id
  ";

  $params = ['user_id' => $user_id];

  if (!empty($selectedPeriod)) {
    $sql .= " AND budgets.period = :period";
    $params['period'] = $selectedPeriod;
  }

  $sql .= " ORDER BY `expense-tracker`.id DESC";

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
    
    <title>My Expenses | Ofi</title>

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
          <form action="add_expense.php" method="POST">
            <label for="category">Expense Category: </label>
            <select name="category" class="expense-cat">
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
            </select><br>

            <label for="title">Expense Description: </label>
            <input type="text" name="title" placeholder="Enter Expense Description" class="expense-input"><br>

            <label for="amount">Expense Amount: </label>
            <input type="number" name="amount" placeholder="Enter Expense Amount" min="0" step="0.01" class="expense-amount"><br>

            <label for="period">Expense Month:</label>
            <input type="month" name="period" id="exp-period" value="<?= isset($expense['period']) ? htmlspecialchars($expense['period']) : date('Y-m') ?>"><br>

            <button type="submit" class="button" id="expense-button">Add Expense</button>
          </form>
        </div>
      </div>

      <form method="GET" action="expenses.php" class="filter-month">
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

      <table class="ex-table-content">
        <h2 style="margin-bottom: 2rem;">Expense Details</h2>
        <thead>
          <tr><th>Category</th><th>Description</th><th>Amount</th><th>Month</th><th>Actions</th></tr>
        </thead>
        <tbody class="ex-table-tr-content" id="ex-table-data">
          <?php foreach ($entries as $expense): ?>
            <tr>
              <td><?= htmlspecialchars($expense['category']) ?></td>
              <td><?= htmlspecialchars($expense['title']) ?></td>
              <td>£<?= number_format($expense['amount'], 2) ?></td>
              <td class="mon"><?= date('F Y', strtotime($expense['period'])) ?></td>
              <td>
                <a href="edit_expenses.php?id=<?= $expense['id'] ?>" class="ex-button edit">Edit</a>
                <a href="delete_expense.php?id=<?= $expense['id'] ?>" onclick="return confirm('Are you sure you want to delete this expense?')" class="ex-button delete">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <script src="/budget-tracker/assets/js/script.js"></script>
  </body>
</html>