<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Stream</title>
<link href="css/bootstrap.css" rel="stylesheet" media="screen">
<link href="css/launchPage.css" rel="stylesheet">
</head>
<body>
<?php include_once('./templates/launch_form.php'); ?> 
<!-- Only connect to the twitter channel on the index page -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Socket.io -->
<script src="<?php echo NODE_SERVER; ?>socket.io/socket.io.js"></script>
<script src="js/stream_manager.php"></script>
</body>
</html>
