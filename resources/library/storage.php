<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

use Aws\SimpleDb\SimpleDbClient;
use Aws\DynamoDb\DynamoDbClient;

class storage { 

	//var $bucketName; // name of the bucket we're storing stuff in
	var $sdb; // simple db
        var $ddb; //dyanmo db

	/** 
	 * Get everything setup 
	 * 
	 * @return void
	 */
        
	function __construct() {

		require_once(dirname(__FILE__)."/../../vendor/autoload.php");
                require_once(dirname(__FILE__)."/../../config.php");

		/*$this->sdb = SimpleDbClient::factory(array(
                    'key'    => myAWSAccessKey,
                    'secret' => myAWSSecretKey,
                    'region' => 'us-west-2'
                ));*/
                
                $this->ddb = DynamoDbClient::factory(array(
                    'key'    => myAWSAccessKey,
                    'secret' => myAWSSecretKey,
                    'region' => 'us-west-2'
                ));

	}

	/** 
	 * Save tweet into a bucket 
	 *
	 * @param $tweetData - array containing all the tweet data we want to store
	 * @return string S3 storage location
	 */
        
	public function put_tweet($searchTerm, $tweetData) {

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
            $time = time();

            $this->ddb->putItem(array(
                'TableName' => 'joeTweets',
                'Item' => array(
                    'id'      => array('N' => '1201'),
                    'time'    => array('N' => $time)
            )
            ));
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
        
        private function getDomain(){
            $int = rand(1, 25);
            return 'tweets' . $int;
        }


}
?>