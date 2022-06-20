<?php
  include "./components/header.php";
  include "./components/menu.php";
  include './includes/config.php';
  include './includes/utilities.php';

  protect_page();

  // Connect to database
  try {
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);
    if(mysqli_connect_errno()){
      throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
    }
  } catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    echo "<br/><a href='./listbookings.php'>[Back to bookings list]</a>";
    $db_connection->close();
    exit;
  }

  // Set initial values for inputs
  $customer_id = get_user_id();
  $room_selection = 0;
  $check_in_date = "";
  $check_out_date = "";
  $contact = "";
  $booking_extras = "";

  // Form Submission
  $error_messages = array();

  if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == "add")){
    
    $errors = 0;

    // Validate room selection
    if(isset($_POST['room']) && is_int(intval($_POST['room']))) {
      $room_selection = intval($_POST['room']);
    } else {
      $errors++;
      array_push($error_messages, "No room selected");
    }

    // Validate check-in date
    if(isset($_POST['checkindate']) && !empty($_POST['checkindate']) && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $_POST['checkindate'])){
      $check_in_date = clean_input($_POST['checkindate']);
      $date1 = $check_in_date;
    } else {
      $errors++;
      array_push($error_messages, "Check-in date is not valid");
    }

    // Validate check-out date
    if(isset($_POST['checkoutdate']) && !empty($_POST['checkoutdate']) && preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $_POST['checkoutdate'])){
      $check_out_date = clean_input($_POST['checkoutdate']);
      $date2 = $check_out_date;
    } else {
      $errors++;
      array_push($error_messages, "Check-out date is not valid");
    }

    // Validate check-in date is not greater than check-out date 
    if(isset($date1) && isset($date2)){
      if($date1 > $date2){
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

    // Submit form if no errors
    if($errors == 0)
    {
      try {
        //code...
        $insert_query = " INSERT INTO booking (customerID, roomID, checkindate, checkoutdate, contactnumber, bookingextras) 
                          VALUES (?,?,?,?,?,?)";

        $insert_statement = mysqli_prepare($db_connection, $insert_query);

        mysqli_stmt_bind_param($insert_statement, "iissss", $customer_id, $room_selection, $check_in_date, $check_out_date, $contact, $booking_extras);
        mysqli_stmt_execute($insert_statement);
        mysqli_stmt_close($insert_statement);

        header("location: ./createbooking.php?success=true");
        exit;

      } catch (Exception $ex) {
        echo "Exception: " . $ex->getMessage();
        echo "<br/><a href='./listbookings.php'>[Back to bookings list]</a>";
        exit;
      }
    }
  };
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Make a booking</h1>

    <p>
      <a href="./listbookings.php">[Return to the Bookings listing]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>
    

    <form method="POST" action="./createbooking.php">
      <p>
        Booking for '<span class="bold"><?php echo get_user(); ?></span>'
      </p>

      <!-- Room --> 
      <p class="field-container">
        <label for="room">Room:</label>
        <select 
          id="room" 
          name="room" 
          required >

          <?php  
            if($room_selection == 0){
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
                echo intval($room_selection) == intval($room_row['roomID']) ? " selected>" : ">";
                echo $room_row['roomname'] .  ", " .  $room_row['roomtype'] . ", ". $room_row['beds'];
                echo "</option>";
              }

            }

            mysqli_free_result($room_result);
          ?>
        </select>
        <span>(name, type, beds)</span>
      </p>

      <!-- Check-in Date -->
      <p class="field-container">
        <label for="checkindate">Checkin date: </label>
        <input 
          value="<?php echo $check_in_date; ?>" 
          id="checkindate"
          name="checkindate"
          class="datepicker startdate"
          type="text" 
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
            value="<?php echo $check_out_date; ?>" 
            id="checkoutdate"
            name="checkoutdate"
            class="datepicker enddate"
            type="text" 
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
          value="<?php echo $contact; ?>"
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
          maxlength="250"><?php echo $booking_extras; ?></textarea>
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

      <!-- Actions -->
      <p class="field-container">
        <button type="submit" name="submit" value="add">Add</button>
        <button id="cancelButton">Cancel</button>
      </p>
    </form>

    <hr />

    <!-- Search Room Availability -->
    <form id="availabilityform" method="POST" onsubmit="return false">
      <h2>Search for room availability</h2>
      <!-- Date filter -->
      <p class="field-container">
          
        <!-- Start Date -->
        <label for="startdate" class="width-fit">Start date: </label>
        <input 
          id="startdate"
          name="startdate"
          class="datepicker startdate"
          type="text" 
          placeholder="dd/mm/yyyy"
          pattern="[0-9]{2}[/][0-9]{2}[/][0-9]{4}"
          title="Format: dd/mm/yyyy"
          size="15"
          autocomplete="off"
          required />

        <!-- End Date -->
        <label for="enddate" class="width-fit">End date: </label>
        <input 
          id="enddate"
          name="enddate"
          class="datepicker enddate"
          type="text" 
          placeholder="dd/mm/yyyy"
          pattern="[0-9]{2}[/][0-9]{2}[/][0-9]{4}"
          title="Format: dd/mm/yyyy"
          size="15"
          autocomplete="off"
          required />

        <button id="searchbutton" type="submit">Search</button>
      </p>
    </form>


    <table id="results-table">
      <thead>
        <tr>
          <th>Room #</th>
          <th>Room Name</th>
          <th>Room Type</th>
          <th>Beds</th>
        </tr>
      </thead>
    </table>

    <p id="results"></p>


  </div>

