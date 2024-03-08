<?php
session_start();

use PhpOffice\PhpSpreadsheet\Reader\Xls;

function daMembersDownload() {

  global $wpdb;
  $da_members_table = DA_MEMBERS_TABLE;
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
  $result = array();

  global $wpdb;
  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  $da_members_table = DA_MEMBERS_TABLE;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $fields = $wpdb->get_results("SELECT label,field_name, priority, required FROM $da_members_form_fields_table");
  //this array will store label=>field_name key pairs, will be used to extract the field_name by label key which user chooses during mapping
  $label_to_field_mapping_array = array();
  foreach ($fields as $field) {
    $label_to_field_mapping_array[trim_and_tolowercase($field->label)] = $field->field_name;
  }
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
        $worksheet_arr = array();
        $worksheet_arr = $worksheet->toArray();
        $excelHeader = array_map('trim_and_tolowercase', $worksheet_arr[0]);
        $number_of_excel_columns = count($excelHeader);
        if ($number_of_excel_columns > 0) {
          // //check if number of excel cols are greater than number of form table columns
          // if ($number_of_excel_columns > count($fields)) {
          //   $result['status'] = 'error';
          //   $result['message'] = 'mismatch columns! number of columns should not be greater than ' . count($fields);
          // } else {
          // Remove header row and store excel data in session
          unset($worksheet_arr[0]);
          $_SESSION['excel-data'] = $worksheet_arr;
        }
      } else {
        //if some how the file wan not found in temp folder
        $result['status'] = 'error';
        $result['message'] = 'Some thing went wrong..';
      }
    } else {
      $result['status'] = 'error';
      $result['message'] = 'File not selected or File format not supported.';
    }
  }

  //UPLOAD 
  if (isset($_POST['submit_excel_data'])) {
    $assocArr = array();
    //get the mapped columns from form
    $columns = $_POST['columns'];
    $orignalFields = array();
    foreach ($columns as $col) {
      //get the field name by key which user has chosen during mapping, save the field_name in array
      if (trim_and_tolowercase($col) !== "ignore" && trim_and_tolowercase($col) !== "--select--") {
        $orignalFields[] = $label_to_field_mapping_array[$col];
      }
    }
    //check if we have excel data in session
    if (isset($_SESSION['excel-data'])) {
      //get excel data from session
      $excel_rows = $_SESSION['excel-data'];
      //total rows of data in excel
      $totalCount = count($excel_rows);
      foreach ($excel_rows as $excel_row) {
        //make an assoc array with key being field_name and value being excel row value
        $assocRow = array_combine($orignalFields, $excel_row);
        //store the assoc row one by one 
        $assocArr[] = $assocRow;
      }
      foreach ($assocArr as $record) {
        //finally insert the data
        $res = $wpdb->insert($da_members_table, $record);
        if (!$res) {
          $emptyRecords++;
        }
      }
    }
    //clear the excel data from session
    unset($_SESSION['excel-data']);
    // echo $wpdb->last_error;
  }
?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <?php
    if (isset($result) && !empty($result)) {
      if ($result['status'] == 'error') {
        echo "<div id='message' class='notice error'><p>" . $result['message'] . "</p></div>";
      }
    }
    ?>
    <div class="form-wrapper" style="display: flex;flex-direction:column;justify-content:center;align-items:center;height:80%;">
      <form action="" method="post" enctype="multipart/form-data" class="file-form">
        <!-- <label for="excel-file">Choose Excel File</label> -->
        <input type="file" name="excel-file" id="excel-file">
        <input type="submit" name="submit_excel" id="submit_excel" class="button button-primary" value="&nbsp;&nbsp;&nbsp;Map&nbsp;&nbsp;&nbsp;">
      </form>
      <?php
      if (isset($number_of_excel_columns) /*&& !($number_of_excel_columns > count($fields))*/) { ?>
        <form action="" method="post" style="text-align: center;">
          <div class="field-mapping-container" style="display: flex;flex-direction:column;gap:20px;margin-bottom:30px;margin-top:30px">
            <?php
            foreach ($excelHeader as $col) { ?>
              <div class="mapping-row" style="display: flex;gap:20px;justify-content:space-between;align-items:center">
                <span><?php echo $col; ?></span>
                <select name="columns[]">
                  <option value="--select--"> --select-- </option>
                  <?php foreach ($fields as $field) {
                    $label = trim_and_tolowercase($field->label);
                    // $selected = $col  == $label ? 'selected' : '';
                    $selected = find_match($col, $label)  ? 'selected' : '';
                    echo "<option value='$label' $selected>$label</option>";
                  } ?>
                  <option value="ignore">ignore</option>
                </select>
              </div>
            <?php
            } ?>
          </div>
          <input type="submit" name="submit_excel_data" id="submit_exCel_data" class="button button-primary" value="&nbsp;&nbsp;&nbsp;Upload&nbsp;&nbsp;&nbsp;">
        </form>
      <?php }
      if (isset($_POST['submit_excel_data'])) { ?>
        <div class="result-container">
          <div class="inserted"><?php echo "inserted record : " . $totalCount - $emptyRecords;  ?></div>
          <div class="not-inserted"><?php echo "records not inserted : " . $emptyRecords; ?></div>
        </div>
      <?php }
      ?>
    </div>
  <?php
}
