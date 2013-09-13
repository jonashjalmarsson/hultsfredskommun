<?php

/** 
 * Description: Echo navigation
 *  */

function hk_breadcrumb() {
	$search = get_query_var("s");
	$cat = get_query_var("cat");
	$tag = get_query_var("tag");

	if (!is_home() ) {
		if ($cat != "") {
			$cats_str = get_category_parents($cat, false, '%#%', true);
			$cats_array = explode('%#%', $cats_str);
			$tag_link = "";
			if ($tag != "") {
				$tag_link = "?tag=".$tag; 
				echo "<a href='" . get_site_url() . $tag_link . "'>Alla</a> &raquo; ";
			}
			foreach  ($cats_array as $c) {
				if ($c != "") {
					$c = get_category_by_slug($c);
					echo "<a href='" . get_category_link($c->term_id) . $tag_link . "'>" . $c->name . "</a> &raquo; ";
				}
			}
			//echo get_category_parents($cat, TRUE, ' &raquo; ');
		}
	
		/*if ($tag != "") {
			echo '<br>Typ av information: ';
			foreach (split(',',$tag) as $t) {
				$t = get_term_by( 'slug', $t, 'post_tag');
				echo "<a href='" . get_tag_link($t->term_id) . "'>" . $t->name . "</a> | ";
			}
			echo "<a class='important-text' href='" . get_category_link($cat) . "'>Rensa alla</a>";
		}*/
	}
	//$categories_list = get_the_category();
	
	/*if (!empty($categories_list)) : foreach ( $categories_list as $list):
		$retValue .= "<a href='".get_category_link($list->term_id)."'>" . $list->name . "</a> | ";
	endforeach; endif; // End if categories
	*/
	/*$tags_list = get_the_terms(get_the_ID(),"post_tag");
	if (!empty($tags_list)) : foreach ( $tags_list as $list):
		$retValue .= "<a href='".get_tag_link($list->term_id)."'>" . $list->name . "</a> | ";
	endforeach; endif; // End if tags
	*/

}

function hk_404() { 
	$options = get_option('hk_theme'); 
	$title = "Hittade du inte den information du s&ouml;kte?";
	$message = "Du kan forts&auml;tta genom att &auml;ndra i ditt urval eller s&ouml;ka fritt i s&ouml;krutan ovan.";
	$message2 = "";
	if ($options["404title"] != "")
		$title = $options["404title"];
	if ($options["404message"] != "")
		$message = $options["404message"];
	if ($options["404message2"] != "")
		$message2 = $options["404message2"];
	?>
		<article id="post-nothing">
			<div class="content-wrapper">
			<div class="summary-content">
				<div class="entry-wrapper">
					<h1 class="entry-title"><?php echo $title; ?></h1>
					<div class="entry-content">
						<p><?php echo $message; ?></p>
						
						<?php if($message2 != "" && function_exists('get_most_viewed')) : ?>
						<p><?php echo $message2; ?></p>
						<ul><?php get_most_viewed('post'); ?></ul>
						<?php endif; ?>
						
					</div>
				</div>
				
			</div><!-- .summary-content -->

			</div>
		</article><!-- #post-0 -->
<?php
}
function hk_empty_search() { 
	$options = get_option('hk_theme'); 
	$title = "Hittade du inte den information du s&ouml;kte?";
	$message = "Du kan forts&auml;tta genom att &auml;ndra i ditt urval eller s&ouml;ka fritt i s&ouml;krutan ovan.";
	$message2 = "";
	if ($options["emptytitle"] != "")
		$title = $options["emptytitle"];
	if ($options["emptymessage"] != "")
		$message = $options["emptymessage"];
	if ($options["emptymessage2"] != "")
		$message2 = $options["emptymessage2"];
	?>
		<article id="post-nothing">
			<div class="content-wrapper">
			<div class="summary-content">
				<div class="entry-wrapper">
					<h1 class="entry-title"><?php echo $title; ?></h1>
					<div class="entry-content">
						<p><?php echo $message; ?></p>
						
						<?php if($message2 != "" && function_exists('get_most_viewed')) : ?>
						<p><?php echo $message2; ?></p>
						<ul><?php get_most_viewed('post'); ?></ul>
						<?php endif; ?>
						
					</div>
				</div>
				
			</div><!-- .summary-content -->

			</div>
		</article><!-- #post-0 -->
<?php
}
function hk_empty_navigation() { 
	$options = get_option('hk_theme'); 
	$title = "H&auml;r finns inga artiklar.";
	$message = "Du kan forts&auml;tta genom att v&auml;lja en underkategori eller s&ouml;ka fritt i s&ouml;krutan ovan.";
	$message2 = "";
	if ($options["emptycattitle"] != "")
		$title = $options["emptycattitle"];
	if ($options["emptycatmessage"] != "")
		$message = $options["emptycatmessage"];
	if ($options["emptycatmessage2"] != "")
		$message2 = $options["emptycatmessage2"];
	if ($options["emptycatmessage3"] != "")
		$message3 = $options["emptycatmessage3"];
	?>
		<article id="post-nothing">
			<div class="content-wrapper">
			<div class="summary-content">
				<div class="entry-wrapper">
					<h1 class="entry-title"><?php echo $title; ?></h1>
					<div class="entry-content">
						<p><?php echo $message; ?></p>
						
						<?php if($message2 != "" && function_exists('get_most_viewed')) : ?>
						<p><?php echo $message2; ?></p>
						<ul><?php get_most_viewed('post'); ?></ul>
						<?php endif; ?>

						<?php if($message3 != "" && get_query_var("cat") != "") : ?>
						<p><?php echo $message3; ?></p>
						<ul><?php wp_list_categories(array(
										'title_li' => "", 'child_of' => get_query_var("cat"))); ?></ul>
						<?php endif; ?>
						
					</div>
				</div>
				
			</div><!-- .summary-content -->

			</div>
		</article><!-- #post-0 -->
<?php
}

