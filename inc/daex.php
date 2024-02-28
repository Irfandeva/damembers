<?php
function daMembersDownload() {

  // Function to export data in Excel format
  global $wpdb;
  if (isset($_GET['download'])) {
    // Query database to fetch data (example)
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}members");

    // Initialize an empty string to store Excel content
    $excelContent = '';

    // Prepare Excel column headers
    $excelContent .= "ID\tFirst Name\tLast Name\n";

    // Loop through the results and append data to the Excel content
    foreach ($results as $row) {
      // Sanitize data before output (example)
      $id = esc_html($row->id);
      $firstName = esc_html($row->first_name);
      $lastName = esc_html($row->last_name);

      // Append data to the Excel content
      $excelContent .= "$id\t$firstName\t$lastName\n";
    }
    $fileName = "da_members_" . date("Y-m-d") . ".xls";
    // Set headers for Excel download
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$fileName\"");

    // Output Excel content
    echo $excelContent;

    // Exit to prevent any additional content from being output
    exit;
  }
}
