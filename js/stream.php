<?php
Header("content-type: application/x-javascript");
require_once('../config.php');
?>

// Connect to our socket port so we can send/recieve in real time
var socket = io.connect('<?php echo NODE_SERVER; ?>');


// Variable setup 
var tweetCount = 0, 
    tweetsBefore = 0, // allows us to track tweets per x 
    tweetsAfter = 0,
    posCount = 1, // Number of positive tweets
    negCount = 1, // Number of negative tweets
    neuCount = 1, // Number of neutral tweets
    tweetHistorySize = 100; // How many tweets to show in the twitter feed at one given time


// Listen for a 'twitter' message to be broadcasted   
socket.on('twitter', function(data) {

    // update our total tweet count and show it
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


    // Add the tweet to the twitter feed
    $('.twitter_feed').prepend("<div class=\"chat-message\">\
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


    // Delete old tweets when we exceed our tweet history size
    if(tweetCount > tweetHistorySize) {
        $( '.chat-message:gt(' + ( tweetHistorySize-1 ) + ')' ).remove();
    }
});


$(function () {
    
    var data = [], totalPoints = 300;
    var tweetSpeed = 0;
    
    function getGraphData() {
        if (data.length > 0)
            data = data.slice(1);

        // look through and add the tweetspeed to the data
        while (data.length < totalPoints) {
            data.push($("#tweetSpeed").html());
        }

        // combine the tweetSpeed with the time value
        var res = [];
        for (var i = 0; i < data.length; ++i)
            res.push([i, data[i]])

        return res;
    }

    // setup speed control widget
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

    var plot = $.plot($("#tweetGraph"), [ getGraphData() ], options);


    //////////////////////////////////////////

    // Setup pie chart
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


        plot.setData([ getGraphData() ]);
        // since the axes don't change, we don't need to call plot.setupGrid()
        plot.draw();

        tweetsBefore = tweetCount; // keep a record of the number of tweets before 
        /////////////////////////////////////////////


        //////////// SENTIMENT SHARE //////////

        var pie = $.plot($("#donut"), [], pieOptions);

        // Re-set the pie chart data
        var pieData = [
            {label: "Positive",  color: '#8AE68A', data: posCount},
            {label: "Neutral",  color: '#d2d2d2', data: neuCount},
            {label: "Negative", color: '#FF7A7A', data: negCount},
        ];

        // Redraw pie chart
        pie.setData(pieData);   
        pie.setupGrid(); //only necessary if your new data will change the axes or grid
        pie.draw();


        ///////////////////////////////////////
        setTimeout(update, updateInterval);
    }



});


