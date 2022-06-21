<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  protect_page();

  $first_name = "";
  $last_name = "";
  $email = "";
  $username = "";
  $password = "";

  $error_messages = array();
  $get_error = false;

  $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

  if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    echo "<br/><a href='./listcustomers.php'>[Back to customers list]</a>";
    exit;
  };


  /* GET */ 
  try {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {

      // Get the Customer ID
      if(isset($_GET['id'])) {
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)) {
          throw new Exception("Invalid Customer ID");
        } else {
          $id = clean_input($_GET['id']);
        } 
      } else {
        throw new Exception("Customer ID Not provided");
      }  

      $query = 'SELECT customerID, firstname, lastname, email, username, password FROM customer WHERE customerid='.$id;
      $result = mysqli_query($db_connection,$query);
      $rowcount = mysqli_num_rows($result);
      
      if($rowcount > 0){
        $row = mysqli_fetch_assoc($result);
    
        if(!empty($row['firstname'])){
          $first_name = $row['firstname'];
        }
    
        if(!empty($row['lastname'])){
          $last_name = $row['lastname'];
        }
    
        if(!empty($row['email'])){
          $email = $row['email'];
        }
    
        if(!empty($row['username'])){
          $username = $row['username'];
        }
        
        if(!empty($row['password'])){
          $password = $row['password'];
        }
      } else {
        $get_error = true;
        throw new Exception("Customer with id $id not found");
      }


    }
  } catch (Exception $ex) {
    array_push($error_messages, $ex->getMessage());
  }


  /* POST */ 
  try {
    if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Update')) {     

      $errors = 0;

      // Validate Customer ID
      if (isset($_POST['id']) && 
          !empty($_POST['id']) && 
          is_integer(intval($_POST['id']))) {
        $id = clean_input($_POST['id']); 
      } else {
        $errors++;
        array_push($error_messages, "Invalid customer id");
        $id = 0;
      }

      // Validate First Name
      if (isset($_POST['firstname']) && 
          !empty($_POST['firstname']) && 
          is_string($_POST['firstname']) &&
          preg_match("/[a-zA-Z\\-]{2,50}/", $_POST['firstname'])) {
        $first_name = clip_string_length(clean_input($_POST['firstname']), 50); 
      } else {
        $errors++;
        array_push($error_messages, "First name must be between 2 and 50 characters");
      }

      // Validate Last Name
      if (isset($_POST['lastname']) && 
          !empty($_POST['lastname']) && 
          is_string($_POST['lastname']) &&
          preg_match("/[a-zA-Z\\-]{2,50}/", $_POST['lastname'])) {
        $last_name = clip_string_length(clean_input($_POST['lastname']), 50); 
      } else {
        $errors++;
        array_push($error_messages, "Last name must be between 2 and 50 characters");
      }
      
      // Validate Email Address
      if (isset($_POST['email']) && 
          !empty($_POST['email']) && 
          is_string($_POST['email']) &&
          strlen($_POST['email']) <= 100 &&
          preg_match("/\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+/", $_POST['email'])) {
        $email = clip_string_length(clean_input($_POST['email']), 100); 
      } else {
        $errors++;
        array_push($error_messages, "Email must be in name@yourdomain.com format (max 100 characters)");
      }  

      // Validate Username
      if (isset($_POST['username']) && 
          !empty($_POST['username']) && 
          is_string($_POST['username']) &&
          strlen($_POST['username']) >= 5 &&
          strlen($_POST['username']) <= 25 &&
          preg_match("/\d*[a-zA-Z][a-zA-Z\d]*/", $_POST['username'])) {
        $username = clip_string_length(clean_input($_POST['username']), 25); 
      } else {
        $errors++;
        array_push($error_messages, "Username required and can only contain letters and numbers and must be between 5-25 characters");
      }

      // Validate Password
      if (isset($_POST['password']) && 
          !empty($_POST['password']) && 
          is_string($_POST['password']) &&
          strlen($_POST['password']) >= 8 &&
          strlen($_POST['password']) <= 40 &&
          preg_match("/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/", $_POST['password'])) {
        $password = clip_string_length(clean_input($_POST['password']), 25); 
      } else {
        $errors++;
        array_push($error_messages, "Password required between 8 - 40 characters and must include one letter, one number and one special character");
      }    
        
      if ($errors == 0 and $id > 0) {

        $sql_update = "UPDATE customer SET firstname=?,lastname=?,email=?,username=?,password=? WHERE customerID=?";
        $update_stmt = $db_connection->prepare($sql_update);

        $update_stmt->bind_param('sssssi', $first_name, $last_name, $email, $username, $password, $id);
        $update_stmt->execute();
        $update_stmt->close();
        $db_connection->close();
        
        // Update Session  Variables if customer is logged in user
        if(isset($_SESSION['userid']) && $_SESSION['userid'] == $id){
          $_SESSION['username'] = $username;
          $_SESSION['userid'] = $id;
          $_SESSION['user'] = $first_name . " " . $last_name;
        }

        header('location: ./editcustomer.php?id=' . $id . '&success=true', true. 303);
        exit;
      } else { 
        throw new Exception("Customer id not provided");
      }
    }
  } catch (Exception $ex) {
    array_push($error_messages, $ex->getMessage());
  }

  $db_connection->close();
