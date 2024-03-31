<?php
function da_members_settings() {
  if (isset($_POST['save_changes'])) {
    global $wpdb;
    $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
    $fields = $wpdb->get_results("SELECT `label` FROM  $da_members_form_fields_table");
    $field_count = count($fields);

    $new_records_per_page = !empty($_POST['r-p-p']) ? sanitize_text_field($_POST['r-p-p']) : 20;
    $new_number_of_poperties_columns = !empty($_POST['t-c']) ? sanitize_text_field($_POST['t-c']) : 5;
    $r_p_p_res = update_option('records_per_page', $new_records_per_page);
    $new_number_of_poperties_columns = $new_number_of_poperties_columns > $field_count ? $field_count : $new_number_of_poperties_columns;
    $t_c_res = update_option('number_of_poperties_columns', $new_number_of_poperties_columns);
    if ($r_p_p_res || $t_c_res) {
      $result['status'] = 'ok';
      $result['message'] = 'Changes saved successfully.';
    }
  }
  $records_per_page = get_option('records_per_page', 20);
  $number_of_poperties_columns = get_option('number_of_poperties_columns', 5);
?>
  <div class="wrap">
    <?php
    $admin_page_url = admin_url('admin.php?page=da-members');
    echo '<a href="' . esc_url($admin_page_url) . '" style="text-decoration: none" class="page-title-action">&larr; GO BACK</a>';
    ?>
    <h1>DA Members Settings</h1>
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
    <div class="settings-wrapper">
      <form action="" method="post">
        <div class="input-item" style="flex-direction: row;gap:30px">
          <label for="r-p-p">Records Per Page</label>
          <input type="number" name="r-p-p" id="r-p-p" style="width:70px" value='<?php echo $records_per_page; ?>'>
        </div>
        <div class="input-item" style="flex-direction: row;gap:30px">
          <label for="t-c">Table Columns</label>
          <input type="number" name="t-c" id="t-c" style="width:70px" value='<?php echo $number_of_poperties_columns; ?>'>
        </div>
        <input type="submit" name="save_changes" value="Save Changes" class="button button-primary" style="margin-top:20px;">
      </form>
      <div class="short-codes">
        <h3>Short Codes</h3>
        <ul>
          <li>
            <p class="title">Members Shortcode</p>
            <p class="desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Officia quia laudantium minima quas, numquam dolores doloremque autem architecto quasi? Dolores obcaecati ad, totam accusamus asperiores quo harum rem! Aperiam, delectus.</p>
            <img src="<?php echo plugin_dir_url(__DIR__) . '/public/images/sc-screenshot.png' ?>" alt="" srcset="">
          </li>
          <li>
            <p class="title">Members By Department</p>
            <p class="desc">Lorem ipsum dolor sit amet consectetur adipisicing elit..</p>
            <img src="<?php echo plugin_dir_url(__DIR__) . '/public/images/sc-screenshot.png' ?>" alt="" srcset="">
          </li>
        </ul>
      </div>
    </div>
  <?php

}
