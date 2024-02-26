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
     address varchar(50) NOT NULL,
     consituency varchar(20) NOT NULL,
     member_type varchar(50) NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

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

  //  initial form inputs
  // (type,label,data_type,size,requird)
  $inputs = array(
    array('text', 'first_name', 'varchar', '20', '1'),
    array('text', 'last_name', 'varchar', '20', '1'),
    array('text', 'bio', 'varchar', '50', '0'),
    array('select', 'country', 'varchar', '20', '0'),
    array('text', 'designation', 'varchar', '20', '0'),
    array('text', 'address', 'varchar', '20', '0'),
    array('text', 'consituency', 'varchar', '20', '0'),
    array('text', 'member_type', 'varchar', '20', '0'),
  );

  // Prepare data for insertion
  $data_to_insert = array();
  foreach ($inputs as $input) {
    $data_to_insert[] = array(
      'type' => $input[0],
      'label' => $input[1],
      'data_type' => $input[2],
      'size' => $input[3],
      'required' => $input[4],

    );
  }

  foreach ($data_to_insert as $dti) {
    $wpdb->insert($table_name_forum_inputs, $dti);
  }
}
