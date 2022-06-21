<?php
  include "./components/header.php";
  include "./components/menu.php";
?>
  
<?php 

  include "./includes/config.php"; 
  include "./includes/utilities.php";

  $username = null;
  $password = null;

  $error_messages = array();

  // If user is logged in, redirect to index page
  if(isset($_SESSION['username'])){
    header("location: ./index.php");
  }

  // If no active session, log in user
  if($_SERVER["REQUEST_METHOD"] == "POST"){

    $errors = 0;

    // Validate Username
    if (isset($_POST['username']) && 
        !empty($_POST['username']) && 
        is_string($_POST['username']) &&
        strlen($_POST['username']) >= 5 &&
        strlen($_POST['username']) <= 25 &&
        preg_match("/\d*[a-zA-Z][a-zA-Z\d]*/", $_POST['username'])
        ) {
      $username = clip_string_length(clean_input($_POST['username']), 25); 
    } else {
      $errors++;
    }   

    // Validate Password
    if (isset($_POST['password']) && 
        !empty($_POST['password']) && 
        is_string($_POST['password']) &&
        strlen($_POST['password']) >= 8 &&
        strlen($_POST['password']) <= 40 &&
        preg_match("/(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}/", $_POST['password'])
        ) {
      $password = clip_string_length(clean_input($_POST['password']), 25); 
    } else {
      $errors++;
    }  

    if($errors == 0) {
      
      // Connect to database
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

      // Check Username and password against database
      try {
        
        $login_sql = " SELECT customerID, firstname, lastname, username, password
                        FROM customer
                        WHERE username=? AND BINARY password=?";

        $login_stmt = $db_connection->prepare($login_sql);
        $login_stmt->bind_param("ss", $username, $password);
        $login_stmt->execute();
        $login_result = $login_stmt->get_result();
        $login_rowcount = mysqli_num_rows($login_result);
        $login_stmt->close();

        if($login_rowcount > 0){
          $row = $login_result->fetch_assoc();
          $_SESSION['username'] = $row['username'];
          $_SESSION['userid'] = $row['customerID'];
          $_SESSION['user'] = $row['firstname'] . " " . $row['lastname'];


          $db_connection->close();

          if(isset($_GET['uri']) && !empty($_GET['uri'])){
            $uri = $_GET['uri'];
            header("location: $uri");
          }

          header("location: ./index.php");
          // exit;

        } else {
          throw new Exception("Invalid username or password");
        }

      } catch (Exception $ex) {
        $db_connection->close();
        array_push($error_messages, $ex->getMessage());
      }

    } else {
      array_push($error_messages, "Invalid username or password");
    }

  }

?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">
  <h1>Customer Login</h1>
  <p>
    <a href='./register.php'>[Register]</a>
    <a href='./index.php'>[Return to the main page]</a>
  </p>

  <form method="POST" action="./login.php">
    <!-- Username -->
    <p class="field-container">
      <label for="username">Username:</label>
      <input 
        type="text" 
        id="username" 
        name="username"
        value="<?php echo $username ?>"
        minlength="5"
        maxlength="40"
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
    </p>

    <p style="color: red;">
      <?php echo implode(", ", $error_messages); ?>
    </p>

    <button type="submit">Login</button>
  </form>

  </div>

</div>

<?php include "./components/footer.php"; ?>