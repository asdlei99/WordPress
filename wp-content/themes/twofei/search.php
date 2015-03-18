<?php get_header(); ?>

	<div id="content">

	<?php if (have_posts()) : ?>

		<div class="search-result" ><?php printf(__('Search Results for: %s', 'picochic'), get_search_query()); ?></div>

		<?php while (have_posts()) : the_post(); ?>
		
			<div class="post">
				<div class="title">
					<h2><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				</div>
					<div class="meta"><a href="<?php the_permalink() ?>"><?php the_time('d. F Y') ?></a> &middot; <?php comments_popup_link(__('Write a comment', 'picochic'), __('1 comment', 'picochic'), __('% comments', 'picochic')); ?>
					<?php
						picochic_show_categories();
						picochic_show_tags();
					?>
					</div>
					<div class="entry">
						<?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) { ?>

						<div class="thumbnail">
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
								<?php the_post_thumbnail(); ?>
							</a>
						</div>
						<div class="indexexzerpt">
							<?php the_content(__('More &raquo;', 'picochic')); ?>
						</div>
						<?php }
						else {
							the_content(__('More &raquo;', 'picochic'));
						} ?>
						<div class="pagelinks">
							<?php wp_link_pages(); ?>
						</div>
					</div>
			</div>

		<?php endwhile; ?>

		<?php 
		if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
		else { 
		?>
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>
		<?php } ?>

	<?php else : ?>
		
		<div class="search-result" >
			<p><?php _e( 'Nothing found', 'picochic' ); ?></p>
		</div>
		<div class="entry">		
			<p><?php _e('It seems we can\'t find what you\'re looking for', 'picochic' )?>.</p>
		</div>

	<?php endif; ?>

	</div>

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
