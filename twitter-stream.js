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
                    
                    var parsedTweet = {
                        'text'        : data.text,
                        'created_at'  : data.created_at,
                        'screen_name' : data.user.screen_name,
                        'name'        : data.user.name, 
                        'profile_image_url' : data.user.profile_image_url,
                        'searchTerm'  : watchList
                    };

                    // Tweet recieved, send it to our sentiment instance
                    request({
                      uri: "http://CloudComputingLB-196057872.us-west-2.elb.amazonaws.com/sentiment.php",
                      method: "POST",
                      form: parsedTweet,
                        
                    }, function(error, response, body) {
                        
                        console.log(body);

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
    The sentiment.php file broadcasts the processed tweet 
    back to the node server. This then broadcasts the 
    message to all connected clients listening to our room
    //////////////////////////////////////////////////////*/
    socket.on('broadcast_twitter', function(data) {
        socket.broadcast.emit('twitter', data);
    });

});
