<?php
session_start();
require("connect-db.php");
require("goal-db.php");
require("rec-db.php");
require("entry-db.php");

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
$my_entries = getAllEntries($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem 0; }
        .navbar { background: rgba(0,0,0,0.8) !important; }
        .card-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .section-header {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .item-box {
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .delete-form {
            display: inline;
        }
        .input-group-custom input {
            border-radius: 8px 0 0 8px;
        }
        .input-group-custom button {
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Comic Tracker</a>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <a href="browse-profiles.php" class="btn btn-outline-light btn-sm">Browse Profiles</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
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

    <div class="row g-4">
        <!-- GOALS SECTION -->
        <div class="col-md-6">
            <div class="card-container">
                <h4 class="section-header">📖 My Reading Goals</h4>
                
                <!-- Add Goal Form -->
                <form method="post" class="input-group input-group-custom mb-4">
                    <input type="hidden" name="action" value="add_goal">
                    <input type="text" name="goal_text" class="form-control" placeholder="Add a new goal..." required>
                    <button class="btn btn-success" type="submit">Add</button>
                </form>

                <!-- List Goals -->
                <?php if (!empty($goals)): ?>
                    <?php foreach ($goals as $goal): ?>
                        <div class="item-box">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><?php echo htmlspecialchars($goal['text']); ?></span>
                                <form method="post" class="delete-form" onsubmit="return confirm('Delete this goal?');">
                                    <input type="hidden" name="action" value="delete_goal">
                                    <input type="hidden" name="goal_id" value="<?php echo $goal['goal_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No goals yet. Set one to stay motivated!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- RECOMMENDATIONS SECTION -->
        <div class="col-md-6">
            <div class="card-container">
                <h4 class="section-header">💡 My Recommendations</h4>
                <p class="text-muted small mb-3">Keep track of comics you want to recommend to others!</p>
                
                <!-- Add Rec Form -->
                <form method="post" class="input-group input-group-custom mb-4">
                    <input type="hidden" name="action" value="add_rec">
                    <input type="text" name="comic_name" class="form-control" placeholder="Add a recommendation..." required>
                    <button class="btn btn-info text-dark" type="submit">Add</button>
                </form>

                <!-- List Recs -->
                <?php if (!empty($recs)): ?>
                    <?php foreach ($recs as $rec): ?>
                        <div class="item-box">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong><?php echo htmlspecialchars($rec['comic_name']); ?></strong>
                                <form method="post" class="delete-form" onsubmit="return confirm('Delete this recommendation?');">
                                    <input type="hidden" name="action" value="delete_rec">
                                    <input type="hidden" name="rec_id" value="<?php echo $rec['rec_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No recommendations yet. Add one!</p>
                <?php endif; ?>

            </div>
        </div>

        <!-- MY COMICS SECTION -->
        <div class="col-12">
            <div class="card-container">
                <h4 class="section-header">📚 My Comics (<?php echo count($my_entries); ?>)</h4>
                <?php if (!empty($my_entries)): ?>
                    <?php foreach ($my_entries as $entry): ?>
                        <div class="item-box d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?php echo htmlspecialchars($entry['comic_name']); ?></strong>
                                <div class="small text-muted">Status: <?php echo htmlspecialchars($entry['curr_status']); ?> • Rating: <?php echo $entry['rating'] ?: '—'; ?></div>
                                <div class="mt-2 text-muted small"><?php echo htmlspecialchars($entry['review']); ?></div>
                            </div>
                            <div class="text-end">
                                <a href="update-entry.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-sm btn-warning mb-2">✏️</a>
                                <form method="post" style="display:inline;" action="dashboard.php">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this entry?')">🗑️</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">You haven't added any comics yet.</p>
                <?php endif; ?>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>