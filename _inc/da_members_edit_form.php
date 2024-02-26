<?php
function edit_form() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'form_inputs';
  $table_name_members = $wpdb->prefix . 'members';
  $result = $wpdb->get_results("SELECT * FROM $table_name");
  $form_input_types = array('text', 'select', 'number');
  $form_input_data_types = array('varchar', 'int');

  if (isset($_POST['new_from_input_submit'])) {
    $type = sanitize_text_field($_POST['type']);
    $label = sanitize_text_field($_POST['label']);
    $data_type = sanitize_text_field($_POST['data_type']);
    $size = (string)sanitize_text_field($_POST['size']);
    $required = sanitize_text_field($_POST['required']);
    $qRes = $wpdb->query($wpdb->prepare("INSERT INTO $table_name (type, label, data_type,size, required) VALUES (%s, %s, %s, %s, %s)", $type, $label, $data_type, $size, $required));
    if (!$qRes) {
      echo "ERROR SAVING NEW INPUT";
    } else {
      echo "SAVED SUCCESSFULLY";
      //if new input was added to db successfully, we will create a new table column for that input in table to store values later.
      $dataType = null;
      if ($data_type == 'varchar') {
        $dataType = "varchar($size)";
      }
      if ($data_type == 'int') {
        $dataType = "int($size)";
      }
      $wpdb->query($wpdb->prepare("ALTER TABLE $table_name_members ADD $label $dataType"));
    }
  }

?>
  <div class="wrap">
    <h1>Edit Form</h1>
    <!-- show all  existing inputs in db -->
    <div class="view_form-inputs">
      <?php
      foreach ($result as $input) {
        echo "
        <div class = 'form-input'>
        <input type=$input->type id=$input->label name=$input->label/>
        </div>
        ";
      } ?>
    </div>
    <!-- add new form input -->
    <div class="add_form_input">
      <h3>ADD NEW INPUT</h3>
      <form action="" method="post">
        <div class="new_input">

          <div class="new_input_row">
            <label for="type">Input Type</label>
            <select name="type" id="type">
              <?php
              foreach ($form_input_types as $form_input_type) {
                echo "<option value=$form_input_type>$form_input_type</option>";
              }
              ?>
            </select>
          </div>

          <div class="new_input_row">
            <label for="label">Enter Label</label>
            <input type="text" id="label" name="label">
          </div>

          <div class="new_input_row">
            <label for="data_type">Data Type</label>
            <select name="data_type" id="data_type">
              <?php
              foreach ($form_input_data_types as $form_input_data_type) {
                echo "<option value=$form_input_data_type>$form_input_data_type</option>";
              }
              ?>
            </select>
          </div>

          <div class="new_input_row">
            <label for="size">Enter Size</label>
            <input type="number" id="size" name="size">
          </div>

          <div class="new_input_row">
            <label for="required">Required</label>
            <select name="required" id="required">
              <option value="0">No</option>
              <option value="1">Yes</option>
            </select>
          </div>

        </div>

        <div class="submit_button" style="margin-top: 20px;">
          <button id="new_from_input_submit" name="new_from_input_submit" type="submit">Save</button>
        </div>
      </form>
    </div>

  </div><!-- end of wrap div  -->
<?php
}
