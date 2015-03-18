<?php get_header(); ?>

	<div id="content-full">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php get_template_part('content'); ?>

			<?php if (picochic_get_settings('show_about_the_author')) { ?>
				<section id="author-box">
					<div id="author-gravatar"><?php echo get_avatar(get_the_author_meta('user_email'), '40');?></div>
					<div id="author-box-content">
						<h3 id="author-box-title"><?php _e('About the author', 'picochic'); ?>: <?php the_author_link(); ?></h3>
						<p id="author-box-text"><?php the_author_meta('description');?></p>
					</div>
				</section>
			<?php } ?>

			<?php comments_template('', true); ?>

	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no article found', 'picochic'); ?>.</p>

<?php endif; ?>

	</div>
	
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