</div>


<script>
  $(document).ready(function(){

    $( function() {

      // Check-in / Check-out Date Picker
      checkindate = $("#checkindate").datepicker({
        changeMonth: true,
        dateFormat: "yy-mm-dd"
      })
      .on("change", function() {
        checkoutdate.datepicker("option", "minDate", getDate(this, "yy-mm-dd"));
      });

      checkoutdate = $("#checkoutdate").datepicker({
        changeMonth: true,
        dateFormat: "yy-mm-dd"
      })
      .on( "change", function() {
        checkindate.datepicker("option", "maxDate", getDate(this, "yy-mm-dd"));
      });;

      // Room Availability Date Picker 
      startdate = $( "#startdate" ).datepicker({
        changeMonth: true,
        dateFormat: "dd/mm/yy"
      })
      .on( "change", function() {
        enddate.datepicker( "option", "minDate", getDate(this, "dd/mm/yy"));
      })
      
      enddate = $( "#enddate" ).datepicker({
        changeMonth: true,
        dateFormat: "dd/mm/yy"
      })
      .on( "change", function() {
        startdate.datepicker( "option", "maxDate", getDate(this, "dd/mm/yy" ));
      });

    });

    function getDate(element, format) {
      try {
        return $.datepicker.parseDate(format, element.value);
      } catch(error) {
        return null;
      }
    }

    $("#cancelButton").click(()=>{
      window.location.href = "./index.php";
    });

    // Search Filter 
    $("#searchbutton").click(()=>{
      const startDate = $("#startdate").val();
      const endDate = $("#enddate").val();
      
      const isValid = (Boolean)(
        endDate.match(/^[0-9]{2}[/][0-9]{2}[/][0-9]{4}$/) &&
        startDate.match(/^[0-9]{2}[/][0-9]{2}[/][0-9]{4}$/)
        )
    
      if(isValid){
        $.ajax({
          method: "POST", 
          url: "./includes/filterbookings.php",
          data: {startdate: startDate, enddate: endDate},
          dataType: "json",
          encode: true,
          statusCode: {
            200: function(res, status, jqXHR){
              console.log(res);
            },
            400: function(res, status, jqXHR) {
              alert(res.responseJSON['error']);
            },
            404: function(res, status, jqXHR){
              const tbl = document.getElementById("results-table");
              tbl.innerHTML = res.responseJSON['error'];
            },
            500: function(res, status, jqXHR){
              alert(res.responseJSON['error']);
            }
          }
        }).done(function(res) {

          const tbl = document.getElementById("results-table");
          let rowCount = tbl.rows.length;
          
          tbl.innerHTML = "<thead><tr><th>Room #</th><th>Room Name</th><th>Room Type</th><th>Beds</th></tr></thead>";
          
          for(let i = 0; i < res.length; i++){
            
            let roomID = res[i]['roomID'];
            let roomName = res[i]['roomname'];
            let roomType = res[i]['roomtype'];
            let roomBeds = res[i]['beds'];
            
            let tr = document.createElement("tr");
            tr = tbl.insertRow(-1);

            var roomIDCell = tr.insertCell(0);
            roomIDCell.innerHTML = roomID;

            var roomNameCell = tr.insertCell(1);
            roomNameCell.innerHTML = roomName;
            
            var roomTypeCell = tr.insertCell(2);
            roomTypeCell.innerHTML = roomType;

            var roomBedsCell = tr.insertCell(3);
            roomBedsCell.innerHTML = roomBeds;
          }
        });
      }
    })
    
  });
</script>

  <?php 
    mysqli_close($db_connection);
  ?>

<?php include "./components/footer.php"; ?>