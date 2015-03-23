<!DOCTYPE html> 
<!-- ,,,::::t:MMMMMMMMMBVt:+.. 
	  　　 ,IVXVYIBttt+::+IVVMMMMMMRR: 
	  　　 ,YYVYItMYti+i++:X+Rt:tXWRMR, 
	  　　 .YRiIYRMViitVXRWRYMI++++itMM.. 
	  　　　.Y+,.,X::,,,YMMMMMMMMRVItXMti 
	  　　　 :X+:,X:,. .,iiIRMWMMMBBRMMBY. 
	  　　　　tR+:I:i:+Y:IitYVYMMMMMMMMRi. 
	  　　　　.+RXt:,::.::XXIBMMMMMMMMM+: 
	  　　　　　,RRXitY+,.::RWMMMMMMMMt. 
	  　　　　　　VYI:::,..:tVMMMMMMBY+. 
	  　　　　　 .VBBW:::::,i.MMMMMBi:. 
	  　　　　　 .tWRRVi:::.X:VMMMMMMY. 
	  　　　　 ,+i+:,XYtt+:,i:,MMMBR: 
	  　　...VV..:..:.tt::++:+,RMYMV. 
	  　:M:::..:,.:,,,.+t+++Ytt.,+: 
	  tRt:,.:,.:,:.:+.,:++tit, 
	  :tXt:,:,:.:.,,+,:+YRY, 
	  　:++:::.,:.:.::+:iMi 
	  　 ,Ri:::,:::::::+Ii 
	  　 ,+:Xii:,,::I:tit,. 
	  　　 :BBt,:+::,::i+. 
	  　　 :BWXX::::::iX. 
	  　　 :BWVIi++t+:V+ 
	  　　　WBXtItii+iWI. 
	  　　　:MWIYIti+iVRY, 
	  　　　 RBXVYItiiIYXWI, 
	  　　　 :MRWWVYttttIIXWt. 
	  　　　 .XMBRRXIti++itIXW, 
	  　　　　.BMBBRVIi+::+ttXX. 
	  　　　　 ,MMBRXYti:::+tIW: 
	  　　　　　tMMRWYti+++ittXV 
	  　　　　　 +MRVYti+++ittWI 
	  　　　　　.VMWVtiiiiiitIR, 
	  　　　　 .XBBWVttttttttXRv 
	  　　　 .,WBRBXVtttttttIWt 
	  　　　 :RWXWBXYtttttttYR: 
	  　　 .iRWVIiBWYIttttttYW, 
	  　　.tWVYti:WRVIttittIVV　　　.:,, 
	  　　tWVIi++:XRVIIttttIXY　 ..:YYtYi:tittV, 
	  　 tXYti+++tWRVYttittYWIiIYYVItt:iXW+.... 
	  　:WIt+++iXRBBVYItitIYXXYti+iIYt++:IMRi:. 
	  .:Xti+:+tRWt:BVYItitIYWVIIIYXXWXVXYt+i+IV: 
	  .tYi+::IWI:::BVVItitIXBRVIt:::::::tIVXRiXt 
	  :Viii++i:itIXBXVttitVW,　　　　　　　 tBIX 
	  .XIiXIttIVRBRBIItttIRX　　　　　　　　 :VX 
	  .,tXXWWWVi+. RXXItiYRV　　　　　　　　　.. 
	  　　　　　　 ,BWYi+IRX 
	  　　　　　　　tBYiitWB, 
	  　　　　　　　.WVtiiIRI 
	  　　　　　　　 VWtiiiIB, 
	  　　　　　　　 ,BIiiiiWt 
	  　　　　　　　 .BViiiiYV 
	  　　　　　　　　XXtii+YV 
	  　　　　　　　　iRiii+YY 
	  　　　　　　　　:Btii+XI 
	  　　　　　　　　 Wtii+R: 
	  　　　　　　　　 XY+t+B. 
	  　　　　　　　　 YX+tYR. 
	  　　　　　　　　 tX+iWV 
	  　　　　　　　　 iXi+RI 
	  　　　　　　　　 tYiIXX 
	  　　　　　　　　,XtiIXRt. 
	  　　　　　　　　:BIVYRWIV 
	  　　　　　　　　+RYXXWiYR. 
	  　　　　　　　　iWIVYtXMV 
	  　　　　　　　　tBYItRtM+ 
	  　　　　　　　　XBWttX:B: 
	  　　　　　　　YYBXXYR:,R: 
	  　　　　　　 tIYYYIY:　i:
-->
<html lang="zh-CN">
<head>
	<meta charset="UTF-8" />
	<title><?php
		global $page, $paged;

		wp_title( '|', true, 'right' );

		bloginfo( 'name' );

		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";

		if ( $paged >= 2 || $page >= 2 )
			echo ' | ' . sprintf( __( 'Page %s', 'picochic' ), max( $paged, $page ) );

		?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('stylesheet_directory'); ?>/font-awesome-4.3.0/css/font-awesome.min.css" />

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="wrapper">

	<header id="header">

		<section id="head">
			<h1 title="Do you like it ?" onclick="location.href = location.protocol + '//' + location.host;"><?php bloginfo('name'); ?><sup> ♡</sup></h1>
			<div class="description" id="today_english">
				<p>&nbsp;</p><p>&nbsp;</p>
			</div>
		</section>

		<nav id="mobnav" class="mono">
			<div id="music_progress"></div>
			<?php wp_nav_menu(array('theme_location' => 'primary')); ?>
		</nav>

		<script type="text/javascript">
			var audio1 = new Audio('/bg.mp3');
			var progress = document.getElementById('music_progress');
			if(audio1) {
				audio1.ontimeupdate = function() {
					var percent = (this.currentTime / this.duration * 100);
					progress.style.width = percent+'%';
				}
				audio1.onended = function() {
					progress.style.width = '0%';
				}
			}
		</script>

		<div title="啊，最近没时间种蘑菇，怎么办？" id="music_button" onclick="audio1.paused ? audio1.play() : audio1.pause();">♪</div>

	</header>

	<section id="main">

