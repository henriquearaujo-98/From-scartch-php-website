<?php
include 'includes/header.php';

if(isset($_GET['bpid'])){

	$ID = $_GET['bpid'];	//Get post id

	$query = "SELECT B.title, B.content, B.date, B.image_name, B.user_ID, C.name as cat_name, U.name as user_name, DATE(B.date) > DATE(NOW()) AS is_old 
                FROM blog_post B, blog_category C, user U 
                WHERE B.ID = '$ID'
                AND B.user_ID = U.ID
                AND B.blog_category_ID = C.ID";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){

			$title = $row['title'];
			$content = $row['content'];
			$date = $row['date'];
			$authorID = $row['user_ID'];
			$authorName = $row['user_name'];
			$category = $row['cat_name'];
			$released = $row['is_old'];
		}
	}else{
		Header("Location: includes/404.php");
	}

}else{
	Header("Location: includes/404.php");
}

if($released == 1 && $_SESSION['mock_role'] === 'user'){
	Header("Location: includes/404.php");
}else if($released == 1 && $_SESSION['mock_role'] === 'sim'){
	echo "WARNING: This post is not yet released to the public.";
}
if(isset($_GET['editbp']) && isset($_SESSION['mock_role'])){


    if ($_SESSION['mock_role'] != 'user') {
?>
<div class="container">
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="edit_blog_post_form">
		<label>Category</label><br>
		<select name="Category" id="edit_blog_post_category" class="form-select" style="width: 25%">
<?php
	$query = "SELECT * FROM `blog_category`";	//Get categories
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
			?>

			<option value='<?php echo $row[$i][0]; ?>' <?php if($row[$i][1] == $category){echo "selected";} ?> ><?php echo $row[$i][1]; ?></option>

			<!-- INDEX LIST: 0-ID, 1-category name -->
<?php
			
		}

	}
?>
		</select>
        <br><br>
		<input type="text" name="title" placeholder="Title" id="edit_blog_post_title" value="<?php echo $title; ?>" style="padding: 10px; width: 80%; margin: 0 15px;">
		<textarea id="edit_blog_post_content" style="margin-left: 15px; margin-top:15px; width: 80%; height: 300px; resize: none;"><?php echo $content;?></textarea><br>

		<?php $newDate = str_replace(' ', 'T', $date);?>
			<label>Change image</label><br>
			<input type="file" name="file" id='editfile'><br><br>
			<label>Change released date</label><br>
			<input type="datetime-local" name="date" id="edit_blog_post_datetime" value="<?php echo $newDate; ?>"><br>
			<small>Current release date: <?php echo $date;?> </small>
        <br><br>
		<input type="submit" name="SubmitBlogPost" value="Submit Blog Post" id="edit_blog_post_submit" class="btn btn-success">
	</form>


<?php
    }
    }else{

?>


    <?php
    if(isset($_SESSION['mock_ID'])){
        if($_SESSION['mock_role'] != 'user'){
            ?>
            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                <button type="button" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-warning">Edit</button>
            </div>
            <?php
        }
    }
    ?>
 <div class="container">
	<h1><?php echo $title; ?></h1>
	<br>
	<article><?php echo $content; ?></article>
	<div><?php echo "<a href='profile.php?userid=".$authorID."'>".$authorName."</a>" ?></div>
	<div><?php echo $category; ?></div>
	<div><?php echo $date; ?></div>

	<div>
		<h4>Comment section</h4>
		<div id="blog_post_comment_container">
<?php
	$query = "SELECT B.content, B.user_ID, B.date, U.name
                FROM blog_comment B, user U
                WHERE blog_post_ID = '$ID'
                AND U.ID = B.user_ID";	//Get comments
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
			?>

			<div style="border: 2px solid grey;">
				<h5><a href="profile.php?userid=<?=$row[$i][1]?>"><?php echo $row[$i][3]; ?></a></h5>
				<hr>
				<p><?php echo $row[$i][0]; ?></p>
				<small><?php echo $row[$i][2]; ?></small>
			</div>

			<!-- INDEX LIST: 3-author name, 1-comment content, 5-comment date -->
<?php
			
		}

	}
?>
	</div>
	</div>
	<br><br>

<?php if(isset($_SESSION['mock_name'])){  ?>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="blog_post_comment_form">
		<span>Leave a comment:</span><br>
  		<input type="text" name="comment" id="blog_post_comment_text"><br>
  		<input type="hidden" name="name" id="blog_post_comment_authorname" value="<?php echo $_SESSION['mock_name']; ?>" />
  		<br><br>
  		<input type="submit" name="leaveacomment" value="Comment" id="blog_post_comment_submit">
  	</form>
<?php }else{?>

<small><span>You must be <a href="login.php">logged in</a> to create a new thread.<br> Don't have an account? Sign up <a href="signup.php">here</a></span></small><br>
        </div>
        </div>

<?php
	 }
}
?>
<div style="width: 100%; position: absolute; left: 0">
<?php
include 'includes/footer.php'
?>
</div>
