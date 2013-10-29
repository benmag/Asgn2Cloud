<?php
/**
 * Storage class 
 * Store processed tweets, retrieve processed tweets. 
 *
 * We are using S3 for storage because it's not designed to store things so
 * it'll use more resources than a normal DB, forcing scaling to occur faster
 */

class storage { 

	var $bucketName; // name of the bucket we're storing stuff in
	var $s3; // S3 object 

	/** 
	 * Get everything setup 
	 * 
	 * @return void
	 */
	function __construct() {

		require_once(dirname(__FILE__)."/../../config.php");
		require_once(dirname(__FILE__)."/S3.php");

		// Setup vars
		$this->bucketName = BUCKET_NAME;

		// Setup S3 storage object
		$this->s3 = new S3(myAWSAccessKey, myAWSSecretKey); 

		// If the bucket doesn't exist, make it.
		$this->s3->putBucket($this->bucketName, S3::ACL_PUBLIC_READ);

	}

	/** 
	 * Save tweet into a bucket 
	 *
	 * @param $tweetData - array containing all the tweet data we want to store
	 * @return string S3 storage location
	 */
	public function put_tweet($searchTerm, $tweetData) {

		$streamNumber = sizeof($this->s3->getBucket($this->bucketName, $searchTerm . 'Stream', null, null, '/')) + 1;
		$folderName = $searchTerm. 'Stream' . $streamNumber . '/';
		  
		//create the filename, unique key is used incase the previous file has not finished uploading. Avoids name conflicts
		$tweetNumber = sizeof($this->s3->getBucket($this->bucketName, $folderName)) + 1;
		$uploadName = $folderName . "tweet" . $this->addLeadingZeroes($tweetNumber) . "_" . $this->generateUniqueTweetId() . ".json";

		//push json object to s3
		$this->s3->putObject(json_encode($tweetData), $this->bucketName, $uploadName, S3::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'));

		return "https://s3.amazonaws.com/".$this->bucketName."/" . $uploadName;

	}


	/** 
	 * Grab a tweet from storage
	 * 
	 * @return json of tweet 
	 */
	public function get_tweet($tweetFile) {

	}



	/** 
	 * Add leading zeros  to ensure numerical order in the file names
	 * 
	 * @param $number
	 * @return int
	 */
	private function addLeadingZeroes($number){   
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
	    for ($i = 0; $i < 20; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }

	    return $randomString;
	}


} 