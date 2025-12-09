<?php
session_start();
require("connect-db.php");
require("user-db.php");
require("goal-db.php");
require("rec-db.php");
require("entry-db.php");
require("friends-db.php");

// Get all users for browsing
$query = "SELECT user_id, username FROM User ORDER BY username";
$statement = $db->prepare($query);
$statement->execute();
$all_users = $statement->fetchAll();
$statement->closeCursor();

$selected_user_id = $_GET['user_id'] ?? null;
$selected_user = null;
$goals = [];
$recs = [];

// If browsing a specific user's profile
if ($selected_user_id) {
    $user_info = "SELECT user_id, username FROM User WHERE user_id = :user_id";
    $stmt = $db->prepare($user_info);
    $stmt->bindValue(':user_id', $selected_user_id);
    $stmt->execute();
    $selected_user = $stmt->fetch();
    $stmt->closeCursor();

    if ($selected_user) {
        $goals = getAllGoals($selected_user_id);
        $recs = getAllRecommendations($selected_user_id);
        $user_entries = getAllEntries($selected_user_id);
    }
}

// Handle friend actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_friend') {
        if (isset($_SESSION['user_id']) && $selected_user_id) {
            addFriend($_SESSION['user_id'], $selected_user_id);
            header('Location: browse-profiles.php?user_id=' . urlencode($selected_user_id));
            exit();
        }
    }
    if (isset($_POST['action']) && $_POST['action'] === 'remove_friend') {
        if (isset($_SESSION['user_id']) && $selected_user_id) {
            removeFriend($_SESSION['user_id'], $selected_user_id);
            header('Location: browse-profiles.php?user_id=' . urlencode($selected_user_id));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Profiles - Comic Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: rgba(0,0,0,0.8) !important; }
        .container-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .user-list-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .user-list-card:hover {
            background: #f5f5f5;
            transform: translateX(4px);
        }
        .user-list-card a {
            color: inherit;
            text-decoration: none;
            display: block;
        }
        .section-title {
            color: #000000ff;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .goal-item, .rec-item {
            background: #f9f9f9;
            border-left: 4px solid #71a246ff;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 4px;
        }
        .profile-header {
            color: #000000ff;
            font-weight: 700;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
    </style>
</head>
<body class="dashboard-bg">

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">WICKSY</a>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="btn btn-outline-light btn-sm">My Profile</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <!-- User List -->
        <div class="col-md-4">
            <div class="container-card text-dark">
                <h3 class="section-title">Browse Users</h3>
                <?php foreach ($all_users as $user): ?>
                    <div class="user-list-card">
                        <a href="?user_id=<?php echo $user['user_id']; ?>">
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-md-8">
            <div class="container-card">
                <?php if ($selected_user): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="profile-header mb-0">
                            <?php echo htmlspecialchars($selected_user['username']); ?>'s Profile
                        </h2>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $selected_user['user_id']): ?>
                            <div>
                                <?php if (areFriends($_SESSION['user_id'], $selected_user['user_id'])): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="remove_friend">
                                        <button class="btn btn-outline-danger btn-sm">Remove Friend</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="add_friend">
                                        <button class="btn btn-success btn-sm">Add Friend</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Goals Section -->
                    <div class="mb-4">
                        <h5 class="section-title">Reading Goals</h5>
                        <?php if (!empty($goals)): ?>
                            <?php foreach ($goals as $goal): ?>
                                <div class="goal-item text-dark">
                                    <?php echo htmlspecialchars($goal['text']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted ">No reading goals shared.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Recommendations Section -->
                    <div class="mb-4">
                        <h5 class="section-title">Recommendations</h5>
                        <?php if (!empty($recs)): ?>
                            <?php foreach ($recs as $rec): ?>
                                <div class="rec-item text-dark">
                                    <strong><?php echo htmlspecialchars($rec['comic_name']); ?></strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No recommendations shared.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Comics Section -->
                    <div class="mb-4">
                        <h5 class="section-title">Shelf</h5>
                        <?php if (!empty($user_entries)): ?>
                            <?php foreach ($user_entries as $ue): ?>
                                <div class="goal-item">
                                    <strong><a href="entry-detail.php?id=<?php echo $ue['entry_id']; ?>" class="btn-link-custom text-success"><?php echo htmlspecialchars($ue['comic_name']); ?></a></strong>
                                    <div class="small text-muted">Status: <?php echo htmlspecialchars($ue['curr_status']); ?> • Rating: <?php echo $ue['rating'] ?: '—'; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comics shared.</p>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="alert alert-secondary text-center">
                        <p class="mb-0">Select a user from the list to view their profile</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
