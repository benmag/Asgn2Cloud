<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);

use Aws\DynamoDb\DynamoDbClient;

require_once('config.php');
require_once(dirname(__FILE__)."/vendor/autoload.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Stream</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="css/bootstrap.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-responsive.css" rel="stylesheet">
<link href="css/default.css" rel="stylesheet">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Socket.io -->
<script src="<?php echo NODE_SERVER; ?>/socket.io/socket.io.js"></script>
<script src="js/stream.php"></script>

<script type="text/javascript" src="js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.js"></script>
<script type="text/javascript" src="js/plugins/charts/jquery.flot.pie.js"></script>
</head>
<body>
	<div class="content container">
		<div class="row">
            <div class="span8">

                <div class="widget">
    	            <div class="inner">
    	                <div class="title">
    	                	<h4>Tweets <small>pre-processing</small></h4>
    	                </div>

    	                <div class="body no-margin">
                            <div id="tweetGraph"></div> 
                            <br />
                            <p align="center">Time between updates: <input id="updateInterval" type="text" value="" style="text-align: right; width:5em"> milliseconds<br /><small>1 second = 1000 milliseconds :)</small></p>
                        </div>
    	            </div>
                </div>

                <div class="widget">
                    <div class="inner">
                        <div class="title">
                            <h4>Twitter Feed <small class="display:none;">Tweets will keep appearing until the backlog is cleared</small></h4>
                        </div>

                        <div class="twitter_feed">
                            <?php 
                                $ddb = DynamoDbClient::factory(array(
                                    'key'    => 'AKIAJHPGFCQYS4DZU35Q',
                                    'secret' => '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',
                                    'region' => 'us-west-2'
                                ));

                                $iterator = $ddb->getIterator('Query', array(
                                    'TableName'     => 'joeTweets',
                                    'KeyConditions' => array(
                                        'id' => array(
                                            'AttributeValueList' => array(
                                                array('S' => 'tweet')
                                            ),
                                            'ComparisonOperator' => 'EQ'
                                        ),
                                        'time' => array(
                                            'AttributeValueList' => array(
                                                array('N' => strtotime("-120 minutes"))
                                            ),
                                            'ComparisonOperator' => 'GT'
                                        )
                                    )
                                ));

                                foreach ($iterator as $item) {
                                    echo "<div class='chat-message'>";
                                    echo "<div class='sender pull-left'>";
                                    echo "<div class='icon'>";
                                    echo "<img src='" . $item['profile_image_url']['S'] . "' alt='' width='50' height='50' class=img-circle>";
                                    echo "</div>";
                                    echo "<div class='time'></div>";
                                    echo "</div>";
                                    echo "<div class='chat-message-body " . $item['sentiment']['S'] . "'> <span class='arrow'></span>";
                                    echo "<div class='sender'><a href='http://twitter.com/" . $item['screen_name']['S'] . "'>" . $item['name']['S'] . "</a>";
                                    echo "</div>";
                                    echo "<div class='text'>" .$item['text']['S'] . "</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
	        </div>

            <div class="span4">
                <div class="widget">
                    <div class="inner">
                        <iframe src="<?php echo SITE_ADDRESS; ?>/launch.php" seamless width="100%" height="110px"></iframe>
                    </div>
                </div>
                <div class="widget">
                    <div class="inner">

                        <div class="title">
                            <h4>Tweet Breakdown</h4>
                        </div>
                        
                        <div class="pie" id="donut"></div>

                        <table style="width:100%;">
                            <tr>
                                <td>Positive</td>
                                <td><span id="posCount">0</span></td>
                            </tr>

                            <tr>
                                <td>Neutral</td>
                                <td><span id="neuCount">0</span></td>
                            </tr>

                            <tr>
                                <td>Negative</td>
                                <td><span id="negCount">0</span></td>
                            </tr>
                            <tr>
                                <td>Analysed Tweets</td>
                                <td><span id="analysedCount">0</span></td>
                            </tr>
                        </table>
                        <hr />
                        <table style="width:100%;"> 
                            <tr>
                                <td>Tweets per <b><span class="updateInterval">1000</span></b> milliseconds</td>
                                <td><span id="tweetSpeed">0</span></td>
                            </tr>
                            <tr>
                                <td>Waiting for Analysis</td>
                                <td><span id="backlog_count">0</span></td>
                            </tr>
                            <tr>
                                <td>Total Tweets</td>
                                <td><span id="total_tweets">0</span></td>
                            </tr>
                            
                            
                        </table>
                    </div>
                </div>
                <div class="widget">
                    <div class="inner">
                        <br />
                        <p>* Tweet speed is calculated from the twitter stream it so <b>will</b> be different to the speed of the analysed tweets</p>
                        <p>* Not all "tweets" get their sentiment processed - replies, retweets and non-english tweets are all ignored</p>
                        <p align="center"><small>Created by Ben and Joe</small></p>
                    </div>
                </div>
            </div>

        </div>


    </div>
</body>
</html>
