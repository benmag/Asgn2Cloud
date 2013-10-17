<?php
$url = 'http://sentiment.vivekn.com/web/text/';
$myvars = 'txt='.urlencode($_REQUEST['message']);

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = json_decode(curl_exec( $ch ));
echo strtolower($response->result);

?>