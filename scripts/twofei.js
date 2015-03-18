// 加载今日英语
jQuery('#today_english').load('/twofei-ajax.php', 'action=today_english');

// 加载评论
function comment_item(cmt) {
	var s = '';
	s += '<li class="comment-li" id="li-comment-' + cmt.comment_ID + '">\n';
	s += '<div class="comment-avatar">\n';
	s += cmt.avatar;
	s += '</div>\n';
	s += '<div class="comment-meta">\n';
	s += '<span>' + cmt.comment_author + '</span>\n';
	s += ' @ ';
	s += '<span>' + cmt.comment_date + '</span>\n';
	s += '</div>\n';
	s += '<div class="comment-content">\n';
	s += cmt.comment_content;
	s += '</div>\n';
	s += '</li>';

	return s;
}

var cmts_loaded = 0;

jQuery('#load-comments button').click(function() {
	jQuery('#load-comments span.loading').show();
	jQuery.get(
		'/twofei-ajax.php',
		'action=get_comments&number=5' 
			+ '&offset=' + cmts_loaded 
			+ '&post_id=' + jQuery('#load-comments #post_id').val(),
		function(data) {
			var cmts = data.cmts || [];
			for(var i=0; i<cmts.length; i++){
				jQuery('.commentlist').append(comment_item(cmts[i]));
			}
			jQuery('#load-comments span.loading').hide();
			cmts_loaded += cmts.length;
			if(cmts.length == 0) {
				jQuery('#load-comments span.none').show();
				setTimeout(function(){
						jQuery('#load-comments span.none').hide();
					},
					1500
				);
			}
		},
		'json'
	);
});

// Ajax评论提交
jQuery('#submit').click(function() {
	jQuery('.form-submit .submitting').show();
	jQuery.post(
		this.form.action,
		jQuery(this.form).serialize()+'&by=twofei',
		function(data){
			console.log(data);
			if(data.errno == 'success') {
				jQuery('.commentlist').append(comment_item(data.cmt));
				jQuery('.comment-form-comment #comment').val('');
			}
			jQuery('.form-submit .submitting').hide();
			jQuery('.form-submit .succeeded').show();
			setTimeout(function() {
					jQuery('.form-submit .succeeded').hide();
				},
				1500
			);
		},
		'json'
	);
	return false;
});

// 正在提交评论...
jQuery('#commentform .form-submit').append(
	'<span class="submitting" style="display: none; color: red; margin-left: 1em;">'
	+	'<i class="fa fa-spin fa-spinner"></i>'
	+	'<span> 正在提交...</span>'
	+ '</span>'
	+ '<span class="succeeded" style="display: none; color: green; margin-left: 1em;">'
	+ '<i class="fa fa-info-circle"></i><span> 提交成功！</span></span>'
);

