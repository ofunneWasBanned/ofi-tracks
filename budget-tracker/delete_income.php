<?php

session_start();
require_once 'config_budget.php';

if (!isset($_GET['id'])) {
    header('Location: income.php');
    exit();
}

$id = (int) $_GET['id'];

// Delete the record
$stmt = $pdo->prepare("DELETE FROM income WHERE id = ?");
$stmt->execute([$id]);

header('Location: income.php');
exit();
