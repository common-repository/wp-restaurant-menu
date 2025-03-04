<?php
/**
	* menu_item_details.php
	* Creates META BOXES (Menu Item Details) for the Food Menu Custom Post Type. 
	* License: GNU GENERAL PUBLIC LICENSE Version 2
	* License URI: http://www.themovation.com/eatery/license 
	* @version    1.0
	* @link       http://www.themovation.com/eatery
*/
// Include & setup custom metabox and fields
$prefix = 'fm_';
$meta_boxes = array();

$meta_boxes[] = array(
    'id' => 'menu_item_details',
    'title' => 'Menu Item Details',
    'pages' => array('foodmenu'), // post type
    'context' => 'normal',
    'priority' => 'high',
    'show_names' => true, // Show field names on the left
    'fields' => array(
        array(
            'name' => 'Price Description',
            'desc' => 'Price Description (optional)',
            'id' => $prefix . 'price_description',
            'type' => 'text'
        ),
		array(
			'name' => 'Price',
			'desc' => 'Price',
			'id' => $prefix . 'price',
			'type' => 'text_money'
		),
		 array(
            'name' => 'Second Price Description',
            'desc' => 'Second Price Description (optional)',
            'id' => $prefix . 'second_price_description',
            'type' => 'text'
        ),
		array(
			'name' => 'Second Price',
			'desc' => 'Second Price (optional)',
			'id' => $prefix . 'second_price',
			'type' => 'text_money'
		),
		array(
            'name' => 'Third Price Description',
            'desc' => 'Third Price Description (optional)',
            'id' => $prefix . 'third_price_description',
            'type' => 'text'
        ),
		array(
			'name' => 'Third Price',
			'desc' => 'Third Price (optional)',
			'id' => $prefix . 'third_price',
			'type' => 'text_money'
		),
		array(
            'name' => 'Fourth Price Description',
            'desc' => 'Fourth Price Description (optional)',
            'id' => $prefix . 'fourth_price_description',
            'type' => 'text'
        ),
		array(
			'name' => 'Fourth Price',
			'desc' => 'Fourth Price (optional)',
			'id' => $prefix . 'fourth_price',
			'type' => 'text_money'
		),
		array(
            'name' => 'Fifth Price Description',
            'desc' => 'Fifth Price Description (optional)',
            'id' => $prefix . 'fifth_price_description',
            'type' => 'text'
        ),
		array(
			'name' => 'Fifth Price',
			'desc' => 'Fifth Price (optional)',
			'id' => $prefix . 'fifth_price',
			'type' => 'text_money'
		),
		array(
			'name' => 'Item Details Link',
			'desc' => 'Disable Link to Item Details Page',
			'id' => $prefix . 'disable_details_link',
			'type' => 'checkbox'
		),
    )
);


foreach ( $meta_boxes as $meta_box ) {
$my_box = new cmb_Meta_Box( $meta_box );
}

/**
* Validate value of meta fields
* Define ALL validation methods inside this class and use the names of these
* methods in the definition of meta boxes (key 'validate_func' of each field)
*/

class cmb_Meta_Box_Validate {
function check_text( $text ) {
if ($text != 'hello') {
return false;
}
return true;
}
}

/*
* url to load local resources.
*/

define( 'CMB_META_BOX_URL', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname(__FILE__) ) ) );

/**
* Create meta boxes
*/

class cmb_Meta_Box {
protected $_meta_box;

function __construct( $meta_box ) {
if ( !is_admin() ) return;

$this->_meta_box = $meta_box;

$upload = false;
foreach ( $meta_box['fields'] as $field ) {
if ( $field['type'] == 'file' || $field['type'] == 'file_list' ) {
$upload = true;
break;
}
}

$current_page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1, -4);

if ( $upload && ( $current_page == 'page' || $current_page == 'page-new' || $current_page == 'post' || $current_page == 'post-new' ) ) {
add_action( 'admin_head', array(&$this, 'add_post_enctype') );
}

/**************************************************************************************************/
// FUN ADDITION!! by Travis Smith
if(isset($meta_box['condition'])){

	if ( $meta_box['condition'] ) {
	add_action( 'admin_print_scripts', array(&$this, 'cmb_header_scripts') , 99 , 1 );
	}
}
// END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/

add_action( 'admin_menu', array(&$this, 'add') );
add_action( 'save_post', array(&$this, 'save') );
}

