<?php

class HK_Contacts_Helper {

    public function __construct() {
        // add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
    }

    public function register_settings() {
        // register_setting( 'hk-contacts-helper', 'hk_contacts_helper_settings' );
    }

    public function add_menu_page() {
        add_submenu_page( 'edit.php?post_type=hk_kontakter', 'Helper', 'Helper', 'manage_options', 'hk-contacts-helper', array( $this, 'menu_page' ) );
    }

    public function menu_page() {
        ?>
        <div class="wrap">
            <h2>Helper</h2>
            <p>Helper page content...</p>
            <?php 
                $all_contacts = $this->getAllContacts();
                $found = $this->findAllPostsWithNumber();
                $posts = $found['posts'];
                $active_contacts = $found['active_contacts'];
                $active_html = "";
                $inactive_html = "";
                $num_active = count($active_contacts);
                $num_inactive = count($all_contacts) - $num_active;
                foreach($all_contacts as $contact) {
                    $id = key($contact);
                    $value = $contact[$id];
                    $is_used = get_field('hk_contact_is_used', $id);
                    $is_used_echo = $is_used ? "true" : "false";
                    if (in_array($id, $active_contacts)) {
                        
                        $active_html .= "<p><a style='color: green;' target='_blank' href='post.php?post=" . $id . "&action=edit'>" . $value . " (" . $id . ")</a></p>";
                    }
                    else {
                        $inactive_html .= "<p><a style='color: red;' target='_blank' href='post.php?post=" . $id . "&action=edit'>" . $value . " (" . $id . ")</a></p>";
                    }
                }
                echo "<div style='display: grid; grid-template-columns: 1fr 1fr;'>";
                echo "<div>";
                echo "<h3>Active contacts (" . $num_active . ")</h3>";
                echo $active_html;
                echo "</div>";
                echo "<div>";
                echo "<h3>Inactive contacts (" . $num_inactive . ")</h3>";
                echo $inactive_html;
                echo "</div>";
                echo "</div>";
                
                echo "<h3>Active shortcodes</h3>";
                foreach($found['active_shortcodes'] as $post_id => $shortcode) {
                    echo "<p><b>Post: " . $post_id . "</b><br>" . implode("<br> ", $shortcode) . "</p>";
                }

                echo "<h3>Posts with number</h3>";
                foreach($posts as $post) {
                    echo "<p>" . $post['id']['title'] . " (" . $post['id']['id'] . ")<br>";
                    if (!empty($post['id']['number'])) {
                        echo "Number: " . implode(", ", $post['id']['number']) . "<br>";
                    }
                    if (!empty($post['id']['shortcode'])) {
                        echo "Shortcode: " . implode(", ", $post['id']['shortcode']) . "<br>";
                    }
                    echo "</p>";
                }
            ?>
        </div>
        <?php
    }

    public function getAllContacts() {
        $args = array(
            'post_type' => 'hk_kontakter',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        );
        $contacts = get_posts( $args );
        // map posts to [id => title]
        $contacts = array_map(function($contact) {
            update_field('hk_contact_is_used', false, $contact->ID); // reset is used
            return [$contact->ID => $contact->post_title];
        }, $contacts);
        return $contacts;
    }

    public function findAllPostsWithNumber() {
        // Shortcode kontakt, and check for attr id, kontaktnamn, kategori, kategorinamn
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        );

