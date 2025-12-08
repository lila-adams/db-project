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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .signup-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #667eea;
        }
        .signup-header h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .signup-header p {
            color: #666;
            font-size: 0.9rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-signup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-signup:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="signup-container">
    <div class="signup-header">
        <h1>📚</h1>
        <h2>Create Account</h2>
        <p>Join the comic community</p>
    </div>
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

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

        <div class="d-grid">
            <button type="submit" class="btn btn-signup btn-lg text-white">Sign Up</button>
        </div>
    </form>
    
    <div class="login-link">
        <small>Already have an account? <a href="login.php">Log in here</a></small>
    </div>
</div>

</body>
</html>