<?php
# add user

function addUser($username, $pwd)
{
    global $db;

    $check = "SELECT user_id FROM User WHERE username = :username";
    $stmt = $db->prepare($check);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $existing = $stmt->fetch();
    $stmt->closeCursor();

    if ($existing) {
        // username already exists
        return false;
    }

    $query = "INSERT INTO User (username, pwd) VALUES (:username, :pwd)";  

    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':pwd', $pwd);

        if ($statement->execute()) {
            $statement->closeCursor();
            return true; 
        } else {
            return false; 
        }
    }
    catch (PDOException $e) {
        return false;
    }
    catch (Exception $e) {
        return false;
    }
}

// get all users
function getAllUsers()
{
    global $db;

    $query = "SELECT * FROM User";
    $statement = $db->prepare($query);
    $statement->execute();
    $results = $statement->fetchAll();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

function getUserInfo($user_id)
{
    global $db;

    $query = "SELECT * FROM User WHERE user_id =:user_id;";
    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}

# could use this to verify that the user put in the correct password
function getPwd_byUsername($username)
{
    global $db;

    $query = "SELECT pwd FROM User WHERE username =:username;";
    $statement = $db->prepare($query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $results = $statement->fetch();   // fetch()
    $statement->closeCursor();
   
    return $results;
}


function delete_user($user_id) {
    global $db;

    $query = "DELETE FROM User WHERE user_id=:user_id";

    $statement = $db->prepare($query);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();    
    
    $statement->closeCursor();
}

# if they want to change their password? idk
function updateUser($user_id, $username, $pwd)
{
    global $db;
    
    $query = "UPDATE User SET username=:username, pwd=:pwd WHERE user_id=:user_id";
    
    $statement = $db->prepare($query);    // compile, leave fill-in-the-blank
    $statement->bindValue(':user_id', $user_id);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':pwd', $pwd);
    $statement->execute();      // run

    $statement->closeCursor();

}


?>