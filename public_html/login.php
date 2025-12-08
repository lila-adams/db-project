<?php
// 1. Start the session at the very top
session_start();

// 2. Include database connection
require("connect-db.php");

// 3. Define the authentication function locally
function authenticate($username, $password) {
    global $db;
    
    // Select the user ID and the HASHED password (pwd)
    $query = "SELECT user_id, pwd FROM User WHERE username = :username";
    
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();

        // Check if user exists AND if the password matches the hash
        // Note: We use $result['pwd'] because that matches your DB column
        if ($result && password_verify($password, $result['pwd'])) {
            return $result['user_id']; // Return the user's ID
        }
    } catch (PDOException $e) {
        // In a real app, log this error
        return false;
    }
    
    return false; // Authentication failed
}

$error_msg = "";

// 4. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    if (empty($user) || empty($pass)) {
        $error_msg = "Please enter both username and password.";
    } else {
        $userId = authenticate($user, $pass);
        
        if ($userId) {
            // SUCCESS: Store ID in session and redirect
            $_SESSION['user_id'] = $userId;
            header("Location: dashboard.php");
            exit();
        } else {
            $error_msg = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #667eea;
        }
        .login-header h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
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
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }
        .signup-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .signup-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>📚</h1>
        <h2>Comic Tracker</h2>
        <p>Track and organize your comics</p>
    </div>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-login btn-lg text-white">Login</button>
        </div>
    </form>
    
    <div class="signup-link">
        <small>Don't have an account? <a href="signup.php">Sign up here</a></small>
    </div>
</div>

</body>
</html>