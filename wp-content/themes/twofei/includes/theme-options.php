<?php
/* picochic Theme Options */
 
function picochic_admin_enqueue_scripts( $hook_suffix ) {
	if ( $hook_suffix != 'appearance_page_theme_options' )
		return;

	wp_enqueue_style( 'picochic-theme-options', get_template_directory_uri().'/includes/theme-options.css', false );
	wp_enqueue_script( 'picochic-theme-options', get_template_directory_uri().'/includes/theme-options.js', array( 'farbtastic' ) );
	wp_enqueue_style( 'farbtastic' );

	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('picochic-image-upload', get_template_directory_uri().'/includes/jquery-scripts.js', array(
		'jquery',
		'media-upload',
		'thickbox'
	));
	wp_enqueue_script('picochic-image-upload');
	wp_enqueue_style('thickbox');

	wp_localize_script('picochic-image-upload', 'picochic_localizing_upload_js', array(
		'use_this_image' => __('Use This Image', 'picochic')
    ));
}

add_action( 'admin_enqueue_scripts', 'picochic_admin_enqueue_scripts' );
 
// Default options values
$picochic_options = array(
	'custom_color' => '#364D96',
	'custom_favicon' => '',
	'custom_logo' => '',
	'logo_centered' => '0',
	'main_background' => '0',
	'show_header_search' => '1',
	'custom_header_height' => '42',
	'show_about_the_author' => '0',
	'link_to_read_more' => '1'
);

if ( is_admin() ) : // Load only if we are viewing an admin page

function picochic_register_settings() {
	// Register the settings
	register_setting( 'picochic_theme_options', 'picochic_options', 'picochic_validate_options' );
}

add_action( 'admin_init', 'picochic_register_settings' );


function picochic_theme_options() {
	// Add theme options page to the addmin menu
	add_theme_page( __( 'Options', 'picochic'), __( 'Options', 'picochic'), 'edit_theme_options', 'theme_options', 'picochic_theme_options_page');
}

add_action( 'admin_menu', 'picochic_theme_options' );



