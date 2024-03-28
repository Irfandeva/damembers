<?php
function export_to_excel() {
  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  require_once(plugin_dir_path(__FILE__) . 'fetch_data.php');
  global $wpdb;
  $result['status'] = 'ok';
  $result['message'] = '';

  if (isset($_POST['excel-download']) && isset($_POST['field_ids'])) {
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
    $records_ = fetch_data_for_excel($department, $fields_string, $da_members_table, $created_after, $updated_after);

    if (count($records_) > 0) {
      if (array_key_exists('bio', json_decode(json_encode($records_[0]), TRUE)))
        $records = array_map('remove_html', $records_);
      else $records = $records_;
      $h = json_decode(json_encode($records[0]), TRUE);
      $header_titles = array_keys($h);
      $formatted_heading = array();
      foreach ($header_titles as $header_title) {
        $formatted_heading[] = ucwords(str_replace('_', ' ', $header_title));
      }

      $membersArray = json_decode(json_encode($records), true);
      array_unshift($membersArray, $formatted_heading);
      $file_name = "da-members-" . date('Y-m-d');
      output_excel($membersArray, $file_name);
    } else {
      $result['status'] = 'error';
      $result['message'] = 'No records found with given details.';
    }
  } else {
    $result['status'] = 'error';
    $result['message'] = 'No fields have been selected for download.';
  }
  return $result;
}

function output_excel($array, $filename = 'da-members', $title = 'damembers') {
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
  header('Cache-Control: max-age=0');
  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $spreadsheet->getActiveSheet()->fromArray($array);
  $spreadsheet->getActiveSheet()->setTitle($title);
  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
  $writer->save('php://output');
  exit();
}
