<?php
function initTables() {
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  //wb members table
  $da_members_table = $wpdb->prefix . 'da_members';
  $daMembersTableQuery = "DROP TABLE IF EXISTS $da_members_table;
     CREATE TABLE $da_members_table (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     first_name varchar(50) NOT NULL,
     last_name varchar(50) NOT NULL,
     bio varchar(50) NOT NULL,
     country varchar(20) NOT NULL,
     designation varchar(50) NOT NULL,
     address varchar(50) NOT NULL,
     consituency varchar(20) NOT NULL,
     member_type varchar(50) NOT NULL,
     member_since date DEFAULT CURRENT_DATE,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     PRIMARY KEY (id)
   ) $charset_collate;";
  dbDelta($daMembersTableQuery);


  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $daMembersFormFieldsTableQuery = "DROP TABLE IF EXISTS $da_members_form_fields_table;
     CREATE TABLE $da_members_form_fields_table (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     label varchar(20) NOT NULL,
     field_type varchar(20) NOT NULL,
     field_values varchar(20),
     priority smallint(2) DEFAULT '0',
     PRIMARY KEY (id)
   ) $charset_collate;";
  dbDelta($daMembersFormFieldsTableQuery);


  $defaultFormFields = array(
    array('first_name', '1', 'text', ''),
    array('last_name', '2', 'text', ''),
    array('bio', '3', 'text', ''),
    array('country', '4', 'select', 'countries'),
    array('designation', '5', 'text', ''),
    array('address', '6', 'text', ''),
    array('consituency', '7',  'text', ''),
    array('member_type', '8', 'select', 'member_types'),
    array('member_since', '9', 'date', ''),
  );

  $defaultFormFieldsToInsert = array();
  foreach ($defaultFormFields as $formField) {
    $defaultFormFieldsToInsert[] = array(
      'label' => $formField[0],
      'priority' => $formField[1],
      'field_type' => $formField[2],
      'field_values' => $formField[3],
    );
  }
  foreach ($defaultFormFieldsToInsert as $dfti) {
    $wpdb->insert($da_members_form_fields_table, $dfti);
  }
}

function resetTables() {
  global $wpdb;
  $da_members_table = $wpdb->prefix . 'da_members';
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $dropFormFieldsTable = "DROP TABLE IF EXISTS $da_members_form_fields_table;";
  $dropDaMembers = "DROP TABLE IF EXISTS $da_members_table;";
  $wpdb->query($dropDaMembers);
  $wpdb->query($dropFormFieldsTable);
}
