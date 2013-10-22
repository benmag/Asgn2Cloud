<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Stream</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="css/bootstrap.css" rel="stylesheet" media="screen">
<script src="<?php echo NODE_ADDRESS; ?>socket.io/socket.io.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<style>

 article, aside, figure, footer, header, hgroup, 
menu, nav, section { display: block; }


body {
    padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
}


.tweets p {
    font: 16px/1.4 Arial, sans-serif;
    margin: 0 0 1em;
}

.comment {
    overflow: hidden;
    padding: 0 0 1em;
    border-bottom: 1px solid #ddd;
    margin: 0 0 1em;
    *zoom: 1;
}

.comment-img {
    float: left;
    margin-right: 33px;
    border-radius: 5px;
    overflow: hidden;
}

.comment-img img {
    display: block;
    width:50px;
    height:50px;
    background: url('img/loading.gif') no-repeat;
    background-position: center center;
}

.comment-body {
    overflow: hidden;
}

.comment .text {
    padding: 10px;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    background: #fff;
}

.comment .text p:last-child {
    margin: 0;
}

.comment .attribution {
    margin: 0.5em 0 0;
    font-size: 14px;
    color: #666;
}

/* Decoration */

.comments,
.comment {
    position: relative;
}

.comments:before,
.comment:before,
.comment .text:before {
    content: "";
    position: absolute;
    top: 0;
    left: 65px;
}

.comments:before {
    width: 3px;
    top: -20px;
    bottom: -20px;
    background: rgba(0,0,0,0.1);
}

.comment:before {
    width: 9px;
    height: 9px;
    border: 3px solid #fff;
    border-radius: 100px;
    margin: 16px 0 0 -6px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.2), inset 0 1px 1px rgba(0,0,0,0.1);
    background: #ccc;
}

.comment:hover:before {
    background: orange;
}

.comment .text:before {
    top: 18px;
    left: 78px;
    width: 9px;
    height: 9px;
    border-width: 0 0 1px 1px;
    border-style: solid;
    border-color: #e5e5e5;
    /*background: #fff;*/
    -webkit-transform: rotate(45deg);
    -moz-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    -o-transform: rotate(45deg);
}

.positive .text { 
background-color: #8AE68A;
}

.negative .text {
    background-color: #FF7A7A;
}

.neutral .text {
    background-color: #ccc;
}
</style>
<link href="css/bootstrap-responsive.css" rel="stylesheet"></head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            
            <a class="brand" href="index.php">Twitter Stream</a>
        
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li><a href="launch.html">Stream Launcher</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>

    <div class="container-fluid" id="stream_container" style="position:relative;">
        <div class="row-fluid">
            <div class="span6" style="margin:0 auto; text-align:center; float:none;">
            <div class="span3 offset1"><h1 id="posCount">0</h1><span class="label label-success">positive</span></div>
            <div class="span3 offset1"><h1 id="neuCount">0</h1><span class="label">neutral</span></div>
            <div class="span3 offset1"><h1 id="negCount">0</h1><span class="label label-important">negative</span></div>
            </div>
        </div>
        <div class="row-fluid" style="padding-top: 20px;">
            <!-- news feed design thanks to: http://jsfiddle.net/necolas/vhZds/ -->
            <div id="local-tweet-container">
                <section class="tweets comments span8" style="margin:0 auto; float:none;">

                </section>
            </div>
        </div>
    </div>
    
    <div class="content">
        <footer style="padding-top:15px;">
            <hr>
            <p class="pull-right">&copy; Ben Maggacis</p>
        </footer>
    </div>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">

        var socket = io.connect('<?php echo NODE_ADDRESS; ?>');
        socket.on('twitter', function(data) {

            $('.tweets').prepend("<article class=\"comment "+data.sentiment+"\">\
              <a class=\"comment-img\" href=\"http://twitter.com/"+data.screen_name+"\">\
                <img src=\""+data.profile_image_url+"\" alt=\"\" width=\"50\" height=\"50\">\
              </a>\
              <div class=\"comment-body\">\
                <div class=\"text\">\
                  <p>"+data.text+"</p>\
                </div>\
                <p class=\"attribution\">by <a href=\"http://twitter.com/"+data.screen_name+"\">"+data.name+"</a> at "+data.created_at+"</p>\
              </div>\
            </article>\
          </section>");
        
        console.log(data.text + " " + data.sentiment);

            // Update the counts
            switch(data.sentiment) {
                case "positive":
                    $("#posCount").html(parseInt($("#posCount").html()) + 1);
                break;

                case "neutral":
                    $("#neuCount").html(parseInt($("#neuCount").html()) + 1);
                break;

                case "negative":
                    $("#negCount").html(parseInt($("#negCount").html()) + 1);
                break;
            }

        });

    </script>
</body>
</html>
