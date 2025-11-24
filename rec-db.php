<?php
# add recommendation
function addRec($comic_name)
{
    global $db;

    $query = "INSERT INTO Recommendation (comic_name, user_id) VALUES (:comic_name, :user_id)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':comic_name', $comic_name);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add recommendation. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add recommendation. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add recommendation. Please ensure all fields are filled in.";
    }
}

// get all tags for an entry
function getAllRecommendations($entry_id)
{
    global $db;

    $query = "SELECT * FROM Recommendations WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getRecByID($rec_id)
{
    global $db;

    $query = "SELECT * FROM Recommendation WHERE rec_id =:rec_id;";
    $statement = $db->prepare($query);
    $statement->bindValue(':rec_id', $rec_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}


#deletes the entry AND all mentions of entry in tag
function delete_rec($rec_id) {
    global $db;

    $query = "DELETE FROM tag WHERE rec_id=:rec_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':rec_id', $rec_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

//update entry info
function updateRec($rec_id, $comic_name)
{
    global $db;
    
    $query = "UPDATE Entry SET comic_name=:comic_name WHERE rec_id=:rec_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':comic_name', $comic_name);
    $statement->bindValue(':rec_id', $rec_id);
    $statement->execute();      // run

    $statement->closeCursor();

}


?>

