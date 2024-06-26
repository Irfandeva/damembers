<?php
function daMembersShow() {
  global $wpdb;
  global $result;
  $da_members_table = DA_MEMBERS_TABLE;
  $search_string = '';
  $result_rows = array();
  //bulk actions
  if (isset($_POST['do_bulk_action']) && isset($_POST['memb_ids'])) {
    $ids = array_map('sanitize_array', $_POST['memb_ids']);
    $action = sanitize_text_field($_POST['bulk_action']);
    $resultArr = array();
    if ($action == 'delete' && !empty($ids)) {
      for ($index = 0; $index < count($ids); $index++) {
        $res =   delete_a_member($ids[$index], $wpdb, $da_members_table);
        if ($res == 1) {
          $resultArr[] = $index;
        }
      }
    }
    if (count($resultArr) > 0) {
      $result['status'] = 'ok';
      $result['message'] = count($resultArr) . ' members have been deleted successfully ';
    }
  }
  //search
  if (isset($_POST['search-submit'])) {
    if (!empty($_POST['member-search-input'])) {
      $search_string = sanitize_text_field($_POST['member-search-input']);
    }
  }

  $start = 0;
  $page = $start;
  $numberOfPropertiesToShow = get_option('number_of_poperties_columns', 5);
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
      $delRes =  delete_a_member($id, $wpdb, $da_members_table);
    if ($delRes == 1) {
      $result['status'] = 'ok';
      $result['message'] = 'Member deleted successfully.';
    }
  }

  $members = $wpdb->get_results("SELECT * FROM $da_members_table");
