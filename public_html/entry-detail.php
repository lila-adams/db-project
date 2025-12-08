<?php
session_start();
require("connect-db.php");
require("entry-db.php");
require("tag-db.php");
require("author-db.php");
require("artist-db.php");
require("comment-db.php");
require("user-db.php");

$user_id = $_SESSION['user_id'] ?? null;
$entry_id = $_GET['id'] ?? null;
$entry = null;
$tags = [];
$authors = [];
$artists = [];
$comments = [];

// 2. Fetch entry data
if ($entry_id) {
    $entry = getEntryById($entry_id);
    if (!$entry) {
        header("Location: dashboard.php");
        exit();
    }

    $tags = getAllTags($entry_id);
    // use public getters so any user can view entry details
    $authors = getAuthorByEntryPublic($entry_id);
    $artists = getArtistByEntryPublic($entry_id);
    $comments = getCommentsByEntry($entry_id);
} else {
    header("Location: dashboard.php");
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $comment_text = trim($_POST['comment_text'] ?? '');
    if (!empty($comment_text)) {
        addComment($user_id, $entry_id, $comment_text);
        // reload to show new comment
        header("Location: entry-detail.php?id=" . urlencode($entry_id));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($entry['comic_name']); ?> - Comic Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: rgba(0,0,0,0.8) !important; }
        .entry-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .rating-display {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .status-new { background: #e3f2fd; color: #1976d2; }
        .status-reading, .status-in-progress { background: #fff3e0; color: #f57c00; }
        .status-complete { background: #e8f5e9; color: #388e3c; }
        .tag-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        .section-title {
            color: #000000ff;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .info-label { font-weight: 600; color: #666; }
        .info-value { color: #333; }
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
    <div class="entry-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="mb-3 text-dark"><?php echo htmlspecialchars($entry['comic_name']); ?></h1>
                <div class="mb-3">
                                        <?php $status_class = 'status-' . str_replace(' ', '-', $entry['curr_status']); ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($entry['curr_status']); ?>
                                        </span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <?php if ($entry['rating']): ?>
                    <div class="rating-display">⭐ <?php echo $entry['rating']; ?>/10</div>
                <?php else: ?>
                    <div class="text-muted">Not rated</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Review Section -->
        <?php if ($entry['review']): ?>
        <div class="mb-4">
            <h5 class="section-title">Review</h5>
            <p class="text-secondary"><?php echo nl2br(htmlspecialchars($entry['review'])); ?></p>
        </div>
        <?php endif; ?>

        <!-- Authors Section -->
        <?php if ($authors): ?>
        <div class="mb-4 text-dark">
            <h5 class="section-title">Author</h5>
            <p><?php echo htmlspecialchars($authors['name']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Artists Section -->
        <?php if ($artists): ?>
        <div class="mb-4 text-dark">
            <h5 class="section-title">Artist</h5>
            <p><?php echo htmlspecialchars($artists['name']); ?></p>
        </div>
        <?php endif; ?>

        <!-- Tags Section -->
        <?php if (!empty($tags)): ?>
        <div class="mb-4">
            <h5 class="section-title">Tags</h5>
            <div>
                <?php foreach ($tags as $tag): ?>
                    <span class="tag-badge"><?php echo htmlspecialchars($tag['tag_text']); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="mt-5 pt-4 border-top">
            <?php if ($user_id && $user_id == $entry['user_id']): ?>
                <a href="update-entry.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-warning">✏️ Edit Entry</a>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div class="container mb-5">
    <div class="entry-card text-dark">
        <h5 class="section-title">Comments</h5>

        <!-- Add Comment Form -->
        <form method="post" class="mb-3">
            <input type="hidden" name="action" value="add_comment">
            <div class="mb-2">
                <textarea name="comment_text" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
            </div>
            <div>
                <button class="btn btn-success" type="submit">Post Comment</button>
            </div>
        </form>

        <!-- Comments List -->
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $c): ?>
                <div class="mb-3 p-3" style="background:#f8f9fa;border-radius:8px;">
                    <div class="d-flex justify-content-between">
                        <div><strong><?php echo htmlspecialchars($c['username']); ?></strong> <small class="text-muted">• <?php echo htmlspecialchars($c['timestamp']); ?></small></div>
                        <?php if ($c['writer_id'] == $user_id): ?>
                            <form method="post" action="delete-comment.php" style="display:inline;">
                                <input type="hidden" name="comment_id" value="<?php echo $c['comment_id']; ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Delete this comment?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2"><?php echo nl2br(htmlspecialchars($c['text'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No comments yet — be the first to share your thoughts.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
