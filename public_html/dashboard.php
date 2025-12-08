<?php
session_start();
require("connect-db.php");
require("entry-db.php");
require("tag-db.php");
require("author-db.php");
require("artist-db.php");

// 1. SECURITY: Force login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_msg = "";
$success_msg = "";

// 2. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- ADD TAG TO ENTRY ---
    if (isset($_POST['action']) && $_POST['action'] == 'add_tag') {
        $entry_id = $_POST['entry_id'];
        $tag_text = trim($_POST['tag_text']);
    
        if (empty($tag_text)) {
            $error_msg = "Tag cannot be empty.";
        } else {
            // see if tag already exists in tag_map
            $existing_tag = getTagByText($tag_text, $user_id);
    
            if ($existing_tag) {
<<<<<<< HEAD
                // tag already exists → just create relationship
                addNewTagRelationship($existing_tag['tag_id'], $entry_id);
                $success_msg = "Tag already existed — linked successfully!  test";
            } else {
                // tag does not exist, so add to tag_map
                $new_tag_id = addNewTagName($tag_text); 
                addNewTagRelationship($new_tag_id, $entry_id);
                $success_msg = "New tag added & linked! test2";
=======
                addNewTagRelationship($entry_id, $existing_tag['tag_id']);
                $success_msg = "Tag linked successfully!";
            } else {
                // 2. Tag does not exist → add to tag_map
                $new_tag_id = addNewTagName($tag_text);
                addNewTagRelationship($entry_id, $new_tag_id);
                $success_msg = "New tag added!";
>>>>>>> 94eabe6b0c2f6e93825517349298f3367ec5a946
            }
        }
    }
    elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'search') {
        $entries = searchEntries(
            $user_id,
            $_POST['comic_name'] ?? "",
            $_POST['rating'] ?? "",
            $_POST['status'] ?? "",
            $_POST['tag_text'] ?? [],
            $_POST['author_name'] ?? "",
            $_POST['artist_name'] ?? ""
        );
    }
    // ADD ENTRY 
    elseif (isset($_POST['action']) && $_POST['action'] == 'add') {
        $name = trim($_POST['comic_name']);
        $rating = $_POST['rating'];
        if ($rating === '') $rating = null;
        $status = $_POST['status'];
        $review = $_POST['review'];
        $author_name = trim($_POST['author_name'] ?? '');
        $author_year = trim($_POST['author_year'] ?? '');
        $artist_name = trim($_POST['artist_name'] ?? '');
        $artist_year = trim($_POST['artist_year'] ?? '');
        
        if (!empty($name)) {
            $new_id = addEntry($name, $rating, $user_id, $status, $review);
            if ($new_id) {
                // Author handling
                if (!empty($author_name)) {
                    $existing = getAuthorByName($author_name);
                    if ($existing) $author_id = $existing['author_id'];
                    else {
                        addAuthor($author_name);
                        $author_id = $db->lastInsertId();
                    }
                    // add relation (year optional)
                    addAuthorToEntry($new_id, $author_id, $author_year ?: null);
                }
                // Artist handling
                if (!empty($artist_name)) {
                    $existingA = getArtistByName($artist_name);
                    if ($existingA) $artist_id = $existingA['artist_id'];
                    else {
                        addartist($artist_name);
                        $artist_id = $db->lastInsertId();
                    }
                    addartistToEntry($new_id, $artist_id);
                }

                $success_msg = "Successfully added " . htmlspecialchars($name);
            } else {
                $error_msg = "Failed to add entry.";
            }
        } else {
            $error_msg = "Comic name is required.";
        }
    }
    
    // --- DELETE ENTRY ---
    elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $entry_id_to_delete = $_POST['entry_id'];
        deleteEntry($entry_id_to_delete);
        $success_msg = "Entry deleted.";
    }
}

