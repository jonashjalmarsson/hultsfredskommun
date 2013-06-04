	<?php if (!is_sub_category_firstpage()) :
			if ($cat != "") : 
				// do query
				$args = array(	paged => $paged,
								category__and => array($cat));
				$options = get_option("hk_theme");
				if ($_REQUEST["orderby"] == "" && get_query_var("cat") != "" && in_array(get_query_var("cat"), split(",",$options["order_by_date"])) ) {
					$args['suppress_filters'] = 'true';
					$args['orderby'] = 'date';
					$args['order'] = 'desc';
				}
	
				/*$args['meta_query'] = array(
				   array(
					   'key' => 'hide_from_category',
					   'compare' => '==',
					   'value' => '1',
					   //'compare' => '=',
					   //'type' => 'NUMERIC',
				   )
				);*/
				query_posts( $args );
			endif;
	?>
	<div id="breadcrumb" class="breadcrumb"><?php hk_breadcrumb(); ?></div>
	<?php endif; ?>

	<div id="primary" class="primary">

	<div id="content" role="main">


		<header class="page-header">
			
			<ul class="num-posts">
				<?php  			
					echo "<li><a class='nolink'>Visar " . $wp_query->post_count;
					if ($wp_query->max_num_pages > 1) {
						echo " av " . $wp_query->found_posts;
					}
					if ($wp_query->post_count <= 1) 
						echo " artikel";
					else
						echo " artiklar";
					if ($wp_query->max_num_pages > 1) {
						if ($paged == 0) { $p = 1; } else { $p = $paged; }
						echo " | Sida " . $p . " av " . $wp_query->max_num_pages . "";
					}
					echo "</a></li>";
				?>
			</ul>
				<?php //echo "<pre>"; print_r($wp_query); echo "</pre>"; ?>

			<ul class="category-tools">
				<?php /* if (related stuff ($cat)) : */ ?>
				<?php echo hk_related_output(true,false);	?>
				<?php /* endif; */ ?>
				
			</ul>
			<ul class="view-tools">
				<li class="menu-item view-mode"> 
				<a class="viewmode_titles js-view-titles active" title="Listvisning" href="#"></a>
				<a class="viewmode_summary js-view-summary" title="Kompakt visning" href="#"></a>
				</li>
			</ul>
			<?php 
				if( function_exists('displaySortOrderButtons') ){
					displaySortOrderButtons();
				} 
			?>

		</header>
		
	<?php
		/**
		 * Default order in orderby no set
		 */
		$shownPosts = array();
		if ($cat != "") : 
			if ( have_posts() ) : while ( have_posts() ) : the_post();
				if ($wp_query->post_count == 1)
					get_template_part( 'content', 'single' );
				else
					get_template_part( 'content', get_post_type() );
				$shownPosts[] = get_the_ID();
			endwhile; endif;
			//echo "<span class='hidden debug'>all from this category . <br>".print_r($args,true)."</span>";

			//wp_reset_query(); // Reset Query
			
			
		endif;
		
		
		
		/* helper array to know which posts is shown if loading more dynamically */
		/*if (empty($sticky)) {
			$allposts = $shownPosts;
		}
		else if (empty($shownPosts)) {
			$allposts = $sticky;
		}
		else if (empty($shownPosts) && empty($sticky)) {
			$allposts = array();
		}
		else {
			$allposts = array_merge($sticky,$shownPosts);
		}
		if (!empty($allposts))
			$allposts = implode(",",$allposts);
		else
			$allposts = "";
			
		echo "<span id='shownposts' class='hidden'>" . $allposts . "</span>";
		*/
		hk_content_nav( 'nav-below' );

		/* help text if nothing is found */
		if (empty($shownPosts)) {
			hk_empty_navigation();
		} ?>

	</div><!-- #content -->

	
	
</div><!-- #primary -->