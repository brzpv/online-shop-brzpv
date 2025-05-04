<?php

$pdo = new PDO(dsn: 'pgsql:host=db;port=5432;dbname=mydb', username: 'user', password: 'pwd');

//$pdo->exec("INSERT INTO users (name, email, password) VALUES ('Ivan', 'ivan@mail.ru', 'qwerty123')");

$statement = $pdo->query("SELECT * FROM users");
echo "<pre>";
$data = $statement->fetchAll();
echo "<pre>";

print_r($data);
?>


