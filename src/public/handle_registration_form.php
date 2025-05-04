<?php

function validate(array $data): array
{
    $errors = [];

    $errorName = validateName($data);
    if (!empty($errorName)) {
        $errors['name'] = $errorName;
    }

    if (isset($data['email'])) {
        $email = $data['email'];
        if (strlen($email) < 3) {
            $errors['email'] = "Email не может содержать меньше 3 символов!";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Некорректный email!";
        } else {
            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $errors['email'] = "Этот Email уже зарегистрирован!";
            }
        }
    } else {
        $errors['email'] = "Email должен быть заполнен!";
    }

    if (isset($data['psw'])) {
        $password = $data['psw'];
        if (strlen($password) < 3) {
            $errors['psw'] = "Пароль не может содержать меньше 3 символов!";
        }

        $passwordRepeat = $data['psw-repeat'];
        if ($password !== $passwordRepeat) {
            $errors['psw-repeat'] = "Пароли не совпадают!";
        }
    } else {
        $errors['psw'] = "Пароль должен быть заполнен!";
    }

    return $errors;
}

function validateName(array $data): null|string
{
    if (isset($data['name'])) {
        $name = $data['name'];
        if (strlen($name) < 3) {
            return "Имя не может быть меньше 3 символов!";
        }

        return null;
    } else {
        return "Имя должно быть заполнено!";
    }
}

$errors = validate($_POST);

if (empty($errors)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['psw'];
    $passwordRepeat = $_POST['psw-repeat'];

    $password = password_hash($password, PASSWORD_DEFAULT);

    $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');

    $stmt = $pdo->prepare(query: "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);

    $stmt = $pdo->prepare(query: "SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    $result = $stmt->fetch();
    header("Location: /handle_login.php");
    //print_r ($result);
}

require_once './registration_form.php';
?>