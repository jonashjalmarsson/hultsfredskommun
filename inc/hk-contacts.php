<?php

/* 
 * Description: Add Contact widget and contact post_type
 *  */


/* WIDGET */
class Hk_Contacts extends WP_Widget {

        public function __construct() {
		parent::__construct(
	 		'hk_contacts', // Base ID
			'Hk_Contacts', // Name
			array( 'description' => __( 'Contact Widget to display contacts from selected category', 'text_domain' ), ) // Args
		);
	}

 	public function form( $instance ) {

	}

	public function update( $new_instance, $old_instance ) {
		return $old_instance;
	}

	public function widget( $args, $instance ) {
	       	extract( $args );
		/* EV TODO : SOME CACHE
		$option_name = 'hk_contacts_cache';
		// output is generate on post_save
		$cache = get_option( $option_name );
		if ($cache != "") {
		   	//echo $cache;
			echo "not using cache riht now";
			echo hk_contacts_generate_cache();
		}
		else
		{
		*/
			echo hk_contacts_generate_cache();
    		//}
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget( "Hk_Contacts" );' ) );





/* REGISTER post_type hk_kontakter */
add_action('init', hk_contacts_init);
function hk_contacts_init() {

	register_post_type( 'hk_kontakter',
		array(
			'labels' => array(
				'name' => __( 'Kontakter' ),
				'singular_name' => __( 'Kontakt' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'kontakt')
		)
	);
	add_post_type_support( "hk_kontakter", "title" );
	add_post_type_support( "hk_kontakter", "editor" );
	add_post_type_support( "hk_kontakter", "author" );
	add_post_type_support( "hk_kontakter", "thumbnail" );
	//add_post_type_support( "hk_kontakter", "excerpt" );
	//add_post_type_support( "hk_kontakter", "trackbacks" );
	//add_post_type_support( "hk_kontakter", "custom-fields" );
	add_post_type_support( "hk_kontakter", "revisions" );

	register_taxonomy_for_object_type( "category", "hk_kontakter" );
	register_taxonomy_for_object_type( "post_tag", "hk_kontakter" );

}


// generate cache on save_post
add_action('save_post', hk_contacts_save);
function hk_contacts_save($postID) {

/* EV TODO
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $postID;
    }
    else
    {
	$post_type = get_post_type($postID);

	// only on hk_kontakter save
	if ($post_type == 'hk_kontakter') {
	   // set option
	   $option_name = 'hk_contacts_cache';

	   $newcache = hk_contacts_generate_cache();
	   //delete_option( $option_name);
	   update_option( $option_name, $newcache ) && add_option( $option_name, $newcache, '', 'no' );
	}
    }
*/
}

function hk_contacts_generate_cache() {

	$retValue = "";
	// outputs the content of the widget

 	$selected_categories = single_cat_title( '', false);
	if ($selected_categories != "")
    	{
		$cat = get_category_by_slug($selected_categories);
		$category_in = array($cat->term_id);
    	}

    	else
    	{
		$category_in = array();
        	foreach(get_the_category() as $cat)
        	{
			$category_in[] = $cat->term_id;
		}
  	}


	$args = array(
		'posts_per_page' => 3,
		'paged' => 1,
		'more' => $more = 0,
		'post_type' => 'hk_kontakter',
		'category__in' => $category_in,
		'order' => 'ASC',
		'suppress_filters' => 1
	);

 	if ($args != "")
  	{
		// search in all posts (ignore filters)
		$the_query = new WP_Query( $args );

		if ($the_query->have_posts())
		{ 
  	        $retValue .= "<aside class='widget'>";
	      	$retValue .= "<h3 class='widget-title'>Kontakter</h3>";				    // The Loop
	   		while ( $the_query->have_posts() ) : $the_query->the_post();
				$retValue .= "<div id='contact-" . get_the_ID() . "' class='" . implode(" ",get_post_class()) . "'>";
				$retValue .= get_the_post_thumbnail(get_the_ID(),"contact-image",array("class"=>"alignleft"));

				//$retValue .= "<h4>" . get_the_title() . "</h4>";
				$retValue .= str_replace("\n","<br>",get_the_content());
				$retValue .= "</div>";
	    	endwhile;
	    	// Reset Post Data
	    	wp_reset_postdata();
			$retValue .= "</aside>";
		}
	}

	return $retValue;

}
?>

