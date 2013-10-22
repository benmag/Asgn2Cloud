<?php
Header("content-type: application/x-javascript");
require_once('../config.php');
?>
var socket = io.connect('<?php echo NODE_ADDRESS; ?>');


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