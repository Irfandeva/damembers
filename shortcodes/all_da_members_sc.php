<?php
function all_da_members_sc($atts) {
  $atts = shortcode_atts(array(
    'department' => '',
  ), $atts, 'da_members');
  $department_name = $atts['department'];
  if (empty($department_name)) {
    return 'Please provide a department name.';
  }

  global $wpdb;
  $da_members = DA_MEMBERS_TABLE;
  $members = $wpdb->get_results("SELECT * FROM $da_members");
  if (!$members) {
    return 'No member found with given department name.';
  }

  $da_members_form_fields_table = DA_MEMBERS_FORM_FIELDS_TABLE;
  $output = '<div class="all-da-members-container">';
  $fields = $wpdb->get_results("SELECT * FROM $da_members_form_fields_table ORDER BY priority ASC");


  foreach ($members as $member) {
    $output .= '<div class="da-members-row">';
    $output .= '<div class="left">
                <div class="left-content">
                <h3>' . $member->first_name . " " . $member->last_name . '</h3>
                <span class="bio" data-popup = "' . $member->id . '" >Bio
                <input type="hidden" value="' . htmlentities(stripslashes($member->bio)) . '"/>
                </span> 
                <span id="popup_' . $member->id . '" style="position:relative;color: #4a6317;font-size: 14px;">
                </span>
                <span class="country">' . $member->country . '</span>
                </div>
                </div>';
    $output .= '<div class="right">
                <div class="right-content">' . $member->designation . '</div>
                </div>';
    $output .= '</div>';
  }

  $output .= '</div>';
  return $output;
}
