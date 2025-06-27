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
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $currentPeriod = $_POST['period'] ?? date('Y-m');

    if (empty($category) || empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Category and a positive amount are required.'
        ];
        header('Location: income.php');
        exit();
    }

    $amount = number_format((float)$amount, 2, '.', '');
    $user_id = $_SESSION['user_id'];

    try {
        // Step 1: Try to get existing budget
        $stmtBudget = $pdo->prepare("SELECT id FROM budgets WHERE user_id = :user_id AND period = :period LIMIT 1");
        $stmtBudget->execute([
            'user_id' => $user_id,
            'period' => $currentPeriod
        ]);
        $budget = $stmtBudget->fetch(PDO::FETCH_ASSOC);

        // Step 2: If not found, create it
        if (!$budget) {
            $stmtCreate = $pdo->prepare("INSERT INTO budgets (user_id, period) VALUES (:user_id, :period)");
            $stmtCreate->execute([
                'user_id' => $user_id,
                'period' => $currentPeriod
            ]);

            // Get the newly created budget ID
            $budget_id = $pdo->lastInsertId();
        } else {
            $budget_id = $budget['id'];
        }

        // Step 3: Insert income using the budget_id
        $stmt = $pdo->prepare("INSERT INTO income (user_id, category, title, amount, created_at, budget_id) 
                               VALUES (:user_id, :category, :title, :amount, NOW(), :budget_id)");

        $stmt->execute([
            ':user_id' => $user_id,
            ':category' => $category,
            ':title' => $title,
            ':amount' => $amount,
            ':budget_id' => $budget_id
        ]);

        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => 'Income added successfully.'
        ];
    } catch (PDOException $e) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ];
    }

    header('Location: income.php');
    exit();
}
?>
