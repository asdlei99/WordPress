var jq = jQuery;

jq.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) + 
    jQuery(window).scrollTop()) + "px");
	this.css("left", Math.max(0, ((jQuery(window).width() - jQuery(this).outerWidth()) / 2) + 
	jQuery(window).scrollLeft()) + "px");
	return this;
};

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


jq('#comment-form-div .closebtn').click(function(){
		jq('#comment-form-div').fadeOut();
});

// 从评论生成html内容
function comment_item(cmt) {
	var s = '';
	s += '<li style="display: none;" class="comment-li" id="comment-' + cmt.id + '">\n';
	s += '<div class="comment-avatar" onclick="location.hash=\'#comment-'+cmt.id + '\';">' + cmt.avatar + '</div>\n';
	s += '<div class="comment-meta">\n';

	if(cmt.is_author) {
		s += '<span style="color: red;" class="author">楼主 </span>';
	} else {
		s += '<span class="nickname">' + cmt.user + '</span>\n';
	}

	s += ' @ <span>' + cmt.date + '</span>\n</div>\n';
	s += '<div class="comment-content">\n' + cmt.content + '</div>\n';
	s += '<div class="reply-to" style="margin-left: 54px;"><a style="cursor: pointer;" onclick="comment_reply_to('+cmt.id+');return false;">回复</a></div>';
	s += '</li>';

	return s;
}

// 弹出回复框
function comment_reply_to(p){
	jq('#comment-form-post-id').val(jq('#post_id').val());
	jq('#comment-form-parent').val(p);
	jq('#comment-form-div').center().fadeIn();
}

// 为上一级评论添加div
function comment_add_reply_div(id){
	if(jq('#comment-'+id+' .comment-replies').length === 0){
		jq('#comment-'+id).append('<div class="comment-replies" id="comment-reply-'+id+'"><ol></ol></div>');
	}
}

// 发表评论按钮
jq('#post-comment').click(function(){
	comment_reply_to(0);
});

// 保存 加载进度/加载总数
var start_id = '0';
var cmt_loaded = 0;

// 加载按钮
jq('#load-comments .load').click(function() {
	if(jq(this).attr('loading') === 'true')
		return;

	var load = this;
	jq(this).attr('loading', 'true');
	jq('#load-comments .loading').show();
	jq.post(
		'/twofei-ajax.php',
		'action=get_comments&number=10' 
			+ '&start_id=' + start_id 
			+ '&post_id=' + jq('#post_id').val(),
		function(data) {
			var cmts = data.cmts || [];
			for(var i=0; i<cmts.length; i++){
				if(cmts[i].parent != 0) { // 回复给某人
					comment_add_reply_div(cmts[i].parent);
					jq('#comment-reply-'+cmts[i].parent + ' ol:first').append(comment_item(cmts[i]));
				} else {
					jq('#comment-list').append(comment_item(cmts[i]));
				}
				jq('#comment-'+cmts[i].id).fadeIn();
			}
			jq('#load-comments .loading').hide();

			if(cmts.length == 0) {
				jq('#load-comments .none').show();
				setTimeout(function(){
						jq('#load-comments .none').hide();
					},
					1500
				);
			} else {
				start_id = cmts[cmts.length-1].id;
				cmt_loaded += cmts.length;
			}
			jq(load).removeAttr('loading');
			jq('#comment-title').text('评论('+cmt_loaded+'/'+data.count+')');
		},
		'json'
	)
	.always(setTimeout(function(){
			jq(load).removeAttr('loading');
	},1500));
});

// Ajax评论提交
jq('#comment-submit').click(function() {
	var timeout = 1500;

	jq(this).attr('disabled', 'disabled');
	jq('#comment-form .comment-submit .submitting').show();
	jq.post(
		jq('#comment-form')[0].action,
		jq('#comment-form').serialize()+'&by=twofei',
		function(data){
			if(data.errno == 'success') {
				jq('#comment-content').val('');
				jq('#comment-form .comment-submit .succeeded span').html(data.errmsg);
				jq('#comment-form .comment-submit .succeeded').show();
				setTimeout(function() {
						jq('#comment-form .comment-submit .succeeded').hide();
						jq('#comment-form-div').fadeOut();
					},
					timeout
				);
			} else {
				jq('#comment-form .comment-submit .failed span').html(data.errmsg);
				jq('#comment-form .comment-submit .failed').show();
				setTimeout(function() {
						jq('.form-submit .failed').hide();
					},
					timeout
				);
			}

			jq('#comment-form .comment-submit .submitting').hide();
		},
		'json'
	)
	.fail(function(xhr, sta, e){
		jq('#comment-form .comment-submit .submitting').hide();
		var info = jq('#comment-form .comment-submit .failed span');
		if(xhr.status == '409'){
			info.text('请不要过快地提交，或提交相同的评论！');
		} else {
			info.text('未知错误！');
		}
		jq('#comment-form .comment-submit .failed').show();
	})
	.always(setTimeout(function(){
			jq('#comment-form .comment-submit .submitting').hide();
			jq('#comment-submit').removeAttr('disabled');
			jq('#comment-form .comment-submit .failed').hide();
		},
		timeout
	));
	return false;
});

