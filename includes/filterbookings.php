<?php

  if(isset($_POST["startdate"], $_POST["enddate"])){

    $startdate = $_POST["startdate"];
    $enddate = $_POST["enddate"];

    // Convert date format to yyyy-mm-dd
    $sdate = date("Y-m-d", strtotime(str_replace("/", "-", $startdate)));
    $edate = date("Y-m-d", strtotime(str_replace("/", "-", $enddate)));


    // Validate Start Date
    if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $sdate)){
      http_response_code(400);
      echo json_encode(["error" => "Start date not in the correct format (YYYY-MM-DD)"]);
      exit;
    }

    // Validate End Date
    if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $edate))
    {
      http_response_code(400);
      echo json_encode(["error" => "End date not in the correct format (YYYY-MM-DD)"]);
      exit;
    }

    // Confirm start date is not greater than end date
    if($startdate > $enddate){
      http_response_code(400);
      echo json_encode(["error" => "Start date is greater than the end date"]);
      exit;
    }

    try {
      include './config.php';
      $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

      // Check connection error code
      if(mysqli_connect_errno()){
        echo "Failed to connect to Database: " . mysqli_connect_error();
        exit;
      }

      $availability_query = " SELECT roomID, roomname, roomtype, beds 
                              FROM room
                              WHERE roomID NOT IN (SELECT roomID FROM booking WHERE checkindate >= '$sdate' AND checkoutdate <= '$edate')";

      $availability_result = mysqli_query($db_connection, $availability_query);
      $availability_row_count = mysqli_num_rows($availability_result);

      if($availability_row_count > 0){
        $available_rooms = [];
        while($row = mysqli_fetch_assoc($availability_result)){
        array_push($available_rooms, $row);
      }
      
      header('Content-Type: text/json; charset=utf-8');
      echo json_encode($available_rooms);

      } else {
        mysqli_free_result($availability_result);
        mysqli_close($db_connection);
        http_response_code(404);
        echo json_encode(["error" => "No rooms available for the provides dates"]);
        exit;
      }

      mysqli_free_result($availability_result);
      mysqli_close($db_connection);

    } catch (Exception $ex) {
      mysqli_free_result($availability_result);
      mysqli_close($db_connection);
      http_response_code(500);
      echo json_encode(["error" => $ex->getMessage()]);
      exit;
    }

  }
?>