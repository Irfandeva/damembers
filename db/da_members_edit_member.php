<?php
//FUNCTION TO EDIT UPDATE A MEMBER
function daMembersEdit() {
  // HELLO THERE, WELCOME TO THE EDIT PAGE {$_GET['uptid']}; -->
  global $wpdb;
  global $result;
  $id = $_GET['uptid'];
  $da_members_table = DA_MEMBERS_TABLE;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  if (isset($_POST['member_upt']) || isset($_POST['member_upt_and_go_back'])) {
    $record = array();
    //loop through form_fields that we get from db,check if user has entered any value in against the labels of those inpts and save the record
    foreach ($form_fields as $form_field) {
      if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
        // $record[$form_field->field_name] = ($_POST[$form_field->field_name]);
        $record[$form_field->field_name] = $form_field->field_name == 'bio' ? htmlentities($_POST[$form_field->field_name]) : sanitize_text_field($_POST[$form_field->field_name]);
      }
    }
    $update_res = $wpdb->update($da_members_table, $record, array('id' => $id));
    if ($update_res == 1) {
      $result['status'] = 'ok';
      $result['message'] = 'Member updated successfully.';
      if (isset($_POST['member_upt_and_go_back'])) {
        $admin_page_url = admin_url('admin.php?page=da-members');
        echo "<script>location.replace('" . esc_url($admin_page_url) . "')</script>";
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
    <?php
    $admin_page_url = admin_url('admin.php?page=da-members');
    echo '<a href="' . esc_url($admin_page_url) . '" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>';
    ?>
    <div class="input-wrapper">
      <form action="" method="post">
        <h1>Edit Member</h1>
        <?php
        require(plugin_dir_path(__DIR__) . 'utils/data/da_members_data.php');

        if (isset($result) && !empty($result['message'])) {
          if ($result['status'] == 'ok') {
            echo "<div id='message' class='notice is-dismissible updated'>
             <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
             <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
          } elseif ($result['status'] == 'error') {
            echo "<div id='message' class='notice error'><p>" . $result['message'] . "</p></div>";
          }
        }
        ?>
        <!-- //new input row -->
        <?php
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;

          if ($form_field->field_name == 'first_name' || $form_field->field_name == 'last_name') {
            $value = htmlentities(stripslashes($da_member->$property));
            echo "<div class='input-item'>";
            echo "<label for=$form_field->field_name>$column</label>";
            echo "<input type='text' id='$form_field->field_name' name='$form_field->field_name' value='$value'>";
            echo "</div>";
          }
        }
        echo "</div>";
        ?>
        <!-- new input row -->
        <?php
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;
          $value = $da_member->$property;
          if ($form_field->field_name == 'country' || $form_field->field_name == 'constituency') {
            $value = htmlentities(stripslashes($value));
            $options = $form_field->field_name == 'country' ? $select_fields_data['countries'] : $select_fields_data['constituencies'];
            echo "<div class='input-item'>";
            echo "<label for=$form_field->field_name>$column</label>
              <select name=$form_field->field_name id=$form_field->field_name>";
            foreach ($options as $data) {
              $selected = (strtolower($value) == strtolower($data)) ? 'selected' : '';
              echo "<option value='$data' $selected>$data</option>";
            }
            echo "</select>";
            echo "</div>";
          }
        }
        echo "</div>";
        ?>

        <!-- new input row -->
        <?php
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;
          $value = $da_member->$property;
          if ($form_field->field_name == 'member_type' || $form_field->field_name == 'member_since' || $form_field->field_name == 'designation') {
            if ($form_field->field_name == 'member_type') {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['member_types'] as $data) {
                $selected = (strtolower($property) == strtolower($data)) ? 'selected' : '';
                echo "<option value='$data' $selected>$data</option>";
              }
              echo "</select>";
              echo "</div>";
            } elseif ($form_field->field_name == 'member_since') {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
            <input type='date' id='$form_field->field_name' name='$form_field->field_name' value='$value'>";
              echo "</div>";
            } else {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
            <input type='text' id='$form_field->field_name' name='$form_field->field_name' value='$value'>";
              echo "</div>";
            }
          }
        }
        echo "</div>";
        ?>
        <!-- new input row -->
        <?php
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;
          $value = $da_member->$property;
          if ($form_field->field_name == 'department' || $form_field->field_name == 'address') {
            $value = htmlentities(stripslashes($value));
            if ($form_field->field_name == 'department') {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['departments'] as $data) {
                $selected = (strtolower($value) == strtolower($data)) ? 'selected' : '';
                echo "<option value='$data' $selected>$data</option>";
              }
              echo "</select>";
              echo "</div>";
            } else {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>";
              echo "<input type='text' id='$form_field->field_name' name='$form_field->field_name' value='$value'>";
              echo "</div>";
            }
          }
        }
        echo "</div>";
        ?>
        <div class="input-item">
          <label for="bio">Bio</label>
          <textarea name='bio' id='default'><?php echo html_entity_decode(stripslashes($da_member->bio)) ?></textarea>
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
