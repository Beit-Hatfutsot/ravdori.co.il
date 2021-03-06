<?php
/**
 * Created by PhpStorm.
 * User: Roy
 * Date: 20/08/2015
 * Time: 13:53
 */


 
 function htmline_add_html_to_content( $content ) {
  
   if( is_singular(STORY_POST_TYPE) AND isset($_GET['advanced_search__word_name']) AND ! empty ($_GET['advanced_search__word_name'])):
   
	 $sr = sanitize_text_field($_GET['advanced_search__word_name']);
	
     $keys = explode(" ",$sr);
     $content = preg_replace('/\b('.implode('|', $keys) .')\b/iu', '<strong class="search-highlight-word">'.$sr.'</strong>', $content);
   
   endif;  
     
  return $content;
}
add_filter( 'the_content', 'htmline_add_html_to_content', 99);
 
 
 
function advanced_search_title_filter( $where, &$wp_query ){
    global $wpdb;

	
    if ( $search_term = $wp_query->get( 'search_story_title' ) ):
		
		$how_to_search = 'LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
		
		if ( $wp_query->get( 'search_story_title_exact' ) == 1 ) {
			
			$term = esc_sql( $wpdb->esc_like( $search_term ) );
			
			$how_to_search = ' REGEXP ' . '\'[[:<:]]' . $term . '[[:>:]]\'';
	
		}
		
        $where .= ' AND ' . $wpdb->posts . '.post_title ' . $how_to_search;
		
		error_log(  print_r($where,true)  );
		
    endif;
    return $where;
}



function advanced_search_content_filter( $where, &$wp_query ){
    global $wpdb;
	
	
    if ( $search_term = $wp_query->get( 'search_story_content' ) ) {
		
		$how_to_search = 'LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
		
		if ( $wp_query->get( 'search_story_content_exact' ) == 1 ) {
			
			$term = esc_sql( $wpdb->esc_like( $search_term ) );
			
			$how_to_search = ' REGEXP ' . '\'[[:<:]]' . $term . '[[:>:]]\'';
	
		}
		
        $where .= ' AND ' . $wpdb->posts . '.post_content  ' . $how_to_search;
    }
    return $where;
}
 
 

/*  By default Relevanssi cleans out ampersands (and other punctuation). 
 *  In order to keep them, you’ll have to modify the way the punctuation is handled 
 *  @see http://www.relevanssi.com/knowledge-base/words-ampersands-cant-found/
 */
function saveampersands_1($a) {
    $a = str_replace('&amp;', 'AMPERSAND', $a);
    $a = str_replace('&', 'AMPERSAND', $a);
    return $a;
}
add_filter('relevanssi_remove_punctuation', 'saveampersands_1', 9);
 

function saveampersands_2($a) {
    $a = str_replace('AMPERSAND', '&', $a);
    return $a;
}
add_filter('relevanssi_remove_punctuation', 'saveampersands_2', 11); 
 
 
 

function rlv_fix_order($orderby) {
    return "relevance";
}
//add_filter('relevanssi_orderby', 'rlv_fix_order');
 
 /* In order to be able to add and work with 
  * custom query vars there is a need to add them to the public query variables available to WP_Query. 
  */
function add_query_vars_filter( $vars ){
  $vars[] = "exactsearch";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );



function rlv_asc_date($query) {
	
	
			// Get the ordering
			$orderby = filter_input(INPUT_GET, 'orderby', FILTER_SANITIZE_STRING);

			if ($orderby == FALSE OR in_array( $orderby, [STORY_GET_PARAM__NEW_STORIES, STORY_GET_PARAM__OLD_STORIES, STORY_GET_PARAM__TITLE_DESC, STORY_GET_PARAM__TITLE_ASC] ) == FALSE ) {
				$orderby = STORY_GET_PARAM__NEW_STORIES;	
			}
			
			$order = 'desc';
			
			switch ( $orderby ) {
				
				case STORY_GET_PARAM__NEW_STORIES:
					$orderby = 'post_date';
					$order   = 'desc';
				break;
				
				case STORY_GET_PARAM__OLD_STORIES:
					$orderby = 'post_date';
					$order   = 'asc';
				break;
				
				case STORY_GET_PARAM__TITLE_DESC:
					$orderby = 'post_title';
					$order   = 'desc';
				break;
				
				case STORY_GET_PARAM__TITLE_ASC:
					$orderby = 'post_title';
					$order   = 'asc';
				break;
				
				case STORY_GET_PARAM__BEST_MATCH:
					$orderby = 'relevance';
					$order   = 'asc';
				break;
				
			};
			
			
    $query->set('orderby', $orderby);
	$query->set('order'  , $order);
	

	/*
	$is_exact_search = '';//get_query_var('exactsearch');
	if ( $is_exact_search == 'true' )
	{
		$search_string = '"' . get_query_var('s') . '"';
		$query->set('s', $search_string );
	}*/
	
    return $query;
}
add_filter('relevanssi_modify_wp_query', 'rlv_asc_date'); // 2019


 
function custom_field_weights($match) {
	
	$studentName = get_post_meta($match->doc, 'acf-story-student-fname', true);
	
	$searched_string = get_query_var('s');


	if ( $searched_string == $studentName ) { 
 		$match->weight = $match->weight * 2;
	}
	else {
		$match->weight = $match->weight / 2;
	}
	
	return $match;
}
add_filter('relevanssi_match', 'custom_field_weights'); 
 

 add_filter('relevanssi_match', 'cfdetail');
