<?php
include 'includes/header.php';


if(isset($_GET['threadID'])){

	$ID=$_GET['threadID'];

	$query = "SELECT F.ID, F.title, F.content, F.date, F.closed, F.file, U.ID AS author_id, U.name AS author_name, FT.name AS topic_name
                FROM forum_thread F, user U, forum_topic FT 
                WHERE F.ID = '$ID'
                AND F.user_ID = U.ID
                AND FT.ID = F.forum_topic_ID";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){
			$threadID = $row['ID'];
			$authorName = $row['author_name'];
			$authorID = $row['author_id'];
			$topic = $row['topic_name'];
			$title = $row['title'];	
			$content = $row['content'];
			$date = $row['date'];
			$status = $row['closed'];
			$filename = $row['file'];
		}
	}else{
		Header("Location: includes/404.php");
	}

}else{
	Header("Location: 404.php");
}
?>
<div style="margin: 25px">
<?php
if(isset($_SESSION['mock_role']) || isset($_SESSION['mock_ID']))
{
	if($_SESSION['mock_role'] != 'user'|| $_SESSION['mock_ID'] == $authorID){
		echo "<button class='btn btn-danger' id='admin_delete_thread_".$threadID."'>Eliminar</button>";
		if($status == 0)
			echo "<button class='btn btn-warning' style='color:white' id='admin_close_thread_".$threadID."'>Close</button>";

		echo "<button class='btn btn-primary'  id='admin_edit_thread_".$threadID."'><a style='color:white' href='forum_create_thread.php?editThread=".$threadID."'>Edit</a></button>";
	}

	
		
}

?>
</div>
<div class="container" id="<?php echo 'forum_thread_id_'.$ID; ?>">
	<div class="forum-thread-header">
		<h1 style="margin:30px"><?php echo $title; ?></h1>
		<p><?php echo $content; ?></p>

		<small><?php echo "<a href='profile.php?userid=".$authorID."'>".$authorName."</a>". " at " . $date;?></small>
		<small>Under <?php echo $topic; ?></small>
		<a href="<?php echo 'includes/download.php?file_path=attachments/'.$filename ?>"><?php echo $filename; ?></a>
        <br><br>
	</div>
	<hr>
    <h4 style="margin-bottom: 60px">Replies</h4>
	<div id="forum-thread-replies">
	</div>


<?php
	if(isset($_SESSION['mock_email']) && $status == 0){
?>
	<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="forum_reply_form">
        <span><b>Reply to this thread</b></span><br>
  		<input type="text" name="comment" id="forum_reply_text"><br>
  		<input type="file" name="file" id='forum_reply_attachment'>
  		<br><br>
  		<input type="submit" name="reply" value="reply" id="forum_reply_submit" class="btn btn-success">
  	</form>

<?php

	}else{

		if($status == 1){
			?>
            <div class="alert alert-danger" role="alert">
                This thread has been closed.
            </div>
            <?php
		}else{
			echo "<small style='margin-bottom: 120px' id='please-log-in'><span>You must be <a href='login.php'>logged in</a> to submit a reply.<br> Don't have an account? Sign up <a href='signup.php'>here</a></span></small><br>";
		}

		
	}

?>
	
</div>

<?php
include 'includes/footer.php';
?>