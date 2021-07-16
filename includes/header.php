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
                  if(!isset($_SESSION['mock_ID'])){
                  ?>
                      <a href="login.php"><button type="button" class="btn btn-outline-light me-2">Login</button></a>
                      <a href="signup.php"><button type="button" class="btn btn-warning">Sign-up</button></a>
                  <?php
                  }else{
                  ?>
                      <a href="<?='profile.php?userid='.$_SESSION['mock_ID']?>"><button type="button" class="btn btn-warning"><?=$_SESSION['mock_name']?></button></a>
                      <a href='index.php' class='navigation-item w-nav-link' id='logoutBTN'><button type="button" class="btn btn-danger">Log out</button></a>

                  <?php
                      if($_SESSION['mock_role'] != 'user'){
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