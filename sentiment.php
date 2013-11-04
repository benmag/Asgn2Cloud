<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if(!isset($_REQUEST['text'])) exit("Health check. Status OK.");
if(!isset($_REQUEST['text']) || !isset($_REQUEST['screen_name']) || !isset($_REQUEST['searchTerm'])) exit("Insufficient data provided");
require_once('resources/library/sentiment.php');
require_once('resources/library/broadcast.php');
require_once('resources/library/storage.php');
$sentiment = new sentiment(); // sentiment analysis object
$storage = new storage();
$broadcast = new broadcast();


// Setup tweet variables
$text = $_REQUEST['text'];
$sentiment = $sentiment->analyse($text);
$screenName = $_REQUEST['screen_name'];
$searchTerm = $_REQUEST['searchTerm'];
if(isset($_REQUEST['name'])) $name = $_REQUEST['name']; else $name = null;
if(isset($_REQUEST['created_at'])) $createdAt = $_REQUEST['created_at']; else $createdAt = null;
if(isset($_REQUEST['profile_image_url'])) $profileImageUrl = $_REQUEST['profile_image_url']; else $profileImageUrl = null;

// Create tweet array for storage 
$tweetArray = array(
                    "text" => $text,   
                    "sentiment" => $sentiment, 
                    "createdAt" => $createdAt, 
                    "screenName" => $screenName, 
                    "name" => $name, 
                    "profileImageUrl" => $profileImageUrl,
                    "searchTerm" => $searchTerm
            );


// Send tweet to client
$broadcast->send($tweetArray);

// Store file. WE DONT STORE FILES HERE ANYMORE
echo "File stored: ". $storage->put_tweet($searchTerm, $tweetArray);

?>