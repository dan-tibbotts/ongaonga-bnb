<?php
  include "./components/header.php";
  include "./components/menu.php";
?>

<div id="site_content">
  <?php include "./components/sidebar.php"; ?>

  <div id="content">

    <?php 
      include "./includes/utilities.php";

      $id = null;
      // Get the id
      if(isset($_GET['id'])){
        $id = clean_input($_GET['id']);
      }

    ?>

    <div class="success-box">
      <?php 
        if(isset($_GET['customer'])) echo "Customer";
        if(isset($_GET['room'])) echo "Room";
        if(isset($_GET['booking'])) echo "Booking";
      ?>
      <?php echo $id?>
      Successfully deleted 
    </div>
    <a href="./">[Return Home]</a>

  </div>

</div>


<?php include "./components/footer.php"; ?>


