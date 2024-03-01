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
     required tinyint(1) DEFAULT '0',
     PRIMARY KEY (id)
   ) $charset_collate;";
  dbDelta($daMembersFormFieldsTableQuery);

  //[label,priority,field type,values,required]
  //1.label=>label name
  //2.priority=>1-n
  //3.field type=>type of input , eg, text, number,select etc
  //4.values=>name of values array in case field type is select
  //5.required=>manditory  or not at the time of filling form
  $defaultFormFields = array(
    array('first_name', '1', 'text', '', '1'),
    array('last_name', '2', 'text', '', '1'),
    array('bio', '3', 'text', '', '0'),
    array('country', '4', 'select', 'countries', '0'),
    array('designation', '5', 'text', '', '0'),
    array('address', '6', 'text', '', '0'),
    array('consituency', '7',  'text', '', '0'),
    array('member_type', '8', 'select', 'member_types', '0'),
    array('member_since', '9', 'date', '', '0'),
  );

  $defaultFormFieldsToInsert = array();
  foreach ($defaultFormFields as $formField) {
    $defaultFormFieldsToInsert[] = array(
      'label' => $formField[0],
      'priority' => $formField[1],
      'field_type' => $formField[2],
      'field_values' => $formField[3],
      'required' => $formField[4],
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
  // $wpdb->query($dropDaMembers);
  $wpdb->query($dropFormFieldsTable);
}
