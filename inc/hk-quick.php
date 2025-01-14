<?php

/* REGISTER post_type hk_quick */

add_action('init', 'hk_quick_init');
function hk_quick_init() {
	// only if in admin and is administrator
	$user_can_edit = hk_perm::quick();
	
	register_post_type( 'hk_quick',
		array(
			'labels' => array(
				'name' => __( 'Mellanstartsida' ),
				'singular_name' => __( 'Mellanstartsida' )
			),
			'public' => false,
			'show_ui' => $user_can_edit,
			'show_in_menu' => $user_can_edit,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'quick'),
			'capability_type' => 'page',
			'menu_icon' => 'dashicons-schedule'
		)
	);
	add_post_type_support( "hk_quick", array("title","revisions") );
	remove_post_type_support( "hk_quick", "editor" );

	register_taxonomy_for_object_type( "category", "hk_quick" );

}

// shortcodes
/*
 * shortcode [mellanstartsida], show quick links (mellanstartsida)
 */
//add_shortcode( 'mellanstartsida', 'hk_view_quick_links' );

function hk_view_quick_links() {
	global $cat, $tag, $default_settings;
	$retValue = '';
	/* don't show anything if not in category */
	if ($cat == "") return;
	if ($tag != "" && strpos($tag, 'is_') === false) return;
	$args = array(
			'post_type' => array('hk_quick'),
			'post_status' => 'publish',
			'category__in' => $cat,
			'suppress_filters' => 1
			);

	// search in all posts (ignore filters)
	$the_query = new WP_Query( $args );

	if (!function_exists('get_field'))
	 	return "Add Advanced Custom Fields plugin to show quick links.";
		
	// print_r($the_query);

	if ($the_query->have_posts())
	{
		//$retValue .= "<style type='text/css'>.main.hk-quick { max-width: 1138px; }</style>";
		// $retValue .= "<div class='quick-posts-wrapper'>";
		// $retValue .= "<div class='quick-posts'>";
		// $retValue .= "<div class='mellanstart-posts'>";
		$a_class = "q";

		// The Loop
		while ($the_query->have_posts()) : $the_query->the_post();
			$retValue .= "<!-- BEGIN WHILE QUICK POST " . get_the_ID() . "-->";
			$retValue .= "<div class='mellanstart-wrapper'>";
			if (empty(get_field('hk_quick_show_articles'))) {
				$default_settings["show_articles"] = false;
			}
			//$retValue .= wp_get_attachment_link($attachId);
			$title = get_the_title();
			

			if (have_rows('hk_quick_link')) :
				while (have_rows('hk_quick_link')) : the_row();
					if (get_sub_field('inactive')) :
						continue;
					endif;

					$row_layout = get_row_layout();
					$column_layout = get_sub_field('layout');
					if (empty($column_layout))
						$column_layout = "one-whole";
					$num_rows_class = get_sub_field('num_rows');

					$wide_size = ($column_layout != "one-whole") ? $column_layout : '';					
					if($row_layout == 'lagg_till_wide_size_start'):
						$retValue .= "</div>";
						$retValue .= "<div class='mellanstart-wrapper $wide_size wide-layout'>";

					elseif($row_layout == 'lagg_till_wide_size_stop'):
						$retValue .= "</div>";
						$retValue .= "<div class='mellanstart-wrapper'>";

					elseif($row_layout != "lagg_till_bubble"):
						$retValue .= "\n<div class='mellanstart-post $column_layout $num_rows_class $row_layout'>";
					endif;



					// check all layout
					if($row_layout == "lagg_till_bubble"):
						$animate = (get_sub_field('animate'))? "js-bubble-slideshow" : "";
						$retValue .= "</div>";
						$retValue .= "<div class='mellanstart-wrapper $wide_size'>";
						$retValue .= "\n<div class='mellanstart-post $column_layout $num_rows_class $row_layout $animate'>";
						$retValue .= hk_bubble();	
						/* reset data to current query */
						$the_query->reset_postdata();
						
						$retValue .= "</div>";
						$retValue .= "</div>";
						$retValue .= "<div class='mellanstart-wrapper'>";


					elseif($row_layout == "lagg_till_links"):


						// $retValue .= "<div class='quick-post $column_layout'><div class='quick-links'>";
						$retValue .= "<div class='mellanstart-links'>";

						$category = get_term(get_query_var("cat"), 'category');
						$title = get_sub_field('title');
						if (!empty($title)) {
							$retValue .= "<h2>$title</h2>";
						}
						$retValue .= "";
						
						/* get link to content */
						if (get_sub_field('link_wrapper')) :
							$retValue .= '<ul>';
							while (has_sub_field('link_wrapper')) :
								$title = $url = '';
								$title = get_sub_field('title');
								
		
								if (get_sub_field('link')) :
									while (has_sub_field('link')) :
										if ( get_row_layout() == 'inlagg' ) {
											$url = get_sub_field('inlagg');
										}
										elseif ( get_row_layout() == 'extern' ) {
											// prepend http:// if not there already
											$url = get_sub_field('extern');
											if ((substr_compare($url, '/', 0, 1) != 0) && (substr_compare($url, 'http', 0, 4) != 0)) {
												$url = 'https://' . $url;
											}
										}
										elseif ( get_row_layout() == 'kategori' ) {
											$url = get_category_link(get_sub_field('kategori'));
										}
										// elseif ( get_row_layout() == 'fil' ) {
										// 	$value = get_sub_field('file');
										// 	if (!empty($value)) {
										// 		if (empty($description)) {
										// 			$description = $value["description"];
										// 		}
										// 		$url = $value["url"];
										// 	}
										// }
									endwhile;
								endif;
							// if (!empty($title) && !empty($url)) {
								$retValue .= "<li><a class='gtm-quick-link' href='$url'>$title</a></li>";
							// }

							endwhile;
							$retValue .= '</ul>';
						endif;
						$retValue .= "</div>";





					elseif($row_layout == "lagg_till_tags"):

						$retValue .= "<div class='quick-tags'>";
						// $retValue .= "<div class='quick-post $column_layout'><div class='quick-tags'>";

						$category = get_term(get_query_var("cat"), 'category');
						$title = get_sub_field('title');
						if (!empty($title)) {
							$retValue .= "<h2>$title</h2>";
						}
						$retValue .= hk_displayTagFilter(false, 'tags-list', false, "", $category->term_id, false);

						$retValue .= "</div>";

					
					
					
					elseif($row_layout == "lagg_till_rekai"):

						
						/* rekai */
						$nrofhits = get_sub_field('nrofhits');
						$category = get_term(get_query_var("cat"), 'category');
						$category_slug = ($category && !is_wp_error( $category )) ? $category->slug : "";
						$title = get_sub_field('title');
						$style = get_sub_field('rek_style');

						// $retValue .= "<div class='quick-post $column_layout'><div class='quick-rekai'>";
						$retValue .= "<div class='quick-rekai $style'>";
						if (!empty($title)) {
							$retValue .= "<h2>$title</h2>";
						}
						// TODO: only for dev
						/* data-projectid='10341068' data-srek='41e77c49' */
						$retValue .= "<div class='rek-prediction' data-renderstyle='list' data-listcols='1' data-excludetree='/artikel/kontakt/' data-addstripes='false' data-nrofhits='$nrofhits' data-pagetype='$category_slug' data-notpagetype='nyheter,kontakter'></div>";


						$retValue .= "</div>";






					elseif($row_layout == "lagg_till_news"):
							
						// $retValue .= "<div class='quick-post $column_layout quick-news'><div>";
						$retValue .= "<div class='quick-news'>";

						/* NEWS */
						$title = get_sub_field('title');
						$num_news = get_sub_field('num_news');
						$num_news_cols = get_sub_field('num_news_cols');
						$image_style = get_sub_field('image_style');
						$news_icon = get_sub_field('news_icon');
						$quick_news_args =
							array("title" => $title,
										"num_news" => $num_news,
										"rss_link_text" => "<span class='rss-icon'></span> RSS",
										"rss_link_url" => "/feed/?tag=nyheter",
										"thumb_size" => "",
										"num_news_cols" => $num_news_cols,
										"image_style" => $image_style,
										"news_icon" => $news_icon,
									);
							$retValue .= "<!-- FÖRE NYHETER -->";
							$retValue .= get_quick_news( $quick_news_args );
							$retValue .= "<!-- EFTER NYHETER -->";

							/* reset data to current query */
							$the_query->reset_postdata();

						/* END NEWS */
						$retValue .= "</div>";



					elseif($row_layout == "lagg_till_code"):							

						// $retValue .= "<div class='quick-post $column_layout quick-code'><div>";
						$retValue .= "<div class='quick-code'>";
						$retValue .= get_sub_field('code');
						$retValue .= "</div>";


						
					elseif($row_layout == "lagg_till_driftstorning"):							

						// $retValue .= "<div class='quick-post $column_layout quick-code'><div>";
						$driftstorning = new Driftstorning();
						
						if ($driftstorning->getActive()) {
							$retValue .= "<div class='quick-driftstorning' id='" . $driftstorning->getID() . "'>";
							$retValue .= $driftstorning->getHTML();
							$retValue .= "</div>";
						};
						
						
							
					elseif($row_layout == "lagg_till_title"):
					

						/* get text */
						$title = get_sub_field('title');
						$title_div = (!empty($title)) ? "<h1>$title</h1>" : "";
						$description = get_sub_field('description');
						$description_div = (!empty($description)) ? "<div class='q-description'><p>$description</p></div>" : '';
						
						$retValue .= "<div class='quick-title $content_layout'>";
						$retValue .= "$title_div$description_div</a>";
						$retValue .= "</div>";


					
					elseif($row_layout == "lagg_till_puff"):
					
						$videourl = '';
						$videocssclass = "";

						/* get link to content */
						if (have_rows('content')) :
							while (have_rows('content')) : the_row();

								// $retValue .= "<div class='quick-post  $imagesize  $column_layout  $cssclass quick-puff'><div style='$style'>";
								$content_layout = get_row_layout();
								$target = "";

								switch ($content_layout) {
									case 'inlagg':
										$value = get_sub_field('post');
										$url = get_permalink($value->ID);
										break;
									case 'category':
										$value = get_sub_field('category');
										$url = get_term_link($value, 'category');
										break;
									case 'extern':
										$url = get_sub_field('extern');
										if ((substr_compare($url, '/', 0, 1) != 0) && (substr_compare($url, 'http', 0, 4) != 0)) {
											$url = 'https://' . $url;
											$target = "target='_blank'";
										}
										break;
									case 'fil':
										$value = get_sub_field('file');
										if (!empty($value)) {
											if (empty($description)) {
												$description = $value["description"];
											}
											$url = $value["url"];
											$target = "target='_blank'";
										}
										break;
									case 'video':
										/* get video */
										$videourl = get_sub_field('video_url');
																			
										if (!empty($videourl)) {
											$videocssclass = "js-video-popup";
											$videourl = "data-video-url='$videourl'";
										}
										break;
									default:
										# code...
										break;
								}
								

								

							endwhile;
						endif;

						/* get image */
						$imagediv = "";
						$image = get_sub_field('image');
						$imagesize = get_sub_field('image-size');
						if ($imagesize == "") {
							$imagesize = "thumbnail-image";
						}
						if ($image != "") :
							if (!empty($image["sizes"][$imagesize])) {
								$src = $image["sizes"][$imagesize];
							}
							else {
								$src = $image["sizes"]["thumbnail-image"];
							}
							$alt = $image["alt"];
							$imagediv .= "<span class='$videocssclass $imagesize slide' $videourl>";
							$imagediv .= "<img src='$src' alt='$alt' title='$alt' />";
							$imagediv .= "</span>";
						endif;

						/* get text */
						$title = get_sub_field('title');
						$title_div = (!empty($title)) ? "<h2>$title</h2>" : "";
						$description = get_sub_field('description');
						$description_div = (!empty($description)) ? "<div class='q-description'><p>$description</p></div>" : '';
						$text_div = "<div class='q-text'>$title_div$description_div</div>";

						$retValue .= "<div class='$imagesize quick-puff $content_layout'>";
						$retValue .= "<a $target class='gtm-quick-link $a_class $a_class-post' href='$url' title='$title'>$imagediv$text_div</a>";
						$retValue .= "</div>";


					endif; // end layout

					if($row_layout != "lagg_till_bubble" && $row_layout != 'lagg_till_wide_size_start' && $row_layout != 'lagg_till_wide_size_stop'):
						$retValue .= '</div>'; // end mellanstart-post
					endif;

				endwhile; // end looping through rows
			endif; // end if quick links exists
			$retValue .= "</div>";
			$retValue .= "<!-- END WHILE QUICK POST " . get_the_ID() . "-->";

		endwhile; // end while have_posts

		// Reset Post Data
		wp_reset_postdata();

		// $retValue .= "</div>";

		return $retValue;
	}

}


