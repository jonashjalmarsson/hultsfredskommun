<?php
/*
 * Add unfiltered_html capabilities to administrator, editor and author
 */
function change_role_caps() {
	$role = get_role( 'administrator' );
	$role->add_cap( 'unfiltered_html' );

	$role = get_role( 'editor' );
	$role->add_cap( 'unfiltered_html' );
	$role->remove_cap('manage_categories');
	$role->remove_cap('manage_links');

	$role = get_role( 'author' );
	$role->add_cap( 'unfiltered_html' );
	$role->remove_cap('manage_links');


	// add new role extended author
	$role = get_role( 'editor' );
	$capabilities = $role->capabilities;
	unset($capabilities["edit_pages"]);
	unset($capabilities["edit_others_pages"]);
	unset($capabilities["edit_published_pages"]);
	unset($capabilities["publish_pages"]);
	unset($capabilities["delete_pages"]);
	unset($capabilities["delete_others_pages"]);
	unset($capabilities["delete_published_pages"]);
	unset($capabilities["delete_private_pages"]);
	unset($capabilities["edit_private_pages"]);
	unset($capabilities["delete_published_pages"]);
	add_role( "extended_author", "Ut&ouml;kad f&ouml;rfattare", $capabilities);
}
add_action( 'admin_init', 'change_role_caps');


// do on save_post
function hk_save_post($postID) {
	global $default_settings;
	$options = get_option("hk_theme");

	// skip check if trash
	$post = get_post($postID);
	if ( 'trash' === $post->post_status ) {
        return;
    }


	// TODO
	// - check image size

	// set to hidden_cat if stop publish date and time has past
	if(function_exists("register_field_group")) : // if acf plugin enabled
		if (get_field("hk_stop_publish_date") != "") {
			if (    get_field("hk_stop_publish_date") < date("Ymd",current_time('timestamp',0)) ||
					(
						( get_field("hk_stop_publish_date") == date("Ymd",current_time('timestamp',0) ) ) &&
						( get_field("hk_stop_publish_hour") < date("G",current_time('timestamp',0) ) )
					)
				) {
				$arr = wp_get_post_categories(get_the_ID());
				$arr[] = $options["hidden_cat"];
				$ret = wp_set_post_categories( get_the_ID(), $arr );
			}
		}
		
		// set to draft if hidden_cat is set
		$arr = wp_get_post_categories(get_the_ID());
		if (in_array($options["hidden_cat"], $arr)) {
			// set status to draft
			remove_action('save_post', 'hk_save_post'); // remove action temp to avoid recursion of save event
			$post = array( 'ID' => get_the_ID(), 'post_status' => 'draft' );
			wp_update_post($post);
			add_action('save_post', 'hk_save_post');
		}

	endif;
}
add_action('save_post', 'hk_save_post');


/*
 * Add reviews functionality
 * hk_reviewed - checkbox to set "now post is reviewed" (not saved)
 * hk_last_reviewed - date last time post reviewed
 * hk_next_review - date next time post should be reviewed
 * hk_review_timespan - selectable list 4 alternatives of time span to next review date (used to set hk_next_review)
 * Cron job to send mail when needed.
 */
