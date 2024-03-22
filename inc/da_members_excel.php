<?php
session_start();
require(plugin_dir_path(__FILE__) . 'export_excel.php');

use PhpOffice\PhpSpreadsheet\Reader\Xls;

function uploadFromExcel() {
  $total_count = 0;
  $empty_records = 0;
  // $result = array();
  // $result['status'] = 'ok';
  // $result['message'] = '';

  global $wpdb;
  global $result;

  require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
  $da_members_table = DA_MEMBERS_TABLE;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $fields = $wpdb->get_results("SELECT label,field_name, priority, required FROM $da_members_form_fields_table");
  //this array will store label=>field_name key pairs from db table, will be used to extract the field_name by label key which user chooses during mapping
  $label_to_field_mapping_array = array();
  foreach ($fields as $field) {
    $label_to_field_mapping_array[trim_and_tolowercase($field->label)] = $field->field_name;
  }
  //submit for mapping
  if (isset($_POST['submit_excel'])) {
    // Allowed mime types 
    $excel_mimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // Validate whether selected file is a Excel file 
    if (!empty($_FILES['excel-file']['name']) && in_array($_FILES['excel-file']['type'], $excel_mimes)) {
      // If the file is uploaded 
      if (is_uploaded_file($_FILES['excel-file']['tmp_name'])) {
        $reader = new Xls();
        $spreadsheet = $reader->load($_FILES['excel-file']['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet_arr = array();
        $worksheet_arr = $worksheet->toArray();
        $excel_header = array_map('trim_and_tolowercase', $worksheet_arr[0]);
        $number_of_excel_columns = count($excel_header);
        if ($number_of_excel_columns > 0) {
          //remove header row from excel data
          unset($worksheet_arr[0]);
          $_SESSION['excel-data'] = $worksheet_arr;
        }
      } else {
        //if some how, the file was not found in temp folder
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
    $assoc_arr = array();
    //get the mapped columns from form
    $columns = $_POST['columns'];
    $form_fields = array();
    foreach ($columns as $col) {
      //get the field name by key which user has chosen during mapping, save the field_name in array
      if (trim_and_tolowercase($col) !== "ignore" && trim_and_tolowercase($col) !== "-1") {
        $form_fields[] = $label_to_field_mapping_array[$col];
      } else {
        $form_fields[] = $col;
      }
    }

    // check if user has not selected or ignored any required column
    foreach ($fields as $field) {
      if ($field->required == 1 && !in_array($field->field_name, $form_fields, true)) {
        $result['status'] = 'error';
        $result['message'] = 'Required columns not mapped!';
      }
    }

    //check if we have excel data in session
    if (isset($_SESSION['excel-data'])) {
      //get excel data from session
      $excel_rows = $_SESSION['excel-data'];
      //total rows of data in excel
      $total_count = count($excel_rows);
      foreach ($excel_rows as $excel_row) {
        //make an assoc array with key being field_name and value being excel row value
        $assoc_row = array_combine($form_fields, $excel_row);
        //check if user has not selected any field for mapping
        if (array_key_exists('-1', $assoc_row)) {
          $result['status'] = 'error';
          $result['message'] = 'You have not selected some columns for mapping';
        }
        //remove any ignored column
        if (array_key_exists('ignore', $assoc_row)) {
          unset($assoc_row['ignore']);
        }
        //store the assoc row one by one 
        $assoc_arr[] = $assoc_row;
      }
      // log_it($assoc_arr);
      $error_row_indexes = array();
      foreach ($assoc_arr as $index => $record) {
        //finally insert the data
        if ($result['status'] !== 'error') {
          $res = $wpdb->insert($da_members_table, $record);
          if (!$res) {
            $empty_records++;
            array_push($error_row_indexes, $index + 1);
          }
        }
      }
    }
    //clear the excel data from session
    unset($_SESSION['excel-data']);
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
    <div class="form-wrapper">
      <form action="" method="post" enctype="multipart/form-data" class="file-form">
        <!-- <label for="excel-file">Choose Excel File</label> -->
        <input type="file" name="excel-file" id="excel-file">
        <input type="submit" name="submit_excel" id="submit_excel" class="button button-primary" value="&nbsp;&nbsp;&nbsp;Map&nbsp;&nbsp;&nbsp;">
      </form>
      <?php
      if (isset($number_of_excel_columns)  /*&& !($number_of_excel_columns > count($fields))*/) { ?>
        <form action="" method="post" style="text-align: center;">
          <div class="field-mapping-container">
            <?php
            foreach ($excel_header as $col) { ?>
              <div class="mapping-row">
                <span><?php echo $col; ?></span>
                <select name="columns[]">
                  <option value="-1"> --select-- </option>
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
      if (isset($_POST['submit_excel_data']) && $result['status'] !== 'error') { ?>
        <div class="result-container">
          <div class="inserted"><?php $inserted = $total_count - $empty_records;
                                $msg = $inserted > 1 ? 'records inserted : ' : 'record inserted : ';
                                echo  $msg . $total_count - $empty_records;  ?></div>

          <div class="not-inserted <?php if (count($error_row_indexes) < 1) echo " hide"; ?>">
            <?php $error_message = count($error_row_indexes) > 1 ? "records with following row numbers have not been inserted : " :
              "record with following row number has not been inserted : ";
            echo $error_message . implode(" ", $error_row_indexes) . " , total : " . $empty_records; ?></div>
        </div>
      <?php }
      ?>
    </div>
  <?php
}

function downloadPage() {
  global $wpdb;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $da_members_table = DA_MEMBERS_TABLE;
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");
  ?>
    <div class="wrap">
      <!-- <a href="<?php echo esc_url(get_permalink()) ?>?action=download" class="btn btn-secondary btn-lg" tabindex="-1" role="button" aria-disabled="true">Download</a> -->

      <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
      <h1 class="wp-heading-block">Export to excel</h1>
      <?php
      if (isset($_POST['excel-download']) && !isset($_POST['field_ids'])) {
        $result['status'] = 'error';
        $result['message'] = 'You have not selected any field for download!';
      }
      if (isset($result) && $result['message'] !== '') {
        if ($result['status'] == 'ok') {
          echo "<div id='message' class='notice is-dismissible updated'>
        <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
        <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
        } elseif ($result['status'] == 'error') {
          echo "<div id='message' class='notice error'><p>" . $result['message'] . "</p></div>";
        }
      }

      ?>
    </div>

    <form action="" method="post">
      <div class="form-wrapper2">
        <div class='labels-input-wrapper'>
          <?php
          echo "<div  style='display:flex;gap:20px;justify-content:flex-start;align-items:center;padding:4px 0px'>
             <input type='checkbox' name='' id='field-ids'>
             <label for=''> <span style='font-size:18px;color:#333'>Choose Fields</span> </label>
             </div>";
          foreach ($form_fields as $form_field) {
            echo "<div  style='display:flex;gap:20px;justify-content:flex-start;align-items:center;padding:4px 0px'>
               <input type='checkbox'  id ='' name='field_ids[]' value =$form_field->id class='select-form-field-check'>
               <label for=''>" . $form_field->label . "</label>
               </div>";
          }
          ?>
        </div>
        <div style="display:flex;flex-direction:column;padding:4px 0px;gap:4px">
          <label for='created_after'> <span style='font-size:16px;color:#333'>Created after</span> </label>
          <input type='date' name='created_after' id='created_after'>
        </div>
        <div style="display:flex;flex-direction:column;padding:4px 0px;gap:4px">
          <label for='updated_after'> <span style='font-size:16px;color:#333'>Updated after</span> </label>
          <input type='date' name='updated_after' id='updated_after'>
        </div>
        <div style="display:flex;flex-direction:column;padding:4px 0px;gap:4px">
          <label for='department'> <span style='font-size:16px;color:#333'>Choose department</span> </label>

          <select name="department" id="department">
            <option value="-1">--select--</option>
            <?php
            require(plugin_dir_path(__DIR__) . 'data/da_members_data.php');
            foreach ($select_fields_data['departments'] as $department) {
              echo "<option value='$department'>$department</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <button id="newsubmit" name="excel-download" type="submit" class="button button-primary">Download</button>
    </form>

  </div>
<?php
}
