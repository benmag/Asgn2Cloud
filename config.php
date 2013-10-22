<?php

// Get the domain so we include from an absolute url
define('SITE_ADDRESS', "http://".$_SERVER['SERVER_NAME']);
define('NODE_ADDRESS', SITE_ADDRESS.":8080/");
?>