function hk_add_review_metabox() {
    add_meta_box( 'custom-metabox', "Granska", 'hk_review_metabox', 'post', 'side', 'high' );
    add_meta_box( 'custom-metabox', "Granska", 'hk_review_metabox', 'hk_kontakter', 'side', 'high' );
    add_meta_box( 'custom-metabox', "Granska", 'hk_review_metabox', 'hk_faq', 'side', 'high' );
}
// echo review meta box with settings for is reviewed and when review next time
function hk_review_metabox() {
    global $post;
    $hk_reviewed = get_post_meta( $post->ID, 'hk_reviewed', true );
    $date = get_post_meta( $post->ID, 'hk_last_reviewed', true );
    $nextdate = get_post_meta( $post->ID, 'hk_next_review', true );
    $timespan = get_post_meta( $post->ID, 'hk_review_timespan', true );
    if ($timespan == "") $timespan = "threemonths"
    ?>
	<?php if ($date != "") : ?>
	    <p>Granskas igen <?php echo get_the_next_review_date(get_the_ID()); ?>.</p>
	<?php else : ?>
	    <p><?php echo "Inlägget har aldrig granskats."; ?></p>
	<?php endif; ?>
    <p><label for="hk_reviewed">
        <input type="checkbox" id="hk_reviewed" name="hk_reviewed">&nbsp;Jag har granskat inlägget</input></label></p>
    <p><label for="hk_review_timespan">Tid till nästa granskning
        <select type="checkbox" id="hk_review_timespan" name="hk_review_timespan">
	        <option value="week" <?php echo ($timespan == "week") ? "selected":""; ?>>Nästa vecka</option>
			<option value="month" <?php echo ($timespan == "month") ? "selected":""; ?>>Nästa månad</option>
			<option value="threemonths" <?php echo ($timespan == "threemonths") ? "selected":""; ?>>Tre månader</option>
			<option value="sixmonths" <?php echo ($timespan == "sixmonths") ? "selected":""; ?>>Sex månader</option>
			<option value="year" <?php echo ($timespan == "year") ? "selected":""; ?>>Nästa år</option>
		</select></label></p>
<?php
}

function hk_save_review_details( $post_ID ) {
    global $post;
    if( $_POST ) {
    	$date = strtotime('now');
    	$timespan = "+3 months";
    	// if is reviewed checkbox is checked
    	if ($_POST["hk_reviewed"]) {
	    	switch($_POST["hk_review_timespan"]) {
		    	case "week": $timespan = "+1 week"; break;
		    	case "month": $timespan = "+1 month"; break;
		    	case "threemonths": $timespan = "+3 months"; break;
		    	case "sixmonths": $timespan = "+6 months"; break;
		    	case "year": $timespan = "+1 year"; break;
			}
	    	$nextdate = strtotime($timespan);
	        add_post_meta( $post->ID, 'hk_last_reviewed', $date, true ) || update_post_meta( $post->ID, 'hk_last_reviewed', $date );
	        add_post_meta( $post->ID, 'hk_next_review', $nextdate, true ) || update_post_meta( $post->ID, 'hk_next_review', $nextdate );
	        add_post_meta( $post->ID, 'hk_review_timespan', $_POST["hk_review_timespan"], true ) || update_post_meta( $post->ID, 'hk_review_timespan', $_POST["hk_review_timespan"] );
    	}
    	// if no review date is set
    	if (get_post_meta( $post->ID, 'hk_last_reviewed', true) == "") {
	    	$nextdate = strtotime($timespan);
	        add_post_meta( $post->ID, 'hk_last_reviewed', $date, true ) || update_post_meta( $post->ID, 'hk_last_reviewed', $date );
	        add_post_meta( $post->ID, 'hk_next_review', $nextdate, true ) || update_post_meta( $post->ID, 'hk_next_review', $nextdate );
	        add_post_meta( $post->ID, 'hk_review_timespan', $_POST["hk_review_timespan"], true ) || update_post_meta( $post->ID, 'hk_review_timespan', $_POST["hk_review_timespan"] );
    	}
	}
}

add_action( 'admin_init', 'hk_add_review_metabox' );
add_action( 'save_post', 'hk_save_review_details' );



/*
 * Media Library and images
 */

// add custom "mime types" (file supertypes)
function filterPostMimeTypes($post_mime_types) {
    $post_mime_types['application'] = array('Document', 'Hantera documents', _n_noop('Dokument <span class="count">(%s)</span>', 'Dokument <span class="count">(%s)</span>'));
    $post_mime_types['audio'] = array('Audio', 'Hantera ljud', _n_noop('Ljud <span class="count">(%s)</span>', 'Ljud <span class="count">(%s)</span>'));
    $post_mime_types['video'] = array('Video', 'Hantera video', _n_noop('Video <span class="count">(%s)</span>', 'Video <span class="count">(%s)</span>'));
    return $post_mime_types;
}
add_filter('post_mime_types', 'filterPostMimeTypes');


// add mime extensions
function custom_upload_mimes ( $existing_mimes=array() ) {
	// add your extension to the array
	$existing_mimes['eps'] = 'image/eps';
	$existing_mimes['svg'] = 'image/svg';

	if (current_user_can('manage_options')) {
		$existing_mimes['exe'] = 'application/exe';
	}
	else {
		unset($existing_mimes['exe']);
	}
	return $existing_mimes;
}
add_filter('upload_mimes', 'custom_upload_mimes');


