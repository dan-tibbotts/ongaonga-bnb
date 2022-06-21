<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  protect_page();

  $id = 0;
  $room_name = "";
  $room_description = "";
  $room_type = "";
  $beds = 0;

  $error_messages = array();

  // Display Successful Delete
  if(isset($_GET['success'])){
    echo "Record successfully deleted! <br/>";
    echo "<a href='./listrooms.php'>Back to Room Listings</a>";
    exit;
  }

  // Connect to database
  try {
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);
    if (mysqli_connect_errno()) {
      throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
    }
  } catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    $db_connection->close();
    exit;
  }


  // GET - Room Record
  try {

    if($_SERVER["REQUEST_METHOD"] == "GET") {

      // Get the room id
      if(isset($_GET['id'])) {
        $id = clean_input($_GET['id']);
        if (empty($id) || !is_numeric($id)) {
          throw new Exception("Invalid Room ID");
        } 
      } else {
        throw new Exception("Room ID Not provided");
      }  


      $sql_select = " SELECT roomID, roomname, description, roomtype, beds
      FROM room WHERE roomID=?";

      $select_stmt = $db_connection->prepare($sql_select);
      $select_stmt->bind_param("i", $id);
      $select_stmt->execute();
      $select_result = $select_stmt->get_result(); // get the mysqli result
      $select_rowcount = mysqli_num_rows($select_result); 
      $select_stmt->close();

      if($select_rowcount > 0){
        $row = mysqli_fetch_assoc($select_result);

        $room_name = $row['roomname'];
        $room_description = $row['description'];
        $room_type = $row['roomtype'];
        $beds = $row['beds'];
      } else {
        throw new Exception("Room $id not found");
      }
    }
    
  } catch (Exception $ex) {
    array_push($error_messages, $ex->getMessage());
  }


  // POST - Delete Room Record 
  try {
    $errors = 0;

    if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Delete')) {

      // Validate Room ID
      if (isset($_POST['id']) && !empty($_POST['id']) && is_integer(intval($_POST['id']))) {
        $id = clean_input($_POST['id']); 
      } else {
        $errors++;
        $id = 0;  
      } 

      // Delete Room
      if ($errors == 0 && $id > 0) {

        $sql_delete = "DELETE FROM room WHERE roomID=?";
        $delete_stmt = mysqli_prepare($db_connection, $sql_delete);

        mysqli_stmt_bind_param($delete_stmt,'i', $id); 
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);    

        header("location: ./deletesuccess.php?room&id=$id");
        $db_connection->close();
        exit;

      } else { 
        throw new Exception("Cannot delete room, RoomID is invalid");
      }  

    }
    
    $db_connection->close();

  } catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    $db_connection->close();
    exit;
  }

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Room details preview before deletion</h1>
    <p>
      <a href='./listrooms.php'>[Return to the Room listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>

    <?php if (isset($select_rowcount) && $select_rowcount > 0) {?>
    <fieldset>
      <legend>Room detail #<?php echo $id; ?></legend>
      <dl>
        <dt>Room name:</dt><dd><?php echo $room_name ?></dd>
        <dt>Description:</dt><dd><?php echo $room_description ?></dd>
        <dt>Room type:</dt><dd><?php echo $room_type ?></dd>
        <dt>Beds:</dt><dd><?php echo $beds ?></dd>
      </dl>
    </fieldset>

    <form method="POST" action="./deleteroom.php">
      <p class="bold">Are you sure you want to delete this Room?</p>

      <input type="hidden" name="id" value="<?php echo $id; ?>">

      <button type="submit" name="submit" value="Delete">Delete</button>
      <a href="./listrooms.php">[Cancel]</a>
    </form>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

  </div>

</div>



<?php include "./components/footer.php"; ?>