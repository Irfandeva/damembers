<?php
function create_table() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'members';
  $sql = "DROP TABLE IF EXISTS $table_name;
     CREATE TABLE $table_name (
     id bigint(11) NOT NULL AUTO_INCREMENT,
     fname varchar(50) NOT NULL,
     lname varchar(50) NOT NULL,
     bio varchar(50) NOT NULL,
     desig varchar(50) NOT NULL,
     PRIMARY KEY (id)
   ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}