?>


<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
  
    <h1>Customer Details Update</h1>
    <p>
      <a href='./listcustomers.php'>[Return to the Customer listing]</a>
      <a href='./index.php'>[Return to the main page]</a>
    </p>
  
    <?php if((isset($_GET['id']) && !empty($_GET['id']) && !$get_error) || isset($_POST['id'])) { ?>
    
      <form method="POST" action="./editcustomer.php">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        
      <!-- First Name -->
      <p class="field-container">
        <label for="firstname">First Name:</label>
        <input 
          required
          type="text" 
          id="firstname" 
          name="firstname"
          value="<?php echo $first_name ?>"
          minlength="2"
          maxlength="50"
          pattern="[a-zA-Z\\-]{2,50}"
          title="A-Z characters only, between 2-50 characters"
          />
      </p>

      <!-- Last Name -->
      <p class="field-container">
        <label for="lastname">Last Name:</label>
        <input 
          type="text" 
          id="lastname" 
          name="lastname"
          value="<?php echo $last_name ?>"
          minlength="2"
          maxlength="50"
          pattern="[a-zA-Z\\-]{2,50}"
          title="A-Z characters only, between 2 - 50 characters"
          required/>
      </p>
  
      <!-- Email -->
      <p class="field-container">
        <label for="email">Email:</label>
        <input 
          type="email" 
          id="email" 
          name="email"
          value="<?php echo $email ?>"
          minlength="3"
          maxlength="100"
          pattern="\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+"
          title="Email Format e.g. your.name@yourdomain.com (max 100 characters)"
          required/>
      </p>

      <!-- Username -->
      <p class="field-container">
        <label for="username">Username:</label>
        <input 
          type="text" 
          id="username" 
          name="username"
          value="<?php echo $username ?>"
          minlength="5"
          maxlength="25"
          pattern="\d*[a-zA-Z][a-zA-Z\d]*"
          title="Username can only contain letters and numbers and must be between 5-25 characters."
          required
          onfocus="this.removeAttribute('readonly')"
          readonly/>
      </p>

      <!-- Password -->
      <p class="field-container">
        <label for="username">Password:</label>
        <input 
          type="password" 
          id="password" 
          name="password"
          value="<?php echo $password ?>"
          minlength="8"
          maxlength="40"
          pattern="(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}"
          title="Password with minimum of 8 characters.  Must include one letter, one number and one special character"
          required
          onfocus="this.removeAttribute('readonly')"
          />
          <i id="viewPassword" class="fa-solid fa-eye"></i>
      </p>
        <button type="submit" name="submit" value="Update">Update</button>
      </form>

      <?php } ?>

      <p style="color: red;">
        <?php echo implode(", ", $error_messages); ?>
      </p>
      
      <p style="color: green;">
        <?php 
          if(isset($_GET['success'])){
            echo "Successfully Edited Customer";
          }
        ?>
      </p>

  </div>

</div>

<script>
  $('#viewPassword').on({ 'touchstart' : function(){
    $("#password").attr("type", "text");
    this.classList.remove("fa-eye");
    this.classList.add("fa-eye-slash");
  }});

  $("#viewPassword").mousedown(function(){
    $("#password").attr("type", "text");
    this.classList.remove("fa-eye");
    this.classList.add("fa-eye-slash");
  });

  $("#viewPassword").mouseup(function(){
    $("#password").attr("type", "password");
    this.classList.remove("fa-eye-slash");
    this.classList.add("fa-eye");
  });

  $("#viewPassword").mouseleave(function(){
    $("#password").attr("type", "password");
    this.classList.remove("fa-eye-slash");
    this.classList.add("fa-eye");
  });
</script>

  
<?php include "./components/footer.php"; ?>