<?php 
  session_start();

  // Session Variables
  /**
   * - username = customer username
   * - userid = customer id
   * - user = customer first and last name
   */


  // Function to redirect user to login screen if not logged in
  function protect_page() {
    if(!isset($_SESSION['userid'])){
      header("location: ./login.php");
    }
  }

  function is_logged_in(){
    return isset($_SESSION['username']);
  }

  function get_user_id(){
    if(isset($_SESSION['userid'])){
      return trim($_SESSION['userid']);
    }
  }

  function get_user(){
    if(isset($_SESSION['user'])){
      return trim($_SESSION['user']);
    }
  }

  function print_login_status() {
    if(isset($_SESSION['username'])){
      echo "Logged in as <strong>" . $_SESSION['username'] . "</strong>";
    } else {
      echo "Logged out";
    }
  }

?>