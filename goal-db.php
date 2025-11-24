<?php
# add recommendation
function addGoal($user_id, $text)
{
    global $db;

    $query = "INSERT INTO Goal (user_id, text) VALUES (:user_id, :text)";  
    
    try {
        $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':text', $text);
        $statement->execute();      // run 

        $statement->closeCursor();

        if ($statement->rowCount() == 0)
            echo "Failed to add goal. <br/>";
    }
    catch (PDOException $e) 
    {
        echo "Failed to add goal. Please ensure all fields are filled in.";
    }
    catch (Exception $e)
    {
       echo "Failed to add goal. Please ensure all fields are filled in.";
    }
}

// get all tags for an entry
function getAllGoals($user_id)
{
    global $db;

    $query = "SELECT * FROM Goal WHERE user_id = :user_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getGoalByID($goal_id)
{
    global $db;

    $query = "SELECT * FROM Goal WHERE goal_id =:goal_id;";
    $statement = $db->prepare($query);
    $statement->bindValue(':goal_id', $goal_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}


#deletes the entry AND all mentions of entry in tag
function delete_goal($goal_id) {
    global $db;

    $query = "DELETE FROM Goal WHERE goal_id=:goal_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':goal_id', $goal_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

//update entry info
function updateGoal($goal_id, $text)
{
    global $db;
    
    $query = "UPDATE Goal SET text=:text WHERE goal_id=:goal_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':goal_id', $goal_id);
    $statement->bindValue(':text', $text);
    $statement->execute();      // run

    $statement->closeCursor();

}


?>