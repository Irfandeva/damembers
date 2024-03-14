<?php
function initTables() {
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $da_members_table = DA_MEMBERS_TABLE;
  $daMembersTableQuery = "DROP TABLE IF EXISTS $da_members_table;
     CREATE TABLE $da_members_table (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     first_name varchar(50) NOT NULL,
     last_name varchar(50) NOT NULL,
     bio varchar(50) NULL,
     country varchar(20) NULL,
     designation varchar(50) NULL,
     address varchar(50) NULL,
     consituency varchar(20) NULL,
     member_type varchar(50) NULL,
     member_since date DEFAULT CURRENT_DATE,
     department varchar(50) NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     PRIMARY KEY (id)
   ) $charset_collate;";
  dbDelta($daMembersTableQuery);

  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $daMembersFormFieldsTableQuery = "DROP TABLE IF EXISTS $da_members_form_fields_table;
     CREATE TABLE $da_members_form_fields_table (
     id mediumint(11) NOT NULL AUTO_INCREMENT,
     field_name varchar(20) NOT NULL,
     label varchar(20) NOT NULL,
     priority smallint(2) NOT NULL DEFAULT '0',
     required tinyint(1) NOT NULL DEFAULT '0',
     PRIMARY KEY (id)
   ) $charset_collate;";
  dbDelta($daMembersFormFieldsTableQuery);

  //[field_name,label,priority,required]
  //1.field_name column name in table
  //2.label=>label name
  //3.priority=>1-n
  //4.required=>manditory  or not at the time of filling form
  $defaultFormFields = array(
    array('first_name', 'First Name', '1',  '1'),
    array('last_name', 'Last Name', '2',   '1'),
    array('bio', 'Bio', '3', '0'),
    array('country', 'Country', '4', '0'),
    array('designation', 'Designation', '5',  '0'),
    array('address', 'Address', '6',  '0'),
    array('constituency', 'Constituency', '7',  '0'),
    array('member_type', 'Member Type', '8', '0'),
    array('member_since', 'Member Since', '9',   '0'),
    array('department', 'Department', '10',   '0'),
  );

  $defaultFormFieldsToInsert = array();
  foreach ($defaultFormFields as $formField) {
    $defaultFormFieldsToInsert[] = array(
      'field_name' => $formField[0],
      'label' => $formField[1],
      'priority' => $formField[2],
      'required' => $formField[3],
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
