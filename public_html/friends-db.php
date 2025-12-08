<?php
// Friendship helpers for is_friends table

function areFriends($user_a, $user_b)
{
    global $db;
    $query = "SELECT 1 FROM is_friends WHERE (user_id1 = :a AND user_id2 = :b) OR (user_id1 = :b AND user_id2 = :a) LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':a', $user_a);
    $statement->bindValue(':b', $user_b);
    $statement->execute();
    $res = $statement->fetch();
    $statement->closeCursor();
    return (bool)$res;
}

function addFriend($user_a, $user_b)
{
    global $db;
    if ($user_a == $user_b) return false;
    // enforce ordering to avoid duplicate mirrored rows
    $first = min($user_a, $user_b);
    $second = max($user_a, $user_b);

    // ensure not already friends
    if (areFriends($first, $second)) return false;

    $query = "INSERT INTO is_friends (user_id1, user_id2) VALUES (:u1, :u2)";
    $statement = $db->prepare($query);
    $statement->bindValue(':u1', $first);
    $statement->bindValue(':u2', $second);
    $statement->execute();
    $statement->closeCursor();
    return true;
}

function removeFriend($user_a, $user_b)
{
    global $db;
    $first = min($user_a, $user_b);
    $second = max($user_a, $user_b);
    $query = "DELETE FROM is_friends WHERE user_id1 = :u1 AND user_id2 = :u2";
    $statement = $db->prepare($query);
    $statement->bindValue(':u1', $first);
    $statement->bindValue(':u2', $second);
    $statement->execute();
    $statement->closeCursor();
}

function getFriends($user_id)
{
    global $db;
    $query = "SELECT u.user_id, u.username FROM User u
        JOIN is_friends f ON (u.user_id = f.user_id1 OR u.user_id = f.user_id2)
        WHERE (f.user_id1 = :id OR f.user_id2 = :id) AND u.user_id != :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

?>
