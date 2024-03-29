<?php
//FUNCTION TO EDIT UPDATE A MEMBER
function daMembersEdit() {
  // HELLO THERE, WELCOME TO THE EDIT PAGE {$_GET['uptid']}; -->
  global $wpdb;
  global $result;
  $id = $_GET['uptid'];
  // $wpdb->query("UPDATE $da_members_table SET name='$name',email='$email' WHERE user_id='$id'");

  $da_members_table = DA_MEMBERS_TABLE;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  if (isset($_POST['member_upt']) || isset($_POST['member_upt_and_go_back'])) {
    $record = array();
    //loop through form_fields that we get from db,check if user has entered any value in against the labels of those inpts and save the record
    foreach ($form_fields as $form_field) {
      if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
        $record[$form_field->field_name] = ($_POST[$form_field->field_name]);
      }
    }
    $update_res = $wpdb->update($da_members_table, $record, array('id' => $id));
    if ($update_res == 1) {
      $result['status'] = 'ok';
      $result['message'] = 'Member updated successfully.';
      if (isset($_POST['member_upt_and_go_back'])) {
        echo "<script>location.replace('http://localhost/wordpress/wp-admin/admin.php?page=da-members')</script>";
      }
    }
  }
  $da_member = null;
  $da_members = $wpdb->get_results("SELECT * FROM $da_members_table WHERE id = $id");
  if (!empty($da_members)) {
    $da_member = $da_members[0];
  }
?>
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <div class="input-wrapper">
      <form action="" method="post">
        <h1>Edit Member</h1>
        <?php
        if (isset($result) && !empty($result)) {
          if ($result['status'] == 'ok' && $result['message'] !== '') {
            echo "<div id='message' class='notice is-dismissible updated'>
        <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
        <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
          }
        } ?>
        <?php
        require(plugin_dir_path(__DIR__) . 'data/da_members_data.php');
        foreach ($form_fields as $form_field) {
          $field = $form_field->field_name;
          $da_member_property = $da_member->$field;
          $column = $form_field->label;
          if ($form_field->label == 'Bio')
            continue;
        ?>
          <div class="input-item">
            <?php
            if ($form_field->label == 'Country') {
              echo
              "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['countries'] as $data) {
                $selected = (strtolower($da_member_property) == strtolower($data)) ? 'selected' : '';
                echo "<option value='$data' $selected>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Type') {
              echo
              "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['member_types'] as $data) {
                $selected = (strtolower($da_member_property) == strtolower($data)) ? 'selected' : '';
                echo "<option value='$data' $selected>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Since') {
              echo "<label for=$form_field->field_name>$column</label>
                <input type='date' id='$form_field->field_name' name='$form_field->field_name' value='$da_member_property'>";
            } else {
              $value = htmlentities(stripslashes($da_member_property));
              echo "<label for=$form_field->field_name>$column</label>";
              echo "<input type='text' id='$form_field->field_name' name='$form_field->field_name' value='$value'>";
            }
            ?>
          </div>
        <?php
        }
        ?>
        <div class="input-item">
          <label for="bio">Bio</label>
          <textarea name='bio' id='default'><?php echo htmlentities(stripslashes($da_member->bio)) ?></textarea>
        </div>
        <div class="form-actions">
          <button id="newsubmit" name="member_upt" type="submit" class="button button-primary">Update</button>
          <button id="newsubmit" name="member_upt_and_go_back" type="submit" class="button button-primary">Update and go back</button>
        </div>
      </form>
    </div>
  </div>

<?php
}
