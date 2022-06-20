<?php 
  session_start();
  session_unset(); // clear session variables
  session_destroy(); // delete session file
  header('location: ../index.php');
?>