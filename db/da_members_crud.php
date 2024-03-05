<?php
function daMembersShow() {
  global $wpdb;
  $da_members_table = $wpdb->prefix . 'da_members';
  $res = null;
  $members = $wpdb->get_results("SELECT * FROM $da_members_table");

  $start = 0;
  $page = $start;

  $records_per_page = 5;
  if (isset($_GET['page_num'])) {
    $page = $_GET['page_num'] - 1;
    $start = $page * $records_per_page;
    $id = $_GET['page_num'];
  }

  $numberOfPropertiesToShow = 4;
  if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    if (!empty($id))
      $res = $wpdb->query("DELETE FROM $da_members_table WHERE id='$id'");
  }
  if ($res == 1)
    echo "<div id='message' class='notice is-dismissible updated'>
  <p>Member deleted successfully</p><button type='button' class='notice-dismiss'>
  <span class='screen-reader-text'>Dismiss this notice.</span></button></div>"
?>
  <div class="wrap">
    <div class="top">
      <h1 class="wp-heading-inline">DA Members</h1>
      <div class="right-col">
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members&download" class="page-title-action">DOWNLOAD &darr; </a>
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=upload-from-excel" class="page-title-action">UPLOAD &uarr; </a>
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members-add" class="page-title-action">Add + </a>
      </div>
    </div>

    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <!-- <th width="8%">User ID</th> -->
          <?php
          $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
          $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table ORDER BY priority ASC");

          for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
            foreach ($fields as $field) {
              if ($field->priority == $i) {
                $column = $field->label;
                echo "<th width='20%'>$column</th>";
              }
            }
          }
          ?>
          <th width="20%">Actions</th>
        </tr>

      </thead>
      <tbody>
        <?php
        $result = $wpdb->get_results("SELECT * FROM $da_members_table LIMIT $start,$records_per_page");
        foreach ($result as $print) {
          echo "<tr>";
          for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
            //show the data based on labels with priority from 1 - $numberOfPropertiesToShow
            foreach ($fields as $field) {
              if ($field->priority == $i) {
                $column = $field->field_name;
                echo "<td width='20%'>{$print->$column}</td>";
              }
            }
          }
          echo "<td width='20%'>
          <a href='http://localhost/wordpress/wp-admin/admin.php?page=da-members-edit&uptid=$print->id'>
          <button type='button' class='button button-primary'>EDIT</button></a>
           <button type='button' class='button button-primary delete_da_member' data-del-id=$print->id>DELETE</button>
           </td></tr>";
        }
        ?>
        <tr>
          <td colspan='<?php echo $numberOfPropertiesToShow + 2; ?>'>
            <?php daMembersPagination($members, $records_per_page); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <br>
    <br>
  </div>
<?php
}
function daMembersAdd() {
  global $wpdb;
  $da_members_table = $wpdb->prefix . 'da_members';
  $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
  $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

  $record = array();
  if (isset($_POST['newsubmit']) || isset($_POST['newsubmit_and_go_back'])) {
    //loop through form_fields that we get from db,check if user has entered any value in against the labels of those inpts and save the record
    foreach ($form_fields as $form_field) {
      if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
        $record[$form_field->field_name] = $_POST[$form_field->field_name];
      }
    }
    $addRes = $wpdb->insert($da_members_table, $record);
    if ($addRes) {
      if (isset($_POST['newsubmit_and_go_back'])) {
        echo "<script>location.replace('http://localhost/wordpress/wp-admin/admin.php?page=da-members')</script>";
      }
      echo "<div id='message' class='notice is-dismissible updated'>
  <p>Member deleted successfully</p><button type='button' class='notice-dismiss'>
  <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
    }
  }
