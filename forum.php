<?php
include 'includes/header.php';

if(!isset($_GET['ftopic'])){
?>

<div class="container">
	<h1 style="margin-top: 60px">Forum</h1>
	<hr>
<?php if(isset($_SESSION['email'])){ ?>
	 <div class="container">
    	<a href="forum_create_thread.php" class="btn btn-info">Create a new thread</a>
<?php }else{ ?>
	<small><span>You must be <a href="login.php">logged in</a> to create a new thread.<br> Don't have an account? Sign up <a href="signup.php">here</a></span></small>
<?php } ?>
	<br><br>
	<br><br>
	<h3 style="margin-bottom: 30px">Topics</h3>
	<div class="div-block">
      <ul class="forum-list">
          <hr>

<?php
	$query = "SELECT ID as topic_id, name, description, (SELECT COUNT(ID) FROM forum_thread WHERE forum_topic_ID = topic_id) FROM `forum_topic`";	//Get categories
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i <  count($row); $i++){
			?>

			<li class="forum-list-topic">
				<?php echo "<a href='forum.php?ftopic=".$row[$i][0]."'>"; ?>
          		<h5><?php echo $row[$i][1]; ?></h5>
          		<p style="text-decoration: none; display: inline-block"><?php echo $row[$i][2];?> -  </p><small style="text-decoration: none; display: inline-block"> <?=$row[$i][3]?> thread(s)</small>
          		</a>

        	</li>
            <hr>

			

			<!-- INDEX LIST: 0-ID, 1-topic name, 2-short description -->
<?php
			
		}

	}
?>
	</ul>
    </div>
  </div>

<?php
}else{//isset ftopic
	
?>
<div class="container" style="margin-bottom: 120px;">

<?php
	$pageTopic = $_GET['ftopic'];

	$query = "SELECT name FROM forum_topic WHERE ID ='$pageTopic'";
	$result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0){

        while($row = mysqli_fetch_assoc($result)){

            $topicName = $row['name'];
        }

    }
?>
	<h1 style="text-align: center; margin-top: 30px"><?php echo $topicName; ?></h1>
	<hr style="margin-bottom: 60px;">
	
<?php

	$query = "SELECT F.ID AS forum_id, F.title, F.content, F.date, F.closed, F.file, F.user_ID, FT.name AS topic_name, U.name AS author_name, (SELECT COUNT(ID) FROM forum_reply WHERE forum_reply.forum_thread_ID = forum_id) AS num_replies
                FROM forum_thread F, user U, forum_topic FT
                WHERE F.forum_topic_ID = '$pageTopic' 
                AND U.ID = F.user_ID
                AND FT.ID = F.forum_topic_ID
                ORDER BY F.date ASC";
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0 ; $i < count($row); $i++){
?>
			<div class="forum_thread_preview_card">
				<h4><a href="forum_thread.php?threadID=<?php echo $row[$i][0] ?>"><?php echo $row[$i][1]; ?></a></h4>
				<p><?php echo $row[$i][2]; ?></p>
				<div>By:<?= "<a href='profile.php?userid=".$row[$i][6]."'>".$row[$i][8]."</a>" ?></div>
				<small><?php echo $row[$i][3]; ?></small>
				<small>Replies: <?php echo $row[$i][9]; ?></small>
				
			</div>
<?php
			//INDEX LIST: 0-ID; 4-TITLE; 1-AUTHOR; 6- DATE
		}
	}


}
?>
</div>
<?php
include 'includes/footer.php';
?>