function hk_navigation() {
	global $post, $default_settings;
	
	$search = get_query_var("s");
	$cat = get_query_var("cat");
	$tags = get_query_var("tag");

	echo "<aside id='nav' class='category-navigation' role='navigation'><nav>";
	if ($search != "") {
		echo "Du s&ouml;kte p&aring; " . $search . ".";
	}
	
	if (is_single()) {
		/* get post first parents */
		$menu_name = "primary";
		$all_categories_object = get_the_category(get_the_ID());
		$all_categories = array();
		foreach ($all_categories_object as $item) { $all_categories[] = $item->cat_ID; }
		$category_hierarchy = hk_get_parent_categories_from_id(get_the_ID(), $menu_name);
		$nav_menu_top_parent = hk_getNavMenuId($category_hierarchy[0], $menu_name);
		$nav_menu_sub_parent = hk_getNavMenuId($category_hierarchy[1], $menu_name);
		$top_parent = $category_hierarchy[0];
		$sub_parent = $category_hierarchy[1];
		$category = $category_hierarchy[2];
		$rest_categories = array();
		if (!empty($all_categories) && !empty($category_hierarchy)) {
			$rest_categories = array_diff($all_categories, $category_hierarchy);
		}
		
		$hk_cat_walker = new hk_Category_Walker();
		$args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'style'              => 'list',
			'hide_empty'         => 0,
			'use_desc_for_title' => 1,
			'child_of'           => $sub_parent,
			'hierarchical'       => true,
			'title_li'           => '',
			'show_option_none'   => '',
			'echo'               => 1,
			'depth'              => 3,
			'taxonomy'           => 'category',
			'walker'			 => $hk_cat_walker,
			'current_category'	 => $category
		);
		//echo "<a class='dropdown-nav'>" . get_the_category_by_ID($category) . "</a>";
		echo "<ul class='parent'>"; 
		$p = get_the_category_by_ID($sub_parent);
		if (!empty($p))
			echo "<li class='heading cat-item $sub_parent current-cat-parent cat-has-children'><a href='#' class='cat-icon'></a><a href='".get_category_link($sub_parent)."'>".$p."</a></li>";
		wp_list_categories( $args );
		echo "</ul>"; 
		//print_r($all_categories);
		//print_r($category_hierarchy);
		//print_r($rest_categories);
		
		if (!empty($rest_categories)) {
			echo "<ul class='more-navigation'>";
			echo "<li class='heading cat-item current-cat-parent cat-has-children'><a href='#' class='cat-icon'></a><a href='#'>Artikeln ing&aring;r &auml;ven i kategorierna</a></li>";
				foreach($rest_categories as $item) {
					$cat = get_term( $item, "category");
					if (!empty($cat)) {
						echo "<li class='cat-item cat-item-" . $cat->term_id . "'><a href='" .
						hk_get_category_link( $cat ) . "' title='Visa allt om ".
						$cat->name. "'>".
						$cat->name. "</a></li>";
					}
				}
			echo "</ul>"; 
		}
	}


	// if in category
	else if ($cat != "") {

		if (is_sub_category()) {
			
			$children =  get_categories(array('child_of' => $cat, 'hide_empty' => false));
			$currentparent = $cat;
			
			$hk_cat_walker = new hk_Category_Walker();
			$parentCat = hk_getMenuParent($cat);
			$args = array(
				'orderby'            => 'name',
				'order'              => 'ASC',
				'style'              => 'list',
				'hide_empty'         => 0,
				'use_desc_for_title' => 1,
				'child_of'           => $parentCat,
				'hierarchical'       => true,
				'title_li'           => '',
				'show_option_none'   => '',
				'echo'               => 1,
				'depth'              => 3,
				'taxonomy'           => 'category',
				'walker'			 => $hk_cat_walker
			);
			
			//echo "<a class='dropdown-nav'>" . get_the_category_by_ID($parentCat) . "</a>";

			echo "<ul class='parent'>"; 
			$currentcat = '';
			if ($parentCat == $cat) {
				$currentcat = 'current-cat';
			}
			echo "<li class='heading cat-item $currentcat current-cat-parent cat-has-children'><a href='#' class='cat-icon'></a><a href='".get_category_link($parentCat)."'>".get_the_category_by_ID($parentCat)."</a></li>";
			wp_list_categories( $args );
			echo "</ul>"; 

			if( function_exists('displayTagFilter') ){
				displayTagFilter();
			}
	
		}
		
	}
	
	
	// if in tag
	else if ($tags != "") {
		//echo "<a class='dropdown-nav'>Etiketter</a>";

		$hk_cat_walker = new hk_Category_Walker();
		$parentCat = hk_getMenuParent($cat);
		$args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'style'              => 'list',
			'hide_empty'         => 0,
			'use_desc_for_title' => 1,
			'child_of'           => $parentCat,
			'hierarchical'       => true,
			'title_li'           => '',
			'show_option_none'   => '',
			'echo'               => 1,
			'depth'              => 2,
			'taxonomy'           => 'category',
			'exclude'			 => $default_settings["hidden_cat"],
			'walker'			 => $hk_cat_walker
		);
		echo "<ul class='parent'>"; 
		wp_list_categories( $args );
		echo "</ul>";
		
		if( function_exists('displayTagFilter') ){
			displayTagFilter();
		}
	}
	
	echo "&nbsp;</nav></aside>";
}

