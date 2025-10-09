<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/hosttype.php';
require_once BASE_PATH . 'config.php';
require_once BASE_PATH . 'vendor/autoload.php';
use MongoDB\BSON\ObjectId;

session_start();

// ---------------- SIGNUP ----------------
if (isset($_POST['signup'])) {
    $name     = $_POST['name'] ?? '';
    $phone    = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($name && $phone && $location && $email && $password && $role) {
        try {
            $existing = $db->users->findOne(['email' => $email]);
            if ($existing) {
                echo "Email already registered!";
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db->users->insertOne([
                'name'       => $name,
                'phone'      => $phone,
                'location'   => $location,
                'email'      => $email,
                'password'   => $hashedPassword,
                'role'       => (int)$role,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            header("Location: " . BASE_URL . "public/login.php?registration=success");
            exit();

        } catch (Exception $e) {
            die("Error registering user: " . $e->getMessage());
        }
    } else {
        echo "Please fill all fields!";
        exit();
    }
}

// ---------------- LOGIN ----------------
if (isset($_POST['login'])) {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        try {
            $user = $db->users->findOne(['email' => $email]);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = (string)$user['_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = (int)$user['role'];

                switch ((int)$user['role']) {
                    case 1: 
                    case 2: header("Location: " . BASE_URL . "public/donor/dashboard.php"); break;
                    case 3: header("Location: " . BASE_URL . "public/ngo/dashboard.php"); break;
                    case 4: header("Location: " . BASE_URL . "public/admin/dashboard.php"); break;
                    default: echo "Role not recognized!"; exit();
                }
                exit();
            } else {
                echo "Invalid email or password!";
                exit();
            }

        } catch (Exception $e) {
            die("Error logging in: " . $e->getMessage());
        }
    } else {
        echo "Please enter email and password!";
        exit();
    }
}

// ---------------- LOGOUT ----------------
if (isset($_GET['logout']) && $_GET['logout'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "public/login.php");
    exit();
}