// Add Hultsfredskommun custom image sizes.
if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'thumbnail-image',  $default_settings['thumbnail-image'][0], $default_settings['thumbnail-image'][1], $default_settings['thumbnail-image'][2] );
	add_image_size( 'featured-image',  $default_settings['featured-image'][0], $default_settings['featured-image'][1], $default_settings['featured-image'][2] );
	add_image_size( 'featured-image-large',  $default_settings['featured-image-large'][0], $default_settings['featured-image-large'][1], $default_settings['featured-image-large'][2] );
	add_image_size( 'slideshow-image',  $default_settings['slideshow-image'][0], $default_settings['slideshow-image'][1], $default_settings['slideshow-image'][2] );
	add_image_size( 'wide-image',  $default_settings['wide-image'][0], $default_settings['wide-image'][1], $default_settings['wide-image'][2] );
	add_image_size( 'contact-image',  $default_settings['contact-image'][0], $default_settings['contact-image'][1], $default_settings['contact-image'][2] );
}

// remove width and height attr on img tags
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );




/**
 * Alter the default admin menus, settings and looks.
 * Change name of menus in admin
 */



// Change the WYSIWYG editor buttons
function hk_formatTinyMCE($in)
{
	//$in['remove_linebreaks']=false;
	//$in['gecko_spellcheck']=false;
	//$in['keep_styles']=false;
	//$in['accessibility_focus']=true;
	//$in['tabfocus_elements']='major-publishing-actions';
	//$in['media_strict']=false;
	//$in['paste_remove_styles']=true;
	//$in['paste_remove_spans']=true;
	//$in['paste_strip_class_attributes']='all';
	//$in['paste_text_use_dialog']=true;
	//$in['wpeditimage_disable_captions']=true;
	//$in['plugins']='inlinepopups,tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpfullscreen';

	//$in['content_css']=get_template_directory_uri() . "/editor-style.css";
	//$in['wpautop']=true;
	//$in['apply_source_formatting']=false;
	//$in['paste_retain_style_properties'] = 'none';
	$in['paste_text_sticky'] = true;
	$in['paste_text_sticky_default'] = true;
	//$in['theme_advanced_blockformats'] = 'p,h2,h3';
    $in['block_formats'] = 'Stycke=p; Rubrik 2=h2; Rubrik 3=h3; Markerad=pre';
	$in['theme_advanced_buttons1']='formatselect,bold,italic,removeformat,|,bullist,numlist,indent,outdent,|,charmap,|,link,unlink,|,undo,redo,|,valideratext,|,wp_fullscreen, wp_adv';
	$in['theme_advanced_buttons2']='table,row_props,cell_props,row_before,row_after,delete_row,|,col_before,col_after,delete_col,|,split_cells,merge_cells';
	$in['theme_advanced_buttons3']='';
	$in['theme_advanced_buttons4']='';
	return $in;
}
add_filter('tiny_mce_before_init', 'hk_formatTinyMCE' );


/**
 * Dashboard setup
 */

