// NODEJS TWITTER STREAM 
// Source: https://github.com/tariknz/nodejs-twitter-stream
// Utilizes: https://npmjs.org/package/twitter
var app = require('http').createServer(handler)
  , io = require('socket.io').listen(app)
  , fs = require('fs'), twitter = require('ntwitter')
  , util = require('util');


var twit = new twitter({
  consumer_key: 'XQQyEKInyk3jSQCazbyFEQ',
  consumer_secret: 'J4Kd7qSKUYf1U6zZi2oP6ap5uavv0tt6csKZv6vnXyY',
  access_token_key: '1003676713-HxsKqgUIfNsfvIQAvVRKxU77qGVE9BpgTwJQfMy',
  access_token_secret:'DHzE1vishw0WBksk6dXmTSqXfeE4qRmPv9aD0P1u6xU'
});


app.listen(8080);

function handler (req, res) {
  fs.readFile(__dirname + '/index.html',
  function (err, data) {
    if (err) {
      res.writeHead(500);
      return res.end('Error loading index.html');
    }

    res.writeHead(200);
    res.end(data);
  });
}

io.sockets.on('connection', function(socket) {

  // Handle client request join channel and track stream
  socket.on('track_stream', function(streamName) {
    socket.join(streamName);

    // Make connection to twitter stream
    twit.stream('statuses/filter', {'track':streamName}, function(stream) {
        
      var streamCallBack = stream;
        
      //When we recieve a tweet
      stream.on('data',function(data){
        socket.emit(streamName,data); // Send it through to the streams channel 
      });

    });

  });

});

