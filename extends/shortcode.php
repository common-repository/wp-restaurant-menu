<?php
function displayFoodMenu_shortcode($atts)
{
	global $sa_general, $currency_symbol, $currency_symbol_align_right;
	
	$general_options = get_option('sa_general', $sa_general); 
	
	$currency_symbol_before = "<span class='currency_symbol'>";
	$currency_symbol_after = "</span>";
	
	$currency_symbol = "";
	
	if(isset($general_options['currency_symbol']))
	{ 
		if ($general_options['currency_symbol'] > ""){
			$currency_symbol = $currency_symbol_before . $general_options['currency_symbol'] . $currency_symbol_after;
		}
	}else{
		$currency_symbol = $currency_symbol_before ."$".$currency_symbol_after;
	}
	
	if(isset($general_options['currency_symbol_align_right']))
	{ 
		$currency_symbol_align_right = $general_options['currency_symbol_align_right'];
			
	}else{
		$currency_symbol_align_right = 0;
	}
	extract(shortcode_atts(array(
			"groups" => 'all',
			"showtitles" => 1,
			"showsubtitles" => 1,
		), $atts));
	
	// SET Taxonomy
	$taxonomy = 'menu-groups'; 
	$output = '';
	
	// If $atts blank then loop through all menu-groups Terms and display their posts.
	if ($groups == 'all'){
		//  GET ALL $taxonomy TERMS and display all child posts
		$args = array( 'orderby' => 'name'); // Order by Name
		$terms = get_terms($taxonomy, $args); // GET ALL TERMS for $taxonomy
		//echo "<pre>";print_r($terms);exit;
		$count = count($terms); $i=0;
			if ($count > 0) {
				$cape_list = '<p class="my_term-archive">';
				foreach ($terms as $term) {
					$i++;
					if($term->parent == 0){ // Only loop for Partent $taxonomy Terms. We will fetch the Child terms inside this loop separately.
					$column = (trim(get_option('column')));
					if($column == 2)
					$output .= '<div class="one_half">';
					else
					$output .= '<div class="full-size">';
						
						if ($showtitles){
							$output .= '<h2 class="menu-title">' . $term->name . '</h2>'; // Write out $taxonomy Term name.
						}
						echo '<link rel="stylesheet" type="text/css" href="' . MENU_PLUGIN_URL.'css/food-menu.css" />';
						// Function to loop through all pots for this $taxonomy Parent Term.
						$output .= dislayAllPartentItems($term->term_id,"menu-groups",false); 
						
						//$output .= displayAllChildItem($term->term_id,"menu-groups",$showsubtitles); // Fun ction to loop through all child $taxonomy terms and display all their posts.
					$output .= '</div>';
					} // END if term->parent
				} // END for each
			} // End if count > 0
	}else{
		//$args = array( 'orderby' => 'name'); // Order by Name
		//$terms = get_terms($taxonomy, $args); // GET ALL TERMS for $taxonomy
		$groupsarr = explode(",", $groups);
		$count = count($groupsarr); $i=0;
		if ($count > 0) {
			$cape_list = '<p class="my_term-archive">';
			foreach ($groupsarr as $group) {
				
				$term = get_term_by('slug', $group, $taxonomy);
				$i++;
				// Only loop for Partent $taxonomy Terms. We will fetch the Child terms inside this loop separately.
				if($term->parent == 0){ 
					if ($showtitles){
						$output .= '<h2 class="menu-title">' . $term->name . '</h2>'; // Write out $taxonomy Term name.
					}
					// Write out $taxonomy Term name.
					$output .= dislayAllPartentItems($term->term_id,"menu-groups",false); // Function to loop through all pots for this $taxonomy Parent Term.
					
					$output .= displayAllChildItem($term->term_id,"menu-groups",$showsubtitles); // Function to loop through all child $taxonomy terms and display all their posts.
				}else{
					$output .= dislayAllPartentItems($term->term_id,"menu-groups",$showsubtitles); // Function to loop through all pots for this $taxonomy Parent Term.
				} // end if term->parent
			} // end foreach
		} // End if count
	} // End $groups == ALL
	return $output;
} // END FUNCTION


