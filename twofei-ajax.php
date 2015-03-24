<?php 

function header_json() {
	header('HTTP/1.1 200 OK');
	header('Content-Type: application/json');
}

function get_today_english() {
	$today = [];

	$ch = curl_init("http://xue.youdao.com/w?method=tinyEngData&date=" . date("Y-m-d"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$html = curl_exec($ch);
	curl_close($ch);
	$doc = new DOMDocument();
	@$doc->loadHTML('<?xml version="1.0" encoding="UTF-8" ?>' . $html);

	$have = false;

	$divlist = $doc->getElementsByTagName('div');
	if($divlist && $divlist->length){
		$div = $divlist->item(0);
		if($div->hasAttributes()){
			$example_english = $div->attributes->getNamedItem('class');
			if($example_english && $example_english->nodeValue == 'example english'){
				$nodes = $div->childNodes;
				$image = $nodes->item(1)->childNodes->item(0)->attributes->getNamedItem('src')->nodeValue;
				$sentence = $nodes->item(3)->childNodes->item(1)->childNodes->item(0)->nodeValue;
				$translate= $nodes->item(3)->childNodes->item(3)->childNodes->item(0)->wholeText;
				//echo "<p><img src=\"" . $image . "\"/></p>\n";
				//echo "<p>" . $sentence . "</p>\n";
				//echo "<p>" . $translate . "</p>\n";
				$today = compact('sentence', 'translate');
				$have = true;
			}
		}
	}

	if($have === false){
		$today = [
			'sentence' => 'Praising what is lost makes the remembrance dear.',
			'translate' => '缅怀已经失去的，将使回忆变得亲切。'
		];
	}

	return $today;
}

function tf_get_comments() {
	/** Sets up the WordPress Environment. */
	require( dirname(__FILE__) . '/wp-load.php' );

	$field_must = ['post_id'];
	foreach($field_must as $m){
		if(!isset($_POST[$m])){
			header_json();
			echo json_encode([
				'errno'		=> 'error',
				'errmsg'	=> '缺少 post_id 字段。',
			]);
			die(0);
		}
	}

	$allowed_fields = ['number', 'post_id', 'start_id'];

	$fields = [
		'status'	=> 'approve',
		'orderby'	=> 'comment_date_gmt',
		'order'		=>'ASC',
	];

	foreach($allowed_fields as $f){
		if(isset($_POST[$f])){
			$fields[$f] = $_POST[$f];
		}
	}

	$cmts = get_comments($fields);
	$count = get_comments([
		'post_id' => $fields['post_id'],
		'count' => true,
		]);

	$pri = ['comment_author_email', 'comment_author_IP', 'comment_agent',
		'comment_karma', 'comment_approved', 'comment_type', 'user_id',
		'comment_date_gmt',
	];

	$ren = ['ID', 'post_ID', 'author', 'author_url', 'date', 'content', 'parent'];
	$ren_to = ['id', 'post_id', 'user', 'url', 'date', 'content', 'parent'];
 

	foreach($cmts as $c){
		// 评论过滤
		$c->comment_content = apply_filters('comment_text', $c->comment_content);

		// 判断是否是我自己的评论
		$c->is_author = $c->comment_author_email === 'anhbk@qq.com';

		// 头像
		$c->avatar = get_avatar($c, 48);

		// 屏蔽隐私 及 一些不需要的字段
		foreach($pri as $p){
			unset($c->$p);
		}

		// 简化
		for($i=0; $i<count($ren); $i++){
			$s = 'comment_'.$ren[$i];
			$d = $ren_to[$i];
			$c->$d = $c->$s;
			unset($c->$s);
		}
	}

	$msg = [
		'errno'		=> 'success',
		'errmsg'	=> '获取成功',
		'cmts'		=> $cmts,
	];

	$msg['count'] = $count;

	header_json();
	
	echo json_encode($msg);
}

$act = $_POST['action'];

if($act == 'today_english'){
	header('200 OK HTTP/1.1');
	$today = get_today_english();
	echo '<p id="today_english_cn">'.$today['translate'].'</p>';
	echo '<p id="today_english_en">'.$today['sentence'].'</p>';
	die(0);
} else if($act == 'get_comments') {
	tf_get_comments();
	die(0);
} 


