<?php


/* REGISTER post_type hk_bubble */
add_action('init', 'hk_bubble_init');
function hk_bubble_init() {
	// only if in admin and is administrator
    //if (is_admin() && current_user_can("administrator")) {
        $user_can_edit = hk_perm::bubble();

		register_post_type( 'hk_bubble',
			array(
				'labels' => array(
					'name' => __( 'Bubblare' ),
					'singular_name' => __( 'Bubblare' ),
					'description' => 'L&auml;gg till ett bubblare.'
				),
				'has_archive' => true,
				'rewrite' => array('slug' => 'bubble'),
				'public' => false,
				'show_ui' => $user_can_edit,
				'show_in_menu' => $user_can_edit,
                'show_in_nav_menus' => false,
                'exclude_from_search' => true,
				'publicly_queryable' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'query_var' => true,
                'supports' => array('title','revisions','author','custom-fields'),
				'taxonomies' => array('category'),
                'menu_icon' => 'dashicons-format-status',
				
			));

	//}
}

function hk_bubble() {
    $ret = '';
    $cat = false;
    if (is_home()) {
        $options = get_option("hk_theme");
        $cat = (isset($options["startpage_cat"])) ? $options["startpage_cat"] : "";
    }
    else if (is_category()) {
        $cat = get_query_var( 'cat' );
    } else {
        return 'no category';
    }
    $args = array(
        'post_type' => 'hk_bubble',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
        'suppress_filters' => 'true',
        "category__and" => array($cat),
    );
    $bubble_query = new WP_Query( $args );
    if ( $bubble_query->have_posts() ) :
        
        $ret .= "<div class='quick-bubble' data-cat='$cat'>";
        $ret .= "<div class='quick-bubble-scroll'>";
        $nr = 0;
        $nav_items = '';
        if (!function_exists("get_field")) {
            return 'ACF not installed';
        }
        while ( $bubble_query->have_posts() ) : $bubble_query->the_post();
            $active_class = ($nr == 0) ? 'active' : '';
            $image_id = get_field('image');
            $video_id = get_field('video');
            $youtube = get_field('youtube');
            $youtube_src = (!empty($youtube)) ? $youtube['src'] : '';
            $youtube_type = (!empty($youtube)) ? $youtube['type'] : '';

            $ret_image = wp_get_attachment_image( $image_id, 'wide-image' );
            $ret_video = '';
            $videocssclass = "";
            $videourl = "";
            if (!empty($youtube_src)) {
                $type = 'youtube';
                if ($youtube_type == 'embed') {
                    $youtube_src = str_replace('watch?v=', 'embed/', $youtube_src);
                    $youtube_src = str_replace('youtu.be/', 'youtube.com/embed/', $youtube_src);
                    $youtube_src = add_query_arg( array('autoplay' => 1, 'mute' => 1, 'controls' => 0, 'rel' => 0, 'loop' => 1), $youtube_src );
                    //width='1138' height='326'
                    $embed = "<iframe src='$youtube_src' style='height: 326px; width: 100%;' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
                    // $embed = wp_oembed_get($youtube_url, [ 'width' => 1138, 'height' => 326 ]);
                    $ret_video = "<div class='youtube' data-url='$youtube_src'>$embed</div>";
                }
                else { // popup
                    $videocssclass = "js-video-popup";
                    $videourl = "data-video-url='$youtube_src'";
                }
                
            }
            if (empty($ret_video) && wp_attachment_is('video', $video_id)) {
                $video_url = wp_get_attachment_url( $video_id, 'wide-image' );
                $att_video = wp_get_attachment_metadata( $video_id, true);
                $video_format = $att_video['fileformat'];
                $poster_url = wp_get_attachment_url( $image_id, 'wide-image' );
                // print_r($att_video);
                $type = 'video';
                $ret_video = "<video poster='$poster_url' autoplay playsinline loop muted><source src='$video_url' type='video/$video_format'>Your browser dooes not support the video format.</video>";
            }
            $ret_media = (!empty($ret_video)) ? $ret_video : $ret_image;

            $title = get_the_title();
            $text = get_field('text');
            $url = '';
            if (have_rows('content')) {
                while (have_rows('content')) {
                    the_row();
                    
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
                    }
                }
            }

            $link_el = ($url) ? "a" : 'span';
            $title_span = "<$link_el href='$url' $target class='title'>$title</$link_el>";
            $text_span = "<$link_el href='$url' $target class='text'>$text</$link_el>";
            
            $ret .= "<div class='bubble bubble-$nr $active_class $videocssclass' data-id='$nr' $videourl>";
            $ret .= "<div class='bubble-image'>$ret_media</div>";
            $ret .= "<div class='bubble-overlay'>$title_span$text_span</div>";            
            $ret .= '</div>';

            
            $nav_items .= "<span class='nav-item nav-item-$nr $active_class' data-id='$nr'></span>";
            $nr++;
        endwhile;
        $ret .= '</div>';

        $ret .= "<div class='nav-arrows'>
            <span class='arrow-left arrow disabled'>
                <svg id='left_arrow' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><polyline points='16.99 20.59 8.31 11.92 16.99 3.24'/></svg>
            </span>
            <span class='arrow-right arrow'>
                <svg id='right_arrow' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><polyline points='8.31 3.24 16.99 11.92 8.31 20.59'/></svg>
            </span></div>";
        $ret .= "<div class='nav-items'>$nav_items</div>";
        $ret .= '</div>';
        
        
        $ret_style = "
        <style type='text/css'>
        :root {
            --hultsfred-nr-bubbles: $nr;
        }";
        if ($nr <= 1) {
            $ret_style .= "
            .quick-bubble .nav-arrows,
            .quick-bubble .nav-items {
                display: none;
            }";
        }
        $ret_style .= "
        </style>";
        wp_reset_postdata();

        return $ret_style . $ret;
    else :
        return "Inga inlägg hittades.";
    endif;
}