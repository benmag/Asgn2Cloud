<?php
$rand = rand(0,2);
switch($rand) {
	case 0:
		echo "negative";
	break;

	case 1:
		echo "positive";
	break;

	case 2: 
		echo "neutral";
	break;
}
?>