<?php
function manageFormFields() {

  global $wpdb;
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $total_form_fields = count($wpdb->get_results("SELECT * FROM $da_members_form_fields_table"));

  //EDIT/UPDATE EXIXTING FORM FIELD
  if (isset($_POST['form_input_submit_upt'])) {
    if (isset($_POST['ids'])) {
      $labels = ($_POST['labels']);
      $priorities = $_POST['priorities'];
      $required_ids = $_POST['required_ids'];
      $ids = $_POST['ids'];
      foreach ($ids as $id) {
        $u_label = $labels[$id];
        $u_required = $required_ids[$id];
        $u_priority = $priorities[$id];
        $updateQuery = $wpdb->query("UPDATE $da_members_form_fields_table SET  label='$u_label', priority='$u_priority',required='$u_required' WHERE id='$id'");
        if ($updateQuery) {
          echo "<div id='message' class='notice is-dismissible updated'>
              <p>Field Updated successfully</p><button type='button' class='notice-dismiss'>
              <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
        } else {
          echo " <div id='message' class='notice error'><p>error updating fields.</p></div>";
        }
      }
    } else {
      echo " <div id='message' class='notice error'><p>No Fields Selected.</p></div>";
    }
  }
  echo $wpdb->last_error
?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <h1 class="wp-heading-block">Manage Form Fields</h1>
    <?php
    $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");
    ?>
    <form method="post" action="">
      <table class="wp-list-table widefat striped">
        <thead>
          <tr>
            <th>Field</th>
            <th>Lablel</th>
            <th>Required</th>
            <th>Priority</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($form_fields as $form_field) {
            echo "<tr>";
            // echo "<td width='5%'>" . '<input type="checkbox" name="ids[]" value="' . $form_field->id . '">' . "</td>";
            echo "<td>" .
              '<input type="checkbox" name="ids[]" value="' . $form_field->id . '" id="' . $form_field->id . '">' .
              '<input type="text" name="field" value="' . $form_field->field_name . '" readonly style="color:#999">' .
              "</td>";
            echo "<td>" . '<input type="text" name="labels[' . $form_field->id . ']" value="' . $form_field->label . '" data-id="' . $form_field->id . '" class="form-field-input">' . "</td>";

            echo "<td>" . '<select name="required_ids[' . $form_field->id . ']" id=$form_field->field_name>';
            $selected_yes = ($form_field->required == '1') ? 'selected' : '';
            $selected_no = ($form_field->required == '0') ? 'selected' : '';
            echo "<option value='1' $selected_yes>yes</option>";
            echo "<option value='0' $selected_no>no</option>";
            echo "</select>" . "</td>";

            echo "<td>" . '<select name="priorities[' . $form_field->id . ']" id=$form_field->field_name>';
            for ($fieldCount = 1; $fieldCount <= $total_form_fields; $fieldCount++) {
              $selected = ($form_field->priority == $fieldCount) ? 'selected' : '';
              echo "<option value=$fieldCount $selected>$fieldCount</option>";
            }
            echo "</select>";

            // echo '<input type="number" name="priorities[' . $form_field->id . ']" value="' . $form_field->priority . '" style="width:50px">';
            echo '<input type="hidden" name="upt_cols[]" value="' . $form_field->id . "," . $form_field->label . '">';
            echo  "</td></tr>";
          }

          echo "<tr ><td colspan='4'>" . '<input type="submit" name="form_input_submit_upt" class="button button-primary" value="&nbsp;&nbsp;Update&nbsp;&nbsp;">' .  "</td></tr>";
          ?>
        </tbody>
      </table>
    </form>
  </div>
<?php
}
