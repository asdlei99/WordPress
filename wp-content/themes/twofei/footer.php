	</section>
	<footer id="footer" class="mono">
		<div style="text-align: center;">&copy; <?php echo date('Y'); ?> 女孩不哭 All rights reserved.</div>
		<div style="text-align: center;">Designed by twofei. Proudly powered by <a target="_blank" style="color: inherit;" href="http://www.wordpress.org">WordPress</a>.</div>
	</footer>
</div>

<div id="iebar">抱歉，本站无法在“IE”浏览器下很好地显示！</div>

<div id="comment-form-div">

	<div class="closebtn" title="关闭">
		<i class="fa fa-times"></i>
	</div>

	<div class="comment-title">
		<span>评论</span>
	</div>

	<form id="comment-form" action="/wp-comments-post.php">
		<div class="fields">
			<div class="field">
				<label>昵称</label>
				<input type="text" name="author" />
				<span class="needed">必填</span>
			</div>
			<div class="field">
				<label>邮箱</label>
				<input type="text" name="email" />
				<span class="needed">必填</span>
			</div>
			<div class="field">
				<label>网址</label>
				<input type="text" name="url" />
			</div>
			<div style="display: none;">
				<input id="comment-form-post-id" type="hidden" name="post_id" value="" />
				<input id="comment-form-parent"  type="hidden" name="parent" value="" />
			</div>
		</div>

		<div class="comment-content">
			<label style="position: absolute;">评论</label>
			<label style="visibility: hidden;">评论</label>
			<textarea id="comment-content" name="content"></textarea>
		</div>

		<div class="comment-submit">
			<span id="comment-submit">发表评论</span>
			<span class="submitting">
				<i class="fa fa-spin fa-spinner"></i>
				<span> 正在提交...</span>
			</span>'
			<span class="succeeded">
				<i class="fa fa-mr fa-info-circle"></i>
				<span></span>
			</span>
			<span class="failed">
				<i class="fa fa-mr fa-info-circle"></i>
				<span></span>
			</span>
		</div>
	</form>
</div>

<?php wp_footer(); ?>

<script type="text/javascript" src="<?php echo get_bloginfo('stylesheet_directory'); ?>/scripts/twofei.js"></script>

</body>
</html>

