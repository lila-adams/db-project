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

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add entry <br/>";
    }
    
    catch (PDOException $e) 
    {
        echo "Failed to add entry. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add entry. Please ensure all fields are correctly filled in.";
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
        $query .= " AND comic_name LIKE :comic_name";
    }
    if ($tag_text !== "") {
        $query .= " AND tag_text = :tag_text";
    }
    if ($rating !== "") {
        $query .= " AND rating = :rating";
    }
    if ($curr_status !== "") {
        $query .= " AND curr_status = :curr_status";
    }
    if ($author_name !== "") {
        $query .= " AND author_name LIKE :author_name";
    }
    if ($artist_name !== "") {
        $query .= " AND artist_name LIKE :artist_name";
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
    if ($tag_id !== "") {
        $statement->bindValue(':tag_text', $tag_text);
    }
    if ($author_id !== "") {
        $statement->bindValue(':author_name', $author_name);
    }
    if ($artist_id !== "") {
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
    $query = "SELECT year FROM Entry
    JOIN written_by.entry_id = Entry.entry_id
    WHERE Entry.entry_id = :entry_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
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

    $query = "SELECT * FROM Entry WHERE entry_id = :entry_id 
    LEFT JOIN tag ON tag.tag_id = Entry.tag_id
    LEFT JOIN tag_map ON tag.tag_id = tag_map.tag_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
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
    $statement->bindValue(':rating', $rating);
    $statement->bindValue(':curr_status', $curr_status);
    $statement->bindValue(':review', $review);
    $statement->execute();      // run

    $statement->closeCursor();

}

#deletes the entry AND all mentions of entry in tag
function deleteEntry($entry_id)
{
    global $db;

    $query = "DELETE FROM Entry WHERE entry_id=:entry_id;
    DELETE FROM tag WHERE tag.entry_id =:entry_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

?>