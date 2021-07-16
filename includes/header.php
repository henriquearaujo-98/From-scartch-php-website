<?php
  include 'connection.php';
  session_start();
?>

<head>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/script.js"></script>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

</head>
<body>
<!-- <div id='backToTop' class='btn btn-primary'><span>&#8249;</span></div>
 <div data-collapse="medium" data-animation="default" data-duration="400" role="banner" class="navigation w-nav">
    <div class="navigation-items">
      <div class="navigation-wrap">
        <nav role="navigation" class="navigation-items w-nav-menu">
          <a href="index.php" class="navigation-item w-nav-link">Inicio</a>
          <a href="manifesto.php" aria-current="page" class="navigation-item w-nav-link w--current">Manifesto</a>
          <a href="blog.php" class="navigation-item w-nav-link">Blog</a>
          <a href="forum.php" class="navigation-item w-nav-link">Forum</a>
          
          <?php/* if(isset($_SESSION['ID'])){
              echo "<a href='profile.php?userid=".$_SESSION['ID']."' class='navigation-item w-nav-link'>".$_SESSION['name']."</a>";
              echo "<a href='index.php' class='navigation-item w-nav-link' id='logoutBTN'>Log out</a>";
              if($_SESSION['permission'] == 'admin' || $_SESSION['permission'] == 'mod')
                echo "<a href='dashboard.php' class='navigation-item w-nav-link'>Dashboard</a>";
          }else{
              echo "<a href='login.php' class='navigation-item w-nav-link'>Log in</a>";
              echo "<a href='signup.php' class='navigation-item w-nav-link'>Registo</a>";
          } */?>
        </nav>
        <div class="menu-button w-nav-button">
          <img src="images/menu-icon_1menu-icon.png" width="22" alt="" class="menu-icon">
        </div>
      </div>
    </div>
  </div>
  <div>
</div>-->
    <div class="back-to-personal">
        <a href="https://henriquearaujo.pt/">Go back to henriquearaujo.pt</a>
    </div>
  <header class="p-3 bg-dark text-white">
      <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
              <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                  <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"/></svg>
              </a>

              <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                  <li><a href="index.php" class="nav-link px-2 text-white">Home</a></li>
                  <li><a href="blog.php" class="nav-link px-2 text-white">Blog</a></li>
                  <li><a href="forum.php" class="nav-link px-2 text-white">Forum</a></li>
              </ul>

              <div class="text-end">
                  <?php
                  if(!isset($_SESSION['ID'])){
                  ?>
                      <a href="login.php"><button type="button" class="btn btn-outline-light me-2">Login</button></a>
                      <a href="signup.php"><button type="button" class="btn btn-warning">Sign-up</button></a>
                  <?php
                  }else{
                  ?>
                      <a href="<?='profile.php?userid='.$_SESSION['ID']?>"><button type="button" class="btn btn-warning"><?=$_SESSION['name']?></button></a>
                      <a href='index.php' class='navigation-item w-nav-link' id='logoutBTN'><button type="button" class="btn btn-danger">Log out</button></a>

                  <?php
                      if($_SESSION['role'] != 'user'){
                          ?>
                            <a href="dashboard.php"><button type="button" class="btn btn-primary">Dashboard</button></a>
                          <?php
                      }
                  }
                  ?>
              </div>
          </div>
      </div>
  </header>

<div id='errorMSG'>
  <span></span>
</div>