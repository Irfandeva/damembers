<?php
function manageFormFields() {

  global $wpdb;

  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $da_members_members = $wpdb->prefix . 'da_members';

  //ADD NEW FORM FIELD
  if (isset($_POST['submit_new_form_field'])) {
    $field_label = add_($_POST['label']);
    $field_priority = $_POST['priority'];
    $q1 = $wpdb->query($wpdb->prepare("INSERT INTO $da_members_form_fields_table (label, priority,field_type) 
                                 VALUES (%s, %s,%s)", $field_label, $field_priority, 'text'));
    $q2 = $wpdb->query("ALTER TABLE $da_members_members ADD $field_label varchar(50);");
    if ($q1 && $q2) {
      echo "<div id='message' class='notice is-dismissible updated'>
    <p>Field Added successfully</p><button type='button' class='notice-dismiss'>
    <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
    } else {
      echo " <div id='message' class='notice error'><p>error adding fields.</p></div>";
    }
  }


  //EDIT/UPDATE EXIXTING FORM FIELD
  if (isset($_POST['form_input_submit_upt'])) {

    if (isset($_POST['ids'])) {
      $labels = ($_POST['labels']);
      $priorities = $_POST['priorities'];
      $ids = $_POST['ids'];
      foreach ($ids as $id) {

        $u_label = add_($labels[$id]);
        // $original_column_name = $labels[$id];
        $u_priority = $priorities[$id];
        $updateQuery = $wpdb->query("UPDATE $da_members_form_fields_table SET  label='$u_label', priority='$u_priority' WHERE id='$id'");
        if ($updateQuery) {
          if (isset($_POST['upt_cols'])) {
            $cols_to_update = $_POST['upt_cols'];
            foreach ($cols_to_update as $ctu) {
              $res = checkForIdInPrefix($ctu, $id);

              //if our label id prfixed with id, then we have found our column to update
              if ($res === 0) {
                $preFix = $id . ',';
                //remove the prefix from column name
                $col_to_update = str_replace($preFix, '', $ctu);
                //get the schema from table of that particular column 
                $original_column_schema = $wpdb->get_results("SHOW COLUMNS FROM $da_members_members WHERE FIELD='$col_to_update';");
                //extract datatype from schema
                $dataType = $original_column_schema[0]->Type;
                //finally update the column name
                $qRes =   $wpdb->query("ALTER TABLE $da_members_members CHANGE $col_to_update  $u_label $dataType");
                if ($qRes) {
                  echo "<div id='message' class='notice is-dismissible updated'>
                  <p>Fields Updated successfully</p><button type='button' class='notice-dismiss'>
                  <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
                } else {
                  echo " <div id='message' class='notice error'><p>error updating fields.</p></div>";
                }
              }
            }
          }
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
    <!-- <h1 class="wp-heading-inline">Manage Form Fields</h1> -->
    <?php
    $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");
    echo '<form method="post" action="" >';
    echo '<h5 class="wp-heading-inline">Edit</h5>';
    foreach ($form_fields as $form_field) {
      echo "<div style='display:flex;gap:16px;align-items:center;margin-bottom:10px'>";
      echo '<input type="checkbox" name="ids[]" value="' . $form_field->id . '">';
      echo '<input type="text" name="labels[' . $form_field->id . ']" value="' . remove_($form_field->label) . '">';
      echo '<input type="number" name="priorities[' . $form_field->id . ']" value="' . $form_field->priority . '">';
      echo '<input type="hidden" name="upt_cols[]" value="' . $form_field->id . "," . $form_field->label . '">';

      echo "</div>";
    }
    echo '<input type="submit" name="form_input_submit_upt" class="button button-primary" value="Update">';
    echo '</form>';
    ?>

    <form action="" method="post">
      <h5 class="wp-heading-inline">Add new</h5>
      <input type="text" id="label" name="label">
      <input type="number" id="priority" name="priority">
      <input type="submit" id="submit_new_form_field" name="submit_new_form_field" class="button button-primary" value="Save New Form Field">
    </form>
  </div>
<?php
}
//this function will check if given string is prefixed with particular given id and ,
function checkForIdInPrefix($inputString, $id) {
  $prefix = $id . ',';
  $res = strpos($inputString, $prefix);
  return $res;
}
