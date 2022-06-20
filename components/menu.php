<div id="header">

  <div id="logo">
    <div id="logo_text">
      <!-- class="logo_colour", allows you to change the colour of the text -->
      <h1><a href="./"><span class="logo_colour">Ongaonga Bed & Breakfast</span></a></h1>
      <h2>Make yourself at home is our slogan. We offer some of the best beds on the east coast. Sleep well and rest well.</h2>
    </div>
  </div>

  <div id="menubar">
    <ul id="menu">
      <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
      <li class="selected"><a href="./">Home</a></li>
      <li><a href="./listrooms.php">Rooms</a></li>

      <?php if(is_logged_in()){?>
        <li><a href="./listbookings.php">Bookings</a></li>
        <li><a href="./listcustomers.php">Customers</a></li>
        <li><a href="./includes/logout.php">Logout</a></li>

        <li class="user">
          <a href="./viewcustomer.php?id=<?php echo get_user_id();?>">
            <i class="fa-solid fa-circle-user"> 
              <span style=" font-family: arial, sans-serif;
                            font-size: 13px; 
                            text-transform: capitalize;
                            font-weight: 400;">
                <?php echo get_user(); ?>
              </span>
            </i>
          </a>
          
        </li>
        
      <?php } else {?>
        <li><a href="./register.php">Register</a></li>
        <li><a href="./login.php">Login</a></li>
      <?php } ?>
    </ul>
  </div>

</div>