function add_post_enctype() {
echo '
<script type="text/javascript">
jQuery(document).ready(function(){
jQuery("#post").attr("enctype", "multipart/form-data");
jQuery("#post").attr("encoding", "multipart/form-data");
});
</script>';
}

/**************************************************************************************************/
// FUN ADDITION!! by Travis Smith
function cmb_header_scripts() {

if (isset($this->_meta_box['condition']['type']) && ($this->_meta_box['condition']['type'] == 'template') ) {

$id = $this->_meta_box['id'];
$template = $this->_meta_box['condition']['template'];

echo "<script type=\"text/javascript\">
jQuery(function($) {
$('#page_template').change(function() {
if ($(this).val() != \"$template\") {
$('#$id').hide();
$('input#$id-hide').prop('checked', false);
}
});
$('#page_template').live('change', function(e) {
if ($(this).val() === \"$template\") {
$('#$id').show();
$('input#$id-hide').prop('checked', true);
}
}).change();

})</script>";

}
elseif (isset($this->_meta_box['condition']['type']) && ($this->_meta_box['condition']['type'] == 'cat') ) {
$id = $this->_meta_box['id'];

if ( $this->_meta_box['condition']['cat_exclude'] ) {
$cats = $this->_meta_box['condition']['cat_exclude'];

echo "<script type=\"text/javascript\">
jQuery(function($)
{
function cmb_check_categories()
{
$('#$id').show();
$('#categorychecklist input[type=\"checkbox\"]').each(function(i,e)
{
var id = $(this).attr('id').match(/-([0-9]*)$/i);
id = (id && id[1]) ? parseInt(id[1]) : null ;

if ($.inArray(id, [$cats]) > -1 && $(this).is(':checked'))
{
$('#$id').hide();
}
});
}
$('#categorychecklist input[type=\"checkbox\"]').live('click', cmb_check_categories);
cmb_check_categories();
});</script>";

}
elseif ( $this->_meta_box['condition']['cat_include'] ) {
$cats = $this->_meta_box['condition']['cat_include'];

echo "<script type=\"text/javascript\">
jQuery(function($)
{
function cmb_check_categories()
{
$('#$id').hide();
$('#categorychecklist input[type=\"checkbox\"]').each(function(i,e)
{
var id = $(this).attr('id').match(/-([0-9]*)$/i);
id = (id && id[1]) ? parseInt(id[1]) : null ;
if ($.inArray(id, [$cats]) > -1 && $(this).is(':checked'))
{
$('#$id').show();
}
});
}
$('#categorychecklist input[type=\"checkbox\"]').live('click', cmb_check_categories);
cmb_check_categories();
});</script>";
}
}
}
// END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/

// Add metaboxes
function add() {
$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'high' : $this->_meta_box['priority'];
foreach ( $this->_meta_box['pages'] as $page ) {


/**************************************************************************************************/
// FUN ADDITION!! by Travis Smith
if ( !isset($this->_meta_box['condition']) ) {
add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;

}
else
{
$post_id = isset($_GET['post']) ? $_GET['post'] : $_POST['post_ID'] ;

if (isset($this->_meta_box['condition']['type']) && ($this->_meta_box['condition']['type'] == 'id') ) {

if ( $this->_meta_box['condition']['id'] == $post_id )
add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
}
else {
add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
}
}
// END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/


}
}

