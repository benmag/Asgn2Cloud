<?php require_once('config.php'); ?>
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
    	                	<h4>Tweets <small></small></h4>
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
                            <h4>Twitter Feed</h4>
                        </div>

                        <div class="twitter_feed">

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
                        </table>
                        <hr />
                        <table style="width:100%;"> 
                            <tr>
                                <td>Tweets per <b><span class="updateInterval">1000</span></b> milliseconds</td>
                                <td><span id="tweetSpeed">0</span></td>
                            </tr>
                            <tr>
                                <td>Total Tweets</td>
                                <td><span id="total_tweets">0</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>


    </div>
</body>
</html>
