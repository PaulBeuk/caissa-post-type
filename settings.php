<?php
if(!class_exists('Caissa_Post_Type_Settings'))
{
	class Caissa_Post_Type_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct() {
			// register actions
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		}
		
        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init() {
        	// register your plugin's settings
        	register_setting('caissa_post-group', 'setting_a');
        	register_setting('caissa_post-group', 'setting_b');

        	// add your settings section
        	add_settings_section(
        	    'caissa_post-section', 
        	    'WP Plugin Template Settings', 
        	    array(&$this, 'settings_section_caissa_post'), 
        	    'caissa_post'
        	);
        	
        	// add your setting's fields
            add_settings_field(
                'caissa_post-setting_a', 
                'Setting A', 
                array(&$this, 'settings_field_input_text'), 
                'caissa_post', 
                'caissa_post-section',
                array(
                    'field' => 'setting_a'
                )
            );
            add_settings_field(
                'caissa_post-setting_b', 
                'Setting B', 
                array(&$this, 'settings_field_input_text'), 
                'caissa_post', 
                'caissa_post-section',
                array(
                    'field' => 'setting_b'
                )
            );
        }
        
        public function settings_section_caissa_post() {
            // Think of this as help text for the section.
            echo 'These settings do things for the WP Plugin Template.';
        }
        
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args) {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        }
        
        /**
         * add a menu
         */		
        public function add_menu() {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'WP Plugin Template Settings', 
        	    'WP Plugin Template', 
        	    'manage_options', 
        	    'caissa_post', 
        	    array(&$this, 'caissa_post_page')
        	);
        }
    
        /**
         * Menu Callback
         */		
        public function caissa_post_page() {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}
	
        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        }
    }
}
