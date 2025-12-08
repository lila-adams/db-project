<?php
session_start();
require("connect-db.php");
require("entry-db.php");
require("artist-db.php");

// 1. Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$artist_id = $_GET['id'] ?? null;
$entries = [];
$artist = null;

// 2. Fetch artist and their entries
if ($artist_id) {
    $entries = getEntriesByArtist($artist_id, $user_id);
    
    if (empty($entries)) {
        $artist = ['artist_id' => $artist_id, 'name' => 'Unknown Artist'];
    } else {
        $artist = $entries[0];
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist - Comic Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: rgba(0,0,0,0.8) !important; }
        .page-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .entry-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .entry-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }
        .artist-header {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 2rem;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
        .status-new { background: #e3f2fd; color: #1976d2; }
        .status-reading, .status-in-progress { background: #fff3e0; color: #f57c00; }
        .status-complete { background: #e8f5e9; color: #388e3c; }
    </style>
</head>
<body class="dashboard-bg">

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">📚 Comic Tracker</a>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="page-container">
        <h1 class="artist-header">🎨 Artist: <?php echo htmlspecialchars($artist['name'] ?? 'Unknown'); ?></h1>

        <?php if (!empty($entries)): ?>
            <p class="text-muted mb-4">You have <?php echo count($entries); ?> comic(s) by this artist.</p>

            <?php foreach ($entries as $entry): ?>
                <div class="entry-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-2">
                                <a href="entry-detail.php?id=<?php echo $entry['entry_id']; ?>" style="color: #667eea; text-decoration: none;">
                                    <?php echo htmlspecialchars($entry['comic_name']); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($entry['review']); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($entry['rating']): ?>
                                <div class="mb-2"><strong>⭐ <?php echo $entry['rating']; ?>/10</strong></div>
                            <?php endif; ?>
                                        <?php $status_class = 'status-' . str_replace(' ', '-', $entry['curr_status']); ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($entry['curr_status']); ?>
                                        </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No entries found for this artist.</div>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary mt-4">← Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
