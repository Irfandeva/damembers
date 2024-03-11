<?php
function da_members_settings() {
  if (isset($_POST['save_per_page_value'])) {
    $new_records_per_page = !empty($_POST['r-p-p']) ? $_POST['r-p-p'] : 20;
    $new_number_of_poperties_columns = !empty($_POST['t-c']) ? $_POST['t-c'] : 5;
    update_option('records_per_page', $new_records_per_page);
    update_option('number_of_poperties_columns', $new_number_of_poperties_columns);
  }
  $records_per_page = get_option('records_per_page', 20);
  $number_of_poperties_columns = get_option('number_of_poperties_columns', 5);
?>
  <div class="wrap">
    <h1>DA Members Settings</h1>
    <form action="" method="post">
      <div class="input-item" style="flex-direction: row;gap:30px">
        <label for="r-p-p">Records Per Page</label>
        <input type="number" name="r-p-p" id="r-p-p" style="width:70px" value='<?php echo $records_per_page; ?>'>
      </div>
      <div class="input-item" style="flex-direction: row;gap:30px">
        <label for="r-p-p">Table Columns</label>
        <input type="number" name="t-c" id="t-c" style="width:70px" value='<?php echo $number_of_poperties_columns; ?>'>
      </div>
      <input type="submit" name="save_per_page_value" value="Save Changes" class="button button-primary" style="margin-top:20px;">
    </form>
  </div>

<?php
}
