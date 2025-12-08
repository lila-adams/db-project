<?php
session_start();
require("connect-db.php");
require("entry-db.php");
require("tag-db.php");


// 1. SECURITY: Force login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//give users privilege to edit their entries now that we confirm there is a user_id


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
                // tag already exists → just create relationship
                addNewTagRelationship($existing_tag['tag_id'], $entry_id);
                $success_msg = "Tag already existed — linked successfully!  test";
            } else {
                // tag does not exist, so add to tag_map
                $new_tag_id = addNewTagName($tag_text); 
                addNewTagRelationship($new_tag_id, $entry_id);
                $success_msg = "New tag added & linked! test2";
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
        
        if (!empty($name)) {
            // Call function from entry-db.php
            addEntry($name, $rating, $user_id, $status, $review);
            $success_msg = "Successfully added " . htmlspecialchars($name);
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

// 3. FETCH DATA (After handling forms, so we see the updates)
if (!isset($_POST['action']) || $_POST['action'] != 'search') {
    $entries = getAllEntries($user_id);
}
$all_tags = getAllTagMappings($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Comic Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Comic Tracker</a>
        <div class="d-flex align-items-center">
            <!-- LINK TO PROFILE -->
            <a href="profile.php" class="btn btn-light btn-sm me-3">My Profile</a>
            <span class="text-light me-3">User ID: <?php echo $user_id; ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>



<div class="container">
    
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $success_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
        
        <!-- LEFT COLUMN: ADD ENTRY FORM -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Add New Entry</div>
                <div class="card-body">
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
                                <option value="new">New</option>
                                <option value="in progress">In Progress</option>
                                <option value="complete">Complete</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Review</label>
                            <textarea name="review" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Add Comic</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: ENTRIES LIST -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">My Comic List</h4>
                </div>
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
                                        <th>Add Tag</th> <th>Actions</th> </tr>
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
                                                
                                                <input list="tag-options" name="tag_text" placeholder="Add tag" class="form-control form-control-sm" style="width: 100px; display:inline-block;">
                                                <datalist id="tag-options">
                                                    <?php foreach ($all_tags as $t): ?>
                                                        <option value="<?= htmlspecialchars($t['tag_text']) ?>"></option>
                                                    <?php endforeach; ?>
                                                </datalist>
                                                <button type="submit" class="btn btn-success btn-sm">+</button>
                                            </form>
                                        </td>

                                        <td>
                                            <a href="update-entry.php?id=<?php echo $entry['entry_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            
                                            <form method="post" action="dashboard.php" style="display:inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">X</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            You haven't added any comics yet. Use the form to add one!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS (Optional, for alert closing) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>