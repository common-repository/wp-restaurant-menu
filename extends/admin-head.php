<?php

add_action('admin_head', 'food_admin_head');

/**
* Change the icon on every page where post type is foodmenu.
* Also save template paths to vars
*/
function food_admin_head() {
	global $post_type;	
	$post_type = isset($post_type) ? $post_type : '';
	$_GET['post_type'] = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	$_GET['post'] = isset($_GET['post']) ? $_GET['post'] : '';
	?>
	<style>
	<?php if (($_GET['post_type'] == 'foodmenu') || ($post_type == 'foodmenu') || (get_post_type($_GET['post']) == 'foodmenu')) : ?>
		#icon-edit, #icon-post {
			background:transparent url('<?php echo WP_PLUGIN_URL . '/food-menu/images/icon.png'; ?>');
			height: 32px;
			width: 32px;
		}
	<?php endif; ?>

		#adminmenu #menu-posts-food .wp-menu-image {
			background:transparent url('<?php echo WP_PLUGIN_URL . '/food-menu/images/menu-icon.png'; ?>') -2px -38px no-repeat;
		}

		#adminmenu #menu-posts-food:hover .wp-menu-image, #adminmenu #menu-posts-food.wp-menu-open .wp-menu-image {
			background-position: -2px -6px;
		}

	</style>
	<?php
}

