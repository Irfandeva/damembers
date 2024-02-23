<?php

/**
 * Plugin Name: DA Members
 * Plugin URI: localhost
 * Description: A plugin for DA Members
 * Version: 1.0
 * Author: Irfan Farooq Deva
 * Author URI: not found
 **/


require(plugin_dir_path(__FILE__) . '/db/da_members_create_table.php');

register_activation_hook(__FILE__, 'create_table');

function enqueue_css_js($hook) {
  if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('da-members-script', plugin_dir_url(__FILE__) . '/public/js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('da-members-style', plugin_dir_url(__FILE__) . '/public/css/styles.css');
  }
}
add_action('admin_enqueue_scripts', 'enqueue_css_js');


function admin_menu_item() {
  add_menu_page('DA Members', 'DA Members', 'manage_options', 'da-members', 'da_members_show', 'dashicons-schedule', 3);
  add_submenu_page('', 'Add Member', 'Add Member', 'manage_options', 'da-members-add', 'da_members_add');
  add_submenu_page('', 'Edit Member', 'Edit Member', 'manage_options', 'da-members-edit', 'da_members_edit');
}
add_action('admin_menu', 'admin_menu_item');
add_action('admin_footer', 'del_popup');

require(plugin_dir_path(__FILE__) . '/db/da_members_crud.php');
require(plugin_dir_path(__FILE__) . '_inc/da_members_popups.php');
