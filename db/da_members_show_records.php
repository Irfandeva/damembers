<?php
function daMembersShow() {
  global $wpdb;
  $da_members_table = DA_MEMBERS_TABLE;
  $res = null;
  $result = array();

  $members = $wpdb->get_results("SELECT * FROM $da_members_table");

  $start = 0;
  $page = $start;
  $numberOfPropertiesToShow = 5;

  $records_per_page = get_option('records_per_page', 20);
  if (isset($_GET['page_num'])) {
    $page = $_GET['page_num'] - 1;
    $start = $page * $records_per_page;
    $id = $_GET['page_num'];
  }
  //delete a member
  if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    if (!empty($id))
      $res = $wpdb->query("DELETE FROM $da_members_table WHERE id='$id'");
  }
  if ($res == 1) {
    $result['status'] = 'ok';
    $result['message'] = 'Member deleted successfully...';
  }

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
    <div class="hello">
      <?php
      if (isset($result) && !empty($result)) {
        if ($result['status'] == 'ok') {
          echo "<div id='message' class='notice is-dismissible updated widefat'>
        <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
        <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
        }
      } ?>
    </div>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th><input type='checkbox' width="25px"></th>
          <?php
          $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
          $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table ORDER BY priority ASC");
          for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
            foreach ($fields as $field) {
              if ($field->priority == $i) {
                $column = $field->label;
                echo "<th width='19%'>$column</th>";
              }
            }
          }
          ?>
          <!-- <th width="20%">Actions</th> -->
        </tr>

      </thead>
      <tbody>
        <?php
        $result = $wpdb->get_results("SELECT * FROM $da_members_table LIMIT $start,$records_per_page");
        foreach ($result as $print) {
          echo "<tr>";
          echo "<td width='25px'> <input type='checkbox' name='memb_ids[]' id='' value=" . $print->id . " style='margin:0px 0px 8px 8px'> </td>";

          for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
            //show the data based on labels with priority from 1 - $numberOfPropertiesToShow
            $count = 0;
            foreach ($fields as $field) {
              $count++;
              if ($field->priority == $i) {
                $column = $field->field_name;
                echo "<td>";
                if ($count == 1) {
                  echo "<a href='http://localhost/wordpress/wp-admin/admin.php?page=da-members-edit&uptid=$print->id' style='font-size:14px'>" . $print->$column . "</a>";
                  echo "<div class='da-members-actions'>";
                  echo "<a href='http://localhost/wordpress/wp-admin/admin.php?page=da-members-edit&uptid=$print->id'>Edit</a>";
                  echo "<span>|</span>";
                  echo "<button type='button' class='delete_da_member' data-del-id=$print->id>Delete</button>";
                  echo "</div>";
                } else {
                  echo  $print->$column;
                }
                echo  "</td>";
              }
            }
          }
          // echo "<td width='20%'>
          // <a href='http://localhost/wordpress/wp-admin/admin.php?page=da-members-edit&uptid=$print->id'>
          // <button type='button' class='button button-primary'>EDIT</button></a>
          //  <button type='button' class='button button-primary delete_da_member' data-del-id=$print->id>DELETE</button>
          //  </td></tr>";
        }
        ?>
        <tr>
          <td colspan='<?php echo $numberOfPropertiesToShow + 2; ?>'>
            <?php daMembersPagination($members, $records_per_page); ?>
          </td>
        </tr>
      </tbody>
    </table>

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
  <div class="pagination" id=<?php echo $id; ?> style="width:100%;display: flex;align-items:center; justify-content:flex-start;gap:30px;">
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