// Show fields
function show() {
global $post;

// Use nonce for verification
echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';
echo '<table class="form-table cmb_metabox">';

foreach ( $this->_meta_box['fields'] as $field ) {
// Set up blank values for empty ones
if ( !isset($field['desc']) ) $field['desc'] = '';
if ( !isset($field['std']) ) $field['std'] = '';

/**************************************************************************************************/
// //FUN ADDITION!! by Travis Smith
if ( $field['type'] == 'multicheck' )
$single = false;
elseif ( $field['type'] == 'multicheck_group' )
$single = false;
else
$single = true;

$meta = get_post_meta( $post->ID, isset($field['id']) ? $field['id'] : '' , $single /* If multicheck/multicheck_group this can be multiple values */ );

// END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/

echo '<tr>';

if ( $field['type'] == "title" ) {
echo '<td colspan="2">';
} else {
if( $this->_meta_box['show_names'] == true ) {
echo '<th style="width:18%"><label for="', $field['id'], '">', $field['name'], '</label></th>';
}
echo '<td>';
}

switch ( $field['type'] ) {
case 'text':
echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" style="width:97%" />','<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'text_small':
echo '<input class="cmb_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'text_medium':
echo '<input class="cmb_text_medium" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'text_date':
echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'text_date_timestamp':
echo '<input class="cmb_text_small cmb_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? date( 'm\/d\/Y', $meta ) : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'text_money':
echo '$ <input class="cmb_text_money" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" /><span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'textarea':
echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'textarea_small':
echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'select':
echo '<select name="', $field['id'], '" id="', $field['id'], '">';
foreach ($field['options'] as $option) {
echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
}
echo '</select>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
/**************************************************************************************************/
//FUN ADDITION!! by Travis Smith
case 'select_group':
echo '<select name="', $field['id'], '" id="', $field['id'], '">';
echo '<option value="">Default</option>';
foreach ($field['options'] as $optgroup => $options) {
echo '<optgroup label="', $optgroup, '"';
foreach ($options as $option) {
echo '<option value="', $option['value'], '"', $meta == $option['value'] ? ' selected="selected"' : '', '>', $option['name'], '</option>';
}
echo '</optgroup>';
}
echo '</select>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
//END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/
case 'radio_inline':
echo '<div class="cmb_radio_inline">';
foreach ($field['options'] as $option) {
echo '<div class="cmb_radio_inline_option"><input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'], '</div>';
}
echo '</div>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'radio':
foreach ($field['options'] as $option) {
echo '<p><input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'].'</p>';
}
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'checkbox':
echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
echo '<span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
case 'multicheck':
echo '<ul>';
foreach ( $field['options'] as $value => $name ) {
// Append `[]` to the name to get multiple values
// Use in_array() to check whether the current option should be checked
echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], '" value="', $value, '"', in_array( $value, $meta ) ? ' checked="checked"' : '', ' /><label>', $name, '</label></li>';
}
echo '</ul>';
echo '<span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
/**************************************************************************************************/
// FUN ADDITION!! by Travis Smith
case 'multicheck_group':
echo '<ul class="cmb_multicheck_group_title">';
foreach ($field['options'] as $optgroup => $options) {
// Append `[]` to the name to get multiple values
// Use in_array() to check whether the current option should be checked
echo '<li>'. $optgroup;
echo '<ul>';
foreach ($options as $option) {
echo '<li><input type="checkbox" name="', $field['id'], '[]" id="', $field['id'], '" value="', $option['value'], '"', in_array( $option['value'], $meta ) ? ' checked="checked"' : '', ' /><label>', $option['name'], '</label></li>';
}
echo '</ul></li>';
}
echo '<span class="cmb_metabox_description">', $field['desc'], '</span>';
break;
//END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/
case 'title':
echo '<h5 class="cmb_metabox_title">', $field['name'], '</h5>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'wysiwyg':
echo '<div id="poststuff" class="meta_mce">';
echo '<div class="customEditor"><textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="7" style="width:97%">', $meta ? wpautop($meta, true) : '', '</textarea></div>';
echo '</div>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'taxonomy_select':
echo '<select name="', $field['id'], '" id="', $field['id'], '">';
$names= wp_get_object_terms( $post->ID, $field['taxonomy'] );
$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
foreach ( $terms as $term ) {
if (!is_wp_error( $names ) && !empty( $names ) && !strcmp( $term->slug, $names[0]->slug ) ) {
echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
} else {
echo '<option value="' . $term->slug . ' ' , $meta == $term->slug ? $meta : ' ' ,' ">' . $term->name . '</option>';
}
}
echo '</select>';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'taxonomy_radio':
$names= wp_get_object_terms( $post->ID, $field['taxonomy'] );
$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
foreach ( $terms as $term ) {
if ( !is_wp_error( $names ) && !empty( $names ) && !strcmp( $term->slug, $names[0]->slug ) ) {
echo '<p><input type="radio" name="', $field['id'], '" value="'. $term->slug . '" checked>' . $term->name . '</p>';
} else {
echo '<p><input type="radio" name="', $field['id'], '" value="' . $term->slug . ' ' , $meta == $term->slug ? $meta : ' ' ,' ">' . $term->name .'</p>';
}
}
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
break;
case 'file_list':
echo '<input id="upload_file" type="text" size="36" name="', $field['id'], '" value="" />';
echo '<input class="upload_button button" type="button" value="Upload File" />';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
$args = array(
'post_type' => 'attachment',
'numberposts' => null,
'post_status' => null,
'post_parent' => $post->ID
);
$attachments = get_posts($args);
if ($attachments) {
echo '<ul class="attach_list">';
foreach ($attachments as $attachment) {
echo '<li>'.wp_get_attachment_link($attachment->ID, 'thumbnail', 0, 0, 'Download');
echo '<span>';
echo apply_filters('the_title', '&nbsp;'.$attachment->post_title);
echo '</span></li>';
}
echo '</ul>';
}
break;
case 'file':
echo '<input id="upload_file" type="text" size="45" class="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
echo '<input class="upload_button button" type="button" value="Upload File" />';
echo '<p class="cmb_metabox_description">', $field['desc'], '</p>';
echo '<div id="', $field['id'], '_status" class="cmb_upload_status">';
if ( $meta != '' ) {
$check_image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $meta );
if ( $check_image ) {
echo '<div class="ifm_status">';
echo '<img src="', $meta, '" alt="" />';
echo '<a href="#" class="remove_file_button" rel="', $field['id'], '">Remove Image</a>';
echo '</div>';
} else {
$parts = explode( "/", $meta );
for( $i = 0; $i < sizeof( $parts ); ++$i ) {
$title = $parts[$i];
}
echo 'File: <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">Download</a> / <a href="# class="remove_file_button" rel="', $field['id'], '">Remove</a>)';
}
}
echo '</div>';
break;
}
echo '</td>','</tr>';
}
echo '</table>';
}

