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
<link href="css/default2.css" rel="stylesheet">

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Socket.io -->
<script src="<?php echo NODE_SERVER; ?>socket.io/socket.io.js"></script>
<!--<script src="js/stream.php"></script>-->

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
                        <input id="streamName" type="text" value=""  placeholder="Enter keyword/hashtag" />
                        <button class="btn btn-block btn-primary" type="button" onclick="launchStream();">Launch</button>
                        <button id="closeStream" class="btn btn-block btn-danger" type="button" onclick="closeStream();">Terminate Stream</button>
                        <small>Coming soon.</small>               
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
<script type="text/javascript">
var socket = io.connect('http://54.200.160.95:8080/');
var tweetCount = 0, 
    tweetsBefore = 0,
    tweetsAfter = 0,
    posCount = 1,
    negCount = 1,
    neuCount = 1;

var tweetHistorySize = 100;

socket.on('twitter', function(data) {

    tweetCount++;
    $("#total_tweets").html(tweetCount);

    // Update the sentiment counts
    switch(data.sentiment) {
        case "positive":
            posCount++;
          $("#posCount").html(posCount);
        break;

        case "neutral":
            neuCount++;
           $("#neuCount").html(neuCount);
        break;

        case "negative":
            negCount++;
            $("#negCount").html(negCount);
        break;
    }


    // Add the tweet to the tweet viewer and give it an id (this id is temporary)
    $('.twitter_feed').prepend("<div class=\"chat-message\" id=\"tw"+tweetCount+"\">\
                            <div class=\"sender pull-left\">\
                                <div class=\"icon\">\
                                    <img src=\""+data.profile_image_url+"\" alt=\"\" width=\"50\" height=\"50\" class=\"img-circle\">\
                                </div>\
                                <div class=\"time\"></div>\
                            </div>\
                            <div class=\"chat-message-body "+data.sentiment+"\">\
                                <span class=\"arrow\"></span>\
                                <div class=\"sender\"><a href=\"http://twitter.com/"+data.screen_name+"\">"+data.name+"</a></div>\
                                <div class=\"text\">"+data.text+"</div>\
                            </div>\
                        </div>");


    // Delete old tweets
    if(tweetCount > tweetHistorySize) {
        $( '.chat-message:gt(' + ( tweetHistorySize-1 ) + ')' ).remove();
    }
});


$(function () {
    // we use an inline data source in the example, usually data would
    // be fetched from a server
    var data = [], totalPoints = 300;
    var tweetSpeed = 0;
    function getRandomData() {
        if (data.length > 0)
            data = data.slice(1);

        // do a random walk
        while (data.length < totalPoints) {
            var prev = data.length > 0 ? data[data.length - 1] : 50;
            var y = prev + Math.random() * 10 - 5;
            if (y < 0)
                y = 0;
            if (y > 100)
                y = 100;
            data.push($("#tweetSpeed").html());
        }

        // zip the generated y values with the x values
        var res = [];
        for (var i = 0; i < data.length; ++i)
            res.push([i, data[i]])

        return res;
    }

    // setup control widget
    var updateInterval = 1000;
    $("#updateInterval").val(updateInterval).change(function () {
        var v = $(this).val();
        if (v && !isNaN(+v)) {
            updateInterval = +v;
            if (updateInterval < 1)
                updateInterval = 1;
            if (updateInterval > 60000)
                updateInterval = 60000;
            $(this).val("" + updateInterval);
        }

        $(".updateInterval").html(updateInterval);

    });

    // setup plot
    var options = {
        series: { shadowSize: 0 }, // drawing is faster without shadows
        yaxis: { min: 0, max: 100 },
        xaxis: { show: false }
    };
    var plot = $.plot($("#tweetGraph"), [ getRandomData() ], options);


    //////////////////////////////////////////
    var pieOptions = {
            series: {
                pie: { 
                    show: true,
                    innerRadius: 0.5,
                    radius: 1,
                    label: {
                        show: false,
                        radius: 2/3,
                        formatter: function(label, series){
                            return '<div style="font-size:11px;text-align:center;padding:4px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                        },
                        threshold: 0.1
                    }
                }
            },
            legend: {
                show: true,
                noColumns: 1, // number of colums in legend table
                labelFormatter: null, // fn: string -> string
                labelBoxBorderColor: "#000", // border color for the little label boxes
                container: null, // container (as jQuery object) to put legend in, null means default on top of graph
                position: "ne", // position of default legend container within plot
                margin: [5, 10], // distance from grid edge to default legend container within plot
                backgroundColor: "#efefef", // null means auto-detect
                backgroundOpacity: 1 // set to 0 to avoid background
            },
    };


    update();
    ///////////////////////////

    // Update graphs/pie charts with real time data
    function update() {

        ////////// TWEETS PER TIME PERIOD ////////////
        // Record the tweets at the starting time 
        tweetsAfter = tweetCount; // how many tweets have we got at the end of the period

        // Work out number of tweets in period 
        tweetSpeed = tweetsAfter - tweetsBefore;

        // Update value 
        $("#tweetSpeed").html(tweetSpeed);


        plot.setData([ getRandomData() ]);
        // since the axes don't change, we don't need to call plot.setupGrid()
        plot.draw();

        tweetsBefore = tweetCount; // keep a record of the number of tweets before 
        /////////////////////////////////////////////


        //////////// SENTIMENT SHARE //////////

        var pie = $.plot($("#donut"), [], pieOptions);

        /*var pieData = [
            {label: "Positive",  color: '#8AE68A', data: parseInt($("#posCount").html())},
            {label: "Neutral",  color: '#d2d2d2', data: parseInt($("#neuCount").html())},
            {label: "Negative", color: '#FF7A7A', data: parseInt($("#negCount").html())},
        ];*/

        var pieData = [
            {label: "Positive",  color: '#8AE68A', data: posCount},
            {label: "Neutral",  color: '#d2d2d2', data: neuCount},
            {label: "Negative", color: '#FF7A7A', data: negCount},
        ];

        pie.setData(pieData);   
        pie.setupGrid(); //only necessary if your new data will change the axes or grid
        pie.draw();


        ///////////////////////////////////////
        setTimeout(update, updateInterval);
    }



});


function launchStream() {

    console.log("Launching stream");
    
    // Connect to stream;  
    socket.emit('open_stream', $("#streamName").val());  
    $("#closeStream").show();

}

function closeStream() {

    // Closing stream
    console.log("Closing stream");
    socket.emit('close_stream');

}
</script>
</body>
</html>
