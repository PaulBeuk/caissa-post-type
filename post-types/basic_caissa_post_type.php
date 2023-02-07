<?php
if(!class_exists('Basic_Caissa_Post_Type')) {

	class Basic_Caissa_Post_Type {
		const POST_TYPE	= "caissa-post";
		private $_meta	= array(
			'meta_subtitle',
			'meta_intro'
		);

		public function __construct() {
			// register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
			add_shortcode('comment_link', array(&$this, 'add_comment_link'));
		}

		public function add_comment_link( $atts ) {
			extract( shortcode_atts( array(), $atts ) );
			$comments_count = wp_count_comments(get_the_ID());
			//$comment_link = '<div class="ca-header ca-team-rating-name">';

			$comment_text = sprintf(
				'reageer<span class="ca-separator">&nbsp;|&nbsp;</span>%s %s',
				$comments_count->approved,
				$comments_count->approved == 1 ? 'reactie' : 'reacties'
			);

			$comment_link .= sprintf('<a href=%s>%s</a>',
				esc_url( apply_filters( 'the_permalink', get_permalink() ) ) . '#comments',
				$comment_text
			);

			//$comment_link .= '</div>';
			//$comment_link .= '<div class="ca-clear"></div>';
			return $comment_link;
		}

		public function init() {
			// Initialize Post Type
			$this->create_post_type();
			add_action('save_post', array(&$this, 'save_post'));
		}

		public function create_post_type() {
			register_post_type(self::POST_TYPE,
				array(
					'labels' => array(
						'name' => __('Artikelen'),
						'singular_name' => __('Artikel'),
						'add_new' => __('Nieuw artikel'),
						'add_new_item' => __('Nieuw artikel toevoegen'),
						'edit_item' => __('Pas artikel aan'),
						'new_item' => __('Nieuw artikel'),
						'view_item' => __('Bekijk artikel'),
						'search_items' => __('Zoek artikelen'),
						'not_found' => __('Artikel niet gevonden')
					),
					'public' => true,
					'has_archive' => FALSE,
					//'rewrite' => FALSE,
					'post-formats' => 'quote',
					'description' => __("Dit is een caissa artikel"),
					'supports' => array(
						'title', 'editor', 'comments' , 'author' , 'revisions'
					),
				)
			);
			register_taxonomy_for_object_type( 'category', self::POST_TYPE );
		}

		/**
		 * Save the metaboxes for this custom post type
		 */
		public function save_post($post_id) {
			// verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if(isset($_POST['post_type'])
				&& $_POST['post_type'] == self::POST_TYPE
				&& current_user_can('edit_post', $post_id)) {

				foreach($this->_meta as $field_name) {
					// Update the post's meta field
					update_post_meta($post_id, $field_name, $_POST[$field_name]);
				}
			} else {
				return;
			}
		}

		public function admin_init() {
			add_action('add_meta_boxes', array(&$this, 'add_meta_boxes_2') , 0 );
			add_action('admin_head', array(&$this, 'caissa_post_help_tab') , 0 );
		}

		public function caissa_post_help_tab() {
			$helpContent =
				'<p><strong>Beknopte beschrijving van de velden van een artikel</strong></p>' .
      			'<ul>' .
					'<li>Titel: verschijnt bovenaan het artikel (verplicht)</li>' .
					'<li>Ondertitel: verschijnt iets kleiner onder titel (niet verplicht)</li>' .
					'<li>Intro: verschijnt als eerste alinea in bold, is samen met titel en ondertitel (niet verplicht)</li>' .
					'<li>Body: verschijnt (zonder lees meer tag) alleen op de artikel pagina</li>' .
					'<li>Op de homepage wordt alleen titel, ondertitel en intro getoond, op artikel pagina worden natuurlijk alle velden van het artikel getoond</li>' .
					'<li>Wil één of meerdere partijen toevoegen? Kijk dan op: <a href="/help#partijen" target="_blank">Partijen toevoegen</a></li>' .
				'</ul>' .
				'<p><strong>Voor meer details, bekijk:</strong></p>' .
				'<p>' .
					'<a href="/help" target="_blank">' .
						'Artikel documentatie' .
					'</a>' .
				'</p>';
		  $screen = get_current_screen();
		  if ( 'caissa-post' != $screen->post_type )
			return;

		  $args = array(
			'id'      => 'caissa_help',
			'title'   => 'caissa post help',
			'content' => $helpContent );

		  $screen->add_help_tab( $args );

		}


		function add_meta_boxes_2() {
			global $_wp_post_type_features;
			if (isset($_wp_post_type_features['caissa-post']['editor']) && $_wp_post_type_features['caissa-post']['editor']) {
				unset($_wp_post_type_features['caissa-post']['editor']);
				add_meta_box(
					sprintf('caissa_post_%s_section', self::POST_TYPE),
					__('header velden'),
					array(&$this, 'add_inner_meta_boxes'),
					'caissa-post', 'normal', 'high'
				);
			}
		}

		function inner_custom_box( $post ) {
			error_log('post: ' . $post );
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
			the_editor($post->post_content);
		}
		/**
		 * called off of the add meta box
		 */
		public function add_inner_meta_boxes($post) {
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
			the_editor($post->post_content);
		}
	}
}
