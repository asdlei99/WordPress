<div id="comments">
	<h3 id="comment-title">评论</h3>

	<ol id="comment-list">

	</ol>
	<div id="load-comments">
		<span class="load">加载评论</span>
		<span class="loading">
			<i class="fa fa-spin fa-spinner"></i> 
			<span> 加载中...</span>
		</span>
		<span class="none">
			<i class="fa fa-info-circle"></i>
			<span> 没有了！</span>
		</span>
		<input type="hidden" id="post_id" name="post_id" value="<?php echo $post->ID; ?>" />
	</div>
</div>