function get_quick_news( $args ) {
	global $thumb_size;
	global $default_settings;

	$options = get_option('hk_theme');
	$title = (isset($args["title"]) && !empty($args["title"])) ? "<h2 class='widget-title'>" . $args["title"] . "</h2>" : "";
	$num_news = (isset($args["num_news"]) && !empty($args["num_news"])) ? $args["num_news"] : 4;
	$num_news_cols = (isset($args["num_news_cols"]) && !empty($args["num_news_cols"])) ? $args["num_news_cols"] : 3;
	
	if (isset($args["rss_link_text"])) $rss_link_text = $args["rss_link_text"];
	else $rss_link_text = "";
	if (isset($args["rss_link_url"])) $rss_link_url = $args["rss_link_url"];
	else $rss_link_url = "";
	if (isset($args["thumb_size"])) $thumb_size = $args["thumb_size"];
	else $thumb_size = "";
	
	$boxclass = "box-list cols-$num_news_cols ";

	/* get all sub categories to use in queries */
	$cat = get_query_var("cat");
	$all_categories = hk_getChildrenIdArray($cat);
	$all_categories[] = $cat;


	$retString = '<div class="newscontent-wrapper2" role="main">';
	$retString .= $title;
	$retString .= '<div id="newscontent" class="' . $boxclass . 'newscontent">';

	/* Query all posts with selected startpage category */
	$cat = get_query_var("cat");
	$query = array( 'posts_per_page' => $num_news,
					'category__and' => $cat,
					'tag__and' => $default_settings["news_tag"],
					'suppress_filters' => 'true',
					'orderby' => 'date',
					'order' => 'desc' );

	$quick_new_loop = new WP_Query( $query );

	$shownposts = array();
	$countrows = 0;
	if ( $quick_new_loop->have_posts() ) :

	 	while ( $quick_new_loop->have_posts() ) :
			$quick_new_loop->the_post();
			$shownposts[] = get_the_ID();
			$retString .= "<!-- " . get_the_ID() . " -->";
			$retString .= load_quick_news($args);

			
		endwhile;
		//$quick_new_loop->reset_postdata();
	endif;
	// Reset Query

	//wp_reset_query();
	//wp_reset_postdata();
	//rewind_posts();



	$hiddenclass = "";
	$after_newslist = "";
	$after_newslist2 = "";

	$after_newslist .= '<span class="read-more-link inline"><a class="gtm-fpcw-news-archive-link" href="' . get_tag_link($default_settings["news_tag"]) . '">Se fler nyheter<span class="right-icon"></span></a></span>';
	
	if ($rss_link_url != "" && $rss_link_text != "") {
		$after_newslist .= "<a href='$rss_link_url' class='gtm-fpcw-rss-link read-more-link rss inline float--right'>$rss_link_text</a>";
	}
	
	$retString .= "</div><!-- END #newscontent -->";
	$retString .= $after_newslist;
	$retString .= "</div><!-- END .newscontent-wrapper -->";

	return $retString;
}