// Save data from metabox
function save( $post_id) {
// verify nonce
if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
return $post_id;
}

// check autosave
if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
return $post_id;
}

// check permissions
if ( 'page' == $_POST['post_type'] ) {
if ( !current_user_can( 'edit_page', $post_id ) ) {
return $post_id;
}
} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
return $post_id;
}

foreach ( $this->_meta_box['fields'] as $field ) {
$name = $field['id'];

/**************************************************************************************************/
// FUN ADDITION!! by Travis Smith
if ( $field['type'] == 'multicheck' )
$single = false;
elseif ( $field['type'] == 'multicheck_group' )
$single = false;
else
$single = true;

$old = get_post_meta( $post_id, $name, $single /* If multicheck this can be multiple values */ );

// END FUN ADDITION!! by Travis Smith
/**************************************************************************************************/
$new = isset( $_POST[$field['id']] ) ? $_POST[$field['id']] : null;

if ( $field['type'] == 'wysiwyg' ) {
$new = wpautop($new);
}

if ( $field['type'] == 'taxonomy_select' ) {
$new = wp_set_object_terms( $post_id, $new, $field['taxonomy'] );
}

if ( $field['type'] == 'taxonomy_radio' ) {
$new = wp_set_object_terms( $post_id, $new, $field['taxonomy'] );
}

if ( ($field['type'] == 'textarea') || ($field['type'] == 'textarea_small') ) {
$new = htmlspecialchars( $new );
}

if ( $field['type'] == 'text_date_timestamp' ) {
$new = strtotime( $new );
}

// validate meta value
if ( isset( $field['validate_func']) ) {
$ok = call_user_func( array( 'cmb_Meta_Box_Validate', $field['validate_func']), $new );
if ( $ok === false ) { // pass away when meta value is invalid
continue;
}
} elseif ( ( 'multicheck' == $field['type'] ) || ( 'multicheck_group' == $field['type'] ) ) {
// Do the saving in two steps: first get everything we don't have yet
// Then get everything we should not have anymore
if ( empty( $new ) ) {
$new = array();
}
$aNewToAdd = array_diff( $new, $old );
$aOldToDelete = array_diff( $old, $new );
foreach ( $aNewToAdd as $newToAdd ) {
add_post_meta( $post_id, $name, $newToAdd, false );
}
foreach ( $aOldToDelete as $oldToDelete ) {
delete_post_meta( $post_id, $name, $oldToDelete );
}
} elseif ( $new && $new != $old ) {
update_post_meta( $post_id, $name, $new );
} elseif ( '' == $new && $old && $field['type'] != 'file' ) {
delete_post_meta( $post_id, $name, $old );
}
}
}
}

/**
* Adding scripts and styles
*/

