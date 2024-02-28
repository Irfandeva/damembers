<?php

/**
 * Plugin Name: DA Members
 * Plugin URI: localhost
 * Description: A plugin for DA Members.
 * Version: 1.0
 * Author: Irfan Farooq Deva
 * Author URI: not found
 **/


require(plugin_dir_path(__FILE__) . '/db/da_members_tables.php');
register_activation_hook(__FILE__, 'initTables');
register_deactivation_hook(__FILE__, 'resetTables');

add_action('admin_enqueue_scripts', 'enqueue_css_js');
add_action('admin_menu', 'adminMenuItem');
add_action('admin_footer', 'deletePopup');
add_action('init', 'daMembersDownload');

require(plugin_dir_path(__FILE__) . '/db/da_members_crud.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_form_fields.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_popups.php');
require(plugin_dir_path(__FILE__) . 'inc/daex.php');

function enqueue_css_js($hook) {
  if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('da-members-script', plugin_dir_url(__FILE__) . '/public/js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('da-members-style', plugin_dir_url(__FILE__) . '/public/css/styles.css');
  }
}

function adminMenuItem() {
  add_menu_page('DA Members', 'DA Members', 'manage_options', 'da-members', 'daMembersShow', 'dashicons-schedule', 3);
  add_submenu_page('', 'Add Member', 'Add Member', 'manage_options', 'da-members-add', 'daMembersAdd');
  add_submenu_page('', 'Edit Member', 'Edit Member', 'manage_options', 'da-members-edit', 'daMembersEdit');
  add_submenu_page('da-members', 'Manage Form Fields', 'Manage Form Fields', 'manage_options', 'manage-form-fields', 'manageFormFields');
  add_submenu_page('da-members', 'Add Values', 'Add Values', 'manage_options', 'add-values', 'addValues');
  // add_submenu_page('da-members', 'Download', 'Download', 'manage_options', 'download', 'daMembersDownload');
}
