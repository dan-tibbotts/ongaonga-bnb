<?php
  include "./components/header.php";
  include "./components/menu.php";
  include "./includes/config.php"; 
  include "./includes/utilities.php";

  $first_name = "";
  $last_name = "";
  $email = "";
  $username = "";
  $password = "";

  $error_messages = array();

  if($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = 0;

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

    // Hash the Password
    //TODO: Hash the password


    if($errors == 0) {
      // Connect to the database
      try {
        $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE);

        if (mysqli_connect_errno()) {
          throw new Exception("Error: Unable to connect to MySQL. ".mysqli_connect_error());
        };
  
      } catch (Exception $ex) {
        $db_connection->close();
        echo "Error: " . $ex->getMessage();
        exit;
      }

      
      try {
        
        // Check Email or Username not already in use
        $check_sql = "SELECT customerID FROM customer 
                      WHERE username=? OR email=?";
        $check_stmt = $db_connection->prepare($check_sql);
        $check_stmt->bind_param("ss", $username, $password);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $check_rowcount = $check_result->num_rows;

        if($check_rowcount > 0) {
          throw new Exception("Username or email address already registerd.");
        } else {
          // Insert Customer
          $sql = "INSERT INTO customer (firstname, lastname, email, username, password) VALUES (?,?,?,?,?)";
          $stmt = $db_connection->prepare($sql);
          $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $password);
          $stmt->execute();
          $stmt->close();
          $db_connection->close();

          header("location: ./createcustomer.php?success=true");
          exit;
        }
        
      } catch (Exception $ex) {
        $db_connection->close();
        array_push($error_messages, $ex->getMessage());
      }
    }
  }
?>


<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
    <h1>Register</h1>

    <p>
      <a href="./listrooms.php">[View rooms listings]</a>
      <a href="./index.php">[Return to the main page]</a>
    </p>
    

    <form method="POST" action="./createcustomer.php">
      <!-- First Name -->
      <p class="field-container">
        <label for="firstname">First Name:</label>
        <input 
          type="text" 
          id="firstname" 
          name="firstname"
          value="<?php echo $first_name ?>"
          minlength="2"
          maxlength="50"
          pattern="[a-zA-Z\\-]{2,50}"
          title="A-Z characters only, between 2-50 characters"
          required />
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
          required />
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
          required />
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
          required />
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
          required />
          <i id="viewPassword" class="fa-solid fa-eye"></i>
      </p>

      <p style="color: red;">
        <?php echo implode(", ", $error_messages); ?>
      </p>

      <p style="color: green;">
        <?php 
          if(isset($_GET['success'])){
            echo "Customer Added Successfully!";
          }
        ?>
      </p>

      <p class="field-container">
        <button type="submit">Register</button>
        <button type="reset">Reset</button>
      </p>

    </form>

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