<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Stream</title>
<?php include_once('./templates/head_tags.php'); ?>
</head>
<body>
<?php include_once('./templates/menu.php'); ?>
<?php include_once('./templates/launch_form.php'); ?> 
<?php include_once('./templates/footer.php'); ?>
<!-- Only connect to the twitter channel on the index page -->
<script src="js/stream_manager.php"></script>
</body>
</html>
