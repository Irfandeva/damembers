<?php
function manageFormFields() {

  global $wpdb;
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $total_form_fields = count($wpdb->get_results("SELECT * FROM $da_members_form_fields_table"));
  //EDIT/UPDATE EXIXTING FORM FIELD
  $res_check = array();
  if (isset($_POST['form_input_submit_upt'])) {
    $priorities = array_map('sanitize_array', $_POST['priorities']);
    $hasDuplicates = count($priorities) > count(array_unique($priorities));
    if ($hasDuplicates) {
      // echo "<div id='message' class='notice error'><p>Duplicate priorities.</p></div>";
      $result['status'] = 'error';
      $result['message'] = 'Duplicate priorities, two labels can not have same priority';
    }
    if (!isset($_POST['ids'])) {
      $result['status'] = 'error';
      $result['message'] = 'No Fields Selected.';
    }
    if ($result['status'] !== 'error') {
      $labels = array_map('sanitize_array', $_POST['labels']);
      $ids = array_map('sanitize_array', $_POST['ids']);
      foreach ($ids as $id) {
        $u_label = sanitize_text_field($labels[$id]);
        $is_required = "required_ids_" . $id;
        $u_required = isset($_POST[$is_required]) ? '1' : 0;
        $u_priority = $priorities[$id];
        $updateQuery = $wpdb->query("UPDATE $da_members_form_fields_table SET  label='$u_label', priority='$u_priority',required='$u_required' WHERE id='$id'");
        if ($updateQuery)
          array_push($res_check, $updateQuery);
      }
    }
  }

  if (!empty($res_check)) {
    $msg = count($res_check) > 1 ? 'Fields' : 'Field';
    $result['status'] = 'ok';
    $result['message'] = $msg . " Updated successfully";

    // echo "<div id='message' class='notice is-dismissible updated'>
    //           <p>" . $msg . " Updated successfully</p><button type='button' class='notice-dismiss'>
    //           <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
  }
  echo $wpdb->last_error
?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <h1 class="wp-heading-block">Manage Form Fields</h1>
    <?php


    if (isset($result) && !empty($result['message'])) {
      if ($result['status'] == 'ok') {
        echo "<div id='message' class='notice is-dismissible updated'>
    <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
    <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
      } elseif ($result['status'] == 'error') {
        echo "<div id='message' class='notice error'><p>" . $result['message'] . "</p></div>";
      }
    }

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
            $checked = ($form_field->required === '1') ? 'checked' : '';
            echo "<td>" . "<input type='checkbox' name='required_ids_" . $form_field->id . "'$checked class='req'>";
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