function hk_getQuickNewsImage($id, $args) {
	$image_style = (isset($args['image_style'])) ? $args['image_style'] : 'none';
	$news_icon = (isset($args['news_icon'])) ? $args['news_icon'] : 0;
	if ($image_style == 'none') return '';

	$retString = '';
	$hk_featured_repeater = get_field('hk_featured_images', $id);
	if (!empty($hk_featured_repeater) && !empty($hk_featured_repeater[0])
	&& !empty($hk_featured_repeater[0]["hk_featured_image"])
	&& !empty($hk_featured_repeater[0]["hk_featured_image"]["sizes"])
	&& !empty($hk_featured_repeater[0]["hk_featured_image"]["sizes"])
	&& !empty($hk_featured_repeater[0]["hk_featured_image"]["sizes"]["thumbnail"]) ) {

		$src = $hk_featured_repeater[0]["hk_featured_image"]["sizes"]["thumbnail"];
		$alt = $hk_featured_repeater[0]["hk_featured_image"]["alt"];
		if ($alt == '') {
			$alt = $hk_featured_repeater[0]["hk_featured_image"]["title"];
		}
		$retString .=  "<div class='news-image-wrapper'><img src='$src' alt='$alt' /></div>";
	} else if ($news_icon > 0) {
		
		$icon = wp_get_attachment_image_src($news_icon, 'thumbnail');
		$src = $icon[0];		
		if (!empty($src)) {
			$retString .=  "<div class='news-image-wrapper default-image'><img src='$src' alt='' role='presentation' /></div>";
		}
	}
	return $retString;
}
function load_quick_news($args = []) {

	$image_style = (isset($args['image_style'])) ? $args['image_style'] : 'none';
	
	$externalclass = "";
	$jstoggle = " js-toggle-article";
	$href = $name = '';
	if (function_exists("get_field")) {
		$href = get_field('hk_external_link_url');
		$name = get_field('hk_external_link_name');
		if (!empty($href))
		{
			$jstoggle = "";
			$externalclass = " js-external-link";
			$title = "Extern länk till " . the_title_attribute( 'echo=0' );
		}
	}
	$img = hk_getQuickNewsImage(get_the_ID(), $args);
	if (empty($href)) {
		$href = get_permalink();
		$title = "Länk till " . the_title_attribute( 'echo=0' );
	}
	$externalclass = "class='gtm-cn-news-link$externalclass$jstoggle'";

	$img_wrapper_class = (!empty($img)) ? "has-image" : "";
	$sticky_class = (is_sticky()) ? "sticky" : "";

	$retString = "<article id='post-" . get_the_ID() . "' class='" . join( ' ', get_post_class() ) . " $image_style $sticky_class $img_wrapper_class news summary" . "'>";
	$retString .= $img;
	$retString .= "<div class='news-text-wrapper'>";
	// if news
	if (!empty($default_settings["news_tag"]) && has_tag($default_settings["news_tag"])) {
		$retString .= "<time>" . get_the_date("Y-m-d") . "</time> ";
	}
	
	$published = get_the_date("Y-m-d");
	$modified = get_the_modified_date("Y-m-d");
	$published_date = ($published != $modified) ? "<span class='modified-date'>".get_the_modified_date("Y-m-d G:i")."</span>" : "<span class='published-date'>" . get_the_date("Y-m-d G:i") . "</span>";
	$retString .= "<div class='news-time-wrapper'>$published_date</div>";
	$retString .= "<h5 class='entry-title'><a $externalclass href='$href' title='$title' rel='bookmark'>" . get_the_title() . "...</a></h5>";


	$retString .= "<span class='hidden article_id'>" . get_the_ID() . "</span>";
	$retString .= "</div><!-- END .news-text-wrapper -->";
	$retString .= "</article><!-- #post-" . get_the_ID() . " -->";

	return $retString;
}

?>
