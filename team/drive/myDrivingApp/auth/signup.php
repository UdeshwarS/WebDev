<?php
/*
    File: signup.php
    Description: Handles user registration for students and instructors.
                 Validates and sanitizes user input using filter_input,
                 checks for duplicate emails, hashes passwords securely,
                 and inserts new users into the database using PDO.
    Group Member Names: Akil Kanwar, Anas Hayat, Ayesha Hasan, Udeshwar Singh Sandhu
    Date: April 22, 2026
*/

session_start();
require_once 'db.php';

$error = '';
$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
$name = '';
$email = '';
$password = '';
$phone_number = '';
$role = '';
$license_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // INPUT VALIDATION (course standard)
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $phone_number = trim($phone_number);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
    $license_type = filter_input(INPUT_POST, 'license_type', FILTER_SANITIZE_SPECIAL_CHARS);

    // Server-side validation
    if (
        $name === null || $name === '' ||
        $email === null ||
        $password === null || $password === '' ||
        $phone_number === null || $phone_number === '' ||
        $role === null || $role === ''
    ) {
        $error = 'All fields are required.';
    } elseif ($email === false) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!in_array($role, ['student', 'instructor'])) {
        $error = 'Invalid role selected.';
    } elseif (!preg_match('/^\d{10}$/', $phone_number)) {
        $error = 'Invalid phone number format.';
    } elseif ($role === 'student' && ($license_type === null || $license_type === '')) {
        $error = 'License type is required for students.';
    } else {
        // Check if email already exists
        $stmt = $dbh->prepare("SELECT `user_id` FROM `users` WHERE `email` = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // Hash password and insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $dbh->prepare(
                "INSERT INTO `users`
                (`name`, `email`, `password`, `phone_number`, `license_type`, `role`)
                VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $hashedPassword, $phone_number, $license_type, $role]);


            $_SESSION['success'] = 'Account created successfully! You can now log in.';
            header("Location: signup.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <script src="../assets/js/validate.js"></script>
</head>

<body>
    <div class="auth-container">
        <h2>Create Account</h2>

        <?php if ($error): ?>
            <div class="server-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="server-success">
                <?php echo htmlspecialchars($success);
                $success = '' ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="signup.php" onsubmit="validateSignup(event)">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your name"
                    value="<?php echo htmlspecialchars($name); ?>" />
                <div class="error-msg" id="name-error">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($email); ?>" />
                <div class="error-msg" id="email-error">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Min. 6 characters" />
                <div class="error-msg" id="password-error"></div>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input
                    type="text"
                    id="phone_number"
                    name="phone_number"
                    placeholder="Enter 10-digit phone number (e.g. 1234567890)"
                    value="<?php echo htmlspecialchars($phone_number); ?>" />
                <div class="error-msg" id="phone_number-error">
                </div>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">-- Select Role --</option>
                    <option
                        value="student"
                        <?php echo ($role === 'student') ? 'selected' : ''; ?>>
                        Student
                    </option>
                    <option
                        value="instructor"
                        <?php echo ($role === 'instructor') ? 'selected' : ''; ?>>
                        instructor
                    </option>
                </select>
                <div class="error-msg" id="role-error"></div>
            </div>

            <div class="form-group">
                <label for="license_type">License Type</label>
                <select id="license_type" name="license_type">
                    <option value="">-- Select License Type --</option>
                    <option
                        value="instructor"
                        <?php echo ($license_type === 'instructor') ? 'selected' : ''; ?>>
                        instructor
                    </option>
                    <option
                        value="G1"
                        <?php echo ($license_type === 'G1') ? 'selected' : ''; ?>>
                        G1
                    </option>
                    <option
                        value="G2"
                        <?php echo ($license_type === 'G2') ? 'selected' : ''; ?>>
                        G2
                    </option>
                    <option
                        value="G"
                        <?php echo ($license_type === 'G') ? 'selected' : ''; ?>>
                        G
                    </option>
                </select>
                <div class="error-msg" id="license_type-error"></div>
            </div>

            <button type="submit" class="btn">Sign Up</button>
        </form>

        <p class="link-text">
            Already have an account? <a href="login.php">Log in</a>
        </p>
    </div>
</body>

</html>