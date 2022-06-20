<?php 
  require_once("./includes/session.php"); 
?>

<!DOCTYPE html>
<html>
  <head>
    <title>W3.CSS Template</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
      .question::after {
        content: " \003F";
      }

      .check::after {
        content: " \2713";
      }
    </style>

  </head>

<body>

  <?php include "./components/header.php"; ?>

  <h1>BIT608 Web Programming </h1>
  <h2>Assessment case study web applicaiton temporary launch page</h2>

  <ul>
    <li>A check mark (&checkmark;) indicates the corresponding page has been connected to the database.</li>
    <li>A question mark (&quest;) indicates the page may not require any additional adjustments on what was provided.</li>
  </ul>

  <dl>
    <dt><strong>Auth</strong></dt>
    <dd>
      <a href="./login.php">Login</a>
      - NOTE: will be redirected to <code>index.php</code> if already logged in
    </dd>
  </dl>

  <dl>
    <dt><strong>Customer</strong></dt>
    <dd class="question"><a href="./listcustomers.php">Customer listing</a></dd>
    <dd class="check"><a href="./createcustomer.php">Create Customer</a></dd>
    <dd class="question"><a href="./viewcustomer.php?id=1">View Customer</a></dd>
    <dd class="question">
      <a href="./editcustomer.php">Edit Customer</a>
      There is some more work to do on this page. There is a username field that needs connecting. 
    </dd>
    <dd class="question"><a href="./deletecustomer.php">Delete Customer</a></dd>
  </dl>

  <dl>
    <dt><strong>Rooms</strong></dt>
    <dd class="check"><a href="./listrooms.php">Rooms Listing</a></dd>
    <dd class="check"><a href="./createroom.php">Create Room</a></dd>
    <dd class="check"><a href="./viewroom.php?id=1">View Room</a></dd>
    <dd class="check"><a href="./editroom.php?id=1">Edit Room</a></dd>
    <dd class="check"><a href="./deleteroom.php?id=1">Delete Room</a></dd>
  </dl>
  
  <dl>
    <dt><strong>Bookings</strong></dt>
    <dd class="check"><a href="./listbookings.php">Bookings listing</a> (todo: Manage Reviews, should this link to 'create room review'?')</dd>
    <dd class="check"><a href="./createbooking.php">Create Booking</a></dd>
    <dd class="check"><a href="./viewbooking.php?id=1">View Booking</a></dd>
    <dd class="check"><a href="./editbooking.php?id=1">Edit Booking</a></dd>
    <dd class="check"><a href="./deletebooking.php?id=1">Delete Booking</a></dd>
    <dd>&nbsp;</dd>
    <dd class="check"><a href="./createroomreview.php?id=1">Create Room Review</a></dd>
    <dd class="check"><a href="./editroomreview.php?id=1">Edit Room Review</a></dd>
  </dl>

</body>
</html>
