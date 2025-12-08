<?php
session_start();
require("connect-db.php");
require("entry-db.php");

// 1. Force Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$entry_id = $_GET['id'] ?? null; // Get ID from URL (e.g., edit_entry.php?id=5)
$entry = null;

// 2. Fetch data
if ($entry_id) {
    $entry = getEntryById($entry_id);
    // Security check: Make sure this entry belongs to the logged-in user!
    // (You might need to update getEntryById to return user_id to check this)
}

// 3. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['entry_id'];
    $name = $_POST['comic_name'];
    $rating = $_POST['rating'];
    if ($rating === '') $rating = null;
    $status = $_POST['status'];
    $review = $_POST['review'];

    updateEntry($id, $name, $rating, $user_id, $status, $review);
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header bg-warning text-dark">Edit Comic</div>
        <div class="card-body">
            <?php if ($entry): ?>
            <form method="post">
                <input type="hidden" name="entry_id" value="<?php echo $entry['entry_id']; ?>">
                
                <div class="mb-3">
                    <label>Comic Name</label>
                    <input type="text" name="comic_name" class="form-control" 
                           value="<?php echo htmlspecialchars($entry['comic_name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label>Rating</label>
                    <input type="number" name="rating" class="form-control" min="0" max="10"
                           value="<?php echo htmlspecialchars($entry['rating']); ?>">
                </div>
                
                <div class="mb-3">
                    <label>Status</label>
                    <select name="status" class="form-select">
                        <option value="new" <?php if($entry['curr_status'] == 'new') echo 'selected'; ?>>New</option>
                        <option value="reading" <?php if($entry['curr_status'] == 'reading') echo 'selected'; ?>>Reading</option>
                        <option value="complete" <?php if($entry['curr_status'] == 'complete') echo 'selected'; ?>>Complete</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label>Review</label>
                    <textarea name="review" class="form-control" rows="3"><?php echo htmlspecialchars($entry['review']); ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning">Save Changes</button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-danger">Entry not found.</div>
                <a href="dashboard.php" class="btn btn-primary">Back</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>