/*
This function displays all items under a term and excludes child terms.
Takes $term_id, $taxonomyName, and $title (true / false) if you would 
like the Term title to be displayed.
*/
function dislayAllPartentItems($term_id,$taxonomyName,$title = false){
	global $currency_symbol, $currency_symbol_align_right;
	
			
	$output = '';
	$post = '';
	if ($title){ // If title is true then grab the term namea and display it.
		$term = get_term( $term_id, $taxonomyName );
		$output .= '<h2 class="menu-title">' . $term->name . '</h2>';
	}
	
	// Filter all child terns.	
	$unwanted_children = get_term_children($term_id, $taxonomyName);
	
	$args = array(
		
		'tax_query' => array(
			array(
				'taxonomy' => $taxonomyName,
				'field' => 'id',
				'terms' => $term_id,
			),
			array( // Filter out all child terms.
				'taxonomy' => $taxonomyName,
				'field' => 'id',
				'terms' => $unwanted_children,
				'operator' => 'NOT IN'
			),
		),
		'orderby' => 'date title',
		'order' => 'ASC',
		'posts_per_page'=>-1,
	);
			
	
	$the_query = new WP_Query( $args ); // The Query
	
	$output .= "<div class='food_menu_wrapper'>";
	
	// The Loop
	while ( $the_query->have_posts() ) : $the_query->the_post(); 
		$output .= "<div class='menu_item_wrapper'>";	
		
		// check if the post has a Post Thumbnail assigned to it.
		if ( has_post_thumbnail() ) { 
		  $output .= "<div class='menu_item_image_sm'>";
		  // THUMB WITH LIGHTBOX
						$thumbnail_id=get_the_post_thumbnail( get_the_ID());
						preg_match ('/src="(.*)" class/',$thumbnail_id,$link);
						// Return Title and use it in the lightbox.
						$title = get_the_title();
						if ($title > ""){
							$title = "title=\"$title\"";
						}
						$output .= '<a href="'.$link[1].'"><image src="'.$link[1].'" width="100" height="100" '.$title.'/></a>';
					// END THUMB WITH LIGHTBOX				
				  //the_post_thumbnail( 'food-menu-thumb' );
		  //the_post_thumbnail( 'food-menu-thumb' );
		  $output .= "</div>"; 
		}
		
		
			$post_id = get_the_ID();
			$single = true;
			
			// Return META Data
			$key = 'mg_price_description';
			$fm_price_description = get_post_meta($post_id, $key, $single);
			$key = 'fm_price';
			$fm_price = get_post_meta($post_id, $key, $single);
			
			$key = 'fm_second_price_description';
			$fm_second_price_description = get_post_meta($post_id, $key, $single);
			$key = 'fm_second_price';
			$fm_second_price = get_post_meta($post_id, $key, $single);
			
			$key = 'fm_third_price_description';
			$fm_third_price_description = get_post_meta($post_id, $key, $single);
			$key = 'fm_third_price';
			$fm_third_price = get_post_meta($post_id, $key, $single);
			
			$key = 'fm_fourth_price_description';
			$fm_fourth_price_description = get_post_meta($post_id, $key, $single);
			$key = 'fm_fourth_price';
			$fm_fourth_price = get_post_meta($post_id, $key, $single);
			
			$key = 'fm_fifth_price_description';
			$fm_fifth_price_description = get_post_meta($post_id, $key, $single);
			$key = 'fm_fifth_price';
			$fm_fifth_price = get_post_meta($post_id, $key, $single);
			
			
			$key = 'fm_disable_details_link';
			$fm_disable_details_link = get_post_meta($post_id, $key, $single);
			
			
			if($fm_disable_details_link !== 'on'){
				$title = "<a href='". get_permalink() ."'>". get_the_title() ."</a>";
			}else{
				$title = get_the_title();
			}
			$output .= '<div class="menuItemTitleWrap">
						<div class="menuItemTitle"><h3 class="menu-title">'.$title.'</h3></div>
							<div class="item-price-block">';		
			
			
			$before_single_price = "<span class='single_price'>";
			$after_single_price = "</span> ";
			
			if ($fm_price > ""){
				$output .= $before_single_price;
				if ($fm_price_description > ""){
					$output .= $fm_price_description . ":&nbsp;";
				}
				if ($currency_symbol_align_right){
					$output .= "<span class='item-price'>".$fm_price.$currency_symbol."</span>";
				}else{
					$output .= "<span class='item-price'>".$currency_symbol.$fm_price."</span>";
				}
				$output .= $after_single_price;
			}
			
			if ($fm_second_price > ""){
				$output .= $before_single_price;
				if ($fm_second_price_description > ""){
					$output .= $fm_second_price_description . ":&nbsp;";
				}
				if ($currency_symbol_align_right){
					$output .= "<span class='item-price'>".$fm_second_price.$currency_symbol."</span>";
				}else{
					$output .= "<span class='item-price'>".$currency_symbol.$fm_second_price."</span>";
				}
				$output .= $after_single_price;
				
			}
			
			if ($fm_third_price > ""){
				$output .= $before_single_price;
				if ($fm_third_price_description > ""){
					$output .= $fm_third_price_description . ":&nbsp;";
				}
				if ($currency_symbol_align_right){
					$output .= "<span class='item-price'>".$fm_third_price.$currency_symbol."</span>";
				}else{
					$output .= "<span class='item-price'>".$currency_symbol.$fm_third_price."</span>";
				}
				$output .= $after_single_price;
				
			}
			
			if ($fm_fourth_price > ""){
				$output .= $before_single_price;
				if ($fm_fourth_price_description > ""){
					$output .= $fm_fourth_price_description . ":&nbsp;";
				}
				if ($currency_symbol_align_right){
					$output .= "<span class='item-price'>".$fm_fourth_price.$currency_symbol."</span>";
				}else{
					$output .= "<span class='item-price'>".$currency_symbol.$fm_fourth_price."</span>";
				}
				$output .= $after_single_price;
			}
			
			if ($fm_fifth_price > ""){
				$output .= $before_single_price;
				if ($fm_fifth_price_description > ""){
					$output .= $fm_fifth_price_description . ":&nbsp;";
				}
				if ($currency_symbol_align_right){
					$output .= "<span class='item-price'>".$fm_fifth_price.$currency_symbol."</span>";
				}else{
					$output .= "<span class='item-price'>".$currency_symbol.$fm_fifth_price."</span>";
				}
				$output .= $after_single_price;
			}
		
		$output .= "</div></div>";	
		$output .= get_the_excerpt();			
		
		$output .= "<div class='clear'></div></div><!-- END Menu Item Wrapper -->";
	endwhile;
	
	$output .= "</div><!-- END Food Menu Wrapper -->";
	
	
	
	// Reset Post Data
	wp_reset_postdata();
	
	return $output;
}
/*
This function displays all child terms of a partent term and displays their posts.
Takes $term_id, $taxonomyName, and $title (true / false) if you would 
like the Term title to be displayed.
*/
function displayAllChildItem($term_id,$taxonomyName,$title = false)
{
	global $currency_symbol, $currency_symbol_align_right;

	// GET CHILDREN
	$termID = $term_id;
	$taxonomyName = $taxonomyName;
	$termchildren = get_term_children( $termID, $taxonomyName );
	$output = '';
	

	foreach ($termchildren as $child) {
		$term = get_term_by( 'id', $child, $taxonomyName );
		if ($title){
		$output .= '<h2 class="menu-title">' . $term->name . '</h2>';
		}
		//GET ALL MENU ITEMS
		$args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomyName,
					'field' => 'id',
					'terms' => $child,
				),
			),
			'orderby' => 'date title',
			'order' => 'ASC',
			'posts_per_page'=>-1,
		);
		
		 // The Query
		$the_query = new WP_Query( $args );
		
		$output .= "<div class='food_menu_wrapper'>";
		// The Loop
		while ( $the_query->have_posts() ) : $the_query->the_post();
				$output .= "<div class='menu_item_wrapper'>";	
				
				if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
				  $output .= "<div class='menu_item_image_sm'>";
					// THUMB WITH LIGHTBOX
						$thumbnail_id=get_the_post_thumbnail( get_the_ID());
						preg_match ('/src="(.*)" class/',$thumbnail_id,$link);
						// Return Title and use it in the lightbox.
						$title = get_the_title();
						if ($title > ""){
							$title = "title=\"$title\"";
						}
						$output .= '<a href="'.$link[1].'"><image src="'.$link[1].'" width="100" height="100" '.$title.'/></a>';
					// END THUMB WITH LIGHTBOX				
				  //the_post_thumbnail( 'food-menu-thumb' );
				  $output .= "</div>";
				}/*else{ // OPTIONAL to replace missing images with a placeholder.
					echo "<img src='no-image.gif'>";
				}*/				
				echo '<link rel="stylesheet" type="text/css" href="' . MENU_PLUGIN_URL.'css/food-menu.css" />';
							
								$post_id = get_the_ID();
								$single = true;
								
								// Return META Data
								$key = 'fm_price_description';
								$fm_price_description = get_post_meta($post_id, $key, $single);
								$key = 'fm_price';
								$fm_price = get_post_meta($post_id, $key, $single);
								
								$key = 'fm_second_price_description';
								$fm_second_price_description = get_post_meta($post_id, $key, $single);
								$key = 'fm_second_price';
								$fm_second_price = get_post_meta($post_id, $key, $single);
								
								$key = 'fm_third_price_description';
								$fm_third_price_description = get_post_meta($post_id, $key, $single);
								$key = 'fm_third_price';
								$fm_third_price = get_post_meta($post_id, $key, $single);
								
								$key = 'fm_fourth_price_description';
								$fm_fourth_price_description = get_post_meta($post_id, $key, $single);
								$key = 'fm_fourth_price';
								$fm_fourth_price = get_post_meta($post_id, $key, $single);
								
								$key = 'fm_fifth_price_description';
								$fm_fifth_price_description = get_post_meta($post_id, $key, $single);
								$key = 'fm_fifth_price';
								$fm_fifth_price = get_post_meta($post_id, $key, $single);
								
								
								$key = 'fm_disable_details_link';
								$fm_disable_details_link = get_post_meta($post_id, $key, $single);
									
									
									if($fm_disable_details_link !== 'on'){
										$title = "<a href='". get_permalink() ."'>". get_the_title() ."</a>";
									}else{
										$title = get_the_title();
									}
									$output .= '<div class="menuItemTitleWrap">
												<div class="menuItemTitle"><h3 class="menu-title">'.$title.'</h3></div>
													<div class="item-price-block">';
								
								$before_single_price = "<span class='single_price'>";
								$after_single_price = "</span> ";
			
								if ($fm_price > ""){
									$output .= $before_single_price;
									if ($fm_price_description > ""){
										$output .= $fm_price_description . ":&nbsp;";
									}
									if ($currency_symbol_align_right){
										$output .= "<span class='item-price'>".$fm_price.$currency_symbol."</span>";
									}else{
										$output .= "<span class='item-price'>".$currency_symbol.$fm_price."</span>";
									}
									
								}
								
								if ($fm_second_price > ""){
									$output .= $before_single_price;
									if ($fm_second_price_description > ""){
										$output .= $fm_second_price_description . ":&nbsp;";
									}
									if ($currency_symbol_align_right){
										$output .= "<span class='item-price'>".$fm_second_price.$currency_symbol."</span>";
									}else{
										$output .= "<span class='item-price'>".$currency_symbol.$fm_second_price."</span>";
									}
									$output .= $after_single_price;
									
								}
								
								if ($fm_third_price > ""){
									$output .= $before_single_price;
									if ($fm_third_price_description > ""){
										$output .= $fm_third_price_description . ":&nbsp;";
									}
									if ($currency_symbol_align_right){
										$output .= "<span class='item-price'>".$fm_third_price.$currency_symbol."</span>";
									}else{
										$output .= "<span class='item-price'>".$currency_symbol.$fm_third_price."</span>";
									}
									$output .= $after_single_price;
								}
								
								if ($fm_fourth_price > ""){
									$output .= $before_single_price;
									if ($fm_fourth_price_description > ""){
										$output .= $fm_fourth_price_description . ":&nbsp;";
									}
									if ($currency_symbol_align_right){
										$output .= "<span class='item-price'>".$fm_fourth_price.$currency_symbol."</span>";
									}else{
										$output .= "<span class='item-price'>".$currency_symbol.$fm_fourth_price."</span>";
									}
									$output .= $after_single_price;
								}
								
								if ($fm_fifth_price > ""){
									$output .= $before_single_price;
									if ($fm_fifth_price_description > ""){
										$output .= $fm_fifth_price_description . ":&nbsp;";
									}
									if ($currency_symbol_align_right){
										$output .= "<span class='item-price'>".$fm_fifth_price.$currency_symbol."</span>";
									}else{
										$output .= "<span class='item-price'>".$currency_symbol.$fm_fifth_price."</span>";
									}
									$output .= $after_single_price;
								}
			
						$output .= "</div></div>";	
				$output .= get_the_excerpt();			
				
				$output .= "<div class='clear'></div></div><!-- END Menu Item Wrapper -->";
		endwhile;
		
		$output .= "</div><!-- END Food Menu Wrapper -->";
		
		
		// Reset Post Data
		wp_reset_postdata();
		
	}	
	return $output;
}
add_shortcode('foodmenu', 'displayFoodMenu_shortcode');