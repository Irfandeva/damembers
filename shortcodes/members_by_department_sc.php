<?php
//SHORT CODE 
function members_by_department_sc($atts) {
  $atts = shortcode_atts(array(
    'department' => '',
    'cols' => '5',
  ), $atts, 'members_by_department');
  $department_name = $atts['department'];
  $number_of_cols = $atts['cols'];
  if (empty($department_name)) {
    return 'Please provide a department name.';
  }

  global $wpdb;
  $da_members = DA_MEMBERS_TABLE;
  $members = $wpdb->get_results("SELECT * FROM $da_members WHERE department='$department_name'");
  if (!$members) {
    return 'No member found with given department name.';
  }

  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $output = '<div class="members-by-dept-container">';
  $output .= '<table id="members_by_dept">';
  $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table ORDER BY priority ASC");

  $output .= '<thead>';
  $output .= '<tr>';
  for ($i = 1; $i <= $number_of_cols; $i++) {
    foreach ($fields as $field) {
      if ($field->priority == $i) {
        $column = $field->label;
        $output .= '<th>' . $column . '</th>';
      }
    }
  }
  $output .= '</tr>';
  $output .= '</thead>';
  $output .= '<tbody>';

  foreach ($members as $member) {
    $output .= '<tr>';
    for ($i = 1; $i <= $number_of_cols; $i++) {
      foreach ($fields as $field) {
        if ($field->priority == $i) {
          $id = $member->id;
          $column = $field->field_name;
          $td_open_tag = $column == "first_name" ? "<td class='td-first-name' id='name_$id'>" : "<td>";
          $output .= $td_open_tag . $member->$column . '<input type="hidden" value ="' . htmlentities(stripslashes($member->bio)) . '"><span style="position:relative" class="bio-popup' . $id . '"><span></td>';
        }
      }
    }
    $output .= '</tr>';
  }
  $output .= '</tbody>';
  $output .= '</table>';
  $output .= '</div>';
  return $output;
}
