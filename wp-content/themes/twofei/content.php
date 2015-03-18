<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	


	<?php
		if (has_post_format('link')) { ?>
			<div class="title">
				<h1><a href="<?php echo picochic_first_link(get_the_content()) ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?> &raquo;</a></h1>
			</div>
		<?php }

		else { ?>
			<div class="title">
				<?php // 女孩不哭 2015-03-10 14:59:10 显示单篇文章时标题去超链接 ?>
				<?php if(!is_singular()) { ?>
					<h1><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<?php } else { ?>
					<h1><?php the_title(); ?></h1>
				<?php } ?>
			</div>
		<?php }
	?>

		<div class="meta">
			<?php //comments_popup_link(__('Write a comment', ''), __('1 comment', 'picochic'), __('% comments', 'picochic')); ?>
			<?php picochic_show_post_metas(); ?>
			<?php edit_post_link( __( '(Edit)', 'picochic' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

		<?php if (!is_singular()) { ?>
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
					// 查询分类/归档时不显示内容
					if(!is_category() && !is_archive()) {
						the_content(__('More &raquo;', 'picochic'));
					}
				} ?>
				<?php if(wp_link_pages('echo=0') != "") { 
					echo '<div class="pagelinks">';
					wp_link_pages();
					echo '</div>';
				} ?>
			</div>
		<?php } else { ?>
			<div class="entry">
				<?php the_content(__('More &raquo;', 'picochic')); ?>
				<?php if(wp_link_pages('echo=0') != "") { 
					echo '<div class="pagelinks">';
					wp_link_pages();
					echo '</div>';
				} ?>
			</div>
		<?php } ?>
	
</article>
