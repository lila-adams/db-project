<?php
# add link btwn entry and tag
function addEntry($comic_name, $rating, $user_id, $curr_status, $review)
{
    global $db;

    $query = "INSERT INTO tag (entry_id, tag_id) VALUES (:entry_id, :tag_id)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':entry_id', $entry_id);
        $statement->bindValue(':tag_id', $tag_id);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add tag <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add tag to this entry. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add tag to this entry. Please ensure all fields are filled in.";
    }
}

# add to tag_mapping IF tag not already in mapping
function addNewTagName($tag_text)
{
    global $db;

    $query = "INSERT INTO tag_map (tag_text) VALUES (:tag_text)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':tag_text', $tag_text);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add tag <br/>";
        
        
        $tag_id = $db->lastInsertId();
        return $tag_id;
    }
    catch (PDOException $e) 
    {
        echo "Failed to add new tag to system. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add new tag to system. Please ensure all fields are filled in.";
    }
}
function addNewTagRelationship($tag_id, $entry_id)
{
    global $db;

    $query = "INSERT INTO tag (tag_id, entry_id) VALUES (:tag_id, :entry_id)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':tag_id', $tag_id);
        $statement->bindValue(':entry_id', $entry_id);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add tag <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add new tag to system. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add new tag to system. Please ensure all fields are filled in.";
    }
}

function getTagInfoByID($tag_id)  
{
    global $db;

    $query = "SELECT * FROM tag WHERE tag_id = :tag_id 
    LEFT JOIN tag_map ON tag.tag_id = tag_map.tag_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':tag_id', $tag_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;

}

// get all tags for an entry
function getAllTags($entry_id)
{
    global $db;

    $query = "SELECT * FROM tag WHERE entry_id = :entry_id
    JOIN tag_map on tag_map.tag_id = tag.tag_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getAllTagMappings()
{
    global $db;

    $query = "SELECT * FROM tag_map";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}



function delete_tag($entry_id, $tag_id) {
    global $db;

    $query = "DELETE FROM tag WHERE entry_id=:entry_id AND tag_id =:tag_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->bindValue(':tag_id', $tag_id);
    $statement->execute();    
    
    $statement->closeCursor();
}


function delete_tagMapping($tag_id) {
    global $db;

    $query = "DELETE FROM tag_map WHERE tag_id =:tag_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':tag_id', $tag_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

function getTagByText($tag_text) {
    global $db;
    $query = "SELECT tag_id FROM tag_map WHERE tag_text = :tag_text LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':tag_text', $tag_text);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $result;
}

?>