/* return description if one is set, otherwise return the normal category link */
function hk_get_category_link( $cat ) {
	return ($cat->description != "") ? $cat->description : get_category_link( $cat->term_id );
}

function hk_tag_navigation() {
	global $post, $default_settings;
	
	$search = get_query_var("s");
	$cat = get_query_var("cat");
	$tags = get_query_var("tag");

	echo "<aside id='nav' class='category-navigation' role='navigation'><nav>";
	
	//echo "<a class='dropdown-nav'>Etiketter</a>";

	
	
	if( function_exists('displayTagFilter') ){
		displayTagFilter();
	}

	
	echo "&nbsp;</nav></aside>";
}
	
// Walker class: Show which category and tag is selected
class hk_Category_Walker extends Walker_Category {
	function start_el(&$output, $category, $depth, $args) { 
        extract($args); 
		
		$tags_filter = get_query_var("tag");
		if (!empty($tags_filter)) {
			$tags_filter = "?tag=$tags_filter";
		}
				
        $cat_name = esc_attr( $category->name); 
		
		
		// set classes
		$haschildclass = "";
		if (count(get_term_children($category->term_id,"category")) > 0)
			$haschildclass = " cat-has-children";
        if ( isset($current_category) && $current_category ) 
            $_current_category = get_category( $current_category ); 
		$class = 'cat-item cat-item-'.$category->term_id.$haschildclass; 
		if ( isset($current_category) && $current_category && ($category->term_id == $current_category) ) 
			$class .=  ' current-cat'; 
		elseif ( isset($_current_category) && $_current_category && ($category->term_id == $_current_category->parent) ) 
			$class .=  ' current-cat-parent'; 
		elseif ( hk_isParentOf($_current_category->term_id, $category->term_id) ) 
			$class .=  ' current-cat-parent current-cat-grandparent'; 
		
		
		// set expandable icon
		$icon = "";
		if (count(get_term_children($category->term_id,"category")) > 0) {
			if (!strstr($class,"current-cat")) {//count(get_term_children(,"category")) > 0) {
				$icon = " +";
			}
		}
		
        $link = '<a href="' . hk_get_category_link( $category ) . $tags_filter . '" '; 
        $cat_name = apply_filters( 'list_cats', $cat_name, $category ); 
        if ( $use_desc_for_title == 0 || empty($category->description) ) 
            $link .= 'title="Visa allt om ' . $cat_name . '"'; 
        else 
            $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"'; 
        $link .= '>'; 

        $link .= $cat_name . $icon . '</a>'; 
        

        if ( isset($show_count) && $show_count ) 
            $link .= ' (' . intval($category->count) . ')'; 
 
        if ( isset($show_date) && $show_date ) { 
            $link .= ' ' . gmdate('Y-m-d', $category->last_update_timestamp); 
        } 

        if ( 'list' == $args['style'] ) { 
			

            $output .= "\t<li"; 
            
            $output .=  ' class="'.$class.'"'; 
            $output .= ">$link\n"; 
        } else { 
            $output .= "\t$link<br />\n"; 
        } 
	} 

}

