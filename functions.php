<?php
const pattern = '/\[game/';
const withGame = '<span><img class="ca-game" src="/wp-content/avatars/caissagame.png" width="60" heigth="60"></span>';

// Adding Footer Widgets Function
function footer_widgets()  {
  register_sidebar(array(
	'name'          => __( 'Footer 1', 'the-bootstrap' ),
	'id'            => 'footer-1',
	'before_widget' => '<aside class="widget f-well %2$s" id="%1$s">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h2 class="f-widget-title">',
	'after_title'   => '</h2>',
  ) );

  register_sidebar(array(
	'name'          => __( 'Footer 2', 'the-bootstrap' ),
	'id'            => 'footer-2',
	'before_widget' => '<aside class="widget f-well %2$s" id="%1$s">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h2 class="f-widget-title">',
	'after_title'   => '</h2>',
  ) );

/*
  register_sidebar(array(
	'name'          => __( 'Footer 3', 'the-bootstrap' ),
	'id'            => 'footer-3',
	'before_widget' => '<aside class="widget f-well %2$s" id="%1$s">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h2 class="f-widget-title">',
	'after_title'   => '</h2>',
  ) );
*/
  register_sidebar(array(
	'name'          => __( 'Footer 4', 'the-bootstrap' ),
	'id'            => 'footer-4',
	'before_widget' => '<aside class="widget f-well %2$s" id="%1$s">',
	'after_widget'  => '</aside>',
	'before_title'  => '<h2 class="f-widget-title">',
	'after_title'   => '</h2>',
  ) );

}
add_filter('show_admin_bar', '__return_false');

// Hook The 'widgets_init' Action
add_action( 'widgets_init', 'footer_widgets', 11);

function add_caissa_posts($query) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$query->set('post_type', array( 'post', 'caissa-post' ) );
		$query->set( 'cat', '-50' );
	}
	if ( $query->is_author ) {
		$query->set('post_type', array( 'post', 'caissa-post' ) );
	} else if ( $query->is_category ) {
		$query->set('post_type', array( 'post', 'caissa-post' ) );
	}
	//remove_action( 'pre_get_posts', '__set_wiki_for_author' ); // run once!
}

add_action('pre_get_posts','add_caissa_posts');

function change_avatar_css($class) {
	error_log('change');
	$class = str_replace("class='avatar", "class='author_gravatar alignright_icon ", $class) ;
	return $class;
}

add_action('admin_menu','remove_default_post_type');
function remove_default_post_type() {
	remove_menu_page('edit.php');
}
add_action( 'admin_bar_menu', 'wp22_remove_newpost', 999 );
function wp22_remove_newpost ( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'new-post' );
}

function count_custom_posts_1($userid) {
	global $wpdb;
	$query = "SELECT COUNT(ID) FROM ".$wpdb->prefix."posts WHERE post_author = ".$userid.
		" AND post_type = 'caissa-post' AND post_status = 'publish'";
	return $wpdb->get_var( $query );
}

