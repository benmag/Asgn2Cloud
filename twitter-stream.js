// NODEJS TWITTER STREAM 
// Source: https://github.com/tariknz/nodejs-twitter-stream
// Utilizes: https://npmjs.org/package/twitter
var app = require('http').createServer(handler)
  , io = require('socket.io').listen(app)
  , fs = require('fs')
  , twitter = require('ntwitter')
  , request = require('request')
  , util = require('util')
  , $ = require('jquery');
  


var twit = new twitter({
  consumer_key: 'XQQyEKInyk3jSQCazbyFEQ',
  consumer_secret: 'J4Kd7qSKUYf1U6zZi2oP6ap5uavv0tt6csKZv6vnXyY',
  access_token_key: '1003676713-HxsKqgUIfNsfvIQAvVRKxU77qGVE9BpgTwJQfMy',
  access_token_secret:'DHzE1vishw0WBksk6dXmTSqXfeE4qRmPv9aD0P1u6xU'
});


app.listen(8080);

function handler (req, res) {
  var selectedFile;
selectedFile = __dirname + req.url;

fs.readFile(selectedFile,
  function (err, data) {
    if (err) {
      res.writeHead(500);
      return res.end('Error loading '+req.url);
    }

    res.writeHead(200);
    res.end(data);
  });
}

io.sockets.on('connection', function(socket) {

    /*///////////////// STREAM CONTROLLER ////////////////////
    Twitter only allow one stream per user. This meant if 
    someone opened the page somewhere else (another tab or 
    computer) it would boot the previous client off. 

    By creating a stream controller we seperate the part of 
    the code that turns on the twitter stream and the part 
    of the code interested in sending that tweet data 
    //////////////////////////////////////////////////////*/

    // Make connection to twitter stream
    var currentTwitStream;

    socket.on('open_stream', function(watchList) {

        if(currentTwitStream) currentTwitStream.destroy();

        twit.stream('statuses/filter', {'track':watchList}, function(stream) {
            
            currentTwitStream = stream;

            // Tweet recieved
            stream.on('data',function(data) {

                // Only parse tweets, not retweets and replies
                if(data.in_reply_to_status_id == null && data.retweeted == false && data.text.substr(0, 3) != "RT ") {
                    // Tweet recieved, analyse sentiment 
                    request({
                      uri: "http://ec2-54-200-86-66.us-west-2.compute.amazonaws.com/sentiment.php",
                      method: "POST",
                      form: {
                        'text'        : data.text,
                        'created_at'  : data.created_at,
                        'screen_name' : data.user.screen_name,
                        'name'        : data.user.name, 
                        'profile_image_url' : data.user.profile_image_url,
                        'searchTerm'  : watchList
                      }
                        
                    }, function(error, response, body) {

                       $.getJSON(body, function(tweet) {
                            var parsedTweet = {
                                'sentiment'   : tweet.sentiment, 
                                'text'        : tweet.text,
                                'created_at'  : tweet.createdAt,
                                'screen_name' : tweet.screenName,
                                'name'        : tweet.name, 
                                'profile_image_url' : tweet.profileImageUrl
                            };

                            socket.broadcast.emit('twitter', parsedTweet);
                        });
                    });
                } else {
                    //console.log("Rejected   ")
                }

            });
        });

    });

    socket.on('close_stream', function() {    
        
        if(currentTwitStream) {
            console.log("Ending");
            currentTwitStream.destroy();
        }

    });


    /*///////////////// BROADCAST TWEETS ///////////////////
    When the a twitter stream is opened (via the stream 
    controller) every time the tweet callback is triggered,
    it emits tweet data to the broadcasting center which then
    forwards them on to all connected clients listening to 
    our channel
    //////////////////////////////////////////////////////*/

    // Handle client request join channel and track stream
    socket.on('broadcast_twitter', function(data) {
        console.log(" \n ------ RECIEVED TWQEET -------\n")
        socket.emit('twitter',data);
    });

});
