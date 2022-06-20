<?php 
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  protect_page();

  $first_name = "none";
  $last_name = "none";
  $email = "none";
  $username = "none";
  $password = "none";

  $error_messages = array();

  $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

  if (mysqli_connect_errno()) {
      echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
      echo "<br/><a href='./listcustomers.php'>[Back to customers list]</a>";
      exit; 
  }

  //retrieve the customerid from the URL
  if ($_SERVER["REQUEST_METHOD"] == "GET") {

    try {
      if(isset($_GET['id'])){
        $id = clean_input($_GET['id']);
        if(empty($id) || !is_numeric($id)){
          throw new Exception("Invalid customer id 1");
        } else {

          $get_query = 'SELECT * FROM customer WHERE customerid=?';
          $get_stmt = $db_connection->prepare($get_query);

          $get_stmt->bind_param('i', $id);
          $get_stmt->execute();
          $get_result = $get_stmt->get_result();
          $get_rowcount = $get_result->num_rows;
          $get_stmt->close();

          if($get_rowcount > 0){
            $row = $get_result->fetch_assoc();

            $first_name = $row['firstname'];
            $last_name = $row['lastname'];
            $email = $row['email'];
            $username = $row['username'];
            $password = $row['password'];
          } else {
            throw new Exception("Customer with id $id not found");
          }
        }
      } else {
        throw new Exception("Customer id not provided");
      }
    } catch (Exception $ex) {
      array_push($error_messages, $ex->getMessage());
    }
  }

  if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Delete')) {     
    $errors = 0; 

    if (isset($_POST['id']) && !empty($_POST['id']) && is_integer(intval($_POST['id']))) {
      $id = clean_input($_POST['id']); 
    } else {
      $errors++;
      array_push($error_messages, "Invalid customer ID");
      $id = 0;  
    }        
    
    if ($errors == 0 and $id > 0) {

      $delete_sql = "DELETE FROM customer WHERE customerID=?";

      $delete_stmt = $db_connection->prepare($delete_sql);
      $delete_stmt->bind_param("i", $id);
      $delete_stmt->execute();
      $delete_stmt->close();
      

      // Check if logged in user is deleting their own account
      if(isset($_SESSION['userid']) && $_SESSION['userid'] == $id){
        header('location: ./includes/logout.php');
      } else {
        header('location: ./deletesuccess.php?customer&id=' . $id);
      }
      $db_connection->close();
      exit;
        
    } 
  }

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    <h1>Customer details preview before deletion</h1>
    <p>
      <a href='./listcustomers.php'>[Return to the Customer listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>

    <?php if(isset($get_rowcount) && $get_rowcount > 0 && !isset($_GET['success'])) {?>

      <?php if(isset($_SESSION['userid']) && $_SESSION['userid'] == $id) {?>
        <div class="caution-box">
          <span>Caution: </span> This is your account! Deleting this account will log you out.
        </div>
      <?php }?>

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

      <form method="POST" action="./deletecustomer.php">
        <p class="bold">Are you sure you want to delete this customer?</p>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="submit" value="Delete">Delete</button>
        <a href="./listcustomers.php">[Cancel]</a>
      </form>
    <?php }?>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

  </div>
  </div>
</div>

  <?php    
    mysqli_free_result($result); //free any memory used by the query
    mysqli_close($db_connection); //close the connection once done
  ?>

<?php include "./components/footer.php"; ?>