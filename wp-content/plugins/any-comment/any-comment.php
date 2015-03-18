<?php
/**
 * Plugin Name: Any Comment
 * Plugin URI: http://blog.twofei.com/wordpress/plugins/any-comment.html
 * Description: Any Comment in comment area
 * Version: 0.0.0
 * Author: twofei
 * Author URI: http://blog.twofei.com
 * License: (None)
 */

defined('ABSPATH') or die("No script kiddies please!");

// 获取评论时转换实体，虽然保存时已经转换过，以防出错。
// 转换空格是为了保存格式
function anycmt_comment_text($comment) {
	$s = [' ',      '<'   , '>'   , '\''  , '"'];
	$r = ['&nbsp;', '&lt;', '&gt;', '&#39;', '&#34;'];

	return str_replace($s, $r, $comment);
}

// 在评论输出到编辑区之前，比上面的少一个空格转换
// 注意'&'要最后转换
function anycmt_comment_edit_pre($comment) {
	$s = ['&lt;', '&gt;', '&#39;', '&#34;', '&amp;'];
	$r = ['<'   , '>'   , '\''  , '"',    '&'];

	return str_replace($s, $r, $comment);
}

// 在保存评论之前转换实体 -> 注意'&'要最先转换！
// 同时还要避免转换BBcode或SyntaxHighlighter等的简码
$save = array();
function anycmt_wrap_bbcode($matches) {
	global $save;
	$i = '[[[[['.count($save).']]]]]';
	$save[$i] = $matches[0];
	return $i;
}

function anycmt_pre_comment_content($comment) {
	// 先保存BBcode
	$ret = preg_replace_callback('/\[[a-z]+].*\[\/[a-z]+]/U', 'anycmt_wrap_bbcode', $comment);

	// 处理转换实体
	$s = ['&',     '<'   , '>'   , '\''  , '"'];
	$r = ['&amp;', '&lt;', '&gt;', '&#39;', '&#34;'];
	$ret = str_replace($s, $r, $ret);

	// 恢复BBcode
	global $save;
	foreach($save as $k => $v) {
		$ret = str_replace($k, $v, $ret);
	}
	unset($save);

	return $ret;
}

add_filter('comment_text', 'anycmt_comment_text', 1);
add_filter('comment_edit_pre', 'anycmt_comment_edit_pre', 100);
add_filter('pre_comment_content', 'anycmt_pre_comment_content', 100);

