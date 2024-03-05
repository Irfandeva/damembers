<?php

use PhpOffice\PhpSpreadsheet\Reader\Xls;

function daMembersDownload() {

  global $wpdb;
  $da_members_table = $wpdb->prefix . "da_members";
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
      foreach ($member_columns_names as $label) {
        $excelContent .= $row->$label . "\t";
      }
      // Find the position of the last occurrence of '\t'
      $lastTabIndex = strrpos($excelContent, "\t");
      // Replace the last occurrence of '\t' with '\n'
      $excelContent = substr_replace($excelContent, "\n", $lastTabIndex, 1);
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

  $totalCount = 0;
  $emptyRecords = 0;

  global $wpdb;
  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  $da_members_table = $wpdb->prefix . "da_members";
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $fields = $wpdb->get_results("SELECT label, priority, required FROM $da_members_form_fields_table");

  if (isset($_POST['submit_excel'])) {
    // Allowed mime types 
    $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // Validate whether selected file is a Excel file 
    if (!empty($_FILES['excel-file']['name']) && in_array($_FILES['excel-file']['type'], $excelMimes)) {
      // If the file is uploaded 
      if (is_uploaded_file($_FILES['excel-file']['tmp_name'])) {

        $reader = new Xls();
        $spreadsheet = $reader->load($_FILES['excel-file']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet_arr = $worksheet->toArray();
        $totalCount = count($worksheet_arr) - 1;
        //get header row, make it all lowercase , replace spaces with _
        $excelHeader = array_map('add_', $worksheet_arr[0]);

        //due to some reason spreadsheet library adds null as last column, check if it is null and remove it
        // if (end($excelHeader) == NULL)
        //   array_pop($excelHeader);

        // Remove header row 
        unset($worksheet_arr[0]);
        $assocArr = array();
        foreach ($worksheet_arr as $row) {
          //due to some reason spreadsheet library adds null as last column, check if it is null and remove it
          // if (end($row) == NULL)
          //   array_pop($row);
          //this will creat associate array with heading value its key and row value its value
          $assocRow = array_combine($excelHeader, $row);
          $assocArr[] = $assocRow;

          //loop through all fields , check if it is required, if requred then we must find a value against this label in excel cell
          //if value is empty remove the last assocRow from assocArr
          foreach ($fields as $field) {
            if ($field->required == '1' && array_key_exists($field->label, $assocRow) && empty($assocRow[$field->label])) {
              array_pop($assocArr);
              $emptyRecords++;
              break;
            }
          }
        }
        foreach ($assocArr as $record) {
          $wpdb->insert($da_members_table, $record);
        }
        $records_inserted = $totalCount - $emptyRecords;
        echo "<div id='message' class='notice is-dismissible updated'>
        <p>records inserted :$records_inserted records with error : $emptyRecords </p><button type='button' class='notice-dismiss'>
        <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
      }
    } else {
      echo " <div id='message' class='notice error'><p>File format not supported.</p></div>";
    }
  }
  excelFormHtml();
}


function excelFormHtml() {
?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <div class="form-wrapper" style="display: flex;justify-content:center;align-items:center;height:80%;background-color:red">
      <form action="" method="post" enctype="multipart/form-data">
        <!-- <label for="excel-file">Choose Excel File</label> -->
        <input type="file" name="excel-file" id="excel-file">
        <input type="submit" name="submit_excel" id="submit_excel" class="button button-primary" value="&nbsp;&nbsp;&nbsp;Map&nbsp;&nbsp;&nbsp;">
      </form>
    </div>
  </div>
<?php
}
