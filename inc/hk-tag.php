
	<div id="breadcrumb" class="breadcrumb"><?php hk_breadcrumb(); ?></div>

	<div id="primary" class="primary">

	<div id="content" class="tag-listing" role="main">

	<?php
	
		/**
		 * Default order in orderby no set
		 */
		$shownPosts = array();
		if ($tag != "") : 
		
			// get selected category if any
			if ($cat != "") {
				$top_cat_arr = array($cat);
				$cat_title = "kategori <strong>" . single_tag_title("",false) . "</strong>";
			}
			else {
				// get all categories with parent == 0
				foreach(get_categories(array('parent' => 0, 'hide_empty' => true)) as $c) :
					$top_cat_arr[] = $c->cat_ID;
				endforeach;
				$cat_title = "<strong>hela webbplatsen</strong>";
			}
			?>
			<header class="page-header">
				<ul class="num-posts">
					<?php echo "<li><a class='nolink'>Inneh&aring;ll av typen <b>" . $tag . "</b> i " . $cat_title . "</a></li>"; ?>
				</ul>
			</header>
			<?php
			// loop top categories (one or many)
			$lastsub = 0;
			foreach($top_cat_arr as $cat) :
				
				// get child categories and include parent cat in children array
				$childrenarr =  get_categories(array('child_of' => $cat, 'hide_empty' => true));
				$children = array();
				$children[] = $cat;
				foreach($childrenarr as $child) :
					$children[] = $child->cat_ID;
				endforeach;
				
				
				// get category child posts
				foreach ($children as $childcatid) :
					$childcat = get_category($childcatid);
					$args = array( 'posts_per_page' => -1,
					'ignore_sticky_posts' => 1,
					'orderby' => 'title',
					'order' => 'ASC',);
					if ($childcat->cat_ID != "")
						$args["category__and"] = array($childcat->cat_ID);
					if ($tag != "")
						$args["tag_slug__in"] = split(",",$tag);
					
					query_posts( $args );
					//print_r($childcat);
					if ( have_posts() ) : 
						$catarr[] = $childcat->slug;
						
						foreach(hk_getParentsSlugArray($childcat->cat_ID) as $slug) :
							if (!in_array($slug,$catarr)) :
								$catarr[] = $slug;
								$c = get_category_by_slug($slug);
								$sub = hk_countParents($c->cat_ID); 
								while ($sub <= $lastsub--) {
									echo "</div>";
								}
								$lastsub = $sub;
								echo "<h$sub class='indent$sub'>" . $c->name . "</h$sub>";
								echo "<div class='wrapper$sub wrapper'>";
							endif;
							
						endforeach;
						
						$sub = hk_countParents($childcat->cat_ID); 
						while ($sub <= $lastsub--) {
							echo "</div>";
						}
						$lastsub = $sub;
						echo "<h$sub class='indent$sub'>" . $childcat->name . "</h$sub>";
						echo "<div class='wrapper$sub wrapper'>";
						echo "<ul class='indent$sub'>";
						while ( have_posts() ) : the_post();
							get_template_part( 'content', 'single-line' );
							$shownPosts[] = get_the_ID();
						endwhile;
						echo "</ul>";
					endif;

					wp_reset_query(); // Reset Query

				endforeach;
				
			endforeach;
			while (0 < $lastsub--) {
				echo "</div>";
			}
			
		endif; /* end if has tag */
		
		
		
		/* helper array to know which posts is shown if loading more dynamically */
		if (empty($sticky)) {
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

		//hk_content_nav( 'nav-below' );

		/* help text if nothing is found */
		if (empty($shownPosts)) {
			hk_empty_navigation();
		} ?>

	</div><!-- #content -->

	
	
</div><!-- #primary -->