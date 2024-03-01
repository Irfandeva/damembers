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
        $excelHeader = array_map('formateString', $worksheet_arr[0]);

        //due to some reason spreadsheet library adds null as last column, check if it is null and remove it
        if (end($excelHeader) == NULL)
          array_pop($excelHeader);

        // Remove header row 
        unset($worksheet_arr[0]);
        $assocArr = array();
        foreach ($worksheet_arr as $row) {
          //due to some reason spreadsheet library adds null as last column, check if it is null and remove it
          if (end($row) == NULL)
            array_pop($row);
          //this will creat associate aray with heading value being key and row value being its value
          $assocRow = array_combine($excelHeader, $row);
          $assocArr[] = $assocRow;

          //loop through all fields , check if it is required, if requred then we must find a value against this label in excel cell
          //if value is empty remove the last assocRow from assocArr
          foreach ($fields as $field) {
            if ($field->required == 1 && array_key_exists($field->label, $assocRow) && empty($assocRow[$field->label])) {
              echo " found it       ";
              array_pop($assocArr);
              $emptyRecords++;
              break;
            }
          }
          // $first_name = $row[0];
          // $last_name = $row[1];
          // $bio = $row[2];
          // $wpdb->query($wpdb->prepare("INSERT INTO $da_members_table (first_name, last_name,bio) VALUES (%s, %s,%s)", $first_name, $last_name, $bio));
        }
        foreach ($assocArr as $record) {
          $wpdb->insert($da_members_table, $record);
        }

        echo "<h1>EXCEL RECORDS</h1>";
        echo "<pre>";
        var_dump($assocArr);
        echo "<pre>";
        echo "<h1>FIELDS</h1>";
        echo "<pre>";
        var_dump($fields);
        echo $wpdb->last_error;
        echo "<pre>";

        echo "<pre>";
        echo "RECORDS INSERTED";
        echo "records with eror : $emptyRecords";
        echo "records inserted :" . $totalCount - $emptyRecords;
        echo "</pre>";
      }
    }
  }
  excelFormHtml();
}


function excelFormHtml() { ?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="input-item">
        <label for="excel-file">Choose Excel File</label>
        <input type="file" name="excel-file" id="excel-file">
      </div>
      <input type="submit" name="submit_excel" id="submit_excel" class="button button-primary" value="Upload">
    </form>
  </div>
<?php

}

function formateString($str) {
  if (!empty($str))
    return strtolower(str_replace(' ', '_', $str));
}
