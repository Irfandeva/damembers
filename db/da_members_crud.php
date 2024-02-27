<?php

function daMembersShow() {
  global $wpdb;
  $res = null;
  $table_name = $wpdb->prefix . 'members';
  if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    if (!empty($id))
      $res = $wpdb->query("DELETE FROM $table_name WHERE id='$id'");
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
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members-add" class="page-title-action">DOWNLOAD &darr; </a>
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members-add" class="page-title-action">UPLOAD &uarr; </a>
        <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members-add" class="page-title-action">Add + </a>
      </div>
    </div>

    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th width="10%">User ID</th>
          <th width="20%">First Name</th>
          <th width="20%">Last Name</th>
          <th width="20%">Designation</th>
          <th width="30%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $wpdb->get_results("SELECT * FROM $table_name");
        foreach ($result as $print) {
          echo "
              <tr>
                <td width='10%'>$print->id</td>
                <td width='20%'>$print->first_name</td>
                <td width='20%'>$print->last_name</td>
                <td width='20%'>$print->designation</td>
                <td width='30%'>
                <a href='http://localhost/wordpress/wp-admin/admin.php?page=da-members-edit&uptid=$print->id'>
                <button type='button'>EDIT</button></a>
                 <button type='button' class='delete_da_member' data-del-id=$print->id>DELETE</button>
                 </td>
              </tr>";
        }
        ?>
      </tbody>
    </table>
    <br>
    <br>
    <?php
    if (isset($_GET['upt'])) {
      $upt_id = $_GET['upt'];
      $result = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='$upt_id'");
      foreach ($result as $print) {
        $name = $print->name;
        $email = $print->email;
      }
      echo "
        <table class='wp-list-table widefat striped'>
          <thead>
            <tr>
              <th width='25%'>User ID</th>
              <th width='25%'>Name</th>
              <th width='25%'>Email Address</th>
              <th width='25%'>Actions</th>
            </tr>
          </thead>
          <tbody>
            <form action='' method='post'>
              <tr>
                <td width='25%'>$print->user_id <input type='hidden' id='uptid' name='uptid' value='$print->user_id'></td>
                <td width='25%'><input type='text' id='uptname' name='uptname' value='$print->name'></td>
                <td width='25%'><input type='text' id='uptemail' name='uptemail' value='$print->email'></td>
                <td width='25%'><button id='uptsubmit' name='uptsubmit' type='submit' class='button-danger'>UPDATE</button> <a href='admin.php?page=crud.php'><button type='button' class='button-danger'>CANCEL</button></a></td>
              </tr>
            </form>
          </tbody>
        </table>";
    }
    ?>
  </div>
<?php
}
function daMembersAdd() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'members';
  $inputs = $wpdb->get_results("SELECT * FROM `wp_form_inputs`");

  $record = array();
  if (isset($_POST['newsubmit'])) {
    //loop through inputs that we get from db,check if user has entered any value in against the labels of those inpts and save the record
    foreach ($inputs as $input) {
      if (isset($_POST[$input->label]) && !empty($_POST[$input->label])) {
        $record[$input->label] = $_POST[$input->label];
      }
    }
    $wpdb->insert($table_name, $record);

    echo "<pre>";
    var_dump($record);
    echo "</pre>";
    // $fname = sanitize_text_field($_POST['first_name']);
    // $lname = sanitize_text_field($_POST['last_name']);
    // $bio = sanitize_text_field($_POST['bio']);
    // $desig = sanitize_text_field($_POST['designation']);
    // $wpdb->query($wpdb->prepare("INSERT INTO $table_name (first_name, last_name, bio, designation) VALUES (%s, %s, %s, %s)", $fname, $lname, $bio, $desig));
  }


?>
  <div class="wrap">
    <div class="da-members-add-container">
      <div class="top">
        <div class="right-col">
          <a href="http://localhost/wordpress/wp-admin/admin.php?page=da-members" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>
        </div>
      </div>
      <form action="" method="post">
        <!-- <h2>
        Add a member
      </h2> -->
        <div class="input-wrapper">
          <?php
          foreach ($inputs as $input) {
          ?>
            <div class="input-item">
              <?php
              $label = ucwords(str_replace('_', ' ', $input->label));
              if ($input->type == 'select') {
                echo "
            <label for=$input->label>$label</label>
            <select name=$input->label id=$input->label>
            </select>
              ";
              }
              if ($input->type == 'text' || $input->type == 'number') {
                echo "
                <label for=$input->label>$label</label>
                <input type=$input->type name=$input->label id=$input->label>
            </input>
              ";
              }
              if ($input->type == 'checkbox') {
                echo "
                <label for=$input->label>$label</label>
                <input type=$input->type name=$input->label id=$input->label value=$input->type>
            </input>
              ";
              }

              ?>
            </div>
          <?php
          }
          ?>
          <!-- <div class="input-item">
            <label for="fname" class="first">First Name</label>
            <input type="text" id="fname" name="fname" placeholder="First Name">
          </div>

          <div class="input-item">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" placeholder="Last Name">
          </div>

          <div class="input-item">
            <label for="bio">Bio</label>
            <input type="text" id="bio" name="bio" placeholder="Role">
          </div>

          <div class="input-item">
            <label for="desig">Designation</label>
            <input type="text" id="desig" name="desig" placeholder="Country">
          </div> -->
        </div>

        <div class="form-actions">
          <button id="newsubmit" name="newsubmit" type="submit">Save And Go Back</button>
          <button id="newsubmit" name="newsubmit" type="submit">Save And Enter New</button>

        </div>
      </form>
    </div>
  </div>

<?php
}

function daMembersEdit() {
  echo "<h1>HELLO THERE, WELCOME TO THE EDIT PAGE {$_GET['uptid']}</h1>";
  global $wpdb;
  $table_name = $wpdb->prefix . 'members';
  if (isset($_POST['uptsubmit'])) {
    $id = $_POST['uptid'];
    $name = $_POST['uptname'];
    $email = $_POST['uptemail'];
    $wpdb->query("UPDATE $table_name SET name='$name',email='$email' WHERE user_id='$id'");
    // echo "<script>location.replace('admin.php?page=crud.php');</script>";
  }
}
