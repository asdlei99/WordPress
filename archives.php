<?php
	if(!isset($_GET['pid']))
		exit;
	
	if(($pid = intval($_GET['pid'])) <= 0)
		exit;

	$if_modified = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
		? $_SERVER['HTTP_IF_MODIFIED_SINCE'] 
		: '';
	
	if($if_modified !== '')
		$if_modified = date('Y-m-d G:i:s', strtotime($if_modified));

	$db = mysql_connect(
		'localhost',
		'wordpress',
		'wordpressfwycfkdj'
		);

	if($db == false){
		header('HTTP/1.1 500');
		die('Failed to connect to MySQL.');
	}
	
	mysql_set_charset('utf8', $db);

	$sql = "
		SELECT post_content,post_title,post_modified_gmt FROM wordpress.wp_posts
		WHERE 
			ID=$pid 
			AND post_status='publish' 
			AND post_password='' 
			AND (post_type='post' OR post_type='page')
		";
	
	if($if_modified !== ''){
		$sql .= " AND UNIX_TIMESTAMP(post_modified_gmt)>UNIX_TIMESTAMP('$if_modified')";
	}

	$sql .= ";";

	$results = mysql_query($sql, $db);

	if($results == false){
		echo mysql_error($db);
		exit;
	}

	if(($row = mysql_fetch_assoc($results)) == false){
		if($if_modified === ''){
			echo 'Sorry, no such post.';
			exit;
		}
		else{
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
	}

	$content = $row['post_content'];

	function cb($matches){
		return '<pre class="brush: php; title: ; notranslate;" title="">'.$matches[2].'</pre>';
	}

	$p = '#(\[php])((.|\n)*)(\[/php])#';
	$content = preg_replace_callback($p, 'cb', $content);

	$title = $row['post_title'];

	$modified = $row['post_modified_gmt'];
	$modified_gmt = date('D, d M Y G:i:s ', strtotime($modified)).'GMT';

	$syntax = '
<script type="text/javascript" src="/wp-content/plugins/syntaxhighlighter/syntaxhighlighter3/scripts/shCore.js?ver=3.0.9b"></script>
<script type="text/javascript" src="/wp-content/plugins/syntaxhighlighter/syntaxhighlighter3/scripts/shBrushPhp.js?ver=3.0.9b"></script>
<script type="text/javascript">
(function(){
	var corecss = document.createElement("link");
	var themecss = document.createElement("link");
	var corecssurl = "/wp-content/plugins/syntaxhighlighter/syntaxhighlighter3/styles/shCore.css?ver=3.0.9b";
	if ( corecss.setAttribute ) {
		corecss.setAttribute( "rel", "stylesheet" );
		corecss.setAttribute( "type", "text/css" );
		corecss.setAttribute( "href", corecssurl );
	} else {
		corecss.rel = "stylesheet";
		corecss.href = corecssurl;
	}
	document.getElementsByTagName("head")[0].insertBefore( corecss, document.getElementById("syntaxhighlighteranchor") );
	var themecssurl = "/wp-content/plugins/syntaxhighlighter/syntaxhighlighter3/styles/shThemeDefault.css?ver=3.0.9b";
	if ( themecss.setAttribute ) {
		themecss.setAttribute( "rel", "stylesheet" );
		themecss.setAttribute( "type", "text/css" );
		themecss.setAttribute( "href", themecssurl );
	} else {
		themecss.rel = "stylesheet";
		themecss.href = themecssurl;
	}
	document.getElementsByTagName("head")[0].insertBefore( themecss, document.getElementById("syntaxhighlighteranchor") );
	})();
	SyntaxHighlighter.config.strings.expandSource = "+ expand source";
	SyntaxHighlighter.config.strings.help = "?";
	SyntaxHighlighter.config.strings.alert = "SyntaxHighlighter\n\n";
	SyntaxHighlighter.config.strings.noBrush = "Can\"t find brush for: ";
	SyntaxHighlighter.config.strings.brushNotHtmlScript = "Brush wasn\"t configured for html-script option: ";
	SyntaxHighlighter.defaults["pad-line-numbers"] = false;
	SyntaxHighlighter.all();
</script>
';

	$content_all =
'<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>'.$title.'</title>
	<link rel="stylesheet" type="text/css" href="/wp-content/themes/mypicochic/style.css" />
</head>
<body>
<div id="wrapper">
<div>
<h2>'.$title.'</h2>
<hr />
</div>
<div class="post">
';
	$content_all .= $content;
	$content_all .= '
</div>
</div>
'.$syntax.'
</body>
</html>';

	header('HTTP/1.1 200 OK');
	header('Last-Modified: '.$modified_gmt);
	header('Content-Length: '.strlen($content_all));

	echo $content_all;

