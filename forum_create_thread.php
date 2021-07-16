<?php
include 'includes/header.php';

if(!isset($_SESSION['mock_email'])){
	echo "<small><span>You must be <a href='login.php'>logged in</a> to create a new thread.<br> Don't have an account? Sign up <a href='signup.php'>here</a></span></small><br>";
	return;
}

if(!isset($_GET['editThread'])){

?>

<div class="container">
	<h1>Create a new thread</h1>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="dashboard_create_BPost_form" onsubmit="return false">
		<label>Topic</label>
		<select name="Category" id="forum_create_thread_topic_select">

<?php
	$query = "SELECT * FROM `forum_topic`";	//Get categories
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
			?>

			<option value='<?php echo $row[$i][0]; ?>'><?php echo $row[$i][1]; ?></option>

			<!-- INDEX LIST: 0-ID, 1-category name -->
<?php
			
		}

	}
?>
		</select><br>
		<input type="text" name="title" placeholder="Title" id="forum_create_thread_title" style="padding: 10px; width: 80%; margin: 0 15px;" >
		<textarea id="forum_create_thread_content" style="margin-left: 15px; margin-top:15px; width: 80%; height: 300px; resize: none;"></textarea><br>
		<input type="file" name="file" id='forum_file'>
		<input type="submit" name="SubmitBlogPost" value="Submeter nova thread" id="forum_submit_new_thread" class='w-button'>
	</form>

</div>
<?php }else{ //Editing the thread

		$threadID = $_GET['editThread'];

		$query = "SELECT user_ID, forum_topic_ID, title, content FROM forum_thread WHERE ID='$threadID'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

				while($row = mysqli_fetch_assoc($result)){

					$author_id = $row['user_ID'];
					$topic = $row['forum_topic_ID'];
					$title = $row['title'];
					$content = $row['content'];
				}
		}

		if($author_id != $_SESSION['mock_ID'] || $_SESSION['mock_role'] == 'user'){
			header("Location: index.php");
			die();
			return;
		}
?>

	<div class="container">
	<h1>Edit thread</h1>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="dashboard_create_BPost_form" onsubmit="return false">
		<label>Thread</label>
		<select name="Category" id="forum_edit_thread_topic_select">

<?php
	$query = "SELECT * FROM `forum_topic`";	//Get categories
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
		   // echo "<script>console.log(".$topic." ". $row[$i][1]")</script>"
				if($topic == $row[$i][0]){
			?>
					<option value='<?php echo $row[$i][0]; ?>' selected><?php echo $row[$i][1]; ?></option>
			<?php
				}else{
			?>
					<option value='<?php echo $row[$i][0]; ?>'><?php echo $row[$i][1]; ?></option>
<?php
				}
		}
		//<!-- INDEX LIST: 0-ID, 1-category name -->
	}
?>
		</select><br>
		<input type="text" name="title" value="<?php echo $title; ?>" id="forum_edit_thread_title" style="padding: 10px; width: 80%; margin: 0 15px;" >
		<textarea id="forum_edit_thread_content" style="margin-left: 15px; margin-top:15px; width: 80%; height: 300px; resize: none;"><?php echo $content; ?></textarea><br>

		<input type="submit" name="SubmitBlogPost" value="Atualizar thread" id="forum_submit_edit_thread" class='btn btn-success'>
	</form>

<?php } ?>
    </div>
<?php
include 'includes/footer.php';
?>