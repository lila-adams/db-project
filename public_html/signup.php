<?php
require("connect-db.php");
require("user-db.php");

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic Validation
    if (empty($username) || empty($password)) {
        $error_msg = "Please fill in all fields.";
    } 
    elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } 
    else {
        // --- SECURITY STEP ---
        // Hash the password before sending it to the DB function.
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        
        // Call addUser (defined in user-db.php)
        try {
            // We pass the hashed version!
            $result = addUser($username, $hashed_pwd);
            
            if ($result) {
                $success_msg = "Account created! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error_msg = "Error: Username might already be taken.";
            }
        } catch (Exception $e) {
            $error_msg = "Error: Username might already be taken.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="card-body login-card p-4">
        <h3 class="text-center mb-4">Sign Up</h3>
        <!-- Outer Background Box -->
    <div class="w-100 p-3 rounded card-bg">

        <!-- Inner Card Box -->
        <div class="p-4 border border-success rounded-0">

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="signup.php" method="post">

                <form action="signup.php" method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

                <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success btn-lg rounded-0">
                        Sign Up
                    </button>
                </div>
            </form>

            <div class="text-center pt-4">
                <span class="text-muted">Already have an account?</span>
                <a href="login.php" class="fw-bold text-success">Log in</a>
            </div>

        </div>
    </div>
    </div>
</div>

</body>
</html>