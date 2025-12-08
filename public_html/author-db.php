<?php

# add author
function addAuthor($name)
{
    global $db;

    $query = "INSERT INTO Author (name) VALUES (:name)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':name', $name);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add author. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add author. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add author. Please ensure all fields are filled in.";
    }
}

function getAuthorByName($name)
{
    global $db;
    $query = "SELECT * FROM Author WHERE name = :name LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':name', $name);
    $statement->execute();
    $result = $statement->fetch();
    $statement->closeCursor();
    return $result;
}

// delete any written_by rows for an entry
function deleteWrittenByByEntry($entry_id)
{
    global $db;
    $query = "DELETE FROM written_by WHERE entry_id = :entry_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $statement->closeCursor();
}


function addAuthorToEntry($entry_id, $author_id, $year)
{
    global $db;

    $query = "INSERT INTO written_by (entry_id, author_id, year) VALUES (:entry_id, :author_id, :year)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':entry_id', $entry_id);
        $statement->bindValue(':author_id', $author_id);
        $statement->bindValue(':year', $year);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add author. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add author. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add author. Please ensure all fields are filled in.";
    }
}

# get author info from entry_id
function getAuthorByEntry($entry_id, $user_id)
{
    global $db;
    $query = "SELECT * FROM Author 
    JOIN written_by ON Author.author_id = written_by.author_id
    LEFT JOIN Entry on written_by.entry_id = Entry.entry_id
    WHERE written_by.entry_id = :entry_id
    AND Entry.user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

// Public variant: returns author information for an entry regardless of owner
function getAuthorByEntryPublic($entry_id)
{
    global $db;
    $query = "SELECT Author.* FROM Author
    JOIN written_by ON Author.author_id = written_by.author_id
    WHERE written_by.entry_id = :entry_id LIMIT 1";
    $statement = $db->prepare($query);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();
    $results = $statement->fetch();
    $statement->closeCursor();

    return $results;
}



# get ALL entries by Author
function getEntriesByAuthor($author_id, $user_id)
{
    global $db;
    $query = "SELECT Entry.* FROM Entry
    JOIN written_by ON written_by.entry_id = Entry.entry_id
    WHERE written_by.author_id = :author_id
    AND Entry.user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':author_id', $author_id);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();

    return $results;
}


function delete_author($author_id) {
    global $db;

    $query = "DELETE FROM Author WHERE author_id=:author_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':author_id', $author_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

function deleteWrittenBy($author_id, $entry_id) {
    global $db;

    $query = "DELETE FROM written_by WHERE author_id=:author_id AND entry_id =:entry_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':author_id', $author_id);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

function updateAuthor($author_id, $name)
{
    global $db;
    
    $query = "UPDATE Author SET name=:name WHERE author_id=:author_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':author_id', $author_id);
    $statement->bindValue(':name', $name);
    $statement->execute();      // run

    $statement->closeCursor();

}
function updateWrittenBy($entry_id, $author_id) {
    global $db;
    
    $query = "UPDATE written_by SET author_id=:author_id WHERE entry_id=:entry_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':author_id', $author_id);
    $statement->bindValue(':entry_id', $entry_id);
    $statement->execute();      // run

    $statement->closeCursor();

}

?>