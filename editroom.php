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

  try {
    
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);
    
    if (mysqli_connect_errno()) {
      throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
    }

    /* 
      GET - ROOM RECORD 
    */
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

      // Get the room ID
      if(isset($_GET['id'])) {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
          throw new Exception("Invalid Room ID");
        } 
      } else {
        throw new Exception("Room ID Not provided");
      }  

      // get the room details
      $sql_select = " SELECT roomID, roomname, description, roomtype, beds
                      FROM room WHERE roomID=?";

      $select_stmt = $db_connection->prepare($sql_select);
      $select_stmt->bind_param("i", $id);
      $select_stmt->execute();
      $select_result = $select_stmt->get_result(); // get the mysqli result
      $select_rowcount = mysqli_num_rows($select_result); 
      mysqli_stmt_close($select_stmt);

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

    /* 
      POST - UPDATE ROOM
    */
    if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
      
      $errors = 0;

      // Validate Room ID 
      if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
        $id = clean_input($_POST['id']); 
      } else {
        $errors++; //bump the error flag
        array_push($error_messages, "Invalid room ID");
        $id = 0;  
      } 
      

      // Validate Room Name
      if (isset($_POST['roomname']) && 
          !empty($_POST['roomname']) && 
          is_string($_POST['roomname']) &&
          !(strlen($_POST['roomname']) < 5) && 
          !(strlen($_POST['roomname']) > 100)) {
        $room_name_cleaned = clean_input($_POST['roomname']);
        $room_name = clip_string_length($room_name_cleaned, 100);
      } else {
        $errors++; 
        array_push($error_messages, "Room name must be between 5 - 100 characters");
      } 


      // Validate room description
      if (isset($_POST['description']) && 
          !empty($_POST['description']) && 
          is_string($_POST['description']) &&
          (strlen($room_description) < 5 || strlen($room_description) > 200)) {
        $room_description_cleaned = clean_input($_POST['description']);
        $room_description = clip_string_length($room_description_cleaned, 100);
      } else {
        $errors++; 
        array_push($error_messages, "Room description must be between 5 and 200 characters");
      } 

      // Validate room type
      if (isset($_POST['roomtype']) && !empty($_POST['roomtype']) && is_string($_POST['roomtype'])) {
        $room_type_cleaned = strtoupper(clean_input($_POST['roomtype']));
        $room_type = clip_string_length($room_type_cleaned, 1);
      } else {
        $errors++; 
        array_push($error_messages, "Room type is not valid");
      } 

      // Validate beds
      if (isset($_POST['beds']) && 
          !empty($_POST['beds']) &&
          !($_POST['beds'] < 1) &&
          !($_POST['beds'] > 5)) {
        $beds = clean_input(intval($_POST['beds']));
      } else {
        $errors++;
        array_push($error_messages, "Number of beds must be between 1 -5");
      } 


      if($errors == 0 && $id > 0){

        $sql_update = "UPDATE room SET roomname=?,description=?,roomtype=?,beds=? WHERE roomID=?";
        $update_stmt = $db_connection->prepare($sql_update);

        $update_stmt->bind_param("sssii", $room_name, $room_description, $room_type, $beds, $id);
        $update_stmt->execute();
        $update_stmt->close();
        $db_connection->close();

        header('location: ./listrooms.php', true, 303);  
        //header("location: ./editroom.php?id=$id&success=true");
        exit;
      }
    }

  } catch (Exception $ex) {
    $db_connection->close();
    array_push($error_messages, $ex->getMessage());
  }

?>


<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    <h1>Room Details Update</h1>
    <p>
      <a href='./listrooms.php'>[Return to the room listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>

    <?php if((isset($select_rowcount) && $select_rowcount > 0) || isset($_POST['submit'])){?>
    <form method="POST" action="./editroom.php">

      <input type="hidden" name="id" value="<?php echo $id;?>">

      <p>
        <label for="roomname">Room name: </label>
        <input 
          type="text" 
          id="roomname" 
          name="roomname" 
          minlength="5" 
          maxlength="100" 
          value="<?php echo $room_name; ?>" 
          required> 
      </p> 

      <p>
        <label for="description">Description: </label>
        <input 
          type="text" 
          id="description" 
          name="description" 
          size="50" 
          minlength="5" 
          maxlength="200" 
          value="<?php echo $room_description; ?>" 
          required> 
      </p>  

      <p>  
        <label for="roomtype-single">Room type: </label>
        <input 
          type="radio" 
          id="roomtype-single" 
          name="roomtype" 
          value="S" 
          <?php echo $room_type =='S'?'Checked':''; ?>> 
          Single 

        <input 
          type="radio" 
          id="roomtype-double" 
          name="roomtype" 
          value="D" 
          <?php echo $room_type =='D'?'Checked':''; ?>> 
          Double 
      </p>

      <p>
        <label for="beds">Beds (1-5): </label>
        <input 
          type="number" 
          id="beds" 
          name="beds" 
          min="1" max="5" 
          value="<?php echo $beds; ?>" 
          required> 
      </p> 

      <input type="submit" name="submit" value="Update">
    </form>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

    <p style="color: green;">
      <?php 
        if(isset($_GET['success'])){
          echo "Successfully updated room";
        }
      ?>
    </p>

  </div>

</div>


<?php include "./components/footer.php"; ?>
  