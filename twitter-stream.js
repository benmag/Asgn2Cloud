// NODEJS TWITTER STREAM 
// Source: https://github.com/tariknz/nodejs-twitter-stream
// Utilizes: https://npmjs.org/package/twitter
var app = require('http').createServer(handler)
  , io = require('socket.io').listen(app)
  , fs = require('fs')
  , twitter = require('ntwitter')
  , request = require('request')
  , util = require('util')
  , AWS = require('aws-sdk')
  , $ = require('jquery');
  
var config = require('./node_config.json');

  
AWS.config.loadFromPath('./awsConfig.json');
var dynamodb = new AWS.DynamoDB();
var tweetsPerMinute = 0;
var previousTweetsPerMinute = new Array();
var timer = setInterval(function(){calculateTweetsPerMinute()}, 60000);
var scaler = setInterval(function(){doScaling()}, 300000);

var twit = new twitter({
  consumer_key: config.consumerKey,
  consumer_secret: config.consumerSecret,
  access_token_key: config.accessTokenKey,
  access_token_secret: config.accessTokenSecret
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
                    
                    var item = {
                        "id": {"S": makeid()},
                        'text'        : {"S": nullifyValue(data.text)},
                        'created_at'  : {"S": nullifyValue(data.created_at)},
                        'screen_name' : {"S": nullifyValue(data.user.screen_name)},
                        'name'        : {"S": nullifyValue(data.user.name)}, 
                        'profile_image_url' : {"S": nullifyValue(data.user.profile_image_url)},
                        'searchTerm'  : {"S": nullifyValue(watchList)}
                    }
                    
                    tweetsPerMinute++;
                    
                    dynamodb.putItem({TableName: config.tableName, Item: item}, function(err, data){
                        if (err) {
                            console.log(err);
                        }
                    });

                    // Tweet recieved, send it to our sentiment instance
                    request({
                      uri: config.sentimentLocation,
                      method: "POST",
                      form: parsedTweet,
                        
                    }, function(error, response, body) {
                        
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

function makeid(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 20; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function calculateTweetsPerMinute(){
    previousTweetsPerMinute.unshift(tweetsPerMinute);
    if (previousTweetsPerMinute.length > 5){
        previousTweetsPerMinute.pop();
    }
    tweetsPerMinute = 0;
}

function doScaling(){
    dynamodb.describeTable({TableName: config.tableName}, function(err, data){
        if (err) {
            console.log(err); // an error occurred
        } else {
            var writeCapacity = data.Table.ProvisionedThroughput.WriteCapacityUnits;
            var totalTweetsOver5Minutes = 0;
            var averageTweetsPerSecond = 0;
            
            for (i = 0; i < previousTweetsPerMinute.length; i++){
                totalTweetsOver5Minutes += previousTweetsPerMinute[i];
            }
            
            averageTweetsPerSecond = totalTweetsOver5Minutes / (previousTweetsPerMinute.length * 60);
            
            if (writeCapacity < averageTweetsPerSecond ){
                dynamodb.updateTable({TableName: config.tableName, ProvisionedThroughput: {ReadCapacityUnits: averageTweetsPerSecond, WriteCapacityUnits: averageTweetsPerSecond}}, function(err, data){
                    console.log("TRIED TO SCALE UP");
                    if (err) {
                        //console.log(err); // an error occurred
                    } else {
                        //console.log("I SCALED UP");
                    }
                });
            } else if (writeCapacity > averageTweetsPerSecond ){
                dynamodb.updateTable({TableName: config.tableName, ProvisionedThroughput: {ReadCapacityUnits: averageTweetsPerSecond,WriteCapacityUnits: averageTweetsPerSecond}}, function(err, data){
                    if (err) {
                        //console.log(err); // an error occurred
                    } else {
                        //console.log("I SCALED DOWN");
                    }
                });
            }
        }
    });
}

function nullifyValue(value){
    if (value === ''){
        return "null";
    } else {
        return value;
    }
}