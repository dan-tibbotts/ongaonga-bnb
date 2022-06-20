<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php";
  include "./includes/utilities.php";

  protect_page();

  $booking_id = 0;
  $customer_id = 0;
  $room_id = 0;
  $room_name = "";
  $checkin_date = "";
  $checkout_date = "";
  $contact_number = "";
  $booking_extras = "";
  $room_review = "";

  $errors = 0;
  $error_messages = array();

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

  /* GET REQUEST */
  if($_SERVER['REQUEST_METHOD'] == "GET"){

    // Get the booking id
    try {
      if(isset($_GET['id'])){
        
        if(empty($_GET['id']) || !is_numeric($_GET['id'])){
          throw new Exception("Invalid booking id");
        } else {
          $booking_id = clean_input($_GET['id']);
        }
      } else {
        throw new Exception("Booking id not provided");
      }

      // GET - Booking Record
      $booking_sql = "SELECT b.bookingID, b.customerID, b.roomID, r.roomname, b.checkindate, b.checkoutdate, b.contactnumber, b.bookingextras, b.roomreview
                      FROM booking AS b
                      INNER JOIN room AS r ON b.roomID = r.roomID
                      WHERE bookingID=?";

      $booking_stmt = $db_connection->prepare($booking_sql);
      $booking_stmt->bind_param("i", $booking_id);
      $booking_stmt->execute();

      $booking_result = $booking_stmt->get_result();
      $booking_rowcount = mysqli_num_rows($booking_result);
      $booking_stmt->close();

      if($booking_rowcount > 0){   
        $booking_row = mysqli_fetch_assoc($booking_result);
        $customer_id = $booking_row['customerID'];
        $room_id = $booking_row['roomID'];;
        $room_name = $booking_row['roomname'];
        $checkin_date = $booking_row['checkindate'];;
        $checkout_date = $booking_row['checkoutdate'];;
        $contact_number = $booking_row['contactnumber'];;
        $booking_extras = $booking_row['bookingextras'];;
        $room_review = $booking_row['roomreview'];;
      } else {
        throw new Exception("Booking $booking_id not found");
      }

      $db_connection->close();
      
    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
      $errors++;
      $db_connection->close();
    }
  }


  /* POST REQUEST */
  if($_SERVER['REQUEST_METHOD'] == "POST"){

    // Validate Booking ID
    if (isset($_POST['bookingid']) && 
        !empty($_POST['bookingid']) && 
        is_integer(intval($_POST['bookingid'])) &&
        $_POST['bookingid'] != 0) {
      $booking_id = clean_input($_POST['bookingid']); 
    } else {
      $errors++;
      $booking_id = 0;  
    }    

    if($errors == 0 && $booking_id > 0){

      $delete_sql = "DELETE FROM booking WHERE bookingID=?";

      $delete_stmt = $db_connection->prepare($delete_sql);
      $delete_stmt->bind_param("i", $booking_id);
      $delete_stmt->execute();
      $delete_stmt->close();
      
      $db_connection->close();
      header('location: ./deletesuccess.php?booking&id=' . $booking_id);
      exit;
    }

    $db_connection->close();
  }
?>


<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
      
    <h1>
      Booking preview before deletion
    </h1>

    <p>
      <a href="./listbookings.php">[Return to the booking listing]</a>
      <a href="./index.php">[Return to the main page]</a>
  </p>

    <?php if(($errors == 0) ||  $_SERVER["REQUEST_METHOD"] == "POST" ){?>
      <fieldset>
        <legend>Booking detail #<?php echo $booking_id; ?></legend>
        <dl>
          <dt>Room name:</dt>
          <dd><?php echo $room_name;?></dd>

          <dt>Checkin date:</dt>
          <dd><?php echo $checkin_date;?></dd>

          <dt>Checkout date:</dt>
          <dd><?php echo $checkout_date;?></dd>
        </dl>
      </fieldset>

      <p class="bold caution-box">Are you sure you want to delete this Booking?</p>

      <form method="POST" action="./deletebooking.php">
        <input id="bookingid" name="bookingid" value="<?php echo $booking_id ?>" hidden/>
        <button onclick="deleteBooking()">Delete</button>
        <a href="./index.php">[Cancel]</a>
      </form>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

  </div>
  
</div>




<?php include "./components/footer.php"; ?>