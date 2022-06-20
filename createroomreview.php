<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  protect_page();

  $booking_id = 0;
  $room_review = "";
  $customer_name = "";

  $errors = 0;
  $error_messages = array();

  // Connect to database
  try {
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);
    if(mysqli_connect_errno()){
      throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
    }
  } catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    $db_connection->close();
    exit;
  }


  if($_SERVER["REQUEST_METHOD"] == "GET"){

    // Confirm Booking id
    try {
      if(isset($_GET['id'])) {
        
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
          throw new Exception("Invalid Room ID");
        } else {
          $booking_id = clean_input($_GET['id']);
        }
      } else {
        throw new Exception("Booking id not provided");
      }  

      $select_sql = " SELECT b.roomreview, b.customerID, c.firstname, c.lastname
                      FROM (booking AS b 
                      INNER JOIN customer AS c ON b.customerID = c.customerID)
                      WHERE bookingID=?";
      
      $select_stmt = $db_connection->prepare($select_sql);
      $select_stmt->bind_param("i", $booking_id);
      $select_stmt->execute();
      $select_result = $select_stmt->get_result();
      $select_rowcount = mysqli_num_rows($select_result); 
      $select_stmt->close();

      if($select_rowcount > 0){
        $row = mysqli_fetch_assoc($select_result);
        $room_review = $row['roomreview'];
        $customer_name = $row['firstname'] . " " . $row['lastname'];

        // Display Error if Reivew already placed
        if(!empty($room_review) && !isset($_GET['success'])){
          throw new Exception("Review already placed for booking #$booking_id");
        }

      } else {
        throw new Exception("Booking #$booking_id not found");
      }
          
      $db_connection->close();

    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
      $errors++;
      $db_connection->close();
    }
    
  }


  /* POST METHOD - Update Booking */
  if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check ID
    if (isset($_POST['bookingID']) && !empty($_POST['bookingID']) && is_integer(intval($_POST['bookingID']))) {
      $booking_id = clean_input($_POST['bookingID']); 
    } else {
      $errors++;
      array_push($error_messages, "Invalid Booking ID");
      $booking_id = 0;  
    } 

    // Validate room review
    if(isset($_POST['roomreview']) && 
      !empty($_POST['roomreview']) &&
      !(strlen($_POST['roomreview']) < 5) &&
      !(strlen($_POST['roomreview']) > 250)
    ){
      $room_review = clean_input($_POST['roomreview']);
      $room_review = clip_string_length($room_review, 250);
    } else {
      $errors++;
      array_push($error_messages, "Room review is required betweem 5 and 20 characters");
    } 

    // Get Customer Name
    if(isset($_POST['customername']) && 
      !empty($_POST['customername'])
    ){
      $customer_name = clean_input($_POST['customername']);
    } else {
      $customer_name = "unknown";
    } 


    if($errors == 0 && $booking_id > 0) {

      try {
        // Save Room Review
        $update_sql = " UPDATE booking
        SET roomreview=?
        WHERE bookingID=?";

        $update_stmt = $db_connection->prepare($update_sql);
        $update_stmt->bind_param("si", $room_review, $booking_id);
        $update_stmt->execute();
        $update_stmt->close();

        header("location: ./createroomreview.php?id=$booking_id&success=true");
        $db_connection->close();

      } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
        $db_connection->close();
        exit;
      }
    }
  }

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Add room review</h1>

    <p>
      <a href="./listbookings.php">[Return to the Bookings listing]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>

    <?php if((isset($select_rowcount) && $select_rowcount > 0 && $errors == 0 && !isset($_GET['success'])) || $_SERVER["REQUEST_METHOD"] == "POST") {?>
      <p>Review made by '<span class="bold"><?php echo $customer_name; ?></span>'</p>

      <form method="POST" action="./createroomreview.php">

        <input type="text" name="customername" value="<?php echo $customer_name; ?>" hidden/>

        <input 
          type="text" 
          id="bookingID" 
          name="bookingID" 
          value="<?php echo $booking_id ?>"
          hidden/>

        <!-- Room Review -->
        <p class="field-container">
          <label for="roomreview">Room review:</label>
          <textarea
            id="roomreview"
            name="roomreview"
            rows="5"
            cols="35"
            maxlength="250"
            required><?php echo $room_review; ?></textarea>
        </p>

        <button type="submit">Add</button>
        <a href="./listbookings.php">[Cancel]</a>
      </form>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

    <p style="color: green;">
      <?php 
        if(isset($_GET['success'])){
          echo "Successfully added Room Review!";
        }
      ?>
    </p>


  </div>
  
</div>


<?php include "./components/footer.php"; ?>