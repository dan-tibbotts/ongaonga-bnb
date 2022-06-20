<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  protect_page();

  $id = 0;
  $customer_id = 0;
  $room_id = 0;
  $room_name = "";
  $checkin_date = "";
  $checkout_date = "";
  $contact_number = "";
  $booking_extras = "";
  $room_review = "";

  $error_messages = array();

  // Get the booking id
  try {
    if(isset($_GET['id'])){
      $id = clean_input($_GET['id']);
      if(empty($id) || !is_numeric($id)){
        throw new Exception("Invalid booking id");
      }
    } else {
      throw new Exception("Booking id not provided");
    }
  } catch (Exception $ex) {
    array_push($error_messages, $ex->getMessage());
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

  if($_SERVER['REQUEST_METHOD'] == "GET" && $id > 0){

    // GET - Booking Record
    try {
      $booking_sql = "SELECT b.bookingID, b.customerID, b.roomID, r.roomname, b.checkindate, b.checkoutdate, b.contactnumber, b.bookingextras, b.roomreview
                      FROM booking AS b
                      INNER JOIN room AS r ON b.roomID = r.roomID
                      WHERE bookingID=?";

      $booking_stmt = $db_connection->prepare($booking_sql);
      $booking_stmt->bind_param("i", $id);
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
        throw new Exception("Booking $id not found");
      }
    $db_connection->close();

    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
      $db_connection->close();
    }
  }
  
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    <h1>Booking Details View</h1>
    <p>Logged in as <?php echo $_SESSION['user']; ?></p>

    <p>
      <a href="./listbookings.php">[Return to the booking listing]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>
    
    <?php if(isset($booking_rowcount) && $booking_rowcount > 0) { ?>
    <fieldset>
      <legend>Room detail #<?php echo $id ?></legend>

      <dl>
        <dt>Room name:</dt>
        <dd><?php echo $room_name ?></dd>

        <dt>Checkin date:</dt>
        <dd><?php echo $checkin_date ?></dd>

        <dt>Checkout date:</dt>
        <dd><?php echo $checkout_date ?></dd>

        <dt>Contact number:</dt>
        <dd><?php echo $contact_number ?></dd>

        <dt>Extras:</dt>
        <dd><?php echo $booking_extras ?></dd>

        <dt>Room review:</dt>
        <dd><?php echo $room_review ?></dd>
      </dl>
    </fieldset>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

  </div>
</div>

<?php include "./components/footer.php"; ?>