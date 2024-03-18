<?php
ob_start(); // create buffer fo ob_end_clean()
require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
if (isset($_POST['excel-download'])) {
  global $wpdb;

  if (isset($_POST['field_ids'])) {
    $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
    $da_members_table = DA_MEMBERS_TABLE;
    $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");
    $fields = array();
    $label_ids_to_download = array_map('sanitize_array', $_POST['field_ids']);
    $created_after = sanitize_text_field($_POST['created_after']);
    $updated_after = sanitize_text_field($_POST['updated_after']);
    $department = sanitize_text_field($_POST['department']);
    for ($id = 0; $id < count($label_ids_to_download); $id++) {
      foreach ($form_fields as $form_field) {
        if ($form_field->id == $label_ids_to_download[$id]) {
          $fields[] = "`$form_field->field_name`";
        }
      }
    }
    $fields_string = implode(', ', $fields);
    if ($department == '-1')
      $records = $wpdb->get_results("SELECT $fields_string FROM  $da_members_table WHERE `created_at` >='$created_after' AND `updated_at` >='$updated_after';");
    else
      $records = $wpdb->get_results("SELECT $fields_string FROM  $da_members_table WHERE `created_at` >='$created_after' AND `updated_at` >='$updated_after' AND `department`='$department';");
    if (count($records) > 0) {
      $h = json_decode(json_encode($records[0]), TRUE);
      $header_titles = array_keys($h);
      $formatted_heading = array();
      foreach ($header_titles as $header_title) {
        $formatted_heading[] = ucwords(str_replace('_', ' ', $header_title));
      }

      $membersArray = json_decode(json_encode($records), true);
      array_unshift($membersArray, $formatted_heading);
      // array_columns_delete($membersArray, ['ID']);
      $file_name = "da-members-" . date('Y-m-d');
      outputEXCEL($membersArray, $file_name);
    } else {
      echo "<script>alert('no records found with details given.')</script>";
    }
  }
}

function outputEXCEL($array, $filename = 'da-members', $title = 'damembers') {
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
  header('Cache-Control: max-age=0');

  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $spreadsheet->getActiveSheet()->fromArray($array);
  $spreadsheet->getActiveSheet()->setTitle($title);
  ob_end_clean();
  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
  $writer->save('php://output');
  exit();
}
