<?php
// Comment DB helpers
function addComment($writer_id, $entry_id, $text)
{
    global $db;

    $query = "INSERT INTO Comment (writer_id, entry_id, text, timestamp) VALUES (:writer_id, :entry_id, :text, CURRENT_TIMESTAMP)";
    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':writer_id', $writer_id);
        $statement->bindValue(':entry_id', $entry_id);
        $statement->bindValue(':text', $text);
        $statement->execute();
        $statement->closeCursor();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getCommentsByEntry($entry_id)
{
    global $db;
    $query = "SELECT c.comment_id, c.writer_id, c.entry_id, c.text, c.timestamp, u.username
        FROM Comment c
        JOIN User u ON c.writer_id = u.user_id
        WHERE c.entry_id = :entry_id
        ORDER BY c.timestamp DESC";

    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

// allow a user to delete their own comment
function deleteComment($comment_id, $writer_id)
{
    global $db;
    $query = "DELETE FROM Comment WHERE comment_id = :comment_id AND writer_id = :writer_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':comment_id', $comment_id);
    $statement->bindValue(':writer_id', $writer_id);
    $statement->execute();
    $statement->closeCursor();
}

?>
