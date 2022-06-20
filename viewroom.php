<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php";
  include "./includes/utilities.php";

  $error_messages = array();

  try {

    
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

    if (mysqli_connect_errno()) {
      throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
    }


    // check if id exists

    try {
      if(isset($_GET['id'])){
        $id = clean_input($_GET['id']);
        if(empty($id) || !is_numeric($id)){
          throw new Exception("Invalid room id");
        }
      } else {
        throw new Exception("Room id not provided");
      }
    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
    }


    $sql = "SELECT roomID, roomname, description, roomtype, beds
            FROM room WHERE roomID=?";

    $stmt = $db_connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $rowcount = mysqli_num_rows($result); 
    
  } catch (Exception $ex) {
    mysqli_stmt_close($stmt);
    mysqli_close($db_connection);
    echo "Error: " . $ex->getMessage();
    exit;
  }
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>
  <div id="content">
    <h1>Room Details View</h1>
    <p>
      <a href='./listrooms.php'>[Return to the Room listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>
    
    <?php
      if ($rowcount > 0) {  
          echo "<fieldset><legend>Room detail #$id</legend><dl>"; 
          $row = mysqli_fetch_assoc($result);
          echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
          echo "<dt>Description:</dt><dd>".$row['description']."</dd>".PHP_EOL;
          echo "<dt>Room type:</dt><dd>".$row['roomtype']."</dd>".PHP_EOL;
          echo "<dt>Beds:</dt><dd>".$row['beds']."</dd>".PHP_EOL; 
          echo '</dl></fieldset>'.PHP_EOL;  
      } else echo "<p class='error-message'>No Room found!</p>"; //suitable feedback

      mysqli_stmt_close($stmt);
      mysqli_close($db_connection);
    ?>
  </div>
</div>

<?php include "./components/footer.php"; ?>