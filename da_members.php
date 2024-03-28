<?php

/**
 * Plugin Name: DA Members
 * Plugin URI: localhost
 * Description: A plugin for DA Members.
 * Version: 1.0
 * Author: Irfan Farooq Deva
 * Author URI: not found
 **/
$result = array();
$result['status'] = 'ok';
$result['message'] = '';
add_action('admin_enqueue_scripts', 'enqueue_css_js');
add_action('wp_enqueue_scripts', 'enqueue_user_css_js');
add_action('init', 'export_to_excel');
// add_action('init', 'output_excel');
//for admin
function enqueue_css_js() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('da-members-script', plugin_dir_url(__FILE__) . '/public/js/script.js', array('jquery'), '1.0', true);
  wp_enqueue_style('da-members-style', plugin_dir_url(__FILE__) . '/public/css/styles.css');

  wp_enqueue_script('tinymce-jq', plugin_dir_url(__FILE__) . 'tinymce/tinymce.min.js', array('jquery'), null, true);
  wp_enqueue_script('tinymce-script', plugin_dir_url(__FILE__) . 'tinymce/script.js', array(), null, true);
  // wp_enqueue_style('tinymce-css', plugin_dir_url(__FILE__ . '/tinymce/tinymce.min.css'), array(), null);
}
//for user
function enqueue_user_css_js() {
  wp_enqueue_script('jquery');
  wp_enqueue_script('da-members-user-script', plugin_dir_url(__FILE__) . '/public/js/user_script.js', array('jquery'), '1.0', true);
  wp_enqueue_style('da-members-user-style', plugin_dir_url(__FILE__) . '/public/css/user_styles.css');
}

require(plugin_dir_path(__FILE__) . '/db/config.php');
require(plugin_dir_path(__FILE__) . '/utils/helper_fns.php');
require(plugin_dir_path(__FILE__) . '/db/da_members_tables.php');
require(plugin_dir_path(__FILE__) . '/shortcodes/members_by_department_sc.php');
require(plugin_dir_path(__FILE__) . '/shortcodes/all_da_members_sc.php');

register_activation_hook(__FILE__, 'init_tables');
register_deactivation_hook(__FILE__, 'reset_tables');

require(plugin_dir_path(__FILE__) . '/db/da_members_crud.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_form_fields.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_popups.php');
require(plugin_dir_path(__FILE__) . 'inc/da_members_excel.php');
require(plugin_dir_path(__FILE__) . 'excel/export_to_excel.php');
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


//SHORT CODES
if (!is_admin()) {
  add_shortcode('members_by_department', 'members_by_department_sc');
  add_shortcode('da_members', 'all_da_members_sc');
}
