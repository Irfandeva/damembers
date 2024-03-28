<?php
function fetch_data_for_excel($department, $fields_string, $da_members_table, $created_after, $updated_after) {
  global $wpdb;
  if ($department == '-1')
    $records = $wpdb->get_results("SELECT $fields_string FROM  $da_members_table WHERE `created_at` >='$created_after' AND `updated_at` >='$updated_after';");
  else
    $records = $wpdb->get_results("SELECT $fields_string FROM  $da_members_table WHERE `created_at` >='$created_after' AND `updated_at` >='$updated_after' AND `department`='$department';");
  return $records;
}
