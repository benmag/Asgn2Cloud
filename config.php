<?php

// Get the domain so we include from an absolute url
define('SITE_ADDRESS', "http://".$_SERVER['SERVER_NAME']);

// only one instance needs to run the node part. The other instances just handle the sentiment calls
define('NODE_SERVER', "http://54.200.160.95:8080"); 

// What bucket do you want to store the tweets in?
define('BUCKET_NAME', 'benM');

// Amazon keys
define('myAWSAccessKey', 'AKIAJHPGFCQYS4DZU35Q');
define('myAWSSecretKey', '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');

?>