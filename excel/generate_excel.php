<?php
function generate_excel($array, $filename = 'da-members', $title = 'damembers') {
  $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
  $spreadsheet->getActiveSheet()->fromArray($array);
  $spreadsheet->getActiveSheet()->setTitle($title);
  // Generate temporary file path with file extension
  $temp_file = tempnam(sys_get_temp_dir(),  $filename . '_') . '.' . 'xls';
  $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
  // Save Excel data to a temporary file
  $writer->save($temp_file);
  // Reurn temporary file path with file extension
  return $temp_file;
}
