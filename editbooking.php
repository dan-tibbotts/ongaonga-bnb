<?php
  include "./components/header.php";
  include "./components/menu.php";
  include './includes/config.php';
  include './includes/utilities.php';

  protect_page();

  // Set initial values for inputs
  $booking_id = 0;
  $customer_id = 0;
  $customer_name = "TODO: Create INNER JOIN to retrieve Customer ID";
  $room_id = 5;
  $check_in_date = "";
  $check_out_date = "";
  $contact = "";
  $booking_extras = "";
  $room_review = "";

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


  // GET REQUESTS
  if($_SERVER["REQUEST_METHOD"] == "GET") {

    // Get the booking id
    try {
      if(isset($_GET['id'])) {
        
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
          throw new Exception("Invalid Booking ID");
        } else {
          $booking_id = clean_input($_GET['id']);
        }
      } else {
        throw new Exception("Booking id not provided");
      }  

      // Get booking data
      $sql_select = " SELECT  b.bookingID, b.customerID, c.firstname, c.lastname, 
                              b.roomID, b.checkindate, b.checkoutdate, 
                              b.contactnumber, b.bookingextras, roomreview
                      FROM (booking AS b
                      INNER JOIN Customer AS c ON b.customerID = c.customerID)
                      WHERE bookingID=?";

      $select_stmt = $db_connection->prepare($sql_select);
      $select_stmt->bind_param("i", $booking_id);
      $select_stmt->execute();
      $select_result = $select_stmt->get_result();
      $select_rowcount = mysqli_num_rows($select_result); 
      $select_stmt->close();

      if($select_rowcount > 0){
        $row = mysqli_fetch_assoc($select_result);

        $customer_id = $row['customerID'];
        $customer_name = $row['firstname'] . " " . $row['lastname'];
        $room_id = $row['roomID'];
        $check_in_date = $row['checkindate'];
        $check_out_date = $row['checkoutdate'];
        $contact = $row['contactnumber'];
        $booking_extras = $row['bookingextras'];
        $room_review = $row['roomreview'];

      } else {
        throw new Exception("Booking #$booking_id not found");
      }


    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
      $errors++;
      $db_connection->close();
    }
  }

  if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Booking ID 
    if (isset($_POST['bookingID']) && !empty($_POST['bookingID']) && is_integer(intval($_POST['bookingID']))) {
      $booking_id = clean_input($_POST['bookingID']); 
    } else {
      $errors++; //bump the error flag
      array_push($error_messages, "Invalid Booking ID");
      $booking_id = 0;  
    } 

    // Validate Room ID 
    if (isset($_POST['roomID']) && 
        !empty($_POST['roomID']) && 
        is_integer(intval($_POST['roomID'])) && 
        $_POST['roomID'] != 0) {
      $room_id = clean_input($_POST['roomID']); 
    } else {
      $errors++; //bump the error flag
      array_push($error_messages, "Invalid Room ID");
      $room_id = 0;  
    } 

    // Validate check-in date
    if(isset($_POST['checkindate']) && !empty($_POST['checkindate']) && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $_POST['checkindate'])){
      $check_in_date = clean_input($_POST['checkindate']);
    } else {
      $errors++;
      array_push($error_messages, "Check-in date is not valid");
    }

    // Validate check-out date
    if(isset($_POST['checkoutdate']) && !empty($_POST['checkoutdate']) && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $_POST['checkoutdate'])){
      $check_out_date = clean_input($_POST['checkoutdate']);
    } else {
      $errors++;
      array_push($error_messages, "Check-out date is not valid");
    }

    // Validate check-in date is not greater than check-out date 
    if(isset($check_in_date) && isset($check_out_date)){
      if($check_in_date > $check_out_date){
        $errors++;
        array_push($error_messages, "Check-in date cannot occur after the check-out date");
      }
    }

    // Validate contact number
    if(isset($_POST['contact']) && !empty($_POST['contact']) && preg_match("/[\(][(\+|0-9)][0-9]{2}[\)][\s][0-9]{3}[\s][0-9]{4}/", $_POST['contact'])){
      $contact = clip_string_length(clean_input($_POST['contact']), 14);
    } else {
      $errors++;
      array_push($error_messages, "Contact number is not valid");
    }

    // Validate booking extras
    if(isset($_POST['bookingextras']) && !empty($_POST['bookingextras'])){
      $booking_extras = clean_input($_POST['bookingextras']);
      $booking_extras = clip_string_length($booking_extras, 250);
    }

    // Validate room review
    if(isset($_POST['roomreview']) && !empty($_POST['roomreview'])){
      $room_review = clean_input($_POST['roomreview']);
      $room_review = clip_string_length($room_review, 250);
    }

    // Update record if no errors
    if($errors == 0 && $booking_id > 0){
      
      try {
        $sql_update ="UPDATE booking 
                      SET roomID=?,checkindate=?,checkoutdate=?, contactnumber=?, bookingextras=?, roomreview=? 
                      WHERE bookingID=?";

        $update_stmt = $db_connection->prepare($sql_update);
        $update_stmt->bind_param("isssssi", $room_id, $check_in_date, $check_out_date, $contact, $booking_extras, $room_review, $booking_id);
        $update_stmt->execute();
        $update_stmt->close();     
        $db_connection->close();

        header("location: ./editbooking.php?id=$booking_id&success=true");
        exit;

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
    
    <h1>Edit a booking</h1>

    <p>
      <a href="./listbookings.php">[Return to the Bookings listing]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>

    <?php if(($errors == 0 && $booking_id > 0) || $_SERVER["REQUEST_METHOD"] == "POST" ) {?>
    <form method="POST" action="./editbooking.php">
      <p>Booking for '<span class="bold"><?php echo $customer_name ?></span>'</p>

      <!-- Booking ID -->
      <input type="text" id="bookingID" name="bookingID" value="<?php echo $booking_id ?>"  hidden/>

      <!-- Room --> 
      <p class="field-container">
        <label for="roomID">Room:</label>
        <select 
          id="roomID" 
          name="roomID" 
          required >

          <?php  
            if($room_id == 0){
              echo "<option value='' disabled selected hidden>Choose room</option>";
            }

            $room_query = " SELECT roomID, roomname, roomtype, beds
                            FROM room
                            ORDER BY roomname";

            $room_result = mysqli_query($db_connection, $room_query);
            $room_row_count = mysqli_num_rows($room_result);

            if($room_row_count > 0){

              while($room_row = mysqli_fetch_assoc($room_result)){
                echo "<option value=" . $room_row['roomID'] ;
                echo intval($room_id) == intval($room_row['roomID']) ? " selected>" : ">";
                echo $room_row['roomname'] .  ", " .  $room_row['roomtype'] . ", ". $room_row['beds'];
                echo "</option>";
              }

            }

            mysqli_free_result($room_result);
          ?>
        </select>
        <span>(name,type,beds)</span>
      </p>

      <!-- Check-in Date -->
      <p class="field-container">
        <label for="checkindate">Checkin date: </label>
        <input 
          id="checkindate"
          name="checkindate"
          class="datepicker"
          type="text" 
          value="<?php echo $check_in_date ?>"
          placeholder="yyyy-mm-dd"
          pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
          title="Format: yyyy-mm-dd"
          autocomplete="off"
          required />
      </p>
        
      <!-- Check-out Date -->
      <p class="field-container">
        <label for="checkoutdate">Checkout date: </label>
        <input 
          id="checkoutdate"
          name="checkoutdate"
          class="datepicker"
          type="text" 
          value="<?php echo $check_out_date ?>"
          placeholder="yyyy-mm-dd"
          pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
          title="Format: yyyy-mm-dd"
          autocomplete="off"
          required />
      </p>

      <!-- Contact Number -->
      <p class="field-container">
        <label for="contact">Contact number:</label>
        <input 
          type="tel" 
          id="contact"
          name="contact"
          value="<?php echo $contact ?>"
          placeholder="(###) ### ####"
          pattern="[\(][(\+|0-9)][0-9]{2}[\)][\s][0-9]{3}[\s][0-9]{4}" 
          title="Format: (###) ### ####"
          maxlength="14"
          required />
      </p>

      <!-- Booking Extras -->
      <p class="field-container">
        <label for="bookingextras">Booking extras:</label>
        <textarea
          id="bookingextras"
          name="bookingextras"
          rows="5"
          cols="35"
          maxlength="250"><?php echo $booking_extras ?></textarea>
      </p>

      <!-- Room Review -->
      <p class="field-container">
        <label for="roomreview">Room review:</label>
        <textarea
          id="roomreview"
          name="roomreview"
          rows="5"
          cols="35"
          maxlength="250"><?php echo $room_review ?></textarea>
      </p>

      <!-- Actions -->
      <p class="field-container">
        <button type="submit">Update</button>
        <button type="reset" id="cancel-button">Cancel</button>
      </p>
      
    </form>
    <?php } ?>

      <p style="color: red;">
        <?php echo implode(", ", $error_messages); ?>
      </p>

      <p style="color: green;">
        <?php 
          if(isset($_GET['success'])){
            echo "Successfully Updated booking";
          }
        ?>
      </p>

  </div>
  
</div>

<script>
  $( function() {
    checkindate = $( "#checkindate" ).datepicker({
        changeMonth: true,
        dateFormat: "yy-mm-dd"
      })
      .on( "change", function() {
        checkoutdate.datepicker( "option", "minDate", getDate( this ) );
      })
      
    checkoutdate = $( "#checkoutdate" ).datepicker({
        changeMonth: true,
        dateFormat: "yy-mm-dd"
      })
      .on( "change", function() {
        checkindate.datepicker( "option", "maxDate", getDate( this ) );
      });
  });

  function getDate( element ) {
    var date;
    try {
      date = $.datepicker.parseDate( "yy-mm-dd", element.value );
    } catch( error ) {
      date = null;
    }
    return date;
  }

  $("#cancel-button").click(()=>{
      window.location.href = "./index.php";
    });

</script>

<?php include "./components/footer.php"; ?>

