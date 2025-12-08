<?php
function addEntry($comic_name, $rating, $user_id, $curr_status, $review)
{
    global $db;

    $query = "INSERT INTO Entry (comic_name, rating, user_id, curr_status, review) VALUES (:comic_name, :rating, :user_id, :curr_status, :review)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':comic_name', $comic_name);
        $statement->bindValue(':rating', $rating, $rating === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':curr_status', $curr_status);
        $statement->bindValue(':review', $review);
        $statement->execute();      // run 

        $newId = $db->lastInsertId();
        $statement->closeCursor();

        if ($newId)
            return $newId;
        else
            return false;
    }
    catch (PDOException $e) 
    {
        // Log the DB error for debugging and return false to the caller
        error_log("addEntry PDOException: " . $e->getMessage());
        return false;
    }
    catch (Exception $e)
    {
        error_log("addEntry Exception: " . $e->getMessage());
        return false;
    }
}

// get all Entries for a user
function getAllEntries($user_id)
{
    global $db;

    $query = "SELECT * FROM Entry WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function grantEntryAccess($user_id)
{
    global $db;

    $query = "GRANT SELECT, INSERT, UPDATE, DELETE ON Entry TO :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $statement->closeCursor();
}

function searchEntries($user_id, $comic_name, $rating, $curr_status, $tag_text, $author_name, $artist_name)
{
    global $db;

    $query = "SELECT * FROM Entry e
        LEFT JOIN written_by wb on e.entry_id = wb.entry_id
        LEFT JOIN drawn_by db on e.entry_id = db.entry_id
        LEFT JOIN tag on tag.entry_id = e.entry_id
        LEFT JOIN tag_map on tag.tag_id = tag_map.tag_id 
        LEFT JOIN Artist on db.artist_id = Artist.artist_id
        LEFT JOIN Author on wb.author_id = Author.author_id

        WHERE user_id = :user_id";

    if ($comic_name !== "") {
        $query .= " AND e.comic_name LIKE :comic_name";
    }
    if (!empty($tag_text)) {
        $tag_placeholders = [];
        foreach ($tag_text as $i => $tag) {
            $tag_placeholders[] = ":tag$i";
        }
        $query .= " AND tag_map.tag_text IN (" . implode(",", $tag_placeholders) . ")";
    }
    if ($rating !== "") {
        $query .= " AND e.rating = :rating";
    }
    if ($curr_status !== "") {
        $query .= " AND e.curr_status = :curr_status";
    }
    if ($author_name !== "") {
        $query .= " AND Author.author_name LIKE :author_name";
    }
    if ($artist_name !== "") {
        $query .= " AND Artist.artist_name LIKE :artist_name";
    }
    

    $statement = $db->prepare($query);

    $statement->bindValue(':user_id', $user_id);
    
    // optional binds
    if ($comic_name !== "") {
        $statement->bindValue(':comic_name', "%$comic_name%");
    }
    if ($rating !== "") {
        $statement->bindValue(':rating', $rating);
    }
    if ($curr_status !== "") {
        $statement->bindValue(':curr_status', $curr_status);
    }
    if (!empty($tag_text)) {
        foreach ($tag_text as $i => $tag) {
            $statement->bindValue(":tag$i", $tag);
        }
    }
    if ($author_name !== "") {
        $statement->bindValue(':author_name', $author_name);
    }
    if ($artist_name !== "") {
        $statement->bindValue(':artist_name', $artist_name);
    }

    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getYearWritten($entry_id)
{
    global $db;
    $query = "SELECT year FROM written_by WHERE entry_id = :entry_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();

    return $results;
}

function findFavoriteEntries($user_id) {
    global $db;

    $stmt = $db->prepare("CALL FindFavoriteComics(:user_id)");
    $stmt->bindValue(":user_id", $user_id);

    $stmt->execute();
    $results = $stmt->fetchAll();
    $stmt->closeCursor();

    return $results;
}


//get specific entry
function getEntryById($entry_id)  
{
    global $db;
    $query = "SELECT * FROM Entry WHERE entry_id = :entry_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();

    return $results;

}

// get all entries across all users (library view)
function getLibraryEntries()
{
    global $db;
    $query = "SELECT Entry.*, User.username FROM Entry JOIN User ON Entry.user_id = User.user_id ORDER BY Entry.entry_id DESC";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}

// Search the global library with optional filters (returns Entry.* plus username)
function searchLibrary($comic_name = '', $rating = '', $curr_status = '', $tag_text = '', $author_name = '', $artist_name = '')
{
    global $db;

    $query = "SELECT DISTINCT Entry.*, User.username
        FROM Entry
        JOIN User ON Entry.user_id = User.user_id
        LEFT JOIN written_by wb ON Entry.entry_id = wb.entry_id
        LEFT JOIN Author ON wb.author_id = Author.author_id
        LEFT JOIN drawn_by db ON Entry.entry_id = db.entry_id
        LEFT JOIN Artist ON db.artist_id = Artist.artist_id
        LEFT JOIN tag t ON t.entry_id = Entry.entry_id
        LEFT JOIN tag_map tm ON t.tag_id = tm.tag_id
        WHERE 1=1";

    $params = [];

    if ($comic_name !== '') {
        $query .= " AND Entry.comic_name LIKE :comic_name";
        $params[':comic_name'] = "%$comic_name%";
    }
    if ($rating !== '') {
        $query .= " AND Entry.rating = :rating";
        $params[':rating'] = $rating;
    }
    if ($curr_status !== '') {
        $query .= " AND Entry.curr_status = :curr_status";
        $params[':curr_status'] = $curr_status;
    }
    if ($tag_text !== '') {
        $query .= " AND tm.tag_text LIKE :tag_text";
        $params[':tag_text'] = "%$tag_text%";
    }
    if ($author_name !== '') {
        $query .= " AND Author.name LIKE :author_name";
        $params[':author_name'] = "%$author_name%";
    }
    if ($artist_name !== '') {
        $query .= " AND Artist.name LIKE :artist_name";
        $params[':artist_name'] = "%$artist_name%";
    }

    $query .= " ORDER BY Entry.entry_id DESC";

    $statement = $db->prepare($query);
    foreach ($params as $k => $v) {
        $statement->bindValue($k, $v);
    }

    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    return $results;
}


//update entry info
function updateEntry($entry_id, $comic_name, $rating, $user_id, $curr_status, $review)
{
    global $db;
    
    $query = "UPDATE Entry SET comic_name=:comic_name, rating=:rating, curr_status=:curr_status, review=:review WHERE entry_id=:entry_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':comic_name', $comic_name);
    $statement->bindValue(':rating', $rating, $rating === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $statement->bindValue(':curr_status', $curr_status);
    $statement->bindValue(':review', $review);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();      // run

    $statement->closeCursor();

}

#deletes the entry AND all mentions of entry in tag
function deleteEntry($entry_id)
{
    global $db;

    // Delete tag relationships first, then entry
    $query1 = "DELETE FROM tag WHERE entry_id = :entry_id";
    $stmt1 = $db->prepare($query1);
    $stmt1->bindValue(':entry_id', $entry_id);
    $stmt1->execute();
    $stmt1->closeCursor();

    $query2 = "DELETE FROM Entry WHERE entry_id = :entry_id";
    $stmt2 = $db->prepare($query2);
    $stmt2->bindValue(':entry_id', $entry_id);
    $stmt2->execute();
    $stmt2->closeCursor();
}

?>