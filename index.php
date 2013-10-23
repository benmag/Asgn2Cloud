<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Stream</title>
<?php include_once('./templates/head_tags.php'); ?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<!-- Socket.io -->
<script src="<?php echo NODE_SERVER; ?>socket.io/socket.io.js"></script>
<script src="js/stream.php"></script>
</head>

<body>
<?php include_once('./templates/menu.php'); ?>
<?php include_once('./templates/tweet_view.php'); ?> 
<?php include_once('./templates/footer.php'); ?>

<!-- Only connect to the twitter channel on the index page -->

</body>
</html>
