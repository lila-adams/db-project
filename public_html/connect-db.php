<?php
// Remember to start the database server (or GCP SQL instance) before trying to connect to it
////////////////////////////////////////////
/** F25, PHP (on Google Standard App Engine) connect to MySQL instance (GCP) **/
// $username = 'comic_app';                       // or your username
// $password = 'ComicDBAUTH1505$';     
// $host = '136.107.47.230';       // e.g., 'cs4750:us-east4:db-demo';  //PUBLIC IP ADDRESS FOR GCP
// $dbname = 'comic-proj-db';           // e.g., 'guestbook';
// $dsn = "mysql:host=136.107.47.230;dbname=comic-proj-db";

//LOCAL instructions
$username = 'comic_app';                  //  Cloud SQL username
$password = 'ComicDBAUTH1505$';                    //  Cloud SQL password
$host = '127.0.0.1';                 // LOCALHOST through Cloud SQL Proxy
$port = 3306;                        // proxy port
$dbname = 'comic-proj-db';           // your database name

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

//     e.g., "mysql:unix_socket=/cloudsql/cs4750:us-east4:db-demo;dbname=comic-proj-db";

//to run locally, go to GoogleCloud instance database-project, Connections->Networking
//add your IP address
//go to VScode, download live server extension (not sure if that did anything tbh)
// run php -S localhost:8000 -t public_html and you should see "test"
// had to run this inside my vscoe terminal: sudo apt update; then sudo apt install php-mysql
//
//-Lila



// to get instance connection name, go to GCP SQL overview page
////////////////////////////////////////////

// To connect from a local PHP to GCP SQL instance, need to add authormized network
// to allow (my)machine to connect to the SQL instance. 
// 1. Get IP of the computer that tries to connect to the SQL instance
//    (use http://ipv4.whatismyv6.com/ to find the IP address)
// 2. On the SQL connections page, add authorized networks, enter the IP address
////////////////////////////////////////////

// DSN (Data Source Name) specifies the host computer for the MySQL datbase 
// and the name of the database. If the MySQL datbase is running on the same server
// as PHP, use the localhost keyword to specify the host computer

// To connect to a MySQL database named db-demo, need three arguments: 
// - specify a DSN, username, and password

// Create an instance of PDO (PHP Data Objects) which connects to a MySQL database
// (PDO defines an interface for accessing databases)
// Syntax: 
//    new PDO(dsn, username, password);


/** connect to the database **/
try 
{
//  $db = new PDO("mysql:host=$hostname;dbname=db-demo", $username, $password);
   $db = new PDO($dsn, $username, $password);

   
   // dispaly a message to let us know that we are connected to the database 
   echo "<p>You are connected to the database -- host=$host</p>";
}
catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
   // Call a method from any object, use the object's name followed by -> and then method's name
   // All exception objects provide a getMessage() method that returns the error message 
   $error_message = $e->getMessage();        
   echo "<p>An error occurred while connecting to the database: $error_message </p>";
}
catch (Exception $e)       // handle any type of exception
{
   $error_message = $e->getMessage();
   echo "<p>Error message: $error_message </p>";
}

?>