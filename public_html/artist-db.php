<?php

# add artist
function addartist($name)
{
    global $db;

    $query = "INSERT INTO Artist (name) VALUES (:name)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':name', $name);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add artist. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add artist. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add artist. Please ensure all fields are filled in.";
    }
}

function getArtistByName($name)
{
    global $db;
    $query = "SELECT * FROM Artist WHERE name = :name LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':name', $name);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();
    return $result;
}

// delete any drawn_by rows for an entry
function deleteDrawnByByEntry($entry_id)
{
    global $db;
    $query = "DELETE FROM drawn_by WHERE entry_id = :entry_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $statement->closeCursor();
}


function addartistToEntry($entry_id, $artist_id)
{
    global $db;

    $query = "INSERT INTO drawn_by (entry_id, artist_id) VALUES (:entry_id, :artist_id)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':entry_id', $entry_id);
        $statement->bindValue(':artist_id', $artist_id);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add artist. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add artist. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add artist. Please ensure all fields are filled in.";
    }
}

# get artist info from entry_id
function getartistByEntry($entry_id, $user_id)
{
    global $db;
    $query = "SELECT * FROM Artist 
    JOIN drawn_by ON Artist.artist_id = drawn_by.artist_id
    LEFT JOIN Entry on drawn_by.entry_id = Entry.entry_id
    WHERE drawn_by.entry_id = :entry_id
    AND Entry.user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

// Public variant: returns artist information for an entry regardless of owner
function getArtistByEntryPublic($entry_id)
{
    global $db;
    $query = "SELECT Artist.* FROM Artist
    JOIN drawn_by ON Artist.artist_id = drawn_by.artist_id
    WHERE drawn_by.entry_id = :entry_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();

    return $results;
}



# get ALL entries by artist
function getEntriesByArtist($artist_id, $user_id)
{
    global $db;
    $query = "SELECT Entry.* FROM Entry
    JOIN drawn_by ON drawn_by.entry_id = Entry.entry_id
    WHERE drawn_by.artist_id = :artist_id
    AND Entry.user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':artist_id', $artist_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

    return $results;
}


function delete_artist($artist_id) {
    global $db;

    $query = "DELETE FROM Artist WHERE artist_id=:artist_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':artist_id', $artist_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

function deleteDrawnBy($artist_id, $entry_id) {
    global $db;

    $query = "DELETE FROM drawn_by WHERE artist_id=:artist_id AND entry_id =:entry_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':artist_id', $artist_id);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

function updateArtist($artist_id, $name)
{
    global $db;
    
    $query = "UPDATE Artist SET name=:name WHERE artist_id=:artist_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':artist_id', $artist_id);
    $statement->bindValue(':name', $name);
    $statement->execute();      // run

    $statement->closeCursor();

}
function updateDrawnBy($entry_id, $artist_id) {
    global $db;
    
    $query = "UPDATE drawn_by SET artist_id=:artist_id WHERE entry_id=:entry_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':artist_id', $artist_id);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();      // run

    $statement->closeCursor();

}

?>