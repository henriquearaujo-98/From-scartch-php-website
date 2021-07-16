<?php
include 'includes/header.php';

if(isset($_SESSION['email'])){
	header('Location: index.php');
}else{

?>
<div class = "container">
	<h1 style="text-align: center; margin-top: 30px">Sign up</h1>
    <hr>
    <br><br>
    <div class="row">
        <div class="col-md-6">
            <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="regform">
                <span>*Name:</span><br>
                <input type="text" name="fullname" id="regName"><br>
                <span>*Email:</span><br>
                <input type="text"  name="email" id="regEmail"><br>
                <span>*Phone number:</span><br>
                <input type="number" name="phone" id="regPhone"><br>
                <span>*Password:</span><br>
                <input type="password" name="password" id="regPassword"><br>
                <span>*Repeat password:</span><br>
                <input type="password" name="password2" id="regPassword2"><br>
                <span>Profile Image:</span><br>
                <input type="file" name="file" id='file_img'>
                <br><br>
                <button class="btn btn-success" id="regBTN">Sign up</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Terms and Coditions</h4>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam amet assumenda at dignissimos iure praesentium quas quis repudiandae. Ad at corporis eaque eos perferendis quis reiciendis sunt suscipit, veniam voluptate?
            </p>
        </div>
    </div>

</div>

<?php
 include 'includes/footer.php'; 
}
 ?>