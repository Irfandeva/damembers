<?php
function daMembersAdd() {
  global $wpdb;
  $result = array();
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
        $result['message'] = 'Requied fields can not be empty';
        break;
      }
      if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
        $record[$form_field->field_name] = $_POST[$form_field->field_name];
      }
    }

    if ($result['status'] !== 'error') {
      $addRes = $wpdb->insert($da_members_table, $record);
      if ($addRes) {
        if (isset($_POST['newsubmit_and_go_back'])) {
          echo "<script>location.replace('http://localhost/wordpress/wp-admin/admin.php?page=da-members')</script>";
        }
        $result['status'] = 'ok';
        $result['message'] = 'Member added successfully';
      }
    }
  }
?>
  <div class="wrap">
    <div class="da-members-add-container">
      <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
      <h1>Add New Member</h1>
    </div>

    <?php
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
    <form action="" method="post">
      <!-- <h2>
        Add a member
      </h2> -->
      <div class="input-wrapper">
        <?php
        require(plugin_dir_path(__DIR__) . 'data/da_members_data.php');
        foreach ($form_fields as $form_field) {
        ?>
          <div class="input-item">
            <?php
            $label = $form_field->required == '1' ? $form_field->label . '<span style="color:red"> * </span>' : $form_field->label;
            if ($form_field->label == 'Country') {
              echo
              "<label for=$form_field->field_name>$label</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['countries'] as $data) {
                echo "<option value='$data'>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Type') {
              echo
              "<label for=$form_field->field_name>$label</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['member_types'] as $data) {
                echo "<option value='$data'>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Since') {
              echo "<label for=$form_field->field_name>$label</label>
                <input type='date' id='$form_field->field_name' name='$form_field->field_name'>";
            } else {
              echo "<label for=$form_field->field_name>$label</label>
              <input type='text' id='$form_field->field_name' name='$form_field->field_name'>";
            }
            ?>
          </div>
        <?php
        }
        ?>
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