function cmb_scripts( $hook ) {
   if ( $hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php' ) {
wp_register_script( 'cmb-scripts', CMB_META_BOX_URL.'jquery.cmbScripts.js', array( 'jquery','media-upload','thickbox' ) );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-core' ); // Make sure and use elements form the 1.7.3 UI - not 1.8.9
wp_enqueue_script( 'media-upload' );
wp_enqueue_script( 'thickbox' );
wp_enqueue_script( 'cmb-scripts' );
wp_enqueue_style( 'thickbox' );
wp_enqueue_style( 'jquery-custom-ui' );
add_action( 'admin_head', 'cmb_styles_inline' );
   }
}
add_action( 'admin_enqueue_scripts', 'cmb_scripts', 10, 1 );

function editor_admin_init( $hook ) {
if ( $hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php' ) {
wp_enqueue_script( 'word-count' );
wp_enqueue_script( 'post' );
wp_enqueue_script( 'editor' );
}
}

function editor_admin_head( $hook ) {
if ( $hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php' ) {
   wp_editor();
}
}

add_action( 'admin_init', 'editor_admin_init' );
add_action( 'admin_head', 'editor_admin_head' );

function cmb_editor_footer_scripts() { ?>
<script type="text/javascript">/* <![CDATA[ */
jQuery(function($) {
var i=1;
$('.customEditor textarea').each(function(e) {
var id = $(this).attr('id');
if (!id) {
id = 'customEditor-' + i++;
$(this).attr('id',id);
}
tinyMCE.execCommand('mceAddControl', false, id);
});
});
/* ]]> */</script>
<?php }
add_action( 'admin_print_footer_scripts', 'cmb_editor_footer_scripts', 99 );


function cmb_styles_inline() {
echo '<link rel="stylesheet" type="text/css" href="' . CMB_META_BOX_URL.'css/food-menu-admin.css" />';
?>
<style type="text/css">
table.cmb_metabox td, table.cmb_metabox th { border-bottom: 1px solid #E9E9E9; }
table.cmb_metabox th { text-align: right; font-weight:bold;}
table.cmb_metabox th label { margin-top:6px; display:block;}
p.cmb_metabox_description { color: #AAA; font-style: italic; margin: 2px 0 !important;}
span.cmb_metabox_description { color: #AAA; font-style: italic;}
input.cmb_text_small { width: 100px; margin-right: 15px;}
input.cmb_text_money { width: 90px; margin-right: 15px;}
input.cmb_text_medium { width: 230px; margin-right: 15px;}
table.cmb_metabox input, table.cmb_metabox textarea { font-size:11px; padding: 5px;}
table.cmb_metabox li { font-size:11px; }
table.cmb_metabox ul { padding-top:5px; }
table.cmb_metabox select { font-size:11px; padding: 5px 10px;}
table.cmb_metabox input:focus, table.cmb_metabox textarea:focus { background: #fffff8;}
.cmb_metabox_title { margin: 0 0 5px 0; padding: 5px 0 0 0; font: italic 24px/35px Georgia,"Times New Roman","Bitstream Charter",Times,serif;}
.cmb_radio_inline { padding: 4px 0 0 0;}
.cmb_radio_inline_option {display: inline; padding-right: 18px;}
table.cmb_metabox input[type="radio"] { margin-right:3px;}
table.cmb_metabox input[type="checkbox"] { margin-right:6px;}
table.cmb_metabox .mceLayout {border:1px solid #DFDFDF !important;}
table.cmb_metabox .mceIframeContainer {background:#FFF;}
table.cmb_metabox .meta_mce {width:97%;}
table.cmb_metabox .meta_mce textarea {width:100%;}
table.cmb_metabox .cmb_upload_status { margin: 10px 0 0 0;}
table.cmb_metabox .cmb_upload_status .ifm_status { position: relative; }
table.cmb_metabox .cmb_upload_status .ifm_status img { border:1px solid #DFDFDF; background: #FAFAFA; max-width:350px; padding: 5px; -moz-border-radius: 2px; border-radius: 2px;}
table.cmb_metabox .cmb_upload_status .ifm_status .remove_file_button { text-indent: -9999px; background: url(<?php echo CMB_META_BOX_URL ?>images/ico-delete.png); width: 16px; height: 16px; position: absolute; top: -5px; left: -5px;}
</style>
<?php
}

// End. That's it, folks! //