function cfdetail($match) {
	global $customfield_data;
	$customfield_data[$match->doc] = $match->customfield_detail;
	return $match;
}



add_filter('relevanssi_match', 'rlv_cf_boost');
function rlv_cf_boost($match) {
	$detail = unserialize($match->customfield_detail);
	if (!empty($detail)) {
		if (isset($detail['acf-story-student-fname'])) {
			$match->weight = $match->weight * 10;
		}
	}
	return $match;
}


 
 
/* 
function extra_user_weight($match) {
	$post_type = relevanssi_get_post_type($match->doc);
	if ("user" == $post_type) {
		$match->weight = $match->weight * 2;
	}
	return $match;
}
add_filter('relevanssi_match', 'extra_user_weight');
*/

 
 
function search_get_schools_ajax()
{

    if ( isset($_REQUEST) )
    {

        // Get the country ID from the user
        $cityId = $_POST['cityid'];


        // Get the top level (All the districts)
        $schools = get_terms ( SCHOOLS_TAXONOMY , array(
                                                            'hide_empty' => true,
                                                            'parent'     => $cityId,
                                                            'orderby'    => 'name',
                                                            'order'      => 'ASC',

                                                        )
                              );


        $outputString = null;

        if ( ! empty( $schools ) && ! is_wp_error( $schools ) )
        {

            //$outputString =  '<label for="school-select" class="title">מיון לפי בית ספר</label><br/>';
            
			$outputString .= '<input type="hidden" id="new-school-selected" name="new-school-selected" value="true">';
			
			$outputString .= '<select id="school-select" name="school-select">';

            $outputString .= '<option value="-1">כל בתי הספר</option> <br/>';

            foreach ($schools as $school)
            {
                $outputString .= "<option value='$school->term_id'>$school->name</option> <br/>";
            }
            $outputString .= '</select>';

            echo $outputString;

        }
        else
        {
            echo 'בית ספר לא נמצא';
        }

    }
    die();

}
add_action( 'wp_ajax_nopriv_search_get_schools_ajax' , 'search_get_schools_ajax' );
add_action( 'wp_ajax_search_get_schools_ajax'        , 'search_get_schools_ajax' );








function search_get_country_cities_ajax()
{

    if ( isset($_REQUEST) )
    {

        // Get the country ID from the user
        $countryId = $_POST['countryId'];

		
		// Get the top level (All the districts)
		$districts = get_terms(SCHOOLS_TAXONOMY, array(
														'hide_empty' => true,
														'parent'     => $countryId
														  )
								  );

									
		$all_cities = array();
		$outputString = null;

		if ( !empty( $districts ) && !is_wp_error( $districts ) )
		{
			foreach ( $districts as $district )
			{
				// Get all the cities under the district $district
				$cities = get_terms(SCHOOLS_TAXONOMY, array(
																'parent' => $district->term_id,
																'hide_empty' => true,
																'orderby' => 'name'
														   )
								   );

				if ( !empty( $cities ) && !is_wp_error( $cities ) )
				{
					$all_cities = array_merge($all_cities, $cities);
				}
			}
		}

		if (!empty($all_cities) && !is_wp_error($all_cities)) 
		{

				   $outputString .= '<option value="-1">בחרו יישוב</option> <br/>';
				   foreach ( $all_cities as $city )
				   {
						$outputString .= '<option value="' . $city->term_id . '" ' . (( $cityId ==  $city->term_id ) ? ' selected ' : '') . ' >' . $city->name . '</option> <br/>';
				   }

				   echo $outputString;

		}
		else
		{
			echo "<option value='-1'>לא נמצאו ערים במערכת</option> <br/>";
		}

    }
    die();

}
add_action( 'wp_ajax_nopriv_search_get_country_cities_ajax' , 'search_get_country_cities_ajax' );
add_action( 'wp_ajax_search_get_country_cities_ajax'        , 'search_get_country_cities_ajax' );