<?php
/*
Plugin Name: User Groups
Description: Add user groups to WordPress
Version: 1.1.1
Author: Katz Web Services, Inc.
Author URI: http://www.idxplus.net
*/

add_action('plugins_loaded', array('PAC_User_Groups', 'load'), 200);

if(!class_exists('PAC_User_Groups')) {
class PAC_User_Groups {

	function load() {
		$PAC_User_Groups = new PAC_User_Groups();
	}

	function pac_group_list_shortcode( $atts ) {
	    // Get the global $wpdb object
	    global $wpdb;
	
	    // Extract the parameters and set the default
	    extract ( shortcode_atts( array(
	        'group' => 'No Group' // No Group is a defined user-group
	        ), $atts ) );
	
	    // The taxonomy name will be used to get the objects assigned to that group
	    $taxonomy = 'user-group';
	
	    // Use a dBase query to get the ID of the user group
	    $userGroupID = $wpdb->get_var(
	                    $wpdb->prepare("SELECT term_id
	                        FROM {$wpdb->terms} t
	                        WHERE t.name = %s", $group));
	
	    // Now grab the object IDs (aka user IDs) associated with the user-group
	    $userIDs = get_objects_in_term($userGroupID, $taxonomy);
	
	    // Check if any user IDs were returned; if so, display!
	    // If not, notify visitor none were found.
	    if ($userIDs) {
	        $content = "<div class='group-list'> <ul>";
	        foreach( $userIDs as $userID ) {
	            $user = get_user_by('id', $userID);
	            $content .= "<li>";
	            $content .= get_avatar( $user->ID, 60 );
	            $content .= "<h3>" . $user->display_name . "</h3>";
	            $content .= "<p><a href='". get_author_posts_url( $user->ID ) . "' class='more-info-icon'>More info</a>";
	            $content .= "<!-- add more here --></p>";
	            $content .= "</li>";
	        }
	        $content .= "</ul></div>";
	    } else {
	        $content =
	        "<div class='group-list group-list-none'>Returned no results</div>";
	
	    }
	    return $content;
	}
}
}
add_shortcode( 'pac-group-list', array( 'PAC_User_Groups', 'pac_group_list_shortcode' ) );
