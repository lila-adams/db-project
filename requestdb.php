<?php
function addRequests($reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;

    $query = "INSERT INTO requests (reqDate, roomNumber, reqBy, repairDesc, reqPriority) VALUES (:reqDate, :roomNumber, :reqBy, :repairDesc, :reqPriority)";  
    
    try {
        // bad way
        // $statement = $db->query($query);    // sql injection
    
        // good way
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':reqDate', $reqDate);
        $statement->bindValue(':roomNumber', $roomNumber);
        $statement->bindValue(':reqBy', $reqBy);
        $statement->bindValue(':repairDesc', $repairDesc);
        $statement->bindValue(':reqPriority', $reqPriority);
        $statement->execute();      // run 

        $statement->closeCursor();

        // most likely, there should not be a problem adding a request since 
        // a primary key of the table is auto_increment
        // if ($statement->rowCount() == 0)
        //     echo "Failed to add a request <br/>";
    }
    catch (PDOException $e) 
    {
        echo $e->getMessage();   

        // if there is a specific SQL-related error message
        //    echo "generic message (don't reveal SQL-specific message)";
    }
    catch (Exception $e)
    {
       echo $e->getMessage();    // be careful, try to make it generic
    }
}

function getAllRequests()
{
    global $db;

    $query = "SELECT * FROM requests";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getRequestById($id)  
{
    global $db;

    $query = "SELECT * FROM requests WHERE reqId = :id";
    $statement = $db->prepare($query);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;

}

function updateRequest($reqId, $reqDate, $roomNumber, $reqBy, $repairDesc, $reqPriority)
{
    global $db;
    
    $query = "UPDATE requests SET reqDate=:reqDate, roomNumber=:roomNumber, reqBy=:reqBy, repairDesc=:repairDesc, reqPriority=:reqPriority WHERE reqId=:reqId";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':reqId', $reqId);
    $statement->bindValue(':reqDate', $reqDate);
    $statement->bindValue(':roomNumber', $roomNumber);
    $statement->bindValue(':reqBy', $reqBy);
    $statement->bindValue(':repairDesc', $repairDesc);
    $statement->bindValue(':reqPriority', $reqPriority);
    $statement->execute();      // run

    $statement->closeCursor();

}

function deleteRequest($reqId)
{
    global $db;

    $query = "DELETE FROM requests WHERE reqId=:fillin";
    $statement = $db->prepare($query);
    $statement->bindValue(':fillin', $reqId);
    $statement->execute();    
    $statement->closeCursor();
}

?>