        $posts = get_posts( $args );
        $posts_with_number = array();
        $active_contacts = array();
        $active_shortcodes = array();
        foreach ( $posts as $post ) {
            $post_content = $post->post_content;
            // $post_content_array[] = $post_content;
            // echo "post id: " . $post->ID . "<br />";

            $shortcode = array();
            if ( strpos( $post_content, '[kontakt' ) !== false ) {
                // find attributes between [kontakt and ]
                $pattern = '/\[kontakt(.*?)\]/';
                preg_match_all( $pattern, $post_content, $matches );
                $shortcodes = $matches[0];
                $active_shortcodes[$post->ID] = $shortcodes;
                foreach($shortcodes as $shortcode) {
                    // get id value from shortcode
                    $pattern = '/id="(.*?)"/';
                    preg_match_all( $pattern, $shortcode, $matches );
                    $id = $matches[1][0];
                    if (!empty($id)) {
                        update_field('hk_contact_is_used', true, $id); // set is used
                        if (!in_array($id, $active_contacts)) {
                            $active_contacts[] = $id;
                        }
                        continue;
                    }
                    // get kontaktnamn value from shortcode
                    $pattern = '/kontaktnamn="(.*?)"/';
                    preg_match_all( $pattern, $shortcode, $matches );
                    $kontaktnamn = $matches[1][0];
                    // get hk_kontakter post with title = kontaktnamn
                    if (!empty($kontaktnamn)) {
                        $get_post_args =array(
                            'name' => $kontaktnamn,
                            'post_type' => 'hk_kontakter',
                            'post_status' => 'publish',
                            'numberposts' => 1
                        );
                        $contacts = get_posts( $get_post_args );
                        if (!empty($contacts)) {
                            $id = $contacts[0]->ID;
                            $contact = $contacts[0];
                            $id = $contact->ID;
                            update_field('hk_contact_is_used', true, $id); // set is used
                            if (!in_array($id, $active_contacts)) {
                                $active_contacts[] = $id;
                            }
                        }
                    }
                }
            }

            $number = array();

            // if( get_field('hk_contact_phones', $post->ID) ) {
            //     echo "has field";
            //     while( has_sub_field('hk_contact_phones') ) {
            //         $nr = get_sub_field('number');
            //         // $type = get_row_layout();
            //         $number[] = $nr;
            //     }
            // }
            while( has_sub_field('hk_contacts', $post->ID) ) {
                $value = get_sub_field('hk_contact', $post->ID);
                if (!empty($value)) {
                    $number[] = $value->ID;
                    update_field('hk_contact_is_used', true, $value->ID); // set is used
                    if (!in_array($value->ID, $active_contacts)) {
                        $active_contacts[] = $value->ID;
                    }
                }
            }

            if ( !empty( $number ) || !empty( $shortcode ) ) {
                $posts_with_number[$post->ID]['id'] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'shortcode' => $shortcode,
                    'number' => $number,
                );
            }
                    
        }

        return ['posts' => $posts_with_number,
                'active_contacts' => $active_contacts,
                'active_shortcodes' => $active_shortcodes
            ];

    }

    public function checkIfValidNumberInContact($post_id) {
        $number = array();
        $is_old = false;
        $is_new = false;
        $has_number = false;
        $is_used = get_field('hk_contact_is_used', $post_id);
        if( get_field('hk_contact_phones', $post_id) ) {
            $has_number = true;
            while( has_sub_field('hk_contact_phones') ) {
                $nr = get_sub_field('number');
                // check if number contains 0495-24, 0495 24, 0495- 24, 0495 -24, 0495 - 24, 049524
                $pattern = '/0495\s*-?\s*24/';
                if (preg_match($pattern, $nr)) {
                    $is_old = true;
                }
                // check if begin with 010
                $pattern = '/^010/';
                if (preg_match($pattern, $nr)) {
                    $is_new = true;
                }
            }
        }
        return ['has_number' => $has_number, 'is_old' => $is_old, 'is_new' => $is_new, 'is_used' => $is_used];
    }
    public function getStatusNumberInContact($post_id) {
        $status = $this->checkIfValidNumberInContact($post_id);
        $has_number = $status['has_number'];
        $is_old = $status['is_old'];
        $is_new = $status['is_new'];
        $is_used = $status['is_used'];
        $status = "";
        $color = $is_used ? "green" : "red";
        if ($has_number) {
            if ($is_old) {
                $status = "Gammalt";
            }
            else if ($is_new) {
                $status = "Nytt";
            }
            else {
                $status = "Annat";
            }
        }
        else {
            $status = "Inget nummer";
        }
        return "<span style='color: {$color}'>{$status}</span>";
    }

}
$hk_contacts_helper = new HK_Contacts_Helper();