?>
  <div class="wrap">
    <h1 class="wp-heading-inline">DA Members</h1>
    <?php
    $add_member_url = admin_url('admin.php?page=da-members-add');
    $download_url = admin_url('admin.php?page=download');
    $upload_url = admin_url('admin.php?page=upload-from-excel');

    echo '<a href="' . esc_url($add_member_url) . '" class="page-title-action">Add New Member &#43;</a>';
    echo '<a href="' . esc_url($download_url) . '" class="page-title-action">Download &darr; </a>';
    echo '<a href="' . esc_url($upload_url) . '" class="page-title-action">Upload &uarr; </a>';

    ?>
    <form action="" method="POST">
      <div class="search-box" style="text-align:end;padding: 8px 0px;">
        <label class="screen-reader-text" for="member-search-input">Search Member:</label>
        <input type="search" id="member-search-input" name="member-search-input" value="<?php echo $search_string; ?>">
        <input type="submit" id="search-submit" name="search-submit" class="button" value="Search Member">
      </div>
    </form>

    <hr class="wp-header-end">

    <?php
    if (isset($result) && !empty($result)) {
      if ($result['status'] == 'ok' && $result['message'] !== '') {
        echo "<div id='message' class='notice is-dismissible updated'>
        <p>" . $result['message'] . "</p><button type='button' class='notice-dismiss'>
        <span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
      }
    } ?>

    <form action="" method="post" onsubmit="return confirm('Are you sure you want to perform this bulk operation?');">
      <table class="wp-list-table widefat striped">
        <thead>
          <tr>
            <td id="check_da_members_rows" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox">
              <label for="cb-select-all-1"><span class="screen-reader-text">Select All</span></label>
            </td>
            <?php
            $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
            $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table ORDER BY priority ASC");
            for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
              foreach ($fields as $field) {
                if ($field->priority == $i) {
                  $column = $field->label;
                  echo "<th>$column</th>";
                }
              }
            } ?>
          </tr>

        </thead>
        <tbody>
          <?php
          //search
          if (!empty($search_string)) {
            $result_rows = $wpdb->get_results("SELECT * FROM $da_members_table WHERE LOCATE('$search_string', CONCAT_WS(' ', `first_name`, `last_name`, `bio`,`country`, `address`, `designation`,`constituency`,`department`)) > 0");
            echo_it($wpdb->last_error);
          } else {
            $result_rows = $wpdb->get_results("SELECT * FROM $da_members_table LIMIT $start,$records_per_page");
          }
          foreach ($result_rows as $result_row) {
            echo "<tr>";
            echo "<td> <input type='checkbox' name='memb_ids[]' id='' value=" . $result_row->id . " class='check_memb_rows'> </td>";

            for ($i = 1; $i <= $numberOfPropertiesToShow; $i++) {
              //show the data based on labels with priority from 1 - $numberOfPropertiesToShow
              $count = 0;
              foreach ($fields as $field) {
                $count++;
                if ($field->priority == $i) {
                  $column = $field->field_name;
                  echo "<td>";
                  if ($count == 1) {
                    $del_url = admin_url('admin.php?page=da-members');
                    echo "<a href='" . admin_url('admin.php?page=da-members-edit&uptid=' . $result_row->id) . "' style='font-size:14px'>" . esc_html($result_row->$column) . "</a> <span id='popup_$result_row->id' style='position:relative'></span>";
                    echo "<div class='da-members-actions' >";
                    echo "<a href='" . admin_url('admin.php?page=da-members-edit&uptid=' . $result_row->id) . "'>Edit</a>";

                    echo "<span>|</span>";
                    echo "<button type='button' class='delete_da_member' data-del-url='" . esc_attr($del_url) . "' data-del-member='" . esc_attr($result_row->first_name . ' ' . $result_row->last_name) . "' data-del-id='" . esc_attr($result_row->id) . "'>Delete</button>";

                    echo "</div>";
                  } else {
                    echo  stripslashes($result_row->$column);
                  }
                  echo  "</td>";
                }
              }
            }
            echo "</tr>";
          }
          ?>
          <tr>
            <td colspan='<?php echo $numberOfPropertiesToShow + 2; ?>'>
              <?php daMembersPagination($members, $records_per_page, $search_string, $result_rows); ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="alignleft actions bulkactions" style="margin-top: 10px;">
        <select name="bulk_action" id="bulk-action-selector-bottom">
          <option value="-1">Bulk actions</option>
          <option value="delete">Delete</option>
        </select>
        <input type="submit" name="do_bulk_action" id="do_bulk_action" class="button action" value="Apply">
      </div>
      <div id="overlay"></div>
  </div>
  </form>

<?php
}
//PAGINATION
function daMembersPagination($members, $records_per_page, $search_string, $result_rows) {

  $total_members = count($members);
  if (($total_members === 0 && $search_string == '')  || (!empty($search_string) && empty($result_rows))) {
    echo '<p>no records found.</p>';
    return;
  }
  //if user has hit search button , dont show pagination
  if (!empty($search_string)) return;
  $total_pages = ceil($total_members / $records_per_page);
  $last_page = $total_pages;
  $id = 1;
  if (isset($_GET['page_num'])) {
    $id = $_GET['page_num'];
  }
?>
  <div class="pagination" id=<?php echo $id; ?>>
    <div class="page-info">
      Showing <?php echo (!isset($_GET['page_num'])) ? '1' : $_GET['page_num'] ?> of <?php echo $total_pages ?> Pages
    </div>
    <div class="links">
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
      } ?>
      <!-- numbered links -->
      <div class="numbered_links">
        <?php
        for ($counter = 1; $counter <= $total_pages; $counter++) { ?>
          <a href='admin.php?page=da-members&page_num=<?php echo $counter ?>'><?php echo $counter ?></a>
        <?php
        } ?>
      </div>

      <?php
      //next button
      if (!isset($_GET['page_num']) && $total_pages >= 2) {
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
      } ?>
    </div>
  </div>
<?php
}
function delete_a_member($id, $wpdb, $da_members_table) {
  $res = $wpdb->query("DELETE FROM $da_members_table WHERE id='$id'");
  return $res;
}
