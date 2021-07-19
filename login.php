<?php
include 'includes/header.php';

if(isset($_SESSION['mock_email'])){
	header('Location: index.php');
}else{
?>

<div class="login-container">
    <div class="alert alert-warning" role="alert">
        You can login with alicebrown@example.com and password 123.
    </div>
    <form onsubmit="return false;" id="loginform">
        <div style="width: 33%; margin: auto">
            <h1>Log in</h1>
            <hr>
        </div>

            <lable class='login_lable'>Email</lable><br>
            <i class="fa fa-user fa-lg"></i><input type="text" name="email" id="loginEmail" class='login_input'><br>
            <lable class='login_lable'>Password</lable><br>
            <i class="fa fa-key fa-lg"></i><input type="password" name="password"  id="loginPassword" class='login_input'><br>
            <br>
            <button type="button" id="loginBTN" class="btn btn-warning" style="color: white">Log in</button>
    </form>
</div>


<?php
include 'includes/footer.php' ;
}
?>

