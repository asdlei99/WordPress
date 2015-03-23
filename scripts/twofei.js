// 加载今日英语
jQuery.post('/twofei-ajax.php',
	'action=today_english',
	function(data){
		jQuery('#today_english').html(data);
	});

// IE - no!
if(/MSIE/.test(navigator.userAgent)){
	jQuery('#iebar').show();
	setTimeout(function(){
		jQuery('#iebar').hide();
	}, 5000);
}

// 加载评论
function comment_item(cmt) {
	var s = '';
	s += '<li class="comment-li" id="li-comment-' + cmt.comment_ID + '">\n';
	s += '<div class="comment-avatar">\n';
	s += cmt.avatar;
	s += '</div>\n';
	s += '<div class="comment-meta">\n';
	if(cmt.is_author) {
		s += '<span style="color: red;">楼主 </span>';
	}
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

var start_id = '0';

jQuery('#load-comments span.load').click(function() {
	if(jQuery(this).attr('loading') === 'true')
		return;

	var load = this;
	jQuery(this).attr('loading', 'true');
	jQuery('#load-comments span.loading').show();
	jQuery.post(
		'/twofei-ajax.php',
		'action=get_comments&number=2' 
			+ '&start_id=' + start_id 
			+ '&post_id=' + jQuery('#load-comments #post_id').val(),
		function(data) {
			var cmts = data.cmts || [];
			for(var i=0; i<cmts.length; i++){
				jQuery('.commentlist').append(comment_item(cmts[i]));
			}
			jQuery('#load-comments span.loading').hide();

			if(cmts.length == 0) {
				jQuery('#load-comments span.none').show();
				setTimeout(function(){
						jQuery('#load-comments span.none').hide();
					},
					1500
				);
			} else {
				start_id = cmts[cmts.length-1].comment_ID;
			}
			jQuery(load).removeAttr('loading');
		},
		'json'
	)
	.always(setTimeout(function(){
			jQuery(load).removeAttr('loading');
	},1500));
});

// Ajax评论提交
jQuery('#submit').click(function() {
	var timeout = 1500;

	jQuery(this).attr('disabled', 'disabled');
	jQuery('.form-submit .submitting').show();
	jQuery.post(
		this.form.action,
		jQuery(this.form).serialize()+'&by=twofei',
		function(data){
			if(data.errno == 'success') {
				jQuery('.comment-form-comment #comment').val('');

				jQuery('.form-submit .succeeded span').html(data.errmsg);
				jQuery('.form-submit .succeeded').show();
				setTimeout(function() {
						jQuery('.form-submit .succeeded').hide();
					},
					timeout
				);
			} else {
				jQuery('.form-submit .failed span').html(data.errmsg);
				jQuery('.form-submit .failed').show();
				setTimeout(function() {
						jQuery('.form-submit .failed').hide();
					},
					timeout
				);
			}

			jQuery('.form-submit .submitting').hide();
		},
		'json'
	)
	.always(setTimeout(function(){
			jQuery('#submit').removeAttr('disabled');
		},
		timeout
	));
	return false;
});

// 正在提交评论...
jQuery('#commentform .form-submit').append(
	'<span class="submitting" style="display: none; color: red; margin-left: 1em;">'
	+	'<i class="fa fa-spin fa-spinner"></i>'
	+	'<span> 正在提交...</span>'
	+ '</span>'
	+ '<span class="succeeded" style="display: none; color: green; margin-left: 1em;">'
	+ '<i class="fa fa-mr fa-info-circle"></i><span></span></span>'
	+ '<span class="failed" style="display: none; color: red; margin-left: 1em;">'
	+ '<i class="fa fa-mr fa-info-circle"></i><span></span></span>'
);

