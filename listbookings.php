<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";
  
  protect_page();

  $db_connection = mysqli_connect(DB_HOSTNAME,DB_USER, DB_PASSWORD, DB_DATABASE);

  // Check connection error code
  if(mysqli_connect_errno()){
    echo "Failed to connect to Database: " . mysqli_connect_error();
    exit();
  }

  // Prepare query
  $query = "SELECT    bookingID, customerID, roomID, checkindate, checkoutdate,
                      contactnumber, bookingextras, roomreview
            FROM      booking
            ORDER BY  checkindate";
  
  $result = mysqli_query($db_connection, $query);    // Perform query on db connection
  $row_count = mysqli_num_rows($result);            // Get number of rows in result set 

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Current Bookings</h1>

    <p>
      <a href="./createbooking.php">[Create a booking]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>

    <table id="bookings-table">
      <thead>
        <tr>
          <th>Booking (room, dates)</th>
          <th>Customer</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 

          if($row_count > 0){

            // Records found
            while($row = mysqli_fetch_assoc($result)){

              $booking_id = $row['bookingID'];

              echo '<tr>';

              /*
                Booking Details (room, dates) 
              */
              $room_query = " SELECT roomID, roomname
                              FROM room
                              WHERE roomID=" . $row['roomID'];

              $room_result = mysqli_query($db_connection, $room_query);
              $room_row_count = mysqli_num_rows($room_result);

              if($room_row_count > 0){
                $room_row = mysqli_fetch_assoc($room_result);

                echo    '<td>';
                echo      $room_row['roomname'] . ": " . $row['checkindate'] . " to " . $row['checkoutdate'];
                echo    '</td>';

              } else {
                echo    '<td>';
                echo      "No Room info for room#" . $row['roomID'] . ": " . $row['checkindate'] . ", " . $row['checkoutdate'];
                echo    '</td>';
              }

              mysqli_free_result($room_result);

              /*
                Customer Details
              */
              $customer_query = " SELECT customerID, firstname, lastname 
                                  FROM customer
                                  WHERE customerID=" . $row["customerID"];
              
              $customer_result = mysqli_query($db_connection, $customer_query);
              $customer_row_count = mysqli_num_rows($customer_result);

              if($customer_row_count > 0){
                $customer_row = mysqli_fetch_assoc($customer_result);

                echo    '<td>';
                echo      $customer_row['firstname'] . " " . $customer_row['lastname'];
                echo    '</td>';

              } else {
                echo    '<td>Error: Customer ' . $row['customerID'] .' not found!</td>';
              }

              mysqli_free_result($customer_result);


              /* Actions */
              echo "
                      <td>
                        <a href='./viewbooking.php?id=$booking_id'>[view]</a>
                        <a href='./editbooking.php?id=$booking_id'>[edit]</a>
                      ";

              // If no review exists, show 'Add Review'
              // if review exists, show 'Edit Review'
              if(empty($row['roomreview'])){
                echo  "<a href='./createroomreview.php?id=$booking_id' class='bold'>[add review]</a> ";
              } else {
                echo  "<a href='./editroomreview.php?id=$booking_id'>[edit review]</a> ";
              }        

              echo    "<a href='./deletebooking.php?id=$booking_id'>[delete]</a>
                    </td></tr>" . PHP_EOL;
            }

          } else { 
            // No records found
            echo  '<tr>';
            echo    '<td colspan="3">';
            echo      "<span>No bookings to display</span>";
            echo    '</td>';
            echo  '</tr>';
          }

        
        ?>
      </tbody>
    </table>

  </div>

</div>

<?php 
  mysqli_free_result($result);
  mysqli_close($db_connection); 
  include "./components/footer.php"; 
?>