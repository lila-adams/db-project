<?php
function addEntry($comic_name, $rating, $user_id, $curr_status)
{
    global $db;

    $query = "INSERT INTO Entry (comic_name, rating, user_id, curr_status, review) VALUES (:comic_name, :rating, :user_id, :curr_status, :review)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':comic_name', $comic_name);
        $statement->bindValue(':rating', $rating);
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
       echo "Failed to add entry. Please ensure all fields are filled in.";
    }
}

// get all Entries for a user
function getAllEntries($user_id)
{
    global $db;

    $query = "SELECT * FROM Entries WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

//get specific entry
function getEntryById($entry_id)  
{
    global $db;

    $query = "SELECT * FROM Entry WHERE entry_id = :entry_id";
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

function deleteEntry($entry_id)
{
    global $db;

    $query = "DELETE FROM Entry WHERE entry_id=:entry_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();    
    $statement->closeCursor();
}

?>