<?php
add_action('init', 'food_menu_init');
/**
* Initialize food-menu plugin
*/
function food_menu_init() {
	$custom_slug = get_option('slug') != '' ? get_option('slug') : 'foodmenu';
	
	$args = array(
		'labels'			=> array(
			'name'					=> __('Food Menu', 'food-menu'),
			'singular_name' 		=> __('Food Menu', 'food-menu'),
			'add_new'				=> __('Add New', 'food-menu'),
			'add_new_item'			=> __('Add New', 'food-menu'),
			'new_item'				=> __('Add New', 'food-menu'),
			'view_item'				=> __('View Food Menu', 'food-menu'),
			'search_items' 			=> __('Search Food Menu', 'food-menu'),
			'edit_item' 			=> __('Edit Food Menu', 'food-menu'),
			'all_items'				=> __('All Menu Items', 'food-menu'),
			'not_found'				=> __('No Food Menu found', 'food-menu'),
			'not_found_in_trash'	=> __('No Food Menu found in Trash', 'food-menu')
		),
		'taxonomies'		=> array('menu-groups', 'foodmenu-groups', 'foodmenu-tags'),
		'public'			=> true,
		'show_ui'			=> true,
		'_builtin'			=> false,
		'_edit_link'		=> 'post.php?post=%d',
		'capability_type'	=> 'post',
		'rewrite'			=> array('slug' => __($custom_slug)),
		'hierarchical'		=> false,
		'menu_position'		=> 20,
		'supports'			=> array('title', 'editor', 'comments', 'thumbnail')
	);
	
	/** create foodmenu categories (taxonomy) */
	register_taxonomy('menu-groups', 'foodmenu', array(
			'hierarchical'		=> true,
			'show_ui'			=> true,
			'rewrite'			=> array('slug' => __($custom_slug . '/category')),
			'labels'			=> array(
					'name' 							=> __('Menu Groups', 'food-menu'),
					'singular_name'					=> __('Menu Groups', 'food-menu'),
					'search_items' 					=> __('Search Menu Groups', 'food-menu'),
					'popular_items'					=> __('Popular Menu Groups', 'food-menu'),
					'all_items'						=> __('All Menu Groups', 'food-menu'),
					'parent_item'					=> __('Parent Menu Groups', 'food-menu'),
					'parent_item_colon'				=> __('Parent Menu Groups', 'food-menu'),
					'edit_item'						=> __('Edit Menu Groups', 'food-menu'),
					'update_item'					=> __('Update Menu Groups', 'food-menu'),
					'add_new_item'					=> __('Add New Menu Groups', 'food-menu'),
					'new_item_name'					=> __('New Menu Groups', 'food-menu'),
					'separate_items_with_commas'	=> __('Separate Menu Groups with commas', 'food-menu'),
					'add_or_remove_items' 			=> __('Add or remove Menu Groups', 'food-menu'),
					'choose_from_most_used' 		=> __('Choose from the most used Menu Groups', 'food-menu')
		)
	));	
	/** create new custom post type */
	register_post_type('foodmenu', $args);
}