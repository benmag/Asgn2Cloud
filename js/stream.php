<?php
Header("content-type: application/x-javascript");
require_once('../config.php');
?>
var socket = io.connect('<?php echo NODE_ADDRESS; ?>');
var tweetCount = 0;
var tweetHistorySize = 100;

socket.on('twitter', function(data) {
  
    // Add the tweet to the tweet viewer and give it an id (this id is temporary)
    $('.tweets').prepend("<article id=\"tw"+tweetCount+"\" class=\"comment "+data.sentiment+"\">\
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


  // Clear the tweet history after x new tweets. 

  // If the tweetCount is divisible by x, loop through and destory those tweets
  //tweetCount++; 


});