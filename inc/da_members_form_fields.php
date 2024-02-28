<?php
function manageFormFields() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'form_inputs';
  $table_name_members = $wpdb->prefix . 'members';
  $result = $wpdb->get_results("SELECT * FROM $table_name");
  //EDIT/UPDATE EXIXTING FORM INPUT/FIELD
  if (isset($_POST['form_input_submit_upt'])) {
    $id = sanitize_text_field($_POST['id']);
    $label = sanitize_text_field($_POST['label']);
    $priority = sanitize_text_field($_POST['$priority']);
    $updateQuery = $wpdb->query("UPDATE $table_name SET  label='$label', priority='$priority' WHERE id='$id'");

    if (!$updateQuery) {
      echo "ERROR UPDATING INPUT" . $wpdb->last_error;
    } else {
      echo "UPDATED SUCCESSFULLY";
      //if new from input was added to db successfully, we will create a new table column for that input in table to store values later.
      // $dataType = $data_type . "(" . $size . ")";
      // $wpdb->query($wpdb->prepare("ALTER TABLE $table_name_members ADD $label $dataType"));
    }
  }

?>
  <div class="wrap">
    <!-- <h1>Manage Form Fields</h1> -->
    <!-- show all  existing inputs in db -->
    <div class="view_form-inputs">
      <h3>EDIT OR DELETE FORM FIELDS</h3>

      <?php
      foreach ($result as $input) {
      ?>
        <!-- edit or delete existing form input -->
        <div class="edit_form_input">
          <form action="" method="post">
            <div class="new_input">

              <div class="new_input_row">
                <label for="label">Label</label>
                <input type="text" id="label" name="label" value=<?php echo $input->label; ?>>
              </div>

              <div class="new_input_row">
                <label for="priority">Priority</label>
                <input type="number" name="priority" id="priority">
              </div>
            </div>
            <input type="hidden" name="id" value=<?php echo $input->id ?>>

            <div class="submit_button" style="">
              <button id="form_input_submit_del" name="form_input_submit_del" type="submit">DELETE</button>
              <button id="form_input_submit_upt" name="form_input_submit_upt" type="submit">UPDATE</button>
            </div>
          </form>
        </div>
      <?php
      } ?>
    </div>
    <!-- add new form input -->
    <div class="add_form_input">
      <h3>ADD NEW FORM FIELD</h3>
      <form action="" method="post">
        <div class="new_input">

          <div class="new_input_row">
            <label for="label">Enter Label</label>
            <input type="text" id="label" name="label">
          </div>

          <div class="new_input_row">
            <label for="priority">Priority</label>
            <input type="number" name="priority" id="priority">
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
function addValues() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'form_inputs';
?>
  <div class="wrap">
    <h1>ADD VALUES</h1>
  </div>
<?php
}
