<?php get_header(); ?>

	<div id="content">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

			<?php get_template_part( 'content', get_post_format()); ?>

			<?php comments_template('', true); ?>

		<?php endwhile; ?>
		<?php the_posts_pagination([
			'mid_size'		=> 3,
			'prev_text'		=> '上一页',
			'next_text'		=> '下一页',
		]); ?>
	<?php else : ?>

		<article class="page" >
			<h2><?php _e('Sorry, nothing found.', 'picochic'); ?></h2>
			<div class="entry">		
				<p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'picochic' );?>.</p>
			</div>
		</article>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
<?php

