<div class="sidebar">

  <!-- Show login screen  -->
  <?php if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {?>
    <div class="login-sidebar">
      <dl>
        <dt>Logged in as</dt><dd><?php echo $_SESSION['user']?></dd>
      </dl>
      <br/>
      <form method="POST" action="./includes/logout.php">
        <button type="submit">Logout</button>
      </form>
    </div>
      
  <?php } ?>

  <h3>Latest News</h3>
  <p>New Web applicaiton Launched</p>
  

  <h3>Useful Links</h3>
  <ul>
    <li><a href="https://en.wikipedia.org/wiki/Ongaonga,_New_Zealand" target="_blank">About Ongaonga</a></li>
    <li><a href="https://nzhistory.govt.nz/keyword/onga-onga" target="_blank">History of Ongaonga</a></li>
    <li><a href="./privacy.php">Privacy statement</a></li>
  </ul>
  <h3>Search</h3>
  <form method="post" action="#" id="search_form">
    <p>
      <input class="search" type="text" name="search_field" value="" placeholder="Enter keywords....."/>
      <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="style/search.png" alt="Search" title="Search" />
    </p>
  </form>
</div>