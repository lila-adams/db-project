<?php
session_start();
require("connect-db.php");
require("entry-db.php");
require("author-db.php");
require("artist-db.php");
require("tag-db.php");

// 1. Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$entry_id = $_GET['id'] ?? null;
$entry = null;

// 2. Fetch data
if ($entry_id) {
    $entry = getEntryById($entry_id);
    if (!$entry || $entry['user_id'] != $user_id) {
        header("Location: dashboard.php");
        exit();
    }
}

// fetch current author/artist (if any)
$current_author = getAuthorByEntryPublic($entry_id);
$current_artist = getArtistByEntryPublic($entry_id);

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['entry_id'];
    $name = $_POST['comic_name'];
    $rating = $_POST['rating'];
    if ($rating === '') $rating = null;
    $status = $_POST['status'];
    $review = $_POST['review'];
    $author_name = trim($_POST['author_name'] ?? '');
    $author_year = trim($_POST['author_year'] ?? '');
    $artist_name = trim($_POST['artist_name'] ?? '');
    $artist_year = trim($_POST['artist_year'] ?? '');
    $tags_input = trim($_POST['tags'] ?? '');

    updateEntry($id, $name, $rating, $user_id, $status, $review);

    // update author relation
    deleteWrittenByByEntry($id);
    if (!empty($author_name)) {
        $existing = getAuthorByName($author_name);
        if ($existing) $author_id = $existing['author_id'];
        else {
            addAuthor($author_name);
            $author_id = $db->lastInsertId();
        }
        addAuthorToEntry($id, $author_id, $author_year ?: null);
    }

    // update artist relation
    deleteDrawnByByEntry($id);
    if (!empty($artist_name)) {
        $existingA = getArtistByName($artist_name);
        if ($existingA) $artist_id = $existingA['artist_id'];
        else {
            addartist($artist_name);
            $artist_id = $db->lastInsertId();
        }
        addartistToEntry($id, $artist_id);
    }
    // update tags
    deleteAllTagsForEntry($id);
    if (!empty($tags_input)) {
        $parts = array_filter(array_map('trim', explode(',', $tags_input)));
        foreach ($parts as $t) {
            $existingT = getTagByText($t);
            if ($existingT) $tag_id = $existingT['tag_id'];
            else $tag_id = addNewTagName($t);
            addNewTagRelationship($tag_id, $id);
        }
    }
    header("Location: entry-detail.php?id=" . $id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Entry - Comic Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background: rgba(0,0,0,0.8) !important; }
        .edit-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 600px;
            margin: 2rem auto;
        }
        .card-header-custom {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body class="dashboard-bg">

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Comic Tracker</a>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>
<div class="edit-card">
    <h3 class="card-header-custom">✏️ Edit Entry</h3>

    <?php if ($entry): ?>
        <form method="post">
            <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
            
            <div class="mb-3">
                <label class="form-label">Comic Name</label>
                <input type="text" name="comic_name" class="form-control" 
                       value="<?php echo htmlspecialchars($entry['comic_name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Rating (0-10)</label>
                <input type="number" name="rating" class="form-control" min="0" max="10"
                       value="<?php echo htmlspecialchars($entry['rating'] ?? ''); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="new" <?php if($entry['curr_status'] == 'new') echo 'selected'; ?>>🆕 New</option>
                    <option value="reading" <?php if($entry['curr_status'] == 'reading') echo 'selected'; ?>>📖 Reading</option>
                    <option value="complete" <?php if($entry['curr_status'] == 'complete') echo 'selected'; ?>>✅ Complete</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Review</label>
                <textarea name="review" class="form-control" rows="3" placeholder="Your thoughts..."><?php echo htmlspecialchars($entry['review'] ?? ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Author</label>
                <div class="input-group">
                    <input type="text" name="author_name" class="form-control" placeholder="Author name (optional)" value="<?php echo htmlspecialchars($current_author['name'] ?? ''); ?>">
                    <input type="number" name="author_year" class="form-control" placeholder="Year (optional)" min="1000" max="9999" value="<?php echo htmlspecialchars($current_author['year'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Artist</label>
                <div class="input-group">
                    <input type="text" name="artist_name" class="form-control" placeholder="Artist name (optional)" value="<?php echo htmlspecialchars($current_artist['name'] ?? ''); ?>">
                    <input type="number" name="artist_year" class="form-control" placeholder="Year (optional)" min="1000" max="9999" value="<?php echo htmlspecialchars($current_artist['year'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Tags (comma-separated)</label>
                <?php
                    $existing_tags = getAllTags($entry_id);
                    $tags_str = '';
                    if (!empty($existing_tags)) {
                        $names = array_map(function($r){ return $r['tag_text']; }, $existing_tags);
                        $tags_str = implode(', ', $names);
                    }
                ?>
                <input type="text" name="tags" class="form-control" placeholder="tag1, tag2" value="<?php echo htmlspecialchars($tags_str); ?>">
            </div>
            
            <div class="d-flex justify-content-between gap-2">
                <a href="entry-detail.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">💾 Save Changes</button>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Entry not found.</div>
        <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
    <?php endif; ?>

</body>
</html>