// dashboard cleanup
function hk_cleanup_dashboard()
{
	global $wp_meta_boxes, $current_user;
	echo "<style>";
	echo "#hk_myhidden_dashboard_widget .inside,
		#hk_mylatestposts_dashboard_widget .inside,
		#hk_mycomingreviews_dashboard_widget .inside,
		#hk_allcomingreviews_dashboard_widget .inside,
		#hk_alllatestposts_dashboard_widget .inside,
		#hk_allhidden_dashboard_widget .inside {
			height: 220px;
			overflow: auto;
		}";
	echo "</style>";
	//Right Now - Comments, Posts, Pages at a glance
	//unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	//Wordpress Development Blog Feed
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	//Other Wordpress News Feed
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	//Quick Press Form
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	//Recent Drafts List
	//unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);


	//My posts to review
	wp_add_dashboard_widget('hk_mycomingreviews_dashboard_widget', 'Mina granskningar', 'hk_display_mycomingreviews_dashboard_widget' );
	//My latest modified posts
	wp_add_dashboard_widget('hk_mylatestposts_dashboard_widget', 'Mina senaste ändringar', 'hk_display_mylatestposts_dashboard_widget' );
	//All my hidden posts
	wp_add_dashboard_widget('hk_myhidden_dashboard_widget', 'Mina ej synliga sidor', 'hk_display_myhidden_dashboard_widget' );


	// remove incoming links info for authors or editors
	if (!current_user_can('administrator'))
	{
		//Incoming Links
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		//Plugins - Popular, New and Recently updated Wordpress Plugins
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		//Recent Comments
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	}
    if (current_user_can('administrator')) {
		//All posts to review
		wp_add_dashboard_widget('hk_allcomingreviews_dashboard_widget', 'Alla kommande granskningar', 'hk_display_allcomingreviews_dashboard_widget' );
		//All latest modified posts
		wp_add_dashboard_widget('hk_alllatestposts_dashboard_widget', 'Alla senaste ändringar', 'hk_display_alllatestposts_dashboard_widget' );
		//All hidden posts
		wp_add_dashboard_widget('hk_allhidden_dashboard_widget', 'Alla ej synliga sidor', 'hk_display_allhidden_dashboard_widget' );
	}
}


// function to display my coming reviews dashboard widget
function hk_display_allcomingreviews_dashboard_widget ()
{
	$options = get_option('hk_theme');

	if ($options["no_reviews_to_cat"] != "")
		$ignore_cats = explode(",",$options["no_reviews_to_cat"]);
	else
		$ignore_cats = array();

	$ignore_cats[] = $options["hidden_cat"];

	//define arguments for WP_Query()
	$qargs = array(
        'category__not_in' => $ignore_cats,
		'posts_per_page' => -1,
		'meta_key' => 'hk_next_review',  // which meta to query
		'meta_value'   => strtotime("+1 day"),  // value for comparison
		'meta_compare' => '<',          // method of comparison
		'meta_type' => 'numeric',
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter','hk_faq'),
		'orderby' => 'meta_value',
		'order' => 'ASC',
		'ignore_sticky_posts' => 1
		);
	// perform the query
	$q = new WP_Query();
	$q->query($qargs);

	//echo Date("H:i:s",$options["hk_review_mail_check_time"]) . "<br>" . $options["hk_review_mail_log"] . "<br><br><br>";

	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Granskas igen</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_next_review_date(get_the_ID())."</span></li>" );
	endwhile;
	echo '</ul>';
}
// function to display my latest dashboard widget
function hk_display_alllatestposts_dashboard_widget()
{
	//define arguments for WP_Query()
	$qargs = array(
		'posts_per_page' => 10,
		'orderby' => 'modified_date',
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter'),
		'order' => 'DESC'
	);
	// perform the query
	$q = new WP_Query();
	$q->query($qargs);

	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Ändrades senast</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_modified_date()."</span></li>" );
	endwhile;
	echo '</ul>';

}
// function to display my hidden posts dashboard widget
function hk_display_allhidden_dashboard_widget ()
{
	global $default_settings;

	//define arguments for WP_Query()
	$qargs = array(
		'category__in' => array($default_settings["hidden_cat"]),
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter'),
		'posts_per_page' => -1
		);
	// perform the query
	$q = new WP_Query();
	$q->query($qargs);


	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Ändrades senast</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_modified_date()."</span></li>" );
	endwhile;
	echo '</ul>';
}
// function to display my coming reviews dashboard widget
function hk_display_mycomingreviews_dashboard_widget ()
{
	global $default_settings;

	$options = get_option('hk_theme');

	if ($options["no_reviews_to_cat"] != "")
		$ignore_cats = explode(",",$options["no_reviews_to_cat"]);
	else
		$ignore_cats = array();

	$ignore_cats[] = $options["hidden_cat"];


	//define arguments for WP_Query()
	$qargs = array(
		'category__not_in' => $ignore_cats,
		'author'=> get_current_user_id(),
		'posts_per_page' => 10,
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter','hk_faq'),
		'orderby' => 'meta_value',
		'meta_key' => 'hk_next_review',
		'order' => 'ASC' );

	// perform the query
	$q = new WP_Query();
	$q->query($qargs);


	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Granskas igen</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_next_review_date(get_the_ID())."</span></li>" );
	endwhile;
	echo '</ul>';
}
// function to display my latest dashboard widget
function hk_display_mylatestposts_dashboard_widget()
{
	global $default_settings;

	//define arguments for WP_Query()
	$qargs = array(
		'category__not_in' => array($default_settings["hidden_cat"]),
		'author'=> get_current_user_id(),
		'posts_per_page' => 10,
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter','hk_faq'),
		'orderby' => 'modified_date',
		'order' => 'DESC'
	);
	// perform the query
	$q = new WP_Query();
	$q->query($qargs);

	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Ändrades senast</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_modified_date()."</span></li>" );
	endwhile;
	echo '</ul>';

}
// function to display my hidden posts dashboard widget
function hk_display_myhidden_dashboard_widget ()
{
	global $default_settings;

	//define arguments for WP_Query()
	$qargs = array(
		'author'=> get_current_user_id(),
		'category__in' => array($default_settings["hidden_cat"]),
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'post_type' => array('post','hk_kontakter'),
		);
	// perform the query
	$q = new WP_Query();
	$q->query($qargs);


	// execute the WP loop
	echo '<ul>';
	echo '<li>&nbsp; <span class="alignright">Ändrades senast</span></li>';
	while ($q->have_posts()) : $q->the_post();
		edit_post_link( get_the_title(), "<li>", "<span class='alignright'>".get_the_modified_date()."</span></li>" );
	endwhile;
	echo '</ul>';
}