if ( ! function_exists( 'caissa_article_header' ) ) :
function caissa_article_header($meta_subtitle,$userid,$featured) {
	$postit = get_post(get_the_ID());
	$hasGame = preg_match(pattern, $postit->post_content);
	$user_post_count = count_custom_posts_1($userid);
	printf( __( '<div class="ca-header ca-font">
					<div class="ca-titles">
						<div class="ca-title">
							%1$s
						</div>
						<div class="ca-subtitle">%2$s</div>
					</div>
					<div class="ca-right">
						<h5>
						<div class="ca-door">door</div>
<div>
	<span class="author vcard">
		<a class="url fn n ca-author" href="%9$s" title="%10$s" rel="author">%5$s</a>
		<a class="url fn n" href="%3$s" title="%4$s" rel="author">
			<span class="number-of-articles">(%6$s)</span>
		</a>
	</span>
</div>
							<div class="ca-date">
								<time class="entry-date" datetime="DD-MM-YYYY" pubdate>%7$s</time>
							</div>
						</h5>
					</div>
					<div class="ca-right">
						<h1>
							%11$s
							<span class="author vcard">
								<a class="url fn n ca-footer" href="%9$s" title="%10$s" rel="author">
									%8$s
								</a>
							</span>
						</h1>
					</div>
				</div>
				<div class="ca-clear"></div>' , 'the-bootstrap' ),
		get_the_title(),
		$meta_subtitle,
		esc_url( get_author_posts_url( $userid , get_the_author_meta( 'user_nicename' ) )),
		esc_attr( sprintf( __( 'Bekijk alle posts van %s', 'the-bootstrap'), get_the_author())),
		get_the_author_meta('display_name'),
		$user_post_count,
		get_the_date(),
		get_avatar( $userid , 60),
		esc_url( site_url('/profile/'.get_the_author_meta( 'user_login' ), 'http')  ),
		esc_attr( sprintf( __( 'Wie is %s', 'the-bootstrap'), get_the_author())),
		$hasGame == '1' ? withGame : ''
	);
	if ( $featured ) {
		printf( '<h3 class="entry-format">%s</h3>', _e( 'Featured', 'the-bootstrap' ) );
	}
}
endif;

if ( ! function_exists( 'caissa_article_footer' ) ) :
function caissa_article_footer($categories_list) {
	if( current_user_can( 'edit_post' , $post_id ) ) {
		$edit_link = sprintf('
			<span class="ca-separator">&nbsp;|&nbsp;</span>
			<a class="url fn n ca-edit" href="%1$s" title="%2$s" rel="author">%3$s</a>',
			get_edit_post_link( get_the_ID() ),
			'Bewerken',
			'Bewerken');
	} else {
		$edit_link = '';
	}
	$comments_count = wp_count_comments(get_the_ID());
	$count = $comments_count->approved;

	if ( $count > 1 ) {
		$comment_text = sprintf('<span class="ca-separator">&nbsp;|&nbsp;</span><span class="leave-reply ca-font ca-footer">%s %s</span>',
			$comments_count->approved, __( 'reacties', 'the-bootstrap' ));
	} else if ( $count == 1 ) {
		$comment_text = sprintf('<span class="ca-separator">&nbsp;|&nbsp;</span><span class="leave-reply ca-font ca-footer">1 %s</span>',
			__( 'reactie', 'the-bootstrap' ));
	} else {
		$comment_text = '';
	}

	$comment_url = sprintf( '<a href=%s><span class="ca-footer ca-font">%s</span></a>',
		esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#respond', __( 'Reageer', 'the-bootstrap' ));

	printf( __( '<footer class="entry-footer">
					<div class="ca-footer ca-font">
						<div class="ca-left">
							<span>%1$s</span>
							%2$s
							%3$s
						</div>
						<div class="ca-right"><span>Geplaatst in %4$s</span></div>
					</div>
					<div class="ca-clear"></div>
				</footer>', 'the-bootstrap' ),
		$comment_url,
		$comment_text,
		$edit_link,
		$categories_list
	);
}
endif;

if ( ! function_exists( 'caissa_article_pac' ) ) :
function caissa_article_pac() {
	if( is_home() ) {
		error_log("is home");
	} else if( is_category() ) {
		error_log("is is_category");
	} else if( is_author() ) {
		error_log("is author");
	} else if( is_single() ) {
		error_log("is single");
	}  else {
		error_log("is else");
	}
}
endif;

if ( ! function_exists( 'caissa_article_footer_home' ) ) :
function caissa_article_footer_home() {
	if( current_user_can( 'edit_post' , $post_id ) ) {
		$edit_link = sprintf('
			<span class="ca-separator">&nbsp;|&nbsp;</span>
			<a class="url fn n ca-edit" href="%1$s" title="%2$s" rel="author">%3$s</a>',
			get_edit_post_link( get_the_id() ),
			'Bewerken',
			'Bewerken');
	} else {
		$edit_link = '';
	}
	$comments_count = wp_count_comments(get_the_ID());
	$count = $comments_count->approved;

	if ( $count > 1 ) {
		$comment_text = sprintf('
			<span class="ca-separator">&nbsp;|&nbsp;</span>
			<a href=%s><span class="ca-footer ca-font">%s %s</span></a>',
			esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#comments',
			$comments_count->approved,
			__( 'reacties', 'the-bootstrap' )
		);
/*		$comment_text = sprintf('
			<span class="ca-separator">&nbsp;|&nbsp;</span>
			<span class="leave-reply ca-font ca-footer">%s %s</span>',
			$comments_count->approved,
			__( 'reacties', 'the-bootstrap' )
		);
*/
	} else if ( $count == 1 ) {
		$comment_text = sprintf(
			'<span class="ca-separator">&nbsp;|&nbsp;</span>
			<a href=%s><span class="ca-footer ca-font">1 %s</span></a>',
			esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#comments',
			__( 'reactie', 'the-bootstrap' )
		);
/*
		$comment_text = sprintf(
			'<span class="ca-separator">&nbsp;|&nbsp;</span>
			<span class="leave-reply ca-font ca-footer">1 %s</span>',
			__( 'reactie', 'the-bootstrap' )
		);
*/	} else {
		$comment_text = '';
	}

	$comment_url = sprintf( '<a href=%s><span class="ca-footer ca-font">%s</span></a>',
		esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#respond', __( 'Reageer', 'the-bootstrap' ));

	printf( __( '<footer class="entry-footer">
					<div class="ca-footer ca-font">
						<div class="ca-left">
						<span class="author vcard"><a class="url fn n ca-author" href="%1$s" title="%2$s" rel="author">%3$s</a></span>
							<span class="ca-separator">&nbsp;|&nbsp;</span>
							<span>%4$s</span>
							%5$s
							%6$s
						</div>
					</div>
					<div class="ca-clear"></div>
				</footer>', 'the-bootstrap' ),
		 get_permalink(),
		is_home() ? 'naar het artikel' : 'naar aaaart',
		'Lees het hele artikel',
		$comment_url,
		$comment_text,
		$edit_link
	);
}
endif;

if ( ! function_exists( 'pac_group_list_elo_average' ) ) :
function pac_group_list_elo_average( $atts ) {
	global $wpdb;

	extract ( shortcode_atts( array( 'group' => 'No Group' ), $atts ) );
	$taxonomy = 'user-group';
	$userGroupID = $wpdb->get_var( $wpdb->prepare("SELECT term_id FROM {$wpdb->terms} t WHERE t.name = %s", $group));
	$userIDs = get_objects_in_term($userGroupID, $taxonomy);

	if ($userIDs) {
		$eloCount = 0;
		$eloTotal = 0;
		foreach( $userIDs as $userID ) {
			$elo = get_user_meta($userID , 'Custom-KNSB-Elo' , True );

			if ( ! empty( $elo ) ) {
				$eloTotal += $elo;
				$eloCount += 1;
			}
		}
		if ( $eloTotal > 0 ) {
			return round( $eloTotal/$eloCount , 0 , PHP_ROUND_HALF_UP );
		} else {
			return 'no rating';
		}
	} else {
		return 'Group not found';
	}
}
endif;
add_shortcode( 'team-elo', 'pac_group_list_elo_average' );
/*
add_action('init', 'pac_add_rewrite_rules');
function pac_add_rewrite_rules() {
	error_log("pac_add_rewrite_rules");
	global $wp_rewrite;
	$wp_rewrite->add_rewrite_tag('%title%', '([^/]+)', 'title=');
	$wp_rewrite->add_rewrite_tag('%subtitle%', '([^/]+)', 'subtitle=');
	$wp_rewrite->add_permastruct('caissa-post', '/%title%/%subtitle%/', false);
}

add_filter('post_type_link', 'caissa_post_permalinks', 10, 3);

function caissa_post_permalinks($permalink, $post, $leavename) {
	error_log("caissa_post_permalinks: " . $permalink );
	$post_id = $post->ID;
	if($post->post_type != 'caissa-post' || empty($permalink) ||
		in_array($post->post_status, array('draft', 'pending', 'auto-draft'))) {
		return $permalink;
	}

	$var1 = get_post_meta($post_id, 'meta_subtitle', true);
	$var1 = sanitize_title($var1);
	if(!$var1) { $var1 = 'no_subtitle'; }
	$permalink = str_replace('%subtitle%', $var1, $permalink);

	$var2 = sanitize_title($post->post_title);
	if(!$var2) { $var2 = 'no-title'; }
	$permalink = str_replace('%title%', $var2, $permalink);

	return $permalink;
}
*/

function pac_get_rating( $userIDs ) {
	$elos = array();
	foreach( $userIDs as $userID ) {
		$elos[$userID] = get_user_meta($userID , 'Custom-KNSB-Elo' , True );
	}
	$eloCount = 0;
	$eloTotal = 0;
	foreach( $userIDs as $userID ) {
		if ( ! empty( $elos[$userID] ) ) {
			$eloTotal += $elos[$userID];
			$eloCount += 1;
		}
	}
	if ( $eloTotal > 0 ) {
		$elos['team'] = round( $eloTotal/$eloCount , 0 , PHP_ROUND_HALF_UP );
	} else {
		$elos['team'] = '-';
	}
	return $elos;
}

if ( ! function_exists( 'pac_group_list_shortcode' ) ) :
function pac_group_list_shortcode( $atts ) {
	global $wpdb;

	extract ( shortcode_atts( array( 'group' => 'No Group' , 'team_elo' => 'TRUE' ), $atts ) );
	$taxonomy = 'user-group';
	$userGroupID = $wpdb->get_var( $wpdb->prepare("SELECT term_id FROM {$wpdb->terms} t WHERE t.name = %s", $group));
	$userIDs = get_objects_in_term($userGroupID, $taxonomy);

	if ($userIDs) {
		$elos = pac_get_rating( $userIDs );
		$teamAverage = $elos['team'];

		$content = '<div class="ca-header ca-font" style="margin:25px 2px 25px 2px">';
		if( $team_elo === 'TRUE' ) {
			$content .= sprintf(
				'<div class="ca-team">
					<span class="ca-team-rating-name">Team</a>
					<span class="ca-team-rating">%1$s</span>
				</div>
				<div class="ca-clear"></div>
				<hr style="margin: 1px 0 2px 0;height:1px;border:none;" />', $teamAverage);
		}
		foreach( $userIDs as $userID ) {
			$user = get_user_by('id', $userID);

			$userItem = sprintf(
				'<div style="margin:5px 0 5px 0">
					<a class="url fn n" href="%2$s" title="%3$s" rel="author">%1$s</a>
					<a class="url fn n ca-team-rating-name" href="%2$s" title="%3$s" rel="author">%4$s</a>
					<span class="ca-rating">%5$s</span>
					<hr style="margin: 1px 0 2px 0;height:1px;border:none;" />
				</div>',
				get_avatar( $user->ID, 40 ),
				esc_url( site_url('/profile/'.$user->user_login, 'http') ),
				'bekijk profiel van '.$user->display_name,
				$user->display_name,
				$elos[$userID]);

			$content .= $userItem;
		}
		$content .= '<div class="ca-clear"></div>';
		$content .= '</div>';
	} else {
		$content = '<div class="group-list group-list-none">-</div>';
	}
	return $content;
}
endif;
add_shortcode( 'team-list', 'pac_group_list_shortcode' );

/*
function getUserArray() {
}
*/

function wpse_lost_password_redirect() {
	wp_redirect( home_url() );
	exit;
}
/* add_action('password_reset', 'wpse_lost_password_redirect'); */

function myfeed_request($qv) {
	if (isset($qv['feed']))
		$qv['post_type'] = get_post_types();
	return $qv;
}
add_filter('request', 'myfeed_request');

if ( ! function_exists( 'display_custom_google_search' ) ) :
function display_custom_google_search() {
	return display_gsc_results();
}
endif;
add_shortcode( 'display_gsc', 'display_custom_google_search' );

/*
 * Members only hack
 */
function member_only_shortcode($atts, $content = null)
{
    if (is_user_logged_in() && !is_null($content) && !is_feed()) {
        return $content;
    } else {
        return 'Je moet ingelogd zijn om deze tekst te kunnen lezen. Heb je hulp nodig bij het inloggen, we helpen je graag, stuur maar een mailtje naar: <a href="mailto:info@caissa-amsterdam.nl">info@caissa-amsterdam.nl</a>';
    }
}
add_shortcode('member_only', 'member_only_shortcode');
?>
