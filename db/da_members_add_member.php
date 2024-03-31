<?php
function daMembersAdd() {
  global $wpdb;
  $result['status'] = 'ok';
  $result['message'] = '';
  $da_members_table = DA_MEMBERS_TABLE;
  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  $record = array();
  if (isset($_POST['newsubmit']) || isset($_POST['newsubmit_and_go_back'])) {
    //loop through form_fields that we get from db,check if user has entered any value in against the labels of those inpts and save the record
    foreach ($form_fields as $form_field) {
      if ($form_field->required == '1' && empty($_POST[$form_field->field_name])) {
        $result['status'] = 'error';
        $result['message'] = 'Required fields (*) can not be empty.';
        break;
      }
      if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
        $record[$form_field->field_name] = $form_field->field_name == 'bio' ? htmlentities($_POST[$form_field->field_name]) : sanitize_text_field($_POST[$form_field->field_name]);
      }
    }
    if ($result['status'] !== 'error') {
      $addRes = $wpdb->insert($da_members_table, $record);
      if ($addRes) {
        if (isset($_POST['newsubmit_and_go_back'])) {
          $admin_page_url = admin_url('admin.php?page=da-members');
          echo "<script>location.replace('$admin_page_url')</script>";
        }
        $result['status'] = 'ok';
        $result['message'] = 'Member added successfully';
      }
    }
  }
  echo_it($wpdb->last_error);
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
        require_once(plugin_dir_path(__DIR__) . 'utils/data/da_members_data.php');
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;

          if ($form_field->field_name == 'first_name' || $form_field->field_name == 'last_name') {
            $label = $form_field->required == '1' ? $form_field->label . '<span style="color:red"> * </span>' : $form_field->label;
            echo "<div class='input-item'>";
            echo "<label for=$form_field->field_name>$label</label>";
            echo "<input type='text' id='$form_field->field_name' name='$form_field->field_name'>";
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

          if ($form_field->field_name == 'country' || $form_field->field_name == 'constituency') {
            $options = $form_field->field_name == 'country' ? $select_fields_data['countries'] : $select_fields_data['constituencies'];
            echo "<div class='input-item'>";
            echo "<label for=$form_field->field_name>$column</label>
              <select name=$form_field->field_name id=$form_field->field_name>";
            foreach ($options as $data) {
              echo "<option value='$data' >$data</option>";
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
          $column = $form_field->label;
          if ($form_field->field_name == 'member_type' || $form_field->field_name == 'member_since' || $form_field->field_name == 'designation') {
            if ($form_field->field_name == 'member_type') {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['member_types'] as $data) {
                echo "<option value='$data' >$data</option>";
              }
              echo "</select>";
              echo "</div>";
            } elseif ($form_field->field_name == 'member_since') {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
            <input type='date' id='$form_field->field_name' name='$form_field->field_name'>";
              echo "</div>";
            } else {
              echo "<div class='input-item'>";
              echo "<label for=$form_field->field_name>$column</label>
            <input type='text' id='$form_field->field_name' name='$form_field->field_name'>";
              echo "</div>";
            }
          }
        }
        echo "</div>";
        ?>
        <!-- //new input row -->
        <?php
        echo "<div class='input-row'>";
        foreach ($form_fields as $form_field) {
          $property = $form_field->field_name;
          $column = $form_field->label;

          if ($form_field->field_name == 'department' || $form_field->field_name == 'address') {
            echo "<div class='input-item'>";
            echo "<label for=$form_field->field_name>$column</label>";
            echo "<input type='text' id='$form_field->field_name' name='$form_field->field_name'>";
            echo "</div>";
          }
        }
        echo "</div>";
        ?>
        <div class="input-item">
          <label for="bio">Bio</label>
          <textarea name='bio' id='default'></textarea>
        </div>
        <div class="form-actions">
          <button id="newsubmit" name="newsubmit_and_go_back" type="submit" class="button button-primary">Save And Go Back</button>
          <button id="newsubmit" name="newsubmit" type="submit" class="button button-primary">Save And Enter New</button>
        </div>
      </form>
    </div>

  </div>
<?php
}
