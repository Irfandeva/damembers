<?php

/**
 * Plugin Name: DA Members
 * Plugin URI: localhost
 * Description: A plugin for DA Members.
 * Version: 1.0
 * Author: Irfan Farooq Deva
 * Author URI: not found
 **/
add_action('admin_enqueue_scripts', 'enqueue_css_js');
function enqueue_css_js() {
  if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('da-members-script', plugin_dir_url(__FILE__) . '/public/js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('da-members-style', plugin_dir_url(__FILE__) . '/public/css/styles.css');
  }
}

require(plugin_dir_path(__FILE__) . '/db/config.php');
require(plugin_dir_path(__FILE__) . '/utils/helper_fns.php');
require(plugin_dir_path(__FILE__) . '/db/da_members_tables.php');
register_activation_hook(__FILE__, 'initTables');
register_deactivation_hook(__FILE__, 'resetTables');

require(plugin_dir_path(__FILE__) . '/db/da_members_crud.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_form_fields.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_popups.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_excel.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_settings.php');


add_action('admin_menu', 'adminMenuItem');
add_action('admin_footer', 'deletePopup');



function adminMenuItem() {
  add_menu_page('DA Members', 'DA Members', 'manage_options', 'da-members', 'daMembersShow', 'dashicons-schedule', 3);
  add_submenu_page('', 'Add Member', 'Add Member', 'manage_options', 'da-members-add', 'daMembersAdd');
  add_submenu_page('', 'Edit Member', 'Edit Member', 'manage_options', 'da-members-edit', 'daMembersEdit');
  add_submenu_page('da-members', 'Manage Form Fields', 'Manage Form Fields', 'manage_options', 'manage-form-fields', 'manageFormFields');
  add_submenu_page('', 'Upload From Excel', 'Upload From Excel', 'manage_options', 'upload-from-excel', 'uploadFromExcel');
  add_submenu_page('', 'Download', 'Download', 'manage_options', 'download', 'downloadPage');
  add_submenu_page('da-members', 'Settings', 'Settings', 'manage_options', 'da-members-settings', 'da_members_settings');
}
