<?php

session_start();
require_once 'config_budget.php';

if (!isset($_GET['id'])) {
    header('Location: expenses.php');
    exit();
}

$id = (int) $_GET['id'];

// Delete the record
$stmt = $pdo->prepare("DELETE FROM `expense-tracker` WHERE id = ?");
$stmt->execute([$id]);

header('Location: expenses.php');
exit();
