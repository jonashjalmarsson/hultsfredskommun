<?php

/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */


global $default_settings, $wp_query;
/* get hk_options */
$hk_options = get_option('hk_theme');

//force the page to use http if not logged in
/*if ($_SERVER["SERVER_PORT"] == 443 && !is_user_logged_in()) {
    $redir = "Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header($redir);
    exit();
}*/
/* hide if single and not visible */
if (isset($hk_options["hidden_cat"]) && in_category($hk_options["hidden_cat"])) {
	header("HTTP/1.0 404 Not Found");
	//TODO print 404 error - include("404.php");?
	die("Inte synlig.");
}

/* get category_as_filter recursive setting */
$catvalue = "category_" . get_query_var("cat");
$default_settings['category_as_filter'] = ((function_exists("get_field") && get_field("category_as_filter", $catvalue)=="")?false:true);
$default_settings['category_show_children'] = ((function_exists("get_field") && get_field("category_show_children", $catvalue)=="")?false:true);

?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php
	if (is_category()) {
		$cat_object = get_category(get_query_var("cat"));
		$cat_desc = $cat_object->description;
		if (!empty($cat_desc)) {
			echo "<meta http-equiv='refresh' content='0;URL=". $cat_desc . "' />";
		}
	}
?>

<?php
$meta_description = '';
if (is_single() && get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true)) {
	$meta_description = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
}
else if (!is_single() && isset($hk_options["meta_description"]) ) {
	$meta_description = $hk_options["meta_description"];
} else if ( !defined('RANK_MATH_PRO_VERSION') && is_single() && get_the_ID() > 0) {
	$meta_description = substr( strip_tags(get_post_field('post_content', get_the_ID())), 0, 200);
}
if ($meta_description != "") :?>
<meta name="description" content="<?php echo $meta_description; ?>" />
<?php endif; ?>
<?php /* SET VIEWPORT */ ?>
<meta name="viewport" content="width=device-width" />

<title><?php

	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	/*
	// Add the blog name.
	//bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );
	*/
	?></title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php wp_site_icon(); ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );


	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */

	/* option to be able to add scipts or other from setting */
	echo isset($hk_options['in_head_section']) ? $hk_options['in_head_section'] : '';

	/* wp_head last in <head> */
	wp_head();
?>
</head>
<?php
$term = get_queried_object();
$current_cat = (!empty($term)) ? $term->slug : '';
$firstpageClass =(is_sub_category_firstpage() && get_query_var("tag") == "") ? "home":"";
$printpageClass = ((!empty($_REQUEST["print"])) && $_REQUEST["print"] == 1) ? "print":"";
$category_as_filter_class = (!empty($default_settings["category_as_filter"]) && $default_settings["category_as_filter"] == 1) ? "hk-js-category-filter  category-filter":"no-category-filter";
$category_show_children_class = (!empty($default_settings["category_show_children"]) && $default_settings["category_show_children"] == 1) ? "hk-js-category-show-children  category-show-children":"no-category-show-children";
$lattlast_cat_array = (!empty($hk_options["show_categorylist_lattlast"])) ? explode(",", str_replace(" ", "", $hk_options["show_categorylist_lattlast"])) : '';
$lattlast = (!empty($lattlast_cat_array) && in_array($current_cat, $lattlast_cat_array))?'lattlast':'';
if (empty($lattlast) && is_single()) {
	if (!empty($lattlast_cat_array)) {
		foreach ($lattlast_cat_array as $lattlast_cat) {
			if (has_category($lattlast_cat, get_the_ID())) {
				$lattlast = "lattlast";
				break;
			}
		}
	}
}
 
?>
<body <?php body_class($lattlast . " " . $category_show_children_class . " " . $category_as_filter_class . " " . $firstpageClass . " " . $printpageClass . " " . $printpageClass . " new-menu " ); ?>>
<?php echo isset($hk_options['in_topbody_section']) ? $hk_options['in_topbody_section'] : ''; ?>
<div id="page" class="hfeed">
	<?php
		require( get_template_directory() . '/inc/hk-header.php');
	?>

	<?php
	// if (!is_sub_category_firstpage() && get_query_var("tag") == "") { 
	if ((get_query_var("cat") != "" || is_home()) && get_query_var("tag") == "") { ?>
		<div class="main hk-quick"><div class="main-wrapper">
			<div class='mellanstart-widget-wrapper'>
			<?php echo hk_view_quick_links(); ?>
			</div>
		</div></div>
	<?php } ?>
		
	
	<div id="main" class="main">
	<div class="main-wrapper">
		
	<?php


// check if hidden page
if ((is_single() && hk_check_if_post_is_hidden(get_the_ID()))
|| (is_category() && hk_check_if_cat_is_hidden($cat))) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 ); exit();
}

