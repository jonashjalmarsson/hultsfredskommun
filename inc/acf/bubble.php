<?php

    acf_add_local_field_group(array(
        'key' => 'group_6318bdea889d0',
        'title' => 'Bubble',
        'fields' => array(
            array(
                'key' => 'field_6318dec7a7399',
                'label' => 'Text',
                'name' => 'text',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '100%',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'rows' => '',
                'new_lines' => 'wpautop',
            ),
            array (
                'key' => 'field_56bb0a146183b',
                'label' => 'Bild',
                'name' => 'image',
                'type' => 'image',
                'instructions' => 'V&auml;lj en bild f&ouml;r inneh&aring;llet.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '50%',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'id',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array (
                'key' => 'field_56bb0a146183c',
                'label' => 'Film (1138x326)',
                'name' => 'video',
                'type' => 'file',
                'instructions' => 'V&auml;lj en film f&ouml;r inneh&aring;llet.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '50%',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'id',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array (
                'key' => 'field_56bb0a416183d',
                'label' => 'Länka till',
                'name' => 'content',
                'type' => 'flexible_content',
                'instructions' => 'Till vilket inneh&aring;ll ska puffen peka.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '50%',
                    'class' => '',
                    'id' => '',
                ),
                'button_label' => 'Länka till',
                'min' => 0,
                'max' => 1,
                'layouts' => array (
                    
                    array (
                        'key' => '66bb0f1acd048',
                        'name' => 'inlagg',
                        'label' => 'Inlägg',
                        'display' => 'block',
                        'sub_fields' => array (
                            array (
                                'key' => 'field_66bb0f1acd04d',
                                'label' => 'Post',
                                'name' => 'post',
                                'type' => 'post_object',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'post_type' => array (
                                    0 => 'post',
                                ),
                                'taxonomy' => array (
                                ),
                                'allow_null' => 0,
                                'multiple' => 0,
                                'return_format' => 'object',
                                'ui' => 1,
                            ),
                        ),
                        'min' => '',
                        'max' => '',
                    ),
                    array(
                        'key' => '66bb0f17cd046',
                        'name' => 'category',
                        'label' => 'Kategori',
                        'display' => 'block',
                        'sub_fields' => array (
                            array(
                                'key' => 'field_7307362bf26db',
                                'label' => 'Kategori',
                                'name' => 'category',
                                'type' => 'taxonomy',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'taxonomy' => 'category',
                                'field_type' => 'select',
                                'allow_null' => 0,
                                'add_term' => 0,
                                'save_terms' => 0,
                                'load_terms' => 0,
                                'return_format' => 'id',
                                'multiple' => 0,
                            ),
                        ),
                        'min' => '',
                        'max' => '',
                    ),
                    array (
                        'key' => '66bb0f17cd042',
                        'name' => 'extern',
                        'label' => 'Extern',
                        'display' => 'block',
                        'sub_fields' => array (
                            array (
                                'key' => 'field_56bb0f17cd005',
                                'label' => 'Extern URL',
                                'name' => 'extern',
                                'type' => 'text',
                                'instructions' => 'Ange URL till extern sida, inklusive https://',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),
                        ),
                        'min' => '',
                        'max' => '',
                    ),

                ),
            ),
            array(
                'key' => 'field_63ed1e9ac9880',
                'label' => 'Youtube',
                'name' => 'youtube',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => 'Bädda in eller länka till en film från Youtube.',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50%',
                    'class' => '',
                    'id' => '',
                ),
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_63ed1ed5c9882',
                        'label' => 'Src',
                        'name' => 'src',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'maxlength' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                    ),
                    array(
                        'key' => 'field_63ed1eaac9881',
                        'label' => 'Type',
                        'name' => 'type',
                        'aria-label' => '',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'embed' => 'Bädda in',
                            'popup' => 'Popup',
                        ),
                        'default_value' => false,
                        'return_format' => 'value',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'ui' => 0,
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                ),
            ),

        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'hk_bubble',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(
            0 => 'the_content',
            1 => 'excerpt',
            2 => 'discussion',
            3 => 'comments',
            4 => 'format',
            5 => 'page_attributes',
            6 => 'featured_image',
            7 => 'tags',
            8 => 'send-trackbacks',
        ),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
    