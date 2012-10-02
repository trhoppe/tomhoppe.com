<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script type="text/javascript" src="http://www.tomhoppe.com/js/jquery.js"></script>

<link rel="stylesheet" href="http://www.tomhoppe.com/css/colorbox.css" type="text/css" media="screen" />
<script src="http://www.tomhoppe.com/js/jquery.colorbox-min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("a.thickbox").colorbox({transition:"fade",speed:100});
});
</script>


<style type="text/css" media="screen">

<?php
// Checks to see whether it needs a sidebar or not
if ( !empty($withcomments) && !is_single() ) {
?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbg-<?php bloginfo('text_direction'); ?>.jpg") repeat-y top; border: none; }
<?php } else { // No sidebar ?>
	#page { background: url("<?php bloginfo('stylesheet_directory'); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
<?php } ?>

</style>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>
<body>

<div id="mainCentering">

<div id="borderTop" class="clearfix">
	<img src="http://www.tomhoppe.com/images/cornerTL.gif" width="13" height="18" alt="" border="0" class="cornerL" />
	<img src="http://www.tomhoppe.com/images/cornerTR.gif" width="13" height="18" alt="" border="0" class="cornerR" />
</div>

<div id="border1"><div id="border2"><div id="border3">

<div id="mainContainer">
	<div id="topBar" class="clearfix">
		<h1>
			<a href="http://www.tomhoppe.com">tomhoppe.com<br /></a>
		</h1>
		<div class="siteDesc">
			Racing, Web Development, Photography, and Beer...Stuff that matters. 
		</div>
	</div>   