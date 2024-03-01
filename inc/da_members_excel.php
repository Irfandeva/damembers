<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

function daMembersDownload() {

  global $wpdb;
  $da_members_table = $wpdb->prefix . "da_members";
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';

  $cols = $wpdb->get_results("SHOW COLUMNS FROM $da_members_table;");
  $member_columns_names = array();
  foreach ($cols as $col) {
    $member_columns_names[] = $col->Field;
  }

  $excelHeader = "";
  foreach ($member_columns_names as $label) {
    $formattedLabel = ucwords(str_replace('_', ' ', $label));
    $excelHeader .= $formattedLabel . "\t";
  }

  // Find the position of the last occurrence of '\t'
  $lastTabIndex = strrpos($excelHeader, "\t");
  // Replace the last occurrence of '\t' with '\n'
  $excelContent = substr_replace($excelHeader, "\n", $lastTabIndex, 1);
  if (isset($_GET['download'])) {
    $results = $wpdb->get_results("SELECT * FROM $da_members_table");
    foreach ($results as $row) {
      // Sanitize data before output (example)
      $id = esc_html($row->id);
      $firstName = esc_html($row->first_name);
      $lastName = esc_html($row->last_name);

      // Append data to the Excel content
      $excelContent .= "$id\t$firstName\t$lastName\n";
    }
    $fileName = "da_members_" . date("Y-m-d") . ".xls";
    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    // Output Excel content
    echo $excelContent;
    // Exit to prevent any additional content from being output
    exit;
  }
}
function uploadFromExcel() {
  global $wpdb;
  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  $da_members_table = $wpdb->prefix . "da_members";
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  if (isset($_POST['submit_excel'])) {
    // Allowed mime types 
    $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // Validate whether selected file is a Excel file 
    echo "<pre>";
    var_dump($_FILES['excel-file']);
    var_dump($_POST['excel-file-text']);
    echo "</pre>";

    if (!empty($_FILES['excel-file']['name']) && in_array($_FILES['excel-file']['type'], $excelMimes)) {
      // If the file is uploaded 
      if (is_uploaded_file($_FILES['excel-file']['tmp_name'])) {

        $reader = new Xls();
        $spreadsheet = $reader->load($_FILES['excel-file']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet_arr = $worksheet->toArray();

        // Remove header row 
        // unset($worksheet_arr[0]);
        foreach ($worksheet_arr as $row) {
          $first_name = $row[0];
          $last_name = $row[1];
          $wpdb->query($wpdb->prepare("INSERT INTO $da_members_table (first_name, last_name) VALUES (%s, %s)", $first_name, $last_name));
        }
      }
    }
  }
?>
  <div class="wrap">

    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="input-item">
        <label for="excel-file">Choose Excel File</label>
        <input type="file" name="excel-file" id="excel-file">
        <input type="text" name="excel-file-text" id="excel-file-text">
      </div>
      <input type="submit" name="submit_excel" id="submit_excel" class="button button-primary" value="Upload">
    </form>
  </div>
<?php
}
