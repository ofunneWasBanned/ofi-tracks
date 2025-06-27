<?php 
session_start();
require 'config_budget.php';

if (isset($_POST['register_btn'])) {
    $firstname = trim($_POST['firstname']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT email FROM `users` WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Email already in use.'
        ];
        $_SESSION['active_form'] = 'register';
    } else {
        $stmt = $pdo->prepare("INSERT INTO `users` (firstname, surname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstname, $surname, $email, $password]);

        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => 'Registration successful. You can now log in.'
        ];
        $_SESSION['active_form'] = 'login';
    }

    header('Location: index.php');
    exit();
}

if (isset($_POST['login_btn'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['surname'] = $user['surname'];
        $_SESSION['email'] = $user['email'];

        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => 'Login successful.'
        ];

        header('Location: index.php');
        exit();
    } else {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Invalid email or password.'
        ];
        $_SESSION['active_form'] = 'login';

        header('Location: index.php');
        exit();
    }
}
