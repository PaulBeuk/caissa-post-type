<?php
/*
Plugin Name: WP caissa post type
Plugin URI: https://github.com/paulbeuk/wp_caissa_post_type
Description: A extention of wp default post type, including separate title and intro fields
Version: 0.9
Author: Paul van Beukering
Author URI: http:/www.paulbeuk.nl
License: GPL2
*/
/*
Copyright 2014  Paul van Beukering (email : paul.van.beukering@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('Caissa_Post_Type')) {
	class Caissa_Post_Type {
		/**
		 * Construct the plugin object
		 */
		public function __construct() {
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			$Caissa_Post_Type_Settings = new Caissa_Post_Type_Settings();

			// Register custom post types
			require_once(sprintf("%s/post-types/basic_caissa_post_type.php", dirname(__FILE__)));
			//$Post_Type_Template = new Post_Type_Template();
			$Basic_Caissa_Post_Type = new Basic_Caissa_Post_Type();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
		}

		/**
		 * Activate the plugin
		 */
		public static function activate() {}

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate() {}

		// Add the settings link to the plugins page
		function plugin_settings_link($links) {
			$settings_link = '<a href="options-general.php?page=wp_plugin_template">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}


	}
}

if(class_exists('Caissa_Post_Type')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Caissa_Post_Type', 'activate'));
	register_deactivation_hook(__FILE__, array('Caissa_Post_Type', 'deactivate'));

	// instantiate the plugin class
	$wp_plugin_template = new Caissa_Post_Type();

}
