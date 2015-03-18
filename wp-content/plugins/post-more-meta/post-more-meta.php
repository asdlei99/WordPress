<?php
/**
 * Plugin Name: Post More Meta
 * Plugin URI: http://blog.twofei.com/wordpress/plugins/post-more-meta.html
 * Description: Post More Meta
 * Version: 0.0.0
 * Author: twofei
 * Author URI: http://blog.twofei.com
 * License: (None)
 */

defined('ABSPATH') or die("No script kiddies please!");

function pmm_activate() {
	global $wpdb;

	$table_name = $wpdb->prefix . "post_more_meta";

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		post_id bigint(20),
		header longtext,
		footer longtext,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

register_activation_hook(__FILE__, 'pmm_activate');


function pmm_add_meta_box() {
	add_meta_box('pmm_id', 'Post More Meta', 'pmm_add_meta_box_cb', 'post');
	add_meta_box('pmm_id', 'Post More Meta', 'pmm_add_meta_box_cb', 'page');
}

function pmm_add_meta_box_cb($post) {
	// 不知道是干嘛的
	wp_nonce_field('pmm_meta_box', 'pmm_meta_box_nonce');

	global $wpdb;
	$table = $wpdb->prefix . "post_more_meta";

	$sql = "SELECT header,footer FROM $table where post_id=$post->ID;";
	$results = $wpdb->get_results($sql);

	$header = count($results) ? $results[0]->header : '';
	$footer = count($results) ? $results[0]->footer : '';
	
	echo '<h3>Header</h3>';
	echo '<textarea name="pmm_header" style="width: 80%;">'.$header.'</textarea>';
	echo '<h3>Footer</h3>';
	echo '<textarea name="pmm_footer" style="width: 80%;">'.$footer.'</textarea>';
}

add_action('add_meta_boxes', 'pmm_add_meta_box');

function pmm_save_meta_box_data($post_id) {
	if(!isset($_POST['pmm_meta_box_nonce']))
		return;

	if(!wp_verify_nonce($_POST['pmm_meta_box_nonce'], 'pmm_meta_box'))
		return;

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if(!isset($_POST['pmm_header']) || !isset($_POST['pmm_footer']))
		return;

	$header = stripslashes($_POST['pmm_header']);
	$footer = stripslashes($_POST['pmm_footer']);

	if(empty($header) && empty($footer))
		return;

	global $wpdb;
	$table = $wpdb->prefix . "post_more_meta";

	$sql = "SELECT post_id FROM $table where post_id=$post_id;";
	$results = $wpdb->get_results($sql);

	if(count($results)){
		$sql = $wpdb->prepare("UPDATE $table set header=%s,footer=%s WHERE post_id=$post_id;",
			$header,
			$footer
		);
		$wpdb->get_results($sql);
	}
	else{
		$sql = $wpdb->prepare("INSERT INTO $table (post_id, header, footer) VALUES (%d,%s,%s);",
			$post_id,
			$header,
			$footer
		);
		$wpdb->get_results($sql);
	}
}

add_action('save_post', 'pmm_save_meta_box_data');

function pmm_head() {
	global $wp_query;
	global $wpdb;

	if(!is_single() && !is_page())
		return;

	if(!isset($wp_query->queried_object_id))
		return;

	$id = $wp_query->queried_object_id;

	$table = $wpdb->prefix . "post_more_meta";

	$sql = "SELECT header,footer FROM $table where post_id=$id;";
	$results = $wpdb->get_results($sql);

	$header = count($results) ? $results[0]->header : '';
	$footer = count($results) ? $results[0]->footer : '';

	$metas = array("header" => $header, "footer" => $footer);

	$GLOBALS['pmm_metas'] = $metas;

	if(!empty($header)){
		echo $header;
	}
}

add_action('wp_head', 'pmm_head');

function pmm_footer() {
	if(!is_single() && !is_page())
		return;

	if(isset($GLOBALS['pmm_metas'])){
		$metas = $GLOBALS['pmm_metas'];
		if(!empty($metas['footer'])){
			echo $metas['footer'];
		}
	}
	unset($GLOBALS['pmm_metas']);
}

add_action('wp_footer', 'pmm_footer');

