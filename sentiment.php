<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

//include the S3 class                
if (!class_exists('S3'))require_once('S3.php');  
require_once('config.php');
  
//AWS access info  
if (!defined('awsAccessKey')) define('awsAccessKey', myAWSAccessKey);  
if (!defined('awsSecretKey')) define('awsSecretKey', myAWSSecretKey); 

//get the sentiment
$myvars = 'txt='.urlencode($_REQUEST['text']);
$url = 'http://sentiment.vivekn.com/web/text/';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = json_decode(curl_exec( $ch ));
  
$s3 = new S3(awsAccessKey, awsSecretKey);  

//create the bucket if it doesnt exist
$bucketName = BUCKET_NAME; 
$s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);

//create the folder name
$searchTerm = $_REQUEST['searchTerm'];
$streamNumber = sizeof($s3->getBucket($bucketName, $searchTerm . 'Stream', null, null, '/')) + 1;
$folderName = $searchTerm. 'Stream' . $streamNumber . '/';
  
//create the filename, unique key is used incase the previous file has not finished uploading. Avoids name conflicts
$tweetNumber = sizeof($s3->getBucket($bucketName, $folderName)) + 1;
$uploadName = $folderName . "tweet" . addLeadingZeroes($tweetNumber) . "_" . generateUniqueTweetId() . ".json";

//ceate the tweet array
$text = $_REQUEST['text'];
$sentiment = strtolower($response->result);
$createdAt = $_REQUEST['created_at'];
$screenName = $_REQUEST['screen_name'];
$name = $_REQUEST['name'];
$profileImageUrl = $_REQUEST['profile_image_url'];

$tweetArray = array("text" => $text, "sentiment" => $sentiment, "createdAt" => $createdAt, "screenName" => $screenName, "name" => $name, "profileImageUrl" => $profileImageUrl);

//push json object to s3
$s3->putObject(json_encode($tweetArray), $bucketName, $uploadName, S3::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));

//echo url of json file
echo "https://s3.amazonaws.com/".$bucketName"/" . $uploadName;

//adds leading zeroes to ensure numerical order in the file names
function addLeadingZeroes($number){   
    switch ($number){
        case $number < 10:
            return "000" . $number;
            break;
        case $number < 100:
            return "00" . $number;
            break;
        case $number < 1000:
            return "0" . $number;
            break;        
    }
    
    return $number;
}

//avoids name clashes
function generateUniqueTweetId() {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>