<?php

$errors = [];

function validate(array $data): array
{
    $errors = [];

    if (!isset($data['username'])) {
        $errors['username'] = "Поле Username должно быть заполнено!";
    }

    if (!isset($data['password'])) {
        $errors['password'] = "Поле Password должно быть заполнено!";
    }

    return $errors;
}

$errors = validate($_POST);

if (empty($errors)) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $username]);
    $user = $stmt->fetch();

    if (!empty($user)) {
        $passwordDB = $user['password'];

        if (password_verify($password, $passwordDB)) {
            session_start();
            $_SESSION['user_id'] = $user['id'];

            header("Location: /catalog.php");
        } else {
            $errors['username'] ="Пароль указан неверно!";
        }
    } else {
        $errors['username'] = "Пользователя с таким логином не существует!";
    }
}

require_once './login_form.php';

?>