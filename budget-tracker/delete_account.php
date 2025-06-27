<?php

session_start();
require_once 'config_budget.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['alerts'][] = [
      'type' => 'error', 
      'message' => 'Please log in to delete your account.'
    ];
    /*header('Location: expenses.php');
    exit();*/
}

$user_id = $_SESSION['user_id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Delete income and expenses linked to budgets
    $stmt = $pdo->prepare("DELETE FROM `income` WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM `expense-tracker` WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM `budgets` WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $stmt = $pdo->prepare("DELETE FROM `users` WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $pdo->commit();

    // Destroy session
    session_unset();
    session_destroy();

    // Redirect with success message
    session_start();
    $_SESSION['alerts'][] = [
        'type' => 'success',
        'message' => 'Your account has been deleted.'
    ];
    header('Location: index.php');
    exit();

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['alerts'][] = [
        'type' => 'error',
        'message' => 'Error deleting account: ' . $e->getMessage()
    ];
    header('Location: account.php');
    exit();
}
?>