//add our function to the dashboard setup hook
add_action('wp_dashboard_setup', 'hk_cleanup_dashboard');



// remove links/menus from the admin bar
function hk_admin_bar_render() {
	global $wp_admin_bar;

}
add_action( 'wp_before_admin_bar_render', 'hk_admin_bar_render' );


/* change names in admin menus */
function change_post_menu_label() {
    global $menu;
	global $submenu;

    // hide tag divs if not administrator
    if (!current_user_can("administrator")) {
        // hide tools menu
	    unset($menu[75]);
	}
}

function hk_change_meta_boxes () {
    // hide meta tag boxes in edit
    // hide tag divs if not administrator
 	//remove_meta_box("tagsdiv-post_tag","post",'side');

    echo '';
}


if (is_admin()) {
	add_action( 'add_meta_boxes', 'hk_change_meta_boxes' );
	add_action( 'admin_menu', 'change_post_menu_label' );
}

function remove_media_upload_fields( $form_fields, $post ) {
    //unset( $form_fields['image-size'] );
    unset( $form_fields['post_excerpt'] );
    //unset( $form_fields['image_alt'] );
    //unset( $form_fields['post_content'] );
    //unset( $form_fields['url'] );
    //unset( $form_fields['image_url'] );
    //unset( $form_fields['align'] );

    return $form_fields;
}
add_filter('attachment_fields_to_edit', 'remove_media_upload_fields', null, 2);


function my_plugin_image_tabs($_default_tabs) {

    //unset($_default_tabs['type']);
    //unset($_default_tabs['type_url']);
    //unset($_default_tabs['gallery']);

	return($_default_tabs);
}
add_filter('media_upload_tabs', 'my_plugin_image_tabs', 10, 1);



/*
 * Extra filter dropdown in admin
 * more info here about adding adminpostfilter:
 * http://wordpress.stackexchange.com/questions/16637/how-to-filter-post-listing-in-wp-dashboard-posts-listing-using-a-custom-field
 */

add_filter( 'parse_query', 'hk_admin_posts_filter' );
add_action( 'restrict_manage_posts', 'hk_admin_posts_filter_restrict_manage_posts' );

function hk_admin_posts_filter( $query )
{
    global $pagenow;
    if ( is_admin() && $pagenow=='edit.php' && isset($_REQUEST['author']) && $_REQUEST['author'] != '') {
        $query->query_vars['author'] = $_REQUEST['author'];
	}
}