// Walker class: Show which tags available to selected
class hk_Tag_Walker extends Walker_Category {
	function start_el(&$output, $tag, $depth, $args) { 
        extract($args);
		$currtagslug = $tag->slug;
		$tags_filter = get_query_var("tag");
		$term_id = hk_getMenuParent(get_query_var("cat")); // get closes menu parent
		//$term_id = get_query_var("cat");
		$orderby = $_REQUEST["orderby"];
		if ($orderby != "") {
			$orderby = "&orderby=$orderby";
		}
		if (!empty($tags_filter))
			$tag_array = explode(",",$tags_filter);
		
		if(!empty($tag_array) && in_array($currtagslug, $tag_array)) {
			$current_tag = true;
			$tags_filter = "?tag=";
		}
		else { 
			$tags_filter = "?tag=".$currtagslug;
		}

		
		// generate tag link
        $cat_name = esc_attr( $tag->name); 
		$href = get_category_link( $term_id ) . $tags_filter. $orderby;

        $link = '<a href="' . $href  . '" '; 
        $cat_name = apply_filters( 'list_cats', $cat_name, $tag ); 
        if ( $use_desc_for_title == 0 || empty($tag->description) ) 
            $link .= 'title="Filtrera med nyckelordet ' .  $cat_name . '"'; 
        else 
            $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $tag->description, $tag ) ) ) . '"'; 
        $link .= '>'; 
        $link .= $cat_name . '</a>'; 
		
		// if feed
        if ( (! empty($feed_image)) || (! empty($feed)) ) { 
            $link .= ' '; 
            if ( empty($feed_image) ) 
                $link .= '('; 
			$href = get_category_feed_link($term_id, $feed_type) . $tags_filter . $orderby;
            $link .= '<a href="' . $href . '"'; 
            if ( empty($feed) ) 
                $alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"'; 
            else { 
                $title = ' title="' . $feed . '"'; 
                $alt = ' alt="' . $feed . '"'; 
                $name = $feed; 
                $link .= $title; 
            } 

            $link .= '>'; 
            if ( empty($feed_image) ) 
                $link .= $name; 
            else 
                $link .= "<img src='$feed_image'$alt$title" . ' />'; 
            $link .= '</a>'; 
            if ( empty($feed_image) ) 
                $link .= ')'; 
        } 

		// show count
        if ( isset($show_count) && $show_count ) 
            $link .= ' (' . intval($tag->count) . ')'; 
		// show date
        if ( isset($show_date) && $show_date ) { 
            $link .= ' ' . gmdate('Y-m-d', $tag->last_update_timestamp); 
        } 
		

		if ( 'list' == $args['style'] ) { 
			$output .= "\t<li"; 
			$class = 'atag-item tag-item-'.$tag->term_id; 
			$icon = "";
			if ($current_tag) {
				$class .=  ' current-tag'; 
				$icon = "<a href='$href' class='delete-icon'></a>";
			}
			$output .=  ' class="'.$class.'"'; 
			$output .= ">$icon$link\n"; 
		} else { 
			$output .= "\t$link\n"; 
		} 

	} 

}


// show tag filter list
function displayTagFilter($show_title = true, $show_selected_tags = true, $ul_class="more-navigation", $exclude_tags = "") {
	global $default_settings;
	if ($default_settings["show_tags"] != 0) :	

		$hk_tag_walker = new hk_Tag_Walker();
		$args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'style'              => 'list',
			'hide_empty'         => 1,
			'use_desc_for_title' => 1,
			'title_li'           => '',
			'show_option_none'   => '',
			'echo'               => 1,
			'taxonomy'           => 'post_tag',
			'hierarchical'		 => 0,
			'exclude'		     => $exclude_tags,
		);
		
		if ($show_selected_tags)
			$args['walker'] = $hk_tag_walker;

		echo "<ul class='$ul_class'>"; 
		if ($show_title) {
			echo "<li class='heading cat-item'><a href='#' class='tag-icon'></a><a href='#'>Visa bara</a></li>";
		}
		if ($show_selected_tags && $_REQUEST["tag"] != "" && get_query_var("cat") != "")
		{
			if( hk_getParent(get_query_var("cat")) > 0) {
				$href = get_category_link( hk_getParent(get_query_var("cat")) ) . "?tag=".$_REQUEST["tag"];
				echo "<li class='tag-item complement-italic-text'><a href='$href' title='G&aring; upp en niv&aring;'>G&aring; upp en niv&aring;</a></li>";
			}
			//$href = get_category_link( get_query_var("cat") ) . "?tag=";
			//echo "<li class='tag-item complement-italic-text'><a href='$href' title='Rensa valt alternativ'>Rensa</a></li>";
		}
		wp_list_categories( $args );
		echo "</ul>";
	endif;
}


?>