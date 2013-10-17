<?php

$request = "http://access.alchemyapi.com/calls/text/TextGetTextSentiment?apikey=1bab760eeacc00230522c325b0435fae1b7c75f9&text=" . urlencode($_POST["message"]) . "&outputMode=json";
$response = json_decode(file_get_contents($request));

echo $response->docSentiment->type;