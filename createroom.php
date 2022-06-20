<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php";
  include "./includes/utilities.php";

  protect_page();

  $room_name = "";
  $room_description = "";
  $room_type = "D";
  $beds = 1;

  $error_messages = array();

  if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {

    try {
      
      
      $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

      if (mysqli_connect_errno()) {
        throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
      };

      $errors = 0;
      

      // Validate room name
      if (isset($_POST['roomname']) && 
          !empty($_POST['roomname']) && 
          is_string($_POST['roomname']) &&
          !(strlen($_POST['roomname']) < 5) && 
          !(strlen($_POST['roomname']) > 100))  {
        $room_name_cleaned = clean_input($_POST['roomname']);
        $room_name = clip_string_length($room_name_cleaned, 100);
      } else {
        $errors++; 
        array_push($error_messages, "Room name must be between 5 and 100 characters");
      } 


      // Validate room description
      if (isset($_POST['description']) && 
          !empty($_POST['description']) && 
          is_string($_POST['description']) &&
          (strlen($room_description) < 5 || strlen($room_description) > 200)) {
        $room_description_cleaned = clean_input($_POST['description']);
        $room_description = clip_string_length($room_description_cleaned, 200);
      } else {
        $errors++; 
        array_push($error_messages, "Room desctription must be between 5 and 200 characters");
      } 


      // Validate room type
      if (isset($_POST['roomtype']) && !empty($_POST['roomtype']) && is_string($_POST['roomtype'])) {
        $room_type_cleaned = strtoupper(clean_input($_POST['roomtype']));
        $room_type = clip_string_length($room_type_cleaned, 1);
      } else {
        $errors++; 
        array_push($error_messages, "Room type is not valid. Please select either single (S) or double (D)");
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


      if($errors == 0){
        $sql = "INSERT INTO room (roomname, description, roomtype, beds) VALUES (?,?,?,?)";
        $stmt = $db_connection->prepare($sql);
        $stmt->bind_param("sssi", $room_name, $room_description, $room_type, $beds);
        $stmt->execute();
        mysqli_stmt_close($stmt);
        mysqli_close($db_connection);
        header("location: ./createroom.php?success=true");
        exit;
      }

    } catch (Exception $ex) {
      mysqli_stmt_close($stmt);
      mysqli_close($db_connection);
      echo "Error: " . $ex->getMessage();
      exit;
    }
}

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Add a new room</h1>
    <p>
      <a href='./listrooms.php'>[Return to the room listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>

    <form method="POST" action="./createroom.php">

      <p>
        <label for="roomname">Room name: </label>
        <input 
          type="text" 
          id="roomname" 
          name="roomname"
          value="<?php echo $room_name; ?>"
          minlength="5" 
          maxlength="100" 
          required> 
      </p> 

      <p>
        <label for="description">Description: </label>
        <input 
          type="text" 
          id="description" 
          value="<?php echo $room_description; ?>"
          size="50" 
          name="description" 
          minlength="5" 
          maxlength="200"
          required> 
      </p>

      <p>  
        <label for="roomtype-single">Room type: </label>
        <input 
          type="radio" 
          id="roomtype-single" 
          name="roomtype" 
          value="S"
          <?php echo $room_type == "S" ? "checked" : ""; ?>> 
          Single 
          
        <input 
          type="radio" 
          id="roomtype-double" 
          name="roomtype" 
          value="D" 
          <?php echo $room_type == "D" ? "checked" : ""; ?>> 
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

      <p style="color: red;">
        <?php echo implode(", ", $error_messages); ?>
      </p>

      <p style="color: green;">
        <?php 
          if(isset($_GET['success'])){
            echo "Successfully created a booking";
          }
        ?>
      </p>

      <button type="submit" name="submit" value="Add">Add</button>
    </form>

  </div>

</div>

<?php include "./components/footer.php"; ?>
  