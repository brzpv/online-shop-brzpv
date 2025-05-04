<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /handle_login.php");
} else {
    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
    $id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
}

require_once './profile_form.php';