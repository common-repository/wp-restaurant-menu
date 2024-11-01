<?php
add_action('admin_init', 'food_register_settings');
add_action('admin_menu', 'food_add_option_page');

register_activation_hook( WP_PLUGIN_DIR . '/food-menu/food-menu.php', 'enable_option_show_credits');

function food_register_settings() {
	register_setting('food-options', 'column', '');
	register_setting('food-options', 'column', '');
}

function enable_option_show_credits() {
	update_option('show-credits', '1');
}

function food_add_option_page() {
	add_options_page('Food Menu Settings', 'Food Menu', 'administrator', 'food-settings', 'food_options');
}

/**
 * Get the preformatted common options as array
 *
 * @see Settings Panel
 * @return array
 */
function get_option_preformatted() {
	$options = array();
	
	$fields = explode(",", get_option('info-fields'));
	foreach ($fields as $field):
		if (trim($field) != ''):
			$options[strtolower(str_replace(' ', '_', $field))] = $field;
		endif;
	endforeach;

	return $options;
}

/**
 * The settings HTML
 */
function food_options() {
?>

<div class="wrap">
	
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e('Food Menu Options', 'food-menu'); ?></h2>

	<form action="options.php" method="post">

		<!-- NOTIFICATION DIALOG FOR PERMALINKS -->
		<?php settings_fields('food-options'); ?>	

		<!-- column SETTINGS -->
		<?php $column = (trim(get_option('column')) != '' ? get_option('column') : 'foodmenu'); ?>
		<h3><?php _e('Column Setting', 'food-menu'); ?></h3>
		<p><?php
			_e('This enables you to change the Column structure for the food menu.', 'food-menu');
		?></p>

		<!-- permalink box -->		
        <div class="theme-options-group">
        <table cellspacing="0" class="widefat theme-options-table">
        <thead><tr><th scope="row" colspan="2"><b><?php _e('Number of column:', 'food-menu'); ?></b></th></tr></thead>
        <tbody><tr><td><p class="description"></p>           
           <select id="column" name="column">
               <option value="1" <? if($column == 1){?>selected="selected"<? }?>>1</option>
               <option value="2" <? if($column == 2){?>selected="selected"<? }?>>2</option>           
            </select>
        </td></tr></tbody>
        </table>
       </div>
       <p class="submit"><input type="button" class="button-primary" value="<?php _e("Save changes", "food-menu");?>" onclick="save_form();"/></p>
	</form>
</div>

<script type="text/javascript">

	function add_field() {
		var field_html = "<span class=\"drag-handle\"></span>";
		field_html += "<input type=\"text\" class=\"regular-text code\" value=\"\" style=\"width:500px;\" />";
		field_html += "<input type=\"button\" value=\"Delete\" onclick=\"delete_field(this);\"  />";

		jQuery('#info_fields ul').prepend('<li>' + field_html + '</li>');
		jQuery('#info_fields ul li').first().hide();
		jQuery('#info_fields ul li').first().slideDown('fast');
	}

	function delete_field(field) {
		jQuery(field).parent().slideUp('fast', function(e) { jQuery(this).html('');	});
	}

	function save_form() {
		var fields = [];
		jQuery('#info_fields input[type="text"]').each(function(index, value) {
			var v = jQuery(value).attr('value');
			if (jQuery.trim(v) != '')
				fields.push(v);
		});

		jQuery('input[type="hidden"][name="info-fields"]').attr('value', fields.join(','));
		jQuery('form[action="options.php"]').submit();
	}

	function help_toggle($el) {
		var helpdiv = jQuery($el).parent().find('#help');
		(helpdiv.css('display') == 'none') ? helpdiv.slideDown() : helpdiv.slideUp();
	}

	function update_validation_blocks($direct) {
		var food = jQuery('#food_validation');
		var single = jQuery('#food_single_validation');

		var food_vis = jQuery('input[name="use-column-rewrite"]:checked').val() == '1' && food.find('#file_exists').text() == 'false';
		var single_vis = single.find('#file_exists').text() == 'false';

		var time_animation = $direct ? 0 : 'fast';
		food_vis ? food.slideDown(time_animation) : food.slideUp(time_animation);
		single_vis ? single.slideDown(time_animation) : single.slideUp(time_animation);
	}

	jQuery(document).ready(function() {
		// sortable info fields
		jQuery('div#info_fields ul').sortable({
			containment: 'parent',
			tolerance: 'pointer',
			handle: '.drag-handle',
			opacity: 0.6
		});

		update_validation_blocks( true );
		jQuery('input[name="use-column-rewrite"]').change(function(){
			update_validation_blocks( false );
		});
	});
</script>
<script type='text/javascript' src='<?php echo WP_PLUGIN_URL; ?>/food-menu/js/jquery-ui-1.8.4.custom.min.js'></script>

<?php
}