?>
  <div class="wrap">
    <div class="da-members-add-container">
      <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
      <h1>Add New Member</h1>
    </div>
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
            $column = $form_field->label;
            if ($form_field->label == 'Country') {
              echo
              "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['countries'] as $data) {
                echo "<option value='$data'>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Type') {
              echo
              "<label for=$form_field->field_name>$column</label>
                <select name=$form_field->field_name id=$form_field->field_name>";
              foreach ($select_fields_data['member_types'] as $data) {
                echo "<option value='$data'>$data</option>";
              }
              echo "</select>";
            } elseif ($form_field->label == 'Member Since') {
              echo "<label for=$form_field->field_name>$column</label>
                <input type='date' id='$form_field->field_name' name='$form_field->field_name'>";
            } else {
              echo "<label for=$form_field->field_name>$column</label>
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

//FUNCTION TO EDIT UPDATE A MEMBER
function daMembersEdit() { ?>
  <!-- // HELLO THERE, WELCOME TO THE EDIT PAGE {$_GET['uptid']}; -->
  <div class="wrap">
    <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
    <h1>Edit Member</h1>
    <?php
    global $wpdb;
    $id = $_GET['uptid'];
    // $wpdb->query("UPDATE $da_members_table SET name='$name',email='$email' WHERE user_id='$id'");

    $da_members_table = $wpdb->prefix . 'da_members';
    $da_members_form_fields_table = $wpdb->prefix . 'da_members_form_fields';
    $form_fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table");

    if (isset($_POST['member_upt'])) {
      $record = array();
      //loop through form_fields that we get from db,check if user has entered any value in against the labels of those inpts and save the record
      foreach ($form_fields as $form_field) {
        if (isset($_POST[$form_field->field_name]) && !empty($_POST[$form_field->field_name])) {
          $record[$form_field->field_name] = $_POST[$form_field->field_name];
        }
      }
      $update_res = $wpdb->update($da_members_table, $record, array('id' => $id));
      if ($update_res == 1)
        echo "<div id='message' class='notice is-dismissible updated'>
  <p>Member updated successfully</p><button type='button' class='notice-dismiss'>
  <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
    }
    $da_members = $wpdb->get_results("SELECT * FROM $da_members_table WHERE id = $id");
    if (!empty($da_members)) {
      $da_member = $da_members[0];
    }
    ?>

    <form action="" method="post">
      <div class="input-wrapper">
        <?php
        require(plugin_dir_path(__DIR__) . 'data/da_members_data.php');
        foreach ($form_fields as $form_field) {
          $field = $form_field->field_name;
          $da_member_property = $da_member->$field;
          $column = $form_field->label;

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
              echo "<label for=$form_field->field_name>$column</label>
              <input type='text' id='$form_field->field_name' name='$form_field->field_name' value='$da_member_property'>";
            }
            ?>
          </div>
        <?php
        }
        ?>
      </div>

      <div class="form-actions" style="justify-content:flex-start">
        <button id="newsubmit" name="member_upt" type="submit" class="button button-primary">Update</button>
      </div>
    </form>
  </div>

<?php
}
function daMembersPagination($members, $records_per_page) {

  $total_members = count($members);
  if ($total_members === 0) {
    echo '<p>no records found.</p>';
    return;
  }
  $total_pages = ceil($total_members / $records_per_page);
  $last_page = $total_pages;
  $id = 1;
  if (isset($_GET['page_num'])) {
    $id = $_GET['page_num'];
  }

?>
  <div class="pagination" id=<?php echo $id; ?> style="width:100%;display: flex;align-items:center; justify-content:space-between;">
    <div class="page-info">
      Showing <?php echo (!isset($_GET['page_num'])) ? '1' : $_GET['page_num'] ?> of <?php echo $total_pages ?> Pages
    </div>
    <div class="links" style="display: flex;align-items:center;gap:8px">
      <?php
      //first button
      if (isset($_GET['page_num']) && $_GET['page_num'] > 1) {
        echo  '<a href="admin.php?page=da-members&page_num=1">First</a>';
      } else {
        echo  '<a>First</a>';
      }
      //previous button
      if (isset($_GET['page_num']) && $_GET['page_num'] > 1) {
        $previous_page = $_GET['page_num'] - 1;
        echo  "<a href='admin.php?page=da-members&page_num=$previous_page'>Previous</a>";
      } else {
        echo  '<a>Previous</a>';
      }
      ?>
      <!-- numbered links -->
      <div class="numbered_links" style="display: flex;align-items:center;gap:8px">
        <?php
        for ($counter = 1; $counter <= $total_pages; $counter++) { ?>
          <a href='admin.php?page=da-members&page_num=<?php echo $counter ?>'><?php echo $counter ?></a>
        <?php
        }
        ?>
      </div>

      <?php
      //next button
      if (!isset($_GET['page_num'])) {
        echo "<a href='admin.php?page=da-members&page_num=2'>Next</a>";
      } else if (isset($_GET['page_num']) && $_GET['page_num'] < $total_pages) {
        $next_page = $_GET['page_num'] + 1;
        echo "<a href='admin.php?page=da-members&page_num=$next_page'>Next</a>";
      } else {
        echo "<a>Next</a>";
      }
      //last button
      if (!isset($_GET['page_num']) && $total_pages > 1 || isset($_GET['page_num']) && $_GET['page_num']  < $last_page) {
        echo "<a href='admin.php?page=da-members&page_num=$total_pages'>Last</a>";
      } else {
        echo "<a>Last</a>";
      }
      ?>
    </div>
  </div>
<?php
}
