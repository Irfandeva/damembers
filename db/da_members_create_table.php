<?php
function create_table() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name_da_members = $wpdb->prefix . 'members';
  $daMembersTableQuery = "DROP TABLE IF EXISTS $table_name_da_members;
     CREATE TABLE $table_name_da_members (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     first_name varchar(50) NOT NULL,
     last_name varchar(50) NOT NULL,
     bio varchar(50) NOT NULL,
     country varchar(20) NOT NULL,
     designation varchar(50) NOT NULL,
     PRIMARY KEY (id)
   ) $charset_collate;";

  $table_name_forum_inputs = $wpdb->prefix . 'form_inputs';
  $daMembersFormInputsTableQuery = "DROP TABLE IF EXISTS $table_name_forum_inputs;
     CREATE TABLE $table_name_forum_inputs (
     id mediumint(6) NOT NULL AUTO_INCREMENT,
     type varchar(10) NOT NULL,
     label varchar(20) NOT NULL,
     data_type varchar(10) NOT NULL,
     size smallint(6) NOT NULL,
     required tinyint(1) NOT NULL,
     PRIMARY KEY (id)
   ) $charset_collate;";

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  dbDelta($daMembersTableQuery);
  dbDelta($daMembersFormInputsTableQuery);
}
