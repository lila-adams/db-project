<?php
session_start();
require("connect-db.php");
require("goal-db.php");
require("rec-db.php");

// 1. Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 2. Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- GOALS ---
    if (isset($_POST['action']) && $_POST['action'] == 'add_goal') {
        $text = trim($_POST['goal_text']);
        if (!empty($text)) {
            addGoal($user_id, $text);
            $success_msg = "Goal added!";
        }
    }
    elseif (isset($_POST['action']) && $_POST['action'] == 'delete_goal') {
        delete_goal($_POST['goal_id']);
        $success_msg = "Goal deleted.";
    }

    // --- RECOMMENDATIONS ---
    elseif (isset($_POST['action']) && $_POST['action'] == 'add_rec') {
        $name = trim($_POST['comic_name']);
        if (!empty($name)) {
            addRec($name, $user_id);
            $success_msg = "Recommendation added!";
        }
    }
    elseif (isset($_POST['action']) && $_POST['action'] == 'delete_rec') {
        delete_rec($_POST['rec_id']);
        $success_msg = "Recommendation deleted.";
    }
}

// 3. Fetch Data
$goals = getAllGoals($user_id);
$recs = getAllRecommendations($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Comic Tracker</a>
        <div>
            <a href="dashboard.php" class="btn btn-outline-light me-2">Entries</a>
            <a href="profile.php" class="btn btn-light me-2">Profile & Goals</a>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- LEFT COLUMN: GOALS -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Reading Goals</h5>
                </div>
                <div class="card-body">
                    <!-- Add Goal Form -->
                    <form method="post" class="mb-4 input-group">
                        <input type="hidden" name="action" value="add_goal">
                        <input type="text" name="goal_text" class="form-control" placeholder="e.g. Read 50 comics..." required>
                        <button type="submit" class="btn btn-success">Add</button>
                    </form>

                    <!-- List Goals -->
                    <ul class="list-group">
                        <?php foreach ($goals as $goal): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($goal['text']); ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_goal">
                                    <input type="hidden" name="goal_id" value="<?php echo $goal['goal_id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm border-0">&times;</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($goals)) echo '<li class="list-group-item text-muted">No goals set yet.</li>'; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: RECOMMENDATIONS -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">My Recommendations</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Keep a list of comics you want to recommend to others (or yourself!).</p>
                    
                    <!-- Add Rec Form -->
                    <form method="post" class="mb-4 input-group">
                        <input type="hidden" name="action" value="add_rec">
                        <input type="text" name="comic_name" class="form-control" placeholder="Comic Name..." required>
                        <button type="submit" class="btn btn-info text-white">Add</button>
                    </form>

                    <!-- List Recs -->
                    <ul class="list-group">
                        <?php foreach ($recs as $rec): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($rec['comic_name']); ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_rec">
                                    <input type="hidden" name="rec_id" value="<?php echo $rec['rec_id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm border-0">&times;</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($recs)) echo '<li class="list-group-item text-muted">No recommendations yet.</li>'; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>