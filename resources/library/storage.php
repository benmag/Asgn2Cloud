<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

//use Aws\SimpleDb\SimpleDbClient;
use Aws\DynamoDb\DynamoDbClient;

class storage { 

	//var $sdb; // simple db
        var $ddb; //dyanmo db
        //var $s3; // S3
        //var $bucketName; // name of the bucket we're storing stuff in

	/** 
	 * Get everything setup 
	 * 
	 * @return void
	 */
        
	function __construct() {

            require_once(dirname(__FILE__)."/../../vendor/autoload.php");
            require_once(dirname(__FILE__)."/../../config.php");
            
            // SimpleDB
            /*$this->sdb = SimpleDbClient::factory(array(
                'key'    => myAWSAccessKey,
                'secret' => myAWSSecretKey,
                'region' => 'sa-east-1'
            ));*/

            $this->ddb = DynamoDbClient::factory(array(
                'key'    => myAWSAccessKey,
                'secret' => myAWSSecretKey,
                'region' => 'sa-east-1'
            ));
            
            // S3
            /*$this->bucketName = BUCKET_NAME;
            $this->s3 = new S3(myAWSAccessKey, myAWSSecretKey); */

	}

	/** 
	 * Save tweet into a bucket 
	 *
	 * @param $tweetData - array containing all the tweet data we want to store
	 */
        
	public function put_tweet($searchTerm, $tweetData) {

            // SimpleDB
            /*$this->sdb->putAttributes(array(
                'DomainName' => $this->getDomain(),
                'ItemName'   => 'tweet' . $this->generateUniqueTweetId(),
                'Attributes' => array(
                    array('Name' => 'text', 'Value' => $tweetData['text']),
                    array('Name' => 'sentiment', 'Value' => $tweetData['sentiment']),
                    array('Name' => 'createdA', 'Value' => $tweetData['createdAt']),
                    array('Name' => 'screenName', 'Value' => $tweetData['screenName']),
                    array('Name' => 'name', 'Value' => $tweetData['name']),
                    array('Name' => 'profileImageUrl', 'Value' => $tweetData['profileImageUrl']),
                    array('Name' => 'searchTerm', 'Value' => $tweetData['searchTerm'])
                )
            ));*/
            
            // DynamoDB
            $time = time();

            $this->ddb->putItem(array(
                'TableName' => 'joeTweets',
                'Item' => array(
                    'id'      => array('S' => $this->generateUniqueTweetId()), //im not sure about this, but everythign ive read says the index should be random
                    'time'    => array('N' => $time) //allows for easier querying if we ever need it
            )
            ));
            
            // s3
            /*$uploadName = $searchTerm . "/tweet_" . $this->generateUniqueTweetId() . ".json";
            $this->s3->putObject(json_encode($tweetData), $this->bucketName, $uploadName, S3::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));*/
	}


	/**
	 * Generate a random id so we always avoid clashes
	 * 
	 * @return random string
	 */
        
	private function generateUniqueTweetId() {
	 	
            // List of characters
	    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';

	    // Pick random ones 
	    for ($i = 0; $i < 10; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }

	    return $randomString;
	}
        
        // simpleDb
        /*private function getDomain(){
            $int = rand(1, 25);
            return 'tweets' . $int;
        }*/


}
?>