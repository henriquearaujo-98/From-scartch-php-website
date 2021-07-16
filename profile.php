<?php
include 'includes/header.php';

if(isset($_GET['userid'])){
	
	$ID = $_GET['userid'];

	$query = "SELECT * FROM user WHERE ID = '$ID'";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){

			$name = $row['name'];
			$date = $row['date'];
			$profile_image = $row['profile_image'];
			$accepted = $row['accepted'];
		}
	}else{
		Header("Location: includes/404.php");
	}

}else{
	Header("Location: includes/404.php");
}

?>

<div class="container" style="margin-top: 120px">
    <div class="row">
        <div class="col-md-4">

            <?php echo "<img src='uploads/".$profile_image."'>" ?>
            <?php
                if(!$accepted){
                    ?>
                    <div class="container">
                        <div class="alert alert-warning" role="alert">
                            This account has not been accepted yet. It may not post, comment or otherwise interact with the website.
                        </div>
                    </div>
                    <?php
                }
            ?>
            <h1><?php echo $name; ?></h1>
            <small><i>Joined <?php echo $date; ?></i></small>
        </div>
        <div class="col-md-8">
            <p id='profile_show_bio'>

            </p>
        </div>
    </div>
	<br>
	

<?php
		if(isset($_SESSION['mock_ID']) && $_GET['userid'] == $_SESSION['mock_ID']){ ?>
			<button id='profile_settings_trigger' class='btn btn-primary'>Settings</button>

			<section id='profile_show_account_settings' style="display: none;">
				<div>
					<h3>Account settings</h3>
			        	<br><br>
			        	<small>Info: You can change any field of your information seperatly.</small>
			        	<div class="row">
      						<div class="col col-4">
					        	<form onsubmit="return false">
					        		<label>Email</label><br>
					        		<input type="text" name="email" id='profile_edit_email' placeholder="<?php echo $_SESSION['mock_email']; ?>"><br>
					        		<label>Name</label><br>
					        		<input type="text" name="name" id='profile_edit_name' placeholder="<?php echo $_SESSION['mock_name']; ?>"><br>
					        		<label>New Password</label><br>
					        		<input type="password" name="Password" id='profile_edit_password'><br>
					        		<label>Retype your new password</label><br>
					        		<input type="password" name="Password2" id='profile_edit_password2'><br>
					        		<br><br>
					        		<small>To make changes to your settings, please enter your current password</small><br>
					        		<label>Password</label><br>
					        		<input type="password" name="Password" id='profile_password'><br>
					        		<br><br>
					        		<button id='profile_edit_info' class="btn btn-success">Change</button><br>
					        	</form>
					        </div>
					        <div class="col col-8">
					        	<form onsubmit="return false">
					        		<h2>Bio</h2>
					        		<textarea id='profile_bio_content' rows="10" cols="100" style="max-width: 100%"></textarea><br>
					        		<button class='btn btn-primary' id='profile_submit_bio'>Submit</button>
					        	</form>
					        </div>
					    </div>    
				</div>
			</section>



			<hr>

			<div id='profile_show_forum_threads' style="margin-bottom: 120px">
								
			</div>
			
			
<?php 	
	}else{ //Other people's profile
?>

		<p id='profile_show_bio'>
				
			</p>

		<hr>
		<h2>User threads</h2>
			<ul class='profile_user_threads_list'>
<?php
				$id = intval($_GET['userid']);
				$query = "SELECT * FROM `forum_thread` WHERE `user_ID`='$id' ORDER BY date DESC";
				$result = mysqli_query($conn, $query);
				if(mysqli_num_rows($result) > 0){

					$row = mysqli_fetch_all($result);
					for($i = 0; $i < count($row); $i++){
?>

						<li class='forum-list-topic profile_thread_card'>
							<?php echo "<a href='forum_thread.php?threadID=".$row[$i][0]."'>"; ?>
			          		<h4><?php echo $row[$i][4]; ?></h4>
			          		<span></span>
			          		<p class="text-block-2" style="text-decoration: none;"><?php echo $row[$i][5];?></p>
			          		
			          		<small><?php echo $row[$i][6]; ?></small>
			          		</a>
			        	</li>

			

			<!-- INDEX LIST: 0-ID, 1-topic name, 2-short description -->
<?php 				} ?>					
					</ul>
<?php			}else{
					echo "This user has no threads active";
				} 

			}
		 
	
?>
</div>


<?php
include 'includes/footer.php';
?>