<?php

add_action( 'after_setup_theme', 'picochic_setup' );

function picochic_get_post_views($id, $inc) {
	$view = 0;
	$metas = get_post_meta(1, 'twofei_views', true);
	preg_match(sprintf('/%d:\\d+,/', $id), $metas, $meta);

	if(count($meta)){
		sscanf($meta[0], '%d:%d', $id, $view);
	}
	else{
		$metas .= "$id:0,";
	}

	if($inc == true && is_single()){
		$metas = preg_replace(sprintf('/%d:%d,/', $id, $view), sprintf('%d:%d,', $id, intval($view)+1), $metas);
		update_post_meta(1, 'twofei_views', $metas);
	}

	return $view;
}

if ( ! function_exists( 'picochic_setup' ) ):


/**
 * Sets up theme defaults and registers support for various WordPress features.
*/

function picochic_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// This theme uses post thumbnails
	add_theme_support('post-thumbnails');

	// Add default posts and comments RSS feed links to head
	add_theme_support('automatic-feed-links');

	// Add support for a variety of post formats
	add_theme_support('post-formats', array('aside', 'link', 'quote'));

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'picochic', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __('Primary Navigation', 'picochic'),
	) );
	
	// This theme allows users to set a custom background
	add_theme_support('custom-background');

	// Thumbnails
	set_post_thumbnail_size('150', '150', true);

	// Register Sidebar 1 and Sidebar 2
	function picochic_sidebars(){
		register_sidebar(array(
			'name' => __('Sidebar 1', 'picochic'),
			'description' => __('Will be displayed on the top sidebar or left.', 'picochic'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		register_sidebar(array(
			'name' => __('Sidebar 2', 'picochic'),
			'description' => __('Will be displayed on the bottom sidebar or right.', 'picochic'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
	}
	add_action('widgets_init', 'picochic_sidebars');

	define('HEADER_TEXTCOLOR', '');
	define('HEADER_IMAGE', '%s/images/headers/stripes.png'); // %s is the template dir uri
	define('HEADER_IMAGE_WIDTH', 1050); // use width and height appropriate for your theme
	define('HEADER_IMAGE_HEIGHT', picochic_get_settings('header_height'));
	define('NO_HEADER_TEXT', true );

	add_theme_support('custom-header').
	#add_custom_image_header('', 'picochic_admin_header_style');

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'stripes' => array(
		'url' => '%s/images/headers/stripes.png',
		'thumbnail_url' => '%s/images/headers/stripes_thumbnail.png',
		/* translators: header image description */
		'description' => __( 'Stripes', 'picochic' )
		)
	) );

	// JavaScript, jQuery etc.
	add_action('wp_enqueue_scripts', 'picochic_scripts');

	// SocialLinks Widget
	add_action('widgets_init', create_function('', 'return register_widget("picochic_SocialLinks_Widget");'));

}
endif;

if ( ! function_exists( 'picochic_admin_header_style' ) ) :


/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 * Referenced via add_custom_image_header() in picochic_setup().
 */
 
function picochic_admin_header_style() {
?><style type="text/css">
        #headimg {
            width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
            height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
            background: no-repeat;
        }
   </style>
<?php
}
endif;


/** 
 * Content Width
 */

if (!isset($content_width)) {
	$content_width = 630;
}


/** 
 * Count Only Real Comments (no pings)
 */

add_filter('get_comments_number', 'picochic_comment_count', 0);
function picochic_comment_count($count) {
        if ( ! is_admin() ) {
                global $id;
                $comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
                return count($comments_by_type['comment']);
        } else {
                return $count;
        }
}

if ( ! function_exists( 'picochic_comment' ) ) :

function picochic_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'picochic' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'picochic' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div class="comment_gravatar">
			<?php
				$avatar_size = 60;
				echo get_avatar( $comment, $avatar_size );
			?>
		</div>
		<div id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-author vcard">
				<?php
					/* translators: 1: comment author, 2: date and time */
					printf( __( '%1$s %2$s', 'picochic' ),
						sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
						sprintf( '<div class="comment-meta"><a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							get_comment_time( 'c' ),
							/* translators: 1: date, 2: time */
							sprintf( __( '%1$s at %2$s', 'picochic' ), get_comment_date(), get_comment_time() )
						)
					);
				?>

				<?php edit_comment_link( __( '(Edit)', 'picochic' ), '<span class="edit-link">', '</span>' ); ?>
				</div>
			</div><!-- .comment-author .vcard -->

			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em class="comment-awaiting-moderation">你的评论正在等待审核。</em>
				<br />
			<?php endif; ?>
			<div class="comment-content"><?php comment_text(); ?></div>

			<p class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'picochic' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</p><!-- .reply -->
		</div><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for picochic_comment()


/** 
 * Custom WordPress Gallery
 */

add_filter('gallery_style', create_function('$a', 'return preg_replace("%<style type=\'text/css\'>(.*?)</style>%s", "", $a);'));


/** 
 * Scripts
 */

function picochic_scripts() {
	if (is_singular() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	wp_enqueue_script('jquery');
    wp_enqueue_script('picochic_scripts', get_template_directory_uri().'/js/scripts.js', array('jquery')); 
	wp_enqueue_script('picochic_respond', get_template_directory_uri().'/js/respond.min.js');
}


/** 
 * Show post metas
 */

function picochic_show_post_metas() {
	// date
	echo '<span><i class="fa fa-calendar"></i>日期: '.get_the_date().'</span>';
	// categories
	$cats = get_the_category_list(', ');
	echo '<span><i class="fa fa-folder"></i>分类: '.$cats.'</span>';
	// tags
	$tags = get_the_tag_list('', ', ');
	echo '<span><i class="fa fa-tag"></i>标签: '.$tags.'</span>';
	// views
	$view = picochic_get_post_views(get_the_ID(), is_single() && !is_preview());
	echo '<span><i class="fa fa-eye"></i>浏览: '.$view.'</span>';
}

/** 
 * Get First Link For Link Post Format (Thank you elmastudio.de / Yoko)
 */

function picochic_first_link( $content = false, $echo = false )
{
    // allows using this function also for excerpts
    if ( $content === false )
        $content = get_the_content(); // You could also use $GLOBALS['post']->post_content;

    $content = preg_match_all( '/href\s*=\s*[\"\']([^\"\']+)/', $content, $links );
    $content = $links[1][0];
    #$content = make_clickable( $content );

    // if you set the 2nd arg to true, you'll echo the output, else just return for later usage
    if ( $echo === true )
        echo $content;

    return $content;
}


/** 
 * Social Links Widget (Thank you elmastudio.de / Yoko)
 */

class picochic_SocialLinks_Widget extends WP_Widget {
	function picochic_SocialLinks_Widget() {
		$widget_ops = array(
		'classname' => 'widget_social_links', 
		'description' => __('Link to your social profiles like twitter, facebook or flickr.', 'picochic'));
		$this->WP_Widget('social_links', 'Social Links', $widget_ops);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

		$rss_title = empty($instance['rss_title']) ? ' ' : apply_filters('widget_rss_title', $instance['rss_title']);	
		$rss_url = empty($instance['rss_url']) ? ' ' : apply_filters('widget_rss_url', $instance['rss_url']);
		$twitter_title = empty($instance['twitter_title']) ? ' ' : apply_filters('widget_twitter_title', $instance['twitter_title']);	
		$twitter_url = empty($instance['twitter_url']) ? ' ' : apply_filters('widget_twitter_url', $instance['twitter_url']);		
		$fb_title = empty($instance['fb_title']) ? ' ' : apply_filters('widget_fb_title', $instance['fb_title']);
		$fb_url = empty($instance['fb_url']) ? ' ' : apply_filters('widget_fb_url', $instance['fb_url']);
		$googleplus_title = empty($instance['googleplus_title']) ? ' ' : apply_filters('widget_googleplus_title', $instance['googleplus_title']);
		$googleplus_url = empty($instance['googleplus_url']) ? ' ' : apply_filters('widget_googleplus_url', $instance['googleplus_url']);		
		$di_title = empty($instance['di_title']) ? ' ' : apply_filters('widget_di_title', $instance['di_title']);
		$di_url = empty($instance['di_url']) ? ' ' : apply_filters('widget_di_url', $instance['di_url']);
		$identi_title = empty($instance['identi_title']) ? ' ' : apply_filters('widget_identi_title', $instance['identi_title']);
		$identi_url = empty($instance['identi_url']) ? ' ' : apply_filters('widget_identi_url', $instance['identi_url']);		
		$flickr_title = empty($instance['flickr_title']) ? ' ' : apply_filters('widget_flickr_title', $instance['flickr_title']);
		$flickr_url = empty($instance['flickr_url']) ? ' ' : apply_filters('widget_flickr_url', $instance['flickr_url']);
		$vimeo_title = empty($instance['vimeo_title']) ? ' ' : apply_filters('widget_vimeo_title', $instance['vimeo_title']);
		$vimeo_url = empty($instance['vimeo_url']) ? ' ' : apply_filters('widget_vimeo_url', $instance['vimeo_url']);
		$linkedin_title = empty($instance['linkedin_title']) ? ' ' : apply_filters('widget_linkedin_title', $instance['linkedin_title']);
		$linkedin_url = empty($instance['linkedin_url']) ? ' ' : apply_filters('widget_linkedin_url', $instance['linkedin_url']);
		$delicious_title = empty($instance['delicious_title']) ? ' ' : apply_filters('widget_delicious_title', $instance['delicious_title']);
		$delicious_url = empty($instance['delicious_url']) ? ' ' : apply_filters('widget_delicious_url', $instance['delicious_url']);
		
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
		echo '<ul>';
		if($rss_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($rss_url) .'" class="rss" target="_blank">'. esc_html($rss_title) .'</a></li>'; }
		if($twitter_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($twitter_url) .'" class="twitter" target="_blank">'. esc_html($twitter_title) .'</a></li>'; }
		if($fb_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($fb_url) .'" class="facebook" target="_blank">'. esc_html($fb_title) .'</a></li>'; }
		if($googleplus_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($googleplus_url) .'" class="googleplus" target="_blank">'. esc_html($googleplus_title) .'</a></li>'; }
		if($di_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($di_url) .'" class="diaspora" target="_blank">'. esc_html($di_title) .'</a></li>'; }
		if($identi_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($identi_url) .'" class="identi" target="_blank">'. esc_html($identi_title) .'</a></li>'; }	
		if($flickr_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($flickr_url) .'" class="flickr" target="_blank">'. esc_html($flickr_title) .'</a></li>'; }
		if($vimeo_title == ' ') { echo ''; } else {  echo  '  <li class="widget_sociallinks"><a href="'. esc_url($vimeo_url) .'" class="vimeo" target="_blank">'. esc_html($vimeo_title) .'</a></li>'; }
		if($linkedin_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($linkedin_url) .'" class="linkedin" target="_blank">'. esc_html($linkedin_title) .'</a></li>'; }
		if($delicious_title == ' ') { echo ''; } else {  echo  '<li class="widget_sociallinks"><a href="'. esc_url($delicious_url) .'" class="delicious" target="_blank">'. esc_html($delicious_title) .'</a></li>'; }
		echo '</ul>';
		echo $after_widget;
		
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		$instance['rss_title'] = strip_tags($new_instance['rss_title']);
		$instance['rss_url'] = strip_tags($new_instance['rss_url']);
		$instance['twitter_title'] = strip_tags($new_instance['twitter_title']);
		$instance['twitter_url'] = strip_tags($new_instance['twitter_url']);
		$instance['fb_title'] = strip_tags($new_instance['fb_title']);
		$instance['fb_url'] = strip_tags($new_instance['fb_url']);
		$instance['googleplus_title'] = strip_tags($new_instance['googleplus_title']);
		$instance['googleplus_url'] = strip_tags($new_instance['googleplus_url']);
		$instance['di_title'] = strip_tags($new_instance['di_title']);
		$instance['di_url'] = strip_tags($new_instance['di_url']);
		$instance['identi_title'] = strip_tags($new_instance['identi_title']);
		$instance['identi_url'] = strip_tags($new_instance['identi_url']);
		$instance['flickr_title'] = strip_tags($new_instance['flickr_title']);
		$instance['flickr_url'] = strip_tags($new_instance['flickr_url']);		
		$instance['vimeo_title'] = strip_tags($new_instance['vimeo_title']);
		$instance['vimeo_url'] = strip_tags($new_instance['vimeo_url']);
		$instance['linkedin_title'] = strip_tags($new_instance['linkedin_title']);
		$instance['linkedin_url'] = strip_tags($new_instance['linkedin_url']);
		$instance['delicious_title'] = strip_tags($new_instance['delicious_title']);
		$instance['delicious_url'] = strip_tags($new_instance['delicious_url']);
		return $instance;
	}
	function form($instance) {
		$instance = wp_parse_args(
		(array) $instance, array( 
			'title' => '',
			'rss_title' => '',
			'rss_url' => '',
			'twitter_title' => '',
			'twitter_url' => '',
			'fb_title' => '',
			'fb_url' => '',
			'googleplus_title' => '',
			'googleplus_url' => '',
			'di_title' => '',
			'di_url' => '',
			'identi_title' => '',
			'identi_url' => '',			
			'flickr_title' => '',
			'flickr_url' => '',
			'vimeo_title' => '',
			'vimeo_url' => '',
			'linkedin_title' => '',
			'linkedin_url' => '',
			'delicious_title' => '',
			'delicious_url' => ''
		) );
		$title = strip_tags($instance['title']);
		$rss_title = strip_tags($instance['rss_title']);
		$rss_url = strip_tags($instance['rss_url']);
		$twitter_title = strip_tags($instance['twitter_title']);
		$twitter_url = strip_tags($instance['twitter_url']);
		$fb_title = strip_tags($instance['fb_title']);
		$fb_url = strip_tags($instance['fb_url']);
		$googleplus_title = strip_tags($instance['googleplus_title']);
		$googleplus_url = strip_tags($instance['googleplus_url']);
		$di_title = strip_tags($instance['di_title']);
		$di_url = strip_tags($instance['di_url']);
		$identi_title = strip_tags($instance['identi_title']);
		$identi_url = strip_tags($instance['identi_url']);		
		$flickr_title = strip_tags($instance['flickr_title']);
		$flickr_url = strip_tags($instance['flickr_url']);
		$vimeo_title = strip_tags($instance['vimeo_title']);
		$vimeo_url = strip_tags($instance['vimeo_url']);
		$linkedin_title = strip_tags($instance['linkedin_title']);
		$linkedin_url = strip_tags($instance['linkedin_url']);
		$delicious_title = strip_tags($instance['delicious_title']);
		$delicious_url = strip_tags($instance['delicious_url']);
?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_html($title); ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('rss_title'); ?>"><?php _e( 'RSS Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('rss_title'); ?>" name="<?php echo $this->get_field_name('rss_title'); ?>" type="text" value="<?php echo esc_html($rss_title); ?>" /></label></p>	
			
			<p><label for="<?php echo $this->get_field_id('rss_url'); ?>"><?php _e( 'RSS  URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('rss_url'); ?>" name="<?php echo $this->get_field_name('rss_url'); ?>" type="text" value="<?php echo esc_url($rss_url); ?>" /></label></p>	
			
			<p><label for="<?php echo $this->get_field_id('twitter_title'); ?>"><?php _e( 'Twitter Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('twitter_title'); ?>" name="<?php echo $this->get_field_name('twitter_title'); ?>" type="text" value="<?php echo esc_html($twitter_title); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('twitter_url'); ?>"><?php _e( 'Twitter  URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('twitter_url'); ?>" name="<?php echo $this->get_field_name('twitter_url'); ?>" type="text" value="<?php echo esc_url($twitter_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('fb_title'); ?>"><?php _e( 'Facebook Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('fb_title'); ?>" name="<?php echo $this->get_field_name('fb_title'); ?>" type="text" value="<?php echo esc_html($fb_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('fb_url'); ?>"><?php _e( 'Facebook URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('fb_url'); ?>" name="<?php echo $this->get_field_name('fb_url'); ?>" type="text" value="<?php echo esc_url($fb_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('googleplus_title'); ?>"><?php _e( 'Google+ Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('googleplus_title'); ?>" name="<?php echo $this->get_field_name('googleplus_title'); ?>" type="text" value="<?php echo esc_html($googleplus_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('googleplus_url'); ?>"><?php _e( 'Google+ URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('googleplus_url'); ?>" name="<?php echo $this->get_field_name('googleplus_url'); ?>" type="text" value="<?php echo esc_url($googleplus_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('di_title'); ?>"><?php _e( 'Diaspora Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('di_title'); ?>" name="<?php echo $this->get_field_name('di_title'); ?>" type="text" value="<?php echo esc_html($di_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('di_url'); ?>"><?php _e( 'Diaspora URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('di_url'); ?>" name="<?php echo $this->get_field_name('di_url'); ?>" type="text" value="<?php echo esc_url($di_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('identi_title'); ?>"><?php _e( 'Identi.ca Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('identi_title'); ?>" name="<?php echo $this->get_field_name('identi_title'); ?>" type="text" value="<?php echo esc_html($identi_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('identi_url'); ?>"><?php _e( 'Identi.ca URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('identi_url'); ?>" name="<?php echo $this->get_field_name('identi_url'); ?>" type="text" value="<?php echo esc_url($identi_url); ?>" /></label></p>			
			<p><label for="<?php echo $this->get_field_id('flickr_title'); ?>"><?php _e( 'Flickr Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('flickr_title'); ?>" name="<?php echo $this->get_field_name('flickr_title'); ?>" type="text" value="<?php echo esc_html($flickr_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('flickr_url'); ?>"><?php _e( 'Flickr URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('flickr_url'); ?>" name="<?php echo $this->get_field_name('flickr_url'); ?>" type="text" value="<?php echo esc_url($flickr_url); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('vimeo_title'); ?>"><?php _e( 'Vimeo Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('vimeo_title'); ?>" name="<?php echo $this->get_field_name('vimeo_title'); ?>" type="text" value="<?php echo esc_html($vimeo_title); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('vimeo_url'); ?>"><?php _e( 'Vimeo URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('vimeo_url'); ?>" name="<?php echo $this->get_field_name('vimeo_url'); ?>" type="text" value="<?php echo esc_url($vimeo_url); ?>" /></label></p>		
			<p><label for="<?php echo $this->get_field_id('linkedin_title'); ?>"><?php _e( 'LinkedIn Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('linkedin_title'); ?>" name="<?php echo $this->get_field_name('linkedin_title'); ?>" type="text" value="<?php echo esc_html($linkedin_title); ?>" /></label></p>		
			<p><label for="<?php echo $this->get_field_id('linkedin_url'); ?>"><?php _e( 'LinkedIn URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('linkedin_url'); ?>" name="<?php echo $this->get_field_name('linkedin_url'); ?>" type="text" value="<?php echo esc_url($linkedin_url); ?>" /></label></p>	
			<p><label for="<?php echo $this->get_field_id('delicious_title'); ?>"><?php _e( 'Delicious Text:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('delicious_title'); ?>" name="<?php echo $this->get_field_name('delicious_title'); ?>" type="text" value="<?php echo esc_html($delicious_title); ?>" /></label></p>
			<p><label for="<?php echo $this->get_field_id('delicious_url'); ?>"><?php _e( 'Delicious URL:', 'picochic' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('delicious_url'); ?>" name="<?php echo $this->get_field_name('delicious_url'); ?>" type="text" value="<?php echo esc_url($delicious_url); ?>" /></label></p>

<?php
	}
}


/** 
 * Hex Color To RGB
 */
 
function picochic_hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
	// from http://www.php.net/manual/de/function.hexdec.php#99478
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}

/** 
 * Remove Link To More-Tag
 */

function picochic_remove_more_jump_link($link) {
	if (picochic_get_settings('link_to_read_more') != 1) {
		$offset = strpos($link, '#more-');
		if ($offset) {
			$end = strpos($link, '"',$offset);
		}
		if ($end) {
			$link = substr_replace($link, '', $offset, $end-$offset);
		}
	}
	return $link;
}

add_filter('the_content_more_link', 'picochic_remove_more_jump_link');


/** 
 * Get Picochic Settings 
 */

function picochic_get_settings($s) {
	global $picochic_options;
	$picochic_settings = get_option('picochic_options', $picochic_options);
	if ($s == 'favicon' && $picochic_settings['custom_favicon']) {	
		echo '<link rel="shortcut icon" href="'.esc_url_raw($picochic_settings['custom_favicon']).'" title="Favicon" />';
	}
	elseif ($s == 'logo' && $picochic_settings['custom_logo']) {
		echo '<a href="';
		echo home_url();
		echo '"><img id="logo" src="'.esc_url_raw($picochic_settings['custom_logo']).'" alt="';
		bloginfo('name');
		echo '" /></a>';
	}
	elseif ($s == 'header_height' && $picochic_settings['custom_header_height']) {
		return wp_filter_nohtml_kses($picochic_settings['custom_header_height']);
	}
	elseif ($s == 'show_about_the_author' && $picochic_settings['show_about_the_author']) {
		return 1;
	}
	elseif ($s == 'link_to_read_more' && $picochic_settings['link_to_read_more']) {
		return 1;
	}
}

remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_content', 'wptexturize' );

remove_filter( 'the_excerpt', 'wpautop' );
remove_filter( 'the_excerpt', 'wptexturize' );

remove_filter( 'comment_text', 'wpautop' );
remove_filter( 'comment_text', 'wptexturize' );

remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

/** 
 * Picochic Theme Settings
 */

require_once (get_template_directory().'/includes/theme-options.php');

