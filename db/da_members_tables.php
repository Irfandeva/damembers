<?php
function initTables() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  //wb members table
  $table_name_da_members = $wpdb->prefix . 'members';
  $daMembersTableQuery = "DROP TABLE IF EXISTS $table_name_da_members;
     CREATE TABLE $table_name_da_members (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     first_name varchar(50) NOT NULL,
     last_name varchar(50) NOT NULL,
     bio varchar(50) NOT NULL,
     country varchar(20) NOT NULL,
     designation varchar(50) NOT NULL,
     address varchar(50) NOT NULL,
     consituency varchar(20) NOT NULL,
     member_type varchar(50) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     PRIMARY KEY (id)
   ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($daMembersTableQuery);
}

function resetTables() {
}
