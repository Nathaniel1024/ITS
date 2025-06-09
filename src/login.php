<?php
require_once 'config/db.php';

// Auto-fill from cookies if set
$email_cookie = $_COOKIE['email'] ?? '';
$password_cookie = $_COOKIE['password'] ?? '';

if (isset($_POST['login'])) {
    session_start();

    // Sanitize and filter input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    // Escape the input for database query
    $email = mysqli_real_escape_string($db, $email);
    $password = mysqli_real_escape_string($db, $password);

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($db, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['password'] = $user['password'];

            // Remember Me functionality
            if (isset($_POST['remember'])) {
                setcookie('email', $email, time() + (86400 * 7), "/"); // 7 days
            }

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: index.html");
            exit();
        }
    } else {
        header("Location: index.html");
        exit();
    }
}
