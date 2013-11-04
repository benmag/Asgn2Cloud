<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

use Aws\DynamoDb\DynamoDbClient;

class storage { 
    
        var $ddb; //dyanmo db

	/** 
	 * Get everything setup 
	 * 
	 * @return void
	 */      
	function __construct() {

            require_once(dirname(__FILE__)."/../../vendor/autoload.php");
            require_once(dirname(__FILE__)."/../../config.php");

            $this->ddb = DynamoDbClient::factory(array(
                'key'    => myAWSAccessKey,
                'secret' => myAWSSecretKey,
                'region' => region
            ));

	}

	/** 
	 * Save tweet into a bucket 
	 *
	 * @param $tweetData - array containing all the tweet data we want to store
	 */
        
	public function put_tweet($searchTerm, $tweetData) {
            
            // Wait until the table is created and updated
            $this->ddb->waitUntilTableExists(array(
                'TableName' => tableName
            ));
            
            $this->ddb->putItem(array( //storing random data at the moment
                'TableName' => tableName,
                'Item' => array(
                    'id'                => array('S' => "tweet"),
                    'time'              => array('N' => time()),
                    'text'              => array('S' => $this->nullifyValue($tweetData['text'])),
                    'sentiment'         => array('S' => $this->nullifyValue($tweetData['sentiment'])),
                    'created_at'        => array('S' => $this->nullifyValue($tweetData['createdAt'])),
                    'screen_name'       => array('S' => $this->nullifyValue($tweetData['screenName'])),
                    'name'              => array('S' => $this->nullifyValue($tweetData['name'])), 
                    'profile_image_url' => array('S' => $this->nullifyValue($tweetData['profileImageUrl'])),
                    'search_term'       => array('S' => $this->nullifyValue($searchTerm))
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
        
        private function nullifyValue($value){
            if ($value == ''){
                return "null";
            } else {
                return $value;
            }
        }
}
?>