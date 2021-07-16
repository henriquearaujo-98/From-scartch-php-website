<?php
	include 'includes/header.php';

	if(!isset($_GET['groupThreadID']) || !isset($_SESSION['ID'])){
		header('Location: index.php');
		die();
		return;
	}

	$threadID = $_GET['groupThreadID'];

	$query = "SELECT * FROM `user_groups_threads` WHERE ID = '$threadID'";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){
			$groupID = $row['group_id'];
			$title = $row['title'];
			$content = $row['content'];
			$authorName = $row['author_name'];
			$authorID = $row['author_id'];
			$date = $row['date'];
			$filename = $row['file'];
			$closed = $row['closed'];
		}
	}else{
		header("Location: index.php");
	}

	$query = "SELECT * FROM `user_groups` WHERE ID = '$groupID'";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){
			$userArr = explode(',', $row['users_id']);
			$groupName = $row['group_name'];
			$public = $row['public'];
		}
	}

	if(!$public){
		if (!in_array($_SESSION['ID'], $userArr) && $_SESSION['permission'] == 'nao') {
			header("Location: index.php");
			die();
			return;
		}else if($_SESSION['permission'] == 'nao'){
			echo 'AVISO: Você não está neste grupo';
		}
	}
	

	


?>

<div class='container'>
	<?php
	if($_SESSION['permission'] == 'sim' || $_SESSION['ID'] == $authorID){
		echo "<button class='w-button' id='admin_delete_groupthread_".$threadID."'>Eliminar</button>";
		if($closed == 0)
			echo "<button class='w-button' id='admin_close_groupthread_".$threadID."'>Encerrar</button>";

		echo "<button class='w-button' id='admin_edit_groupthread_".$threadID."'><a href='group_create_thread.php?groupID=".$groupID."&editGroupThread=".$threadID."'>Editar</a></button>";
	}
	?>
	<h1 style="text-align: center;"><?php echo $groupName ?></h1>
	<br>
	<br>
	<hr>
	<div class="forum-thread-header">
		<h3><?php echo $title; ?></h3>
		<p><?php echo $content; ?></p>

		<small><?php echo "<a href='profile.php?userid=".$authorID."'>".$authorName."</a>". " at " . $date;?></small>
		<a href="<?php echo 'includes/download.php?file_path=attachments/'.$filename ?>"><?php echo $filename; ?></a>
	</div>
	<hr>
	<br>
	<br>

	<div id="group-thread-replies">
	</div>

	<?php 
		if($closed == 0){
		?>
			<form id='group_thread_reply_form' onsubmit="return false;">
				<label>Comentar</label>
				<br>
				<textarea id='group_thread_reply_content'></textarea>
				<br>
				<input type="file" name="file" id='group_thread_reply_file'>
				<br>
				<button id='group_thread_submit_reply'>Submeter</button>
			</form>
		<?php
		}else if($closed == 1){
			?>
			<div>
				<p>Este tópico foi fechado.</p>
			</div>
			<?php
		}
	?>

</div>

<?php
	include 'includes/footer.php';
?>