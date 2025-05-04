<?php

session_start();

$pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

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
    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
    }

    if (!empty($_POST['email'])) {
        $email = $_POST['email'];
        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->execute(['email' => $email, 'id' => $id]);

    }

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $password, 'id' => $id]);
    }

    header("Location: /profile.php");
}

require_once './change_form.php';