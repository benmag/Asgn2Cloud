<?php

/**
 * Small class that gets the sentiment of a string
 */
class sentiment {

	/**
	 * Grab sentiment from sentiment API
	 *
	 * @param $text - string to get sentiment for
	 * @return string [positive/negative/neutral]
	 */ 
	function analyse($text) {

		// Sentiment API (no rate limit, no key, completely free, WHOOPIEEE!) 
		$url = 'http://sentiment.vivekn.com/web/text/';

		// API Params (just sends the string to process)
		$params = 'txt='.urlencode($text);
		
		// cURL the API 
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

		// Get the API results. API response is in JSON. 
		$response = json_decode(curl_exec( $ch ));

		// Return only the sentiment rating [pos/neg/neutral], we don't want the other parts
		return strtolower($response->result);

	}

} 