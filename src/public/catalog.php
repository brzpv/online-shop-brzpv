<?php

session_start();

print_r($_SESSION);

if (isset($_SESSION['user_id'])) {
    $pdo = new PDO(dsn: 'pgsql:host=db;port=5432;dbname=mydb', username: 'user', password: 'pwd');

    $stmt = $pdo->query('SELECT * FROM products');
    $products = $stmt->fetchAll();

    require_once './catalog_page.php';
} else {
    header(header: "Location: /login_form.php");
}