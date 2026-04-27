<?php
/*
    File: login.php
    Description: Handles user login for both students and instructors.
                 Validates login inputs using filter_input, checks credentials
                 against the database using PDO prepared statements, and creates
                 session variables upon successful login.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

session_start();
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect = ($_SESSION['role'] === 'instructor') ? '../instructor/dashboard.php' : '../student/dashboard.php';
    header("Location: $redirect");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($email === null || $email === false || $password === null || $password === '') {
        $error = 'Both fields are required.';
    } else {
        // Get user from DB using the correct column names
        $stmt = $dbh->prepare("SELECT `user_id`, `name`, `email`, `password`, `role` FROM `users` WHERE `email` = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            $redirect = ($user['role'] === 'instructor') ? '../instructor/dashboard.php' : '../student/dashboard.php';
            header("Location: $redirect");
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/validate.js"></script>
</head>

<body>
    <div class="auth-card">
        <h1>Log In</h1>

        <?php if ($error): ?>
            <div class="error-msg show"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" onsubmit="validateLogin(event)">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password">
            </div>

            <button type="submit" class="btn">Log In</button>
        </form>

        <p class="link-text">Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>

</html>