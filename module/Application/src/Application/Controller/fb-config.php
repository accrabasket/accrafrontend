<?php
include_once('php-graph-sdk-5.x/src/Facebook/autoload.php');
$fb = new Facebook\Facebook(array(
	'app_id' => '743729896574839', // Replace with your app id
	'app_secret' => '8fa75a427d427a0ea2b4ce8e334f5f3d',  // Replace with your app secret
	'default_graph_version' => 'v3.2',
));

$helper = $fb->getRedirectLoginHelper();
?>
