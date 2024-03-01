<?php
function manageFormFields() {
  global $wpdb;

  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $table_name_members = $wpdb->prefix . 'da_members';
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  //EDIT/UPDATE EXIXTING FORM INPUT/FIELD
  if (isset($_POST['form_input_submit_upt'])) {
    $id = sanitize_text_field($_POST['id']);
    $label = sanitize_text_field($_POST['label']);
    $priority = sanitize_text_field($_POST['priority']);
    echo "$id, $label, $priority";
    $updateQuery = $wpdb->query("UPDATE $da_members_form_fields_table SET  label='$label', priority='$priority' WHERE id='$id'");

    if (!$updateQuery) {
      echo "ERROR UPDATING FORM FIELD" . $wpdb->last_error;
    } else {
      echo "UPDATED SUCCESSFULLY";
      //if new from input was added to db successfully, we will create a new table column for that input in table to store values later.
      // $dataType = $data_type . "(" . $size . ")";
      // $wpdb->query($wpdb->prepare("ALTER TABLE $table_name_members ADD $label $dataType"));
    }
  }

?>
  <div class="wrap">
    <h2 class="wp-heading-inline">Manage Form Fields</h2>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th width="33%">Label</th>
          <th width="33%">Priority</th>
          <th width="33%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($form_fields as $form_field) {
        ?>
          <tr>
            <form action="" method="post">
              <td><input type="text" id="label" name="label" value='<?php echo $form_field->label; ?>'></td>
              <td><input type="number" id="priority" name="priority" value='<?php echo $form_field->priority; ?>'></td>
              <td>
                <input type="hidden" id="id" name="id" value='<?php echo $form_field->id; ?>'>
                <!-- <button id="form_input_submit_upt" name="form_input_submit_del" type="submit">UPDATE</button> -->
                <input type="submit" name="form_input_submit_upt" id="form_input_submit_upt" class="button button-primary" value="Update">
                <input type="submit" name="form_input_submit_del" id="form_input_submit_del" class="button button-primary" value="Delete">
                <!-- <button id="form_input_submit_del" name="form_input_submit_del" type="submit">DELETE</button> -->
              </td>
            </form>
          </tr>

        <?php
        } ?>
      </tbody>
    </table>

    <!-- add new form input -->

    <table class="wp-list-table widefat striped" style="margin-top: 20px;">
      <thead>
        <tr>
          <th width="33%">Label</th>
          <th width="33%">Priority</th>
          <th width="33%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <form action="" method="post">
            <td><input type="text" id="label" name="label"></td>
            <td><input type="number" id="priority" name="priority"></td>
            <td>
              <input type="submit" id="form_inew_from_input_submit" name="form_inew_from_input_submit" class="button button-primary" value="Save New Form Field">
            </td>
          </form>
        </tr>
      </tbody>
    </table>
  <?php
}
