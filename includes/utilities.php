<?php 

  // Clean input to remove unncessary characters, spaces etc..
  function clean_input($field){
    return htmlspecialchars(stripslashes(trim($field)));
  }
  
  // Clip the length of a string to the specified length
  function clip_string_length($string, $length){
    return trim((strlen($string) > $length) ? substr($string, 1, $length) : $string);
  }

?>