<<<<<<< HEAD
// 3. FETCH DATA (After handling forms, so we see the updates)
if (!isset($_POST['action']) || $_POST['action'] != 'search') {
    $entries = getAllEntries($user_id);
}
$all_tags = getAllTagMappings($user_id);
=======
// 3. FETCH DATA
$library = getLibraryEntries();
// fetch all tag mappings for dropdown filter
$all_tags = getAllTagMappings();
>>>>>>> 94eabe6b0c2f6e93825517349298f3367ec5a946
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .navbar { background: rgba(0,0,0,0.8) !important; backdrop-filter: blur(10px); }
        .container-main { max-width: 1200px; }
        .add-entry-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .entries-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .entry-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .entry-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            border-color: #667eea;
        }
        .entry-item { cursor: pointer; }
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
        .status-new { background: #e3f2fd; color: #1976d2; }
        .status-reading { background: #fff3e0; color: #f57c00; }
        .status-complete { background: #e8f5e9; color: #388e3c; }
        .rating { font-weight: bold; color: #667eea; font-size: 1.1rem; }
        .btn-link-custom { color: #667eea; text-decoration: none; font-weight: 600; }
        .btn-link-custom:hover { color: #764ba2; text-decoration: underline; }
        .tag-badge {
            display: inline-block;
            background: #667eea;
            color: #fff;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            margin-right: 0.4rem;
            margin-bottom: 0.4rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark mb-4">
    <div class="container-main">
        <a class="navbar-brand fw-bold" href="dashboard.php">📚 Comic Tracker</a>
        <div class="d-flex gap-2 align-items-center">
            <a href="browse-profiles.php" class="btn btn-outline-light btn-sm">👥 Browse Profiles</a>
            <a href="profile.php" class="btn btn-light btn-sm">My Profile</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<<<<<<< HEAD


<div class="container">
=======
<div class="container-main">
>>>>>>> 94eabe6b0c2f6e93825517349298f3367ec5a946
    
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

<<<<<<< HEAD
    <div class="row">

    <div class="card mb-4 shadow-sm">
    <div class="card-header bg-secondary text-white">Search Comics</div>
    <div class="card-body">
        <form method="post" action="dashboard.php" class="row g-3">
            
            <!-- comic Name -->
            <div class="col-md-4">
                <input type="text" name="comic_name" class="form-control" placeholder="Comic Name" 
                       value="<?= isset($_POST['comic_name']) ? htmlspecialchars($_POST['comic_name']) : '' ?>">
            </div>

            <!-- tags, multi select-->
            <div class="col-md-4">
                <select name="tag_text[]" class="form-select" multiple>
                    <?php foreach ($all_tags as $t): ?>
                        <option value="<?= htmlspecialchars($t['tag_text']) ?>"
                            <?php 
                                if (isset($_POST['tag_text']) && in_array($t['tag_text'], $_POST['tag_text'])) echo 'selected'; 
                            ?>>
                            <?= htmlspecialchars($t['tag_text']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Hold Ctrl (Windows) / Cmd (Mac) to select multiple</small>
            </div>

            <!-- rating -->
            <div class="col-md-2">
                <input type="number" name="rating" class="form-control" min="0" max="10" placeholder="Rating" 
                       value="<?= isset($_POST['rating']) ? htmlspecialchars($_POST['rating']) : '' ?>">
            </div>

            <!-- status -->
            <div class="col-md-2">
                <select name="curr_status" class="form-select">
                    <option value="">Any Status</option>
                    <option value="new" <?= (isset($_POST['curr_status']) && $_POST['curr_status']=='new') ? 'selected' : '' ?>>New</option>
                    <option value="in progress" <?= (isset($_POST['curr_status']) && $_POST['curr_status']=='in progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="complete" <?= (isset($_POST['curr_status']) && $_POST['curr_status']=='complete') ? 'selected' : '' ?>>Complete</option>
                </select>
            </div>

            <!-- author -->
            <div class="col-md-3">
                <input type="text" name="author_name" class="form-control" placeholder="Author"
                       value="<?= isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name']) : '' ?>">
            </div>

            <!-- artist -->
            <div class="col-md-3">
                <input type="text" name="artist_name" class="form-control" placeholder="Artist"
                       value="<?= isset($_POST['artist_name']) ? htmlspecialchars($_POST['artist_name']) : '' ?>">
            </div>

            <!-- buttons -->
            <div class="col-md-2">
                <button type="submit" name="action" value="search" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="dashboard.php" class="btn btn-secondary w-100">Reset</a>
            </div>

        </form>
    </div>
</div>
=======
    <div class="row g-4">
>>>>>>> 94eabe6b0c2f6e93825517349298f3367ec5a946
        
        <!-- LEFT COLUMN: ADD ENTRY FORM -->
        <div class="col-lg-4">
            <div class="add-entry-card">
                <h5 class="mb-4">➕ Add New Entry</h5>
                <form method="post" action="dashboard.php">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Comic Name</label>
                        <input type="text" name="comic_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Rating (0-10)</label>
                        <input type="number" name="rating" class="form-control" min="0" max="10">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="new">🆕 New</option>
                            <option value="reading">📖 Reading</option>
                            <option value="complete">✅ Complete</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Review</label>
                        <textarea name="review" class="form-control" rows="2" placeholder="What did you think?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <div class="input-group">
                            <input type="text" name="author_name" class="form-control" placeholder="Author name (optional)">
                            <input type="number" name="author_year" class="form-control" placeholder="Year (optional)" min="1000" max="9999">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Artist</label>
                        <div class="input-group">
                            <input type="text" name="artist_name" class="form-control" placeholder="Artist name (optional)">
                            <input type="number" name="artist_year" class="form-control" placeholder="Year (optional)" min="1000" max="9999">
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Add Comic</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: ENTRIES LIST -->
        <div class="col-lg-8">
            <div class="entries-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">📚 Library (<?php echo count($library); ?>)</h5>
                    <form class="d-flex" method="get" action="dashboard.php" id="library-search-form">
                        <input type="text" name="q" class="form-control form-control-sm me-2" placeholder="Search comics, authors..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                        <select name="tag" class="form-select form-select-sm me-2">
                            <option value="">All tags</option>
                            <?php foreach ($all_tags as $t): ?>
                                <option value="<?php echo htmlspecialchars($t['tag_text']); ?>" <?php if(($_GET['tag'] ?? '') == $t['tag_text']) echo 'selected'; ?>><?php echo htmlspecialchars($t['tag_text']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="status" class="form-select form-select-sm me-2">
                            <option value="">All</option>
                            <option value="new" <?php if(($_GET['status'] ?? '') == 'new') echo 'selected'; ?>>New</option>
                            <option value="reading" <?php if(($_GET['status'] ?? '') == 'reading') echo 'selected'; ?>>Reading</option>
                            <option value="complete" <?php if(($_GET['status'] ?? '') == 'complete') echo 'selected'; ?>>Complete</option>
                        </select>
                        <input type="number" name="rating" min="0" max="10" class="form-control form-control-sm me-2" style="width:80px;" placeholder="Rating" value="<?php echo htmlspecialchars($_GET['rating'] ?? ''); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit">Filter</button>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary ms-2">Clear</a>
                    </form>
                </div>
<<<<<<< HEAD
                <div class="card-body p-0">
                    <?php if (count($entries) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Rating</th>
                                        <th>Status</th>
                                        <th>Review</th>
                                        <th>Tags</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($entries as $entry): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($entry['comic_name']); ?></strong></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($entry['rating']); ?>/10</span></td>
                                        <td><?php echo htmlspecialchars($entry['curr_status']); ?></td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($entry['review']); ?></small></td>
                                        <td>
                                            <?php 
                                                $tags = getAllTags($entry['entry_id']);
                                                foreach ($tags as $tag) {
                                                    echo '<span class="badge bg-info text-dark me-1">'.$tag['tag_text'].'</span>';
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <form method="post" action="dashboard.php">
                                                <input type="hidden" name="action" value="add_tag">
                                                <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                                
                                                <input list="tag-options" name="tag_text" placeholder="Add or select tag" class="form-control form-control-sm">
                                                
                                                <datalist id="tag-options">
                                                    <?php foreach ($all_tags as $t): ?>
                                                        <option value="<?= htmlspecialchars($t['tag_text']) ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist>

                                                <button type="submit" class="btn btn-success btn-sm mt-1">Add Tag</button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="post" action="dashboard.php">
                                                <input type="hidden" name="action" value="add_tag">
                                                <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                                
                                                <input list="tag-options" name="tag_text" placeholder="Add or select tag" class="form-control form-control-sm">
                                                
                                                <datalist id="tag-options">
                                                    <?php foreach ($all_tags as $t): ?>
                                                        <option value="<?= htmlspecialchars($t['tag_text']) ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist>

                                                <button type="submit" class="btn btn-success btn-sm mt-1">Add Tag</button>
                                            </form>
                                        </td>
                                        <td>
                                            <!-- EDIT BUTTON LINKS TO update_entry.php -->
                                            <a href="update-entry.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-warning btn-sm me-1">Edit</a>
                                            
                                            <!-- DELETE BUTTON -->
                                            <form method="post" action="dashboard.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
=======

                <?php
                    // if a search/filter is active, call searchLibrary
                    $q = trim($_GET['q'] ?? '');
                    $status_filter = trim($_GET['status'] ?? '');
                    $rating_filter = trim($_GET['rating'] ?? '');

                    $tag_filter = trim($_GET['tag'] ?? '');

                    if ($q !== '' || $status_filter !== '' || $rating_filter !== '' || $tag_filter !== '') {
                        // small heuristic: match q to author/artist/name and allow explicit tag filter
                        $search_results = searchLibrary($q, $rating_filter, $status_filter, $tag_filter, $q, $q);
                    } else {
                        $search_results = $library;
                    }
                ?>

                <?php if (count($search_results) > 0): ?>
                    <?php foreach ($search_results as $entry): ?>
                                <div class="entry-item" data-entry-id="<?php echo $entry['entry_id']; ?>" tabindex="0" role="link">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-2">
                                        <a href="entry-detail.php?id=<?php echo $entry['entry_id']; ?>" class="btn-link-custom">
                                            <?php echo htmlspecialchars($entry['comic_name']); ?>
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($entry['review']); ?></p>
                                    <?php
                                        // show tags for this entry
                                        $entry_tags = getAllTags($entry['entry_id']);
                                        if (!empty($entry_tags)) {
                                            echo '<div class="mt-2">';
                                            foreach ($entry_tags as $et) {
                                                echo '<span class="tag-badge">' . htmlspecialchars($et['tag_text']) . '</span> ';
                                            }
                                            echo '</div>';
                                        }
                                    ?>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php if ($entry['rating']): ?>
                                        <div class="rating mb-2">⭐ <?php echo $entry['rating']; ?></div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <div class="small text-muted">by <?php echo htmlspecialchars($entry['username']); ?></div>
                                        <span class="status-badge status-<?php echo $entry['curr_status']; ?>">
                                            <?php echo ucfirst($entry['curr_status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <?php if ($entry['user_id'] == $user_id): ?>
                                    <a href="update-entry.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-sm btn-warning me-2" onclick="event.stopPropagation();">✏️ Edit</a>
                                    <form method="post" style="display:inline;" onsubmit="event.stopPropagation();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation(); return confirm('Delete this entry?')">🗑️ Delete</button>
                                    </form>
                                <?php else: ?>
                                    <a href="entry-detail.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">View</a>
                                <?php endif; ?>
                            </div>
>>>>>>> 94eabe6b0c2f6e93825517349298f3367ec5a946
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">No comics yet. Add one to get started!</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>



<!-- Bootstrap JS (Optional, for alert closing) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Make entire entry row clickable (but don't navigate when clicking form elements or links)
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.entry-item').forEach(function(item){
        item.addEventListener('click', function(e){
            // if clicked a link, button or inside a form, do nothing
            if (e.target.closest('a') || e.target.closest('form') || e.target.closest('button')) return;
            var id = item.dataset.entryId;
            if (id) {
                window.location.href = 'entry-detail.php?id=' + encodeURIComponent(id);
            }
        });
        // keyboard accessibility: Enter or Space triggers navigation
        item.addEventListener('keydown', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var id = item.dataset.entryId;
                if (id) window.location.href = 'entry-detail.php?id=' + encodeURIComponent(id);
            }
        });
    });
});
</script>
</body>
</html>