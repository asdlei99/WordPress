<?php
/**
 * Plugin Name: Twofei Utility
 * Plugin URI:
 * Description: Some utilities
 * Version: 0.0.0
 * Author: twofei
 * Author URI: http://blog.twofei.com
 * License: (None)
 */

defined('ABSPATH') or die("No script kiddies please!");

function tu_comment_filter() {
	if(is_user_logged_in())
		return;
	
	$email = $_POST['email'];
	if(preg_match('/\*/', $email)){
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode([
			'errno' => 'error',
			'errmsg' => '抱歉，你的邮件地址不被允许！',
		]);
		die(-1);
	}

	$url = $_POST['url'];
	if(preg_match('/\.co\.uk/', $url)) {
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode([
			'errno' => 'error',
			'errmsg' => '抱歉，你的网址不被允许！',
		]);
		die(-1);
	}
}

function tu_get_avatar($img) {
	return str_replace(" src='", " avatar='", $img);
}

add_action('pre_comment_on_post', 'tu_comment_filter', 0, 0);
//add_filter('get_avatar','tu_get_avatar', 100, 1);

