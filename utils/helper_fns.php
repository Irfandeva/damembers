<?php
function log_it($val) {
  echo "<pre>";
  var_dump($val);
  echo "</pre>";
}
function echo_it($val) {
  echo "<pre>";
  echo ($val);
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
function trim_and_tolowercase($str) {
  if (!empty($str))
    return trim(strtolower($str));
}
//find match between two strings
function find_match($pattern, $string) {
  echo $pattern . $string . "<br>";
  $pattern = trim_and_tolowercase($pattern);
  $string = trim_and_tolowercase($string);

  if ($pattern == $string || preg_match("/$pattern/", $string) || preg_match("/$string/", $pattern)) {
    return true;
  } else {
    return false;
  }
}
//sanitize array item
function sanitize_array($arr_item) {
  if (empty($arr_item)) return '';
  return sanitize_text_field($arr_item);
}
