<?php

// Lägg till en ny kolumn
function add_acf_column_to_kontakter($columns) {
    $columns['telefonnummer'] = '0495-24';
    return $columns;
}
add_filter('manage_hk_kontakter_posts_columns', 'add_acf_column_to_kontakter');

// Fyll kolumnen med ACF data
function acf_column_for_kontakter_show_data($column, $post_id) {
    global $hk_contacts_helper;
    switch ($column) {
        case 'telefonnummer':
            // Kontrollera om telefonnumret matchar mönstret
            echo $hk_contacts_helper->getStatusNumberInContact($post_id);
            
            break;
    }
}
add_action('manage_hk_kontakter_posts_custom_column', 'acf_column_for_kontakter_show_data', 10, 2);


