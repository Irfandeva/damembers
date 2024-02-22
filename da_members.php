<?php

/**
 * Plugin Name: DA Members
 * Plugin URI: localhost
 * Description: A plugin for DA Members
 * Version: 1.0
 * Author: Irfan Farooq Deva
 * Author URI: not found
 **/



require(plugin_dir_path(__FILE__) . '/db/da_members_create_table.php');

register_activation_hook(__FILE__, 'create_table');

function enqueue_css_js($hook) {
  if (is_admin()) {
    wp_enqueue_script('jquery');
    wp_enqueue_script('da-members-script', plugin_dir_url(__FILE__) . '/public/js/script.js', array('jquery'), '1.0', true);
    wp_enqueue_style('da-members-style', plugin_dir_url(__FILE__) . '/public/css/styles.css');
  }
}
add_action('admin_enqueue_scripts', 'enqueue_css_js');


function admin_menu_item() {
  add_menu_page(
    __('Page Title', 'my-textdomain'),
    __('DA Members', 'my-textdomain'),
    'manage_options',
    'sample-page',
    'crudAdminPage',
    'dashicons-schedule',
    3
  );
}
add_action('admin_menu', 'admin_menu_item');

function damembers_page_contents() {
?>
  <div class="da-members-form-cotainer">
    <form action="" method="post">
      <h4>
        Add a member
      </h4>
      <label for="fname" class="first">First Name</label>
      <input type="text" id="fname" placeholder="First Name">
      <label for="lname">Last Name</label>
      <input type="text" id="lname" placeholder="Last Name">
      <label for="role">Role</label>
      <input type="text" id="role" placeholder="Role">
      <label for="country">Choose Counrty</label>
      <input type="text" id="country" placeholder="Country">
      <label for="address">Address</label>
      <input type="text" id="address" placeholder="Address">
      <input type="submit" value="Submit">
    </form>
  </div>
<?php
}
function crudAdminPage() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'members';
  if (isset($_POST['newsubmit'])) {
    $fname = sanitize_text_field($_POST['fname']);
    $lname = sanitize_text_field($_POST['lname']);
    $bio = sanitize_text_field($_POST['bio']);
    $desig = sanitize_text_field($_POST['desig']);
    $wpdb->query($wpdb->prepare("INSERT INTO $table_name (fname, lname, bio, desig) VALUES (%s, %s, %s, %s)", $fname, $lname, $bio, $desig));
  }
  if (isset($_POST['uptsubmit'])) {
    $id = $_POST['uptid'];
    $name = $_POST['uptname'];
    $email = $_POST['uptemail'];
    $wpdb->query("UPDATE $table_name SET name='$name',email='$email' WHERE user_id='$id'");
    // echo "<script>location.replace('admin.php?page=crud.php');</script>";
  }
  if (isset($_GET['del'])) {
    $del_id = $_GET['del'];
    $wpdb->query("DELETE FROM $table_name WHERE user_id='$del_id'");
    // echo "<script>location.replace('admin.php?page=crud.php');</script>";
  }
?>
  <div class="wrap">
    <h2>DA Members</h2>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th width="20%">User ID</th>
          <th width="10%">First Name</th>
          <th width="10%">Last Name</th>
          <th width="10%">Bio</th>
          <th width="10%">Designation</th>
          <th width="40%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <form action="" method="post">
          <tr>
            <td><input type="text" value="AUTO_GENERATED" disabled></td>
            <td><input type="text" id="fname" name="fname"></td>
            <td><input type="text" id="lname" name="lname"></td>
            <td><input type="text" id="bio" name="bio"></td>
            <td><input type="text" id="desig" name="desig"></td>
            <td><button id="newsubmit" name="newsubmit" type="submit">INSERT</button></td>
          </tr>
        </form>
        <?php
        $result = $wpdb->get_results("SELECT * FROM $table_name");
        foreach ($result as $print) {
          echo "
              <tr>
                <td width='20%'>$print->id</td>
                <td width='10%'>$print->fname</td>
                <td width='10%'>$print->lname</td>
                <td width='10%'>$print->desig</td>
                <td width='10%'>$print->bio</td>
                <td width='40%'><a href='admin.php?page=crud.php&upt=$print->id'><button type='button'>UPDATE</button></a> <a href='admin.php?page=crud.php&del=$print->id'><button type='button'>DELETE</button></a></td>
              </tr>
            ";
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
                <td width='25%'><button id='uptsubmit' name='uptsubmit' type='submit'>UPDATE</button> <a href='admin.php?page=crud.php'><button type='button'>CANCEL</button></a></td>
              </tr>
            </form>
          </tbody>
        </table>";
    }
    ?>
  </div>
<?php
}