function hk_admin_posts_filter_restrict_manage_posts()
{
    global $wpdb;
    $sql = 'SELECT DISTINCT ID, user_nicename FROM '.$wpdb->users.'';
    $fields = $wpdb->get_results($sql, ARRAY_N);
?>
<select name="author">
<option value="">Författare</option>
<?php
    $current = isset($_REQUEST['author']) ? $_REQUEST['author'] : '';
    foreach ($fields as $field) {
        if (substr($field[0],0,1) != "_"){
        printf
            (
                '<option value="%s"%s>%s</option>',
                $field[0],
                $field[0] == $current? ' selected="selected"':'',
                $field[1]
            );
        }
    }
?>
</select>
<?php
}






/* checks before headers is sent, also add categories and tags to media */
add_action('init', 'hk_init');
function hk_init() {

	/* register category and tags for attachment */
	//register_taxonomy_for_object_type('category', 'attachment');
	//register_taxonomy_for_object_type('post_tag', 'attachment');

	/* remove unwanted fields from media library items */
	//remove_post_type_support("attachment","content");

}




/*
 * Disable easter egg
 */
 add_action('admin_action_diff', function() {
	$left = empty( $_GET['left'] ) ? '' : $_GET['left'];
	$right = empty( $_GET['right'] ) ? '' : $_GET['right'];

	if ($left == $right) {
	   wp_die('Fel val, det g&aring;r inte j&auml;mf&ouml;ra versionen med sig sj&auml;lv.'.
				'<br /><a href="'.get_edit_post_link($left).'">Tillbaka</a>');
	}
});

/*
 * POINTER HELPERS
 */

add_action( 'admin_enqueue_scripts', 'hk_admin_enqueue_scripts' );
$seenit_id = 'hkseenit1'; // filter by writer
$seenit_id2 = 'hkseenit2'; // replace media
function hk_admin_enqueue_scripts() {
	global $seenit_id, $seenit_id2;
    // find out which pointer IDs this user has already seen
    $seen_it = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
    // at first assume we don't want to show pointers
    $do_add_script = false;
	$do_add_script2 = false;

	// Handle our first pointer announcing the plugin's new settings screen.
	// check for dismissal of pksimplenote settings menu pointer 'hkseenit'
	if ( ! in_array( $seenit_id, $seen_it ) ) {
	   $do_add_script = true;
	} // end if
	if ( ! in_array( $seenit_id2, $seen_it ) ) {
	   $do_add_script2 = true;
	} // end if

	// if not seen, then show
	if ( $do_add_script || $do_add_script2 ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		if ($do_add_script)
			add_action( 'admin_print_footer_scripts', 'my_admin_print_footer_scripts' );
		if ($do_add_script2)
			add_action( 'admin_print_footer_scripts', 'my_admin_print_footer_scripts2' );
	}
}

function my_admin_print_footer_scripts() {
	global $seenit_id;
    $pointer_content = '<h3>Filtrera på författare</h3>';
    $pointer_content .= '<p>Du kan nu filtrera fram dina inlägg genom att välja dig själv som författare och klicka på knappen filtrera.</p>';
?>
   <script type="text/javascript">
   //<![CDATA[
   jQuery(document).ready( function($) {
    $('.tablenav .actions:nth-child(2) select:nth-child(3)').pointer({
        content: '<?php echo $pointer_content; ?>',
        position: 'top',
        close: function() {
			$.post( ajaxurl, {
				pointer: '<?php echo $seenit_id; ?>',
				action: 'dismiss-wp-pointer'
			});        }
		}).pointer('open');
	});
   //]]>
   </script>

<?php

}
function my_admin_print_footer_scripts2() {
	global $seenit_id2;
    $pointer_content = '<h3>Ersätt bild eller dokument</h3>';
    $pointer_content .= '<p>Nu går det ersätta en befintlig fil utan att bryta alla länkar. Använd knappen <i>Ladda upp en ny fil</i> och välj om du vill ersätta med en ny fil och behålla namnet, eller om du vill byta både filnamn och fil helt och hållet.</p>';
?>
   <script type="text/javascript">
   //<![CDATA[
   jQuery(document).ready( function($) {
    $('.enable-media-replace .button-secondary').pointer({
        content: '<?php echo $pointer_content; ?>',
        position: 'top',
        close: function() {
			$.post( ajaxurl, {
				pointer: '<?php echo $seenit_id2; ?>',
				action: 'dismiss-wp-pointer'
			});        }
		}).pointer('open');
	});

   //]]>
   </script>

<?php

}




?>
