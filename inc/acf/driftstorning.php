<?php

acf_add_local_field_group(array(
    'key' => 'group_6301d8a12abe7',
    'title' => 'Driftstörningar',
    'fields' => array(
        array(
            'key' => 'field_6301d8a9078e8',
            'label' => 'Driftstörning',
            'name' => 'driftstorning',
            'type' => 'flexible_content',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'layouts' => array(
                'layout_6301da735f257' => array(
                    'key' => 'layout_6301da735f257',
                    'name' => 'dritstorning',
                    'label' => 'Dritstörning',
                    'display' => 'block',
                    'sub_fields' => array(

                        array(
                            'key' => 'field_6301da8016561',
                            'label' => 'Rubrik',
                            'name' => 'title',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '90%',
                                'class' => '',
                                'id' => '',
                            ),
                            'default_value' => '',
                            'placeholder' => '',
                            'prepend' => '',
                            'append' => '',
                            'maxlength' => '',
                        ),
                        array(
                            'key' => 'field_6301daaf16563',
                            'label' => 'Göm',
                            'name' => 'hide',
                            'type' => 'true_false',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '10%',
                                'class' => '',
                                'id' => '',
                            ),
                            'message' => '',
                            'default_value' => 0,
                            'ui' => 0,
                            'ui_on_text' => '',
                            'ui_off_text' => '',
                        ),
                        array(
                            'key' => 'field_6301da8016568',
                            'label' => 'Text',
                            'name' => 'description',
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


                        array(
							'key' => 'field_63064a92d56e2',
							'label' => 'Kategori',
							'name' => 'category',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '25%',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'urgent' => 'Akut',
								'warning' => 'Driftstörning',
							),
							'default_value' => array(
                                'warning'
							),
							'allow_null' => 0,
							'multiple' => 0,
							'ui' => 0,
							'return_format' => 'value',
							'ajax' => 0,
							'placeholder' => '',
						),
                        array(
							'key' => 'field_6306478d18503',
							'label' => 'Länktyp',
							'name' => 'link_type',
							'type' => 'select',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '15%',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'no-link' => 'Ingen länk',
								'post' => 'Inlägg',
								'file' => 'Fil',
                                'external' => 'Extern länk',
							),
							'default_value' => array(
							),
							'allow_null' => 0,
							'multiple' => 0,
							'ui' => 0,
							'return_format' => 'value',
							'ajax' => 0,
							'placeholder' => '',
						),

						array(
							'key' => 'field_630647b018504',
							'label' => 'Länk till inlägg',
							'name' => 'post',
							'type' => 'post_object',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_6306478d18503',
										'operator' => '==',
										'value' => 'post',
									),
								),
							),
							'wrapper' => array(
								'width' => '60%',
								'class' => '',
								'id' => '',
							),
							'post_type' => array(
								0 => 'post',
							),
							'taxonomy' => '',
							'allow_null' => 0,
							'multiple' => 0,
							'return_format' => 'id',
							'ui' => 1,
						),
                        array (
                            'key' => 'field_56bb0d4262d44',
                            'label' => 'Fil',
                            'name' => 'file',
                            'type' => 'file',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => array(
                                array(
                                    array(
                                        'field' => 'field_6306478d18503',
                                        'operator' => '==',
                                        'value' => 'file',
                                    ),
                                ),
                            ),
                            'wrapper' => array (
                                'width' => '60%',
                                'class' => '',
                                'id' => '',
                            ),
                            'return_format' => 'array',
                            'library' => 'all',
                            'min_size' => '',
                            'max_size' => '',
                            'mime_types' => '',
                        ),
                        
						array(
							'key' => 'field_630647fa18505',
							'label' => 'Extern länk',
							'name' => 'external_link',
							'type' => 'text',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => array(
								array(
									array(
										'field' => 'field_6306478d18503',
										'operator' => '==',
										'value' => 'external',
									),
								),
							),
							'wrapper' => array(
								'width' => '60%',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
                        
                        // array(
                        //     'key' => 'field_6301dac816564',
                        //     'label' => 'Beskrivning',
                        //     'name' => 'description',
                        //     'type' => 'wysiwyg',
                        //     'instructions' => '',
                        //     'required' => 0,
                        //     'conditional_logic' => 0,
                        //     'wrapper' => array(
                        //         'width' => '',
                        //         'class' => '',
                        //         'id' => '',
                        //     ),
                        //     'default_value' => '',
                        //     'placeholder' => '',
                        //     'maxlength' => '',
                        //     'rows' => '',
                        //     'new_lines' => 'wpautop',
                        // ),
                    ),
                    'min' => '',
                    'max' => '',
                ),
            ),
            'button_label' => 'Lägg till rad',
            'min' => '',
            'max' => '',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-driftstorningar',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
    'show_in_rest' => 0,
));
