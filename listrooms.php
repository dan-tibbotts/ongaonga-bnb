<?php
  include "./components/header.php";
  include "./components/menu.php";
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    

    <?php
      try {
        include "./includes/config.php"; 
        
        $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);
        
        if (mysqli_connect_errno()) {
            throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error()) ;
        }
        
        $query = 'SELECT roomID, roomname, roomtype FROM room ORDER BY roomtype';
        $result = mysqli_query($db_connection, $query);
        $rowcount = mysqli_num_rows($result); 
          
      } catch (Exception $ex) {
          echo "Error: " . $ex->getMessage();
          exit;
      }
    ?>


  <h1>Room list</h1>
  <p>

    <?php if(is_logged_in()){?>
      <a href='./createroom.php'>[Add a room]</a>
    <?php } ?>

    <a href="./index.php">[Return to main page]</a>
  </p>

    <table>
      <thead>
        <tr>
          <th>Room Name</th>
          <th>Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <?php
        if ($rowcount > 0) {  
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['roomID'];	
                echo '<tr><td>'.$row['roomname'].'</td><td>'.$row['roomtype'].'</td>';
                echo     '<td><a href="./viewroom.php?id='.$id.'">[view]</a>';

                if(isset($_SESSION['username']) && !(empty($_SESSION))){
                  echo '<a href="./editroom.php?id='.$id.'">[edit]</a>';
                  echo '<a href="./deleteroom.php?id='.$id.'">[delete]</a>';
                }

                echo '</td></tr>'.PHP_EOL;
            }
        } else {
          echo "<h2>No rooms found!</h2>"; 
        }

        mysqli_free_result($result);
        mysqli_close($db_connection);
      ?>
    </table>

  </div>

</div>

<?php include "./components/footer.php"; ?>