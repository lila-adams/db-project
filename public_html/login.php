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
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
        <div class="card-body login-card p-4">
            <h3 class="text-center mb-4">Login</h3>
            <div class="w-100 p-3 rounded card-bg">

            <div class="p-4 border border-success rounded-0">
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg rounded-0">Log In</button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <small>Don't have an account? 
                    <a href="signup.php" class="link link-success">Sign Up</a>
                </small>
            </div>
        </div>
        </div>
    </div>
</div>

</body>
</html>