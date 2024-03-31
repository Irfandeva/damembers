<?php
function send_excel_to_mail() {
  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  require_once(plugin_dir_path(__FILE__) . 'fetch_data.php');
  global $wpdb;
  $result['status'] = 'ok';
  $result['message'] = '';

  if (isset($_POST['send-mail']) && isset($_POST['field_ids'])) {
    $mail_address = $_POST['receivers-mail'];
    if (empty($mail_address)) {
      $result['status'] = 'error';
      $result['message'] = 'Please provide your email address';
      return $result;
    }
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
      $result =   send_Mail($membersArray, $mail_address);
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

function send_Mail($array, $mail_address) {

  $result['status'] = 'ok';
  $result['message'] = '';
  $temp_file = generate_excel($array);
  if (!file_exists($temp_file) || !is_readable($temp_file)) {
    $result['status'] = 'error';
    $result['message'] = 'Error: Temporary file does not exist or is not readable.';
    return $result;
  }

  // Email parameters
  $to = "$mail_address";
  $subject = 'Excel file attached';
  $message = 'Please find the attached Excel file.';
  $headers = array('Content-Type: text/html; charset=UTF-8');

  // Attach Excel file to email
  $attachments = array(
    $temp_file
  );

  // Send email with attachment
  $mail_result = wp_mail($to, $subject, $message, $headers, $attachments);
  //clear temp memorys
  unlink($temp_file);
  if ($mail_result) {
    $result['status'] = 'ok';
    $result['message'] = 'Email sent successfully.';
  } else {
    $result['status'] = 'error';
    $result['message'] = 'Failed to send email.';
  }
  return $result;
}

function generate_excel($array, $filename = 'da-members', $title = 'damembers') {
  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $spreadsheet->getActiveSheet()->fromArray($array);
  $spreadsheet->getActiveSheet()->setTitle($title);
  // Generate temporary file path with file extension
  $temp_file = tempnam(sys_get_temp_dir(),  $filename . '_') . '.' . 'xls';
  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
  // Save Excel data to a temporary file
  $writer->save($temp_file);
  // Reurn temporary file path with file extension
  return $temp_file;
}
