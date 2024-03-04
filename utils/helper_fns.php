<?php
function log_it($val) {
  echo "<pre>";
  var_dump($val);
  echo "</pre>";
}
//replace whitespace with _ and make every word lower case.
function add_($str) {
  if (!empty($str))
    return strtolower(str_replace(' ', '_', $str));
}
//replace _ with whitespace and make first char of every word uppercase.
function remove_($str) {
  if (!empty($str))
    return ucwords(str_replace('_', ' ', $str));
}