// Function to generate options page
function picochic_theme_options_page() {
	global $picochic_options, $picochic_categories, $picochic_layouts;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

	<?php screen_icon(); echo '<h2>'.wp_get_theme().' '.__( 'Options', 'picochic' ).'</h2>';
	// This shows the page's name and an icon if one has been provided ?>

	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved', 'picochic' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>


	<form method="post" action="options.php">

	<?php $settings = get_option( 'picochic_options', $picochic_options ); ?>
	
	<?php settings_fields( 'picochic_theme_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>

	<table class="form-table">

	<tr><td><h3><?php _e('Color and background', 'picochic'); ?></h3></td></tr>
	
	<tr valign="top"><th scope="row"><label for="custom_color"><?php _e('Custom color scheme', 'picochic'); ?></label></th>
	<td>
	<input id="custom_color" name="picochic_options[custom_color]" type="text" value="<?php  echo esc_attr($settings['custom_color']); ?>" />
	<a href="#" class="pickcolor hide-if-no-js" id="custom_color-example"></a>
	<input type="button" class="pickcolor button hide-if-no-js" value="<?php esc_attr_e(_e( 'Select a Color', 'picochic' )); ?>">
	<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
	<br />
	<small class="description"><?php _e('Default color', 'picochic'); ?> <a href="javascript:;" id="set_default_color">#364D96</a></small>
	</td>
	</tr>
	
	<tr valign="top"><th scope="row"><label for="main_background"><?php _e('White background for main section', 'picochic'); ?></label></th>
	<td>		
	<input id="main_background" name="picochic_options[main_background]" type="checkbox" value="1" <?php checked('1', $settings['main_background']); ?> />
	<br />
	<small class="description"><?php _e('For dark backgrounds', 'picochic'); ?></small>
	</td>
	</tr>
		
	<tr><td><h3><?php _e('Header', 'picochic'); ?></h3></td></tr>

	<tr valign="top"><th scope="row"><label for="custom_logo"><?php _e('Custom logo', 'picochic'); ?></label></th>
	<td>
	<input id="upload-logo" name="picochic_options[custom_logo]" type="text" value="<?php echo esc_attr($settings['custom_logo']); ?>" />
	<input type="button" class="button hide-if-no-js" id="upload-logo-button" value="<?php _e('Select or upload a logo', 'picochic'); ?>" /> 
	<small class="description"><a href="javascript:;" id="remove-logo-button"><?php _e('Remove logo and show text instead', 'picochic'); ?></a></small>
	</td>
	</tr>

	<tr valign="top"><th scope="row"><label for="logo_centered"><?php _e('Show logo centered', 'picochic'); ?></label></th>
	<td>		
	<input id="logo-centered" name="picochic_options[logo_centered]" type="checkbox" value="1" <?php checked('1', $settings['logo_centered']); ?> />
	</td>
	</tr>

	<tr valign="top"><th scope="row"><label for="show_header_search"><?php _e('Display header search', 'picochic'); ?></label></th>
	<td>
	<input id="show_header_search" name="picochic_options[show_header_search]" type="checkbox" value="1" <?php checked('1', $settings['show_header_search']); ?> />
	</td>
	</tr>

	<tr><td><h3><?php _e('Other', 'picochic'); ?></h3></td></tr>

	<tr valign="top"><th scope="row"><label for="custom_favicon"><?php _e('Custom favicon', 'picochic'); ?></label></th>
	<td>
	<input id="upload-favicon" name="picochic_options[custom_favicon]" type="text" value="<?php echo esc_attr($settings['custom_favicon']); ?>" />
	<input type="button" class="button hide-if-no-js" id="upload-favicon-button" value="<?php _e('Select or upload a favicon', 'picochic'); ?>" /> 
	<small class="description"><a href="javascript:;" id="remove-favicon-button"><?php _e('Remove favicon', 'picochic'); ?></a></small>
	<br />
	<small><?php _e('You don\'t have a favicon to upload? Generate your own on', 'picochic'); ?> <a href="<?php echo esc_url('http://www.faviconr.com/'); ?>" target="_blank">faviconr.com</a></small>
	</td>
	</tr>
		
	<tr valign="top"><th scope="row"><label for="custom_header_height"><?php _e('Custom header height', 'picochic'); ?></label></th>
	<td>
	<input id="custom_header_height" name="picochic_options[custom_header_height]" type="text" value="<?php echo esc_attr($settings['custom_header_height']); ?>" />
	<br />
	<small class="description"><?php _e('Default header height:', 'picochic'); ?> <a href="javascript:;" id="custom_header_height_default">42</a> px. <?php _e('After you changed this value you can', 'picochic'); ?> <a href="<?php echo home_url(); ?>/wp-admin/themes.php?page=custom-header" target="_blank"><?php _e('upload a new header image', 'picochic'); ?></a></small>
	</td>
	</tr>

	<tr valign="top"><th scope="row"><label for="show_about_the_author"><?php _e('Show an "About the Author"-Box', 'picochic'); ?></label></th>
	<td>
	<input id="show_about_the_author" name="picochic_options[show_about_the_author]" type="checkbox" value="1" <?php checked('1', $settings['show_about_the_author']); ?> />
	</td>
	</tr>

	<tr valign="top"><th scope="row"><label for="link_to_read_more"><?php _e('Read-more-link links to read-more-tag', 'picochic'); ?></label></th>
	<td>
	<input id="link_to_read_more" name="picochic_options[link_to_read_more]" type="checkbox" value="1" <?php checked('1', $settings['link_to_read_more']); ?> />
	</td>
	</tr>
	
	</table>

	<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Options', 'picochic'); ?>" /></p>

	</form>

	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="XY5N7E3URRC2C">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="">
	<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
	</form>

	</div>

	<?php
}

function picochic_validate_options( $input ) {
	global $picochic_options, $picochic_categories, $picochic_layouts;

	$settings = get_option( 'picochic_options', $picochic_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS
	$input['custom_color'] = wp_filter_nohtml_kses($input['custom_color']);
	$input['custom_header_height'] = wp_filter_nohtml_kses($input['custom_header_height']);
	$input['custom_favicon'] = esc_url_raw($input['custom_favicon']);
	$input['custom_logo'] = esc_url_raw($input['custom_logo']);

	// Our checkbox value is either 0 or 1
	if (!isset($input['logo_centered']))
		$input['logo_centered'] = null;
	$input['logo_centered'] = ($input['logo_centered'] == 1 ? 1 : 0);

	if (!isset($input['show_header_search']))
		$input['show_header_search'] = null;
	$input['show_header_search'] = ($input['show_header_search'] == 1 ? 1 : 0);

	if (!isset($input['show_about_the_author']))
		$input['show_about_the_author'] = null;
	$input['show_about_the_author'] = ($input['show_about_the_author'] == 1 ? 1 : 0);

	if (!isset($input['link_to_read_more']))
		$input['link_to_read_more'] = null;
	$input['link_to_read_more'] = ($input['link_to_read_more'] == 1 ? 1 : 0);

	if (!isset($input['main_background']))
		$input['main_background'] = null;
	$input['main_background'] = ($input['main_background'] == 1 ? 1 : 0);

	return $input;
}

endif;  // EndIf is_admin()


// Custom CSS for Link Colors
function picochic_insert_custom_color(){
?>

<?php
	#$picochic-theme-uri = get_template_directory_uri();
	global $picochic_options;
	$picochic_settings = get_option( 'picochic_options', $picochic_options );
?>

<?php if( $picochic_settings['custom_color'] != '' ) {
	$rgb_color = picochic_hex2RGB($picochic_settings['custom_color'], 1);
	$rgba_color = 'rgba('.$rgb_color.',0.8)';
	$rgba_color2 = 'rgba('.$rgb_color.',0.2)';
?>
	<style type="text/css">
		a, #comments h3, h3#reply-title {
			color: <?php echo $picochic_settings['custom_color']; ?>;
		}
		
        ::selection, ::-moz-selection {
			background: <?php echo $rgba_color ?>;
			color: #fff;
		}
		
		.format-link h2 a, #header-image-div {
			background: <?php echo $picochic_settings['custom_color']; ?>;
		}
		
		@media screen and (max-width: 850px) {
			nav ul li a:active, nav ul li a:focus, nav ul li a:hover {
				background: <?php echo $picochic_settings['custom_color']; ?>;
			}
		}

	</style>
<?php } ?>

<?php if($picochic_settings['custom_logo'] != '') { ?>
	<style type="text/css">
		header h1, header .description {display: none;}
	</style>
<?php } ?>

<?php if($picochic_settings['logo_centered'] == '1' && $picochic_settings['custom_logo']) { ?>
	<style type="text/css">
		#head {text-align: center;}
		header #logo {padding-left: 0;}
	</style>
<?php } ?>

<?php if($picochic_settings['show_header_search'] != '1') { ?>
	<style type="text/css">
		#headersearch {
			display: none;
		}
	</style>
<?php } ?>

<?php if($picochic_settings['main_background'] == '1') { ?>
	<style type="text/css">
		#wrapper {
			background: #fff;
		}

		nav {
			border-left: none;
			border-right: none;
		}
	</style>
<?php } ?>

<?php if($picochic_settings['custom_header_height']) { ?>
	<style type="text/css">
		@media screen and (min-width: 851px) {
			#headerimage {
				height: <?php echo $picochic_settings['custom_header_height']; ?>px;
			}
		}
	</style>
<?php } ?>

<?php
}

add_action('wp_head', 'picochic_insert_custom_color');
