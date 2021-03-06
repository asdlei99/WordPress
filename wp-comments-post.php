<?php
/**
 * Handles Comment Post to WordPress and prevents duplicate comment posting.
 *
 * @package WordPress
 */

if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}

if( 'twofei' != $_POST['by']){
	header('HTTP/1.1 403 Forbidden');
	header('Content-Type: text/html');
	echo '<span style="font-size: 100px; color: red;">Fuck off!</span>';
	exit;
}

$fs = ['content', 'post_id', 'parent'];
$fd = ['comment', 'comment_post_ID', 'comment_parent'];

for($i=0; $i<count($fs); $i++){
	$_POST[$fd[$i]] = $_POST[$fs[$i]];
	unset($_POST[$fs[$i]]);
}

/** Sets up the WordPress Environment. */
require( dirname(__FILE__) . '/wp-load.php' );

nocache_headers();

$comment_post_ID = isset($_POST['comment_post_ID']) ? (int) $_POST['comment_post_ID'] : 0;

$post = get_post($comment_post_ID);

if ( empty( $post->comment_status ) ) {
	/**
	 * Fires when a comment is attempted on a post that does not exist.
	 *
	 * @since 1.5.0
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'comment_id_not_found', $comment_post_ID );
	exit;
}

// get_post_status() will get the parent status for attachments.
$status = get_post_status($post);

$status_obj = get_post_status_object($status);

if ( ! comments_open( $comment_post_ID ) ) {
	/**
	 * Fires when a comment is attempted on a post that has comments closed.
	 *
	 * @since 1.5.0
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'comment_closed', $comment_post_ID );
	wp_die( __( 'Sorry, comments are closed for this item.' ), 403 );
} elseif ( 'trash' == $status ) {
	/**
	 * Fires when a comment is attempted on a trashed post.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'comment_on_trash', $comment_post_ID );
	exit;
} elseif ( ! $status_obj->public && ! $status_obj->private ) {
	/**
	 * Fires when a comment is attempted on a post in draft mode.
	 *
	 * @since 1.5.1
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'comment_on_draft', $comment_post_ID );
	exit;
} elseif ( post_password_required( $comment_post_ID ) ) {
	/**
	 * Fires when a comment is attempted on a password-protected post.
	 *
	 * @since 2.9.0
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'comment_on_password_protected', $comment_post_ID );
	exit;
} else {
	/**
	 * Fires before a comment is posted.
	 *
	 * @since 2.8.0
	 *
	 * @param int $comment_post_ID Post ID.
	 */
	do_action( 'pre_comment_on_post', $comment_post_ID );
}

$comment_author       = ( isset($_POST['author']) )  ? trim(strip_tags($_POST['author'])) : null;
$comment_author_email = ( isset($_POST['email']) )   ? trim($_POST['email']) : null;
$comment_author_url   = ( isset($_POST['url']) )     ? trim($_POST['url']) : null;
$comment_content      = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;

// If the user is logged in
$user = wp_get_current_user();
if ( $user->exists() ) {
	if ( empty( $user->display_name ) )
		$user->display_name=$user->user_login;
	$comment_author       = wp_slash( $user->display_name );
	$comment_author_email = wp_slash( $user->user_email );
	$comment_author_url   = wp_slash( $user->user_url );
	if ( current_user_can( 'unfiltered_html' ) ) {
		if ( ! isset( $_POST['_wp_unfiltered_html_comment'] )
			|| ! wp_verify_nonce( $_POST['_wp_unfiltered_html_comment'], 'unfiltered-html-comment_' . $comment_post_ID )
		) {
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
} else {
	if ( get_option( 'comment_registration' ) || 'private' == $status ) {
		wp_die( __( 'Sorry, you must be logged in to post a comment.' ), 403 );
	}
}

$comment_type = '';

// 女孩不哭 2015-03-16 23:06:16 评论源代码修改
if ( get_option('require_name_email') && !$user->exists() ) {
	if ( 6 > strlen( $comment_author_email ) || '' == $comment_author ) {
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode([
			'errno' => 'error',
			'errmsg' => '错误，请正确填写你的昵称和电子邮件地址！',
		]);
		die(-1);
		//wp_die( __( '<strong>ERROR</strong>: please fill the required fields (name, email).' ), 200 );
	} else if ( ! is_email( $comment_author_email ) ) {
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json');
		echo json_encode([
			'errno' => 'error',
			'errmsg' => '你确定你所填写的电子邮件地址格式正确？',
		]);
		die(-1);
		//wp_die( __( '<strong>ERROR</strong>: please enter a valid email address.' ), 200 );
	}
}

if ( '' == trim($comment_content )) {
	header('HTTP/1.1 200 OK');
	header('Content-Type: application/json');
	echo json_encode([
		'errno' => 'error',
		'errmsg' => '提交空评论？',
	]);
	die(-1);
	//wp_die( __( '<strong>ERROR</strong>: please type a comment.' ), 200 );
}

$comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;

$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID');

$comment_id = wp_new_comment( $commentdata );
if ( ! $comment_id ) {
	header('HTTP/1.1 200 OK');
	header('Content-Type: application/json');
	echo json_encode([
		'errno' => 'error',
		'errmsg' => '评论提交失败！',
	]);
	die(-1);
	//wp_die( __( "<strong>ERROR</strong>: The comment could not be saved. Please try again later." ) );
}

$comment = get_comment( $comment_id );

/**
 * Perform other actions when comment cookies are set.
 *
 * @since 3.4.0
 *
 * @param object $comment Comment object.
 * @param WP_User $user   User object. The user may not exist.
 */
do_action( 'set_comment_cookies', $comment, $user );

// 因为可能出现多个地方同时提交数据，所以并不直接返回评论
// 只是返回一个状态值，新添加的所有评论通过ajax加载所有。
$msg = [];
$msg['errno'] = 'success';
$msg['errmsg'] = '评论成功';

echo json_encode($msg);
exit;

$location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id;

/**
 * Filter the location URI to send the commenter after posting.
 *
 * @since 2.0.5
 *
 * @param string $location The 'redirect_to' URI sent via $_POST.
 * @param object $comment  Comment object.
 */
$location = apply_filters( 'comment_post_redirect', $location, $comment );

wp_safe_redirect( $location );
exit;
