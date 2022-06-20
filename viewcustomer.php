<?php
  include "./components/header.php";
  include "./components/menu.php";
?>

<?php
  include "./includes/config.php";
  include "./includes/utilities.php";

  protect_page();

  $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

  $first_name = "none";
  $last_name = "none";
  $email = "none";
  $username = "none";
  $password = "none";

  $error_messages = array();

  if (mysqli_connect_errno()) {
      echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
      echo "<br/><a href='./listcustomers.php'>[Back to customers list]</a>";
      exit;
  }

  // Get the customer id
  try {
    if(isset($_GET['id'])){
      $id = clean_input($_GET['id']);
      if(empty($id) || !is_numeric($id)){
        throw new Exception("Invalid customer id");
      }
    } else {
      throw new Exception("Customer id not provided");
    }
  } catch (Exception $ex) {
    array_push($error_messages, $ex->getMessage());
  }

  if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && !empty($_GET['id'])) {

    $query = 'SELECT * FROM customer WHERE customerid='.$id;
    $result = mysqli_query($db_connection,$query);
    $rowcount = mysqli_num_rows($result); 

    if($rowcount > 0){
      $row = mysqli_fetch_assoc($result);

      if(!empty($row['firstname'])){
        $first_name = $row['firstname'];
      } else {
        $first_name = "First name not provided";
      }

      if(!empty($row['lastname'])){
        $last_name = $row['lastname'];
      } else {
        $last_name = "Last name not provided";
      }

      if(!empty($row['email'])){
        $email = $row['email'];
      } else {
        $email = "Email not provided";
      }

      if(!empty($row['username'])){
        $username = $row['username'];
      } else {
        $username = "Username not provided";
      }
      
      if(!empty($row['password'])){
        $password = $row['password'];
      } else {
        $password = "Password not provided";
      }
    } else {
      array_push($error_messages, "No customer found with id $id");
    }

    mysqli_free_result($result);
    mysqli_close($db_connection);
  }
  
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    
    <h1>Customer Details View</h1>
    <p>
      <a href='./listcustomers.php'>[Return to the Customer listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>

    <?php if(isset($rowcount) && $rowcount > 0) {?>
      <fieldset>
        <legend>Customer Detail #<?php echo $id?></legend>
        <dl>
          <dt>First Name</dt> <dd><?php echo $first_name; ?></dd>
          <dt>Last Name</dt> <dd><?php echo $last_name; ?></dd>
          <dt>Email</dt> <dd><?php echo $email; ?></dd>
          <dt>Username</dt> <dd><?php echo $username; ?></dd>
          <dt>Password</dt> <dd><?php echo $password; ?></dd>
        </dl>
      </fieldset>
    <?php } ?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

  </div>

</div>



<?php include "./components/footer.php"; ?>