<?php
	include 'includes/header.php';

	if(!isset($_SESSION['ID']) || !isset($_GET['groupID'])){
		header('Location: index.php');
		die();
		return;
	}

	$groupID = $_GET['groupID'];

	$query = "SELECT * FROM `user_groups` WHERE ID = '$groupID'";

	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		while($row = mysqli_fetch_assoc($result)){
			$userArr = explode(',', $row['users_id']);
			$group_name = $row['group_name'];
			$public = $row['public'];
		}
	}

	if(!$public){
		if (!in_array($_SESSION['ID'], $userArr)) {
			header("Location: index.php");
			die();
			return;
		}
	}
	
?>
<?php if(!isset($_GET['editGroupThread'])){ ?>
			<div class='container'>
				<h3>Criar um tópico para <?php echo $group_name; ?></h3>
				<hr>
				<br>
				<form onsubmit="return false;">
					<input type="text" name="title" id='group_thread_title' placeholder="Título">
					<textarea id='group_thread_content' placeholder="Conteúdo"></textarea>
					<input type="file" name="file" id='group_thread_file'>
					<br>
					<button id='group_thread_submit'>Submeter</button>
				</form>
			</div>
<?php }else{

		$threadID = $_GET['editGroupThread'];
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
			echo "Could not find record ".$threadID;
		}
?>
		<div class='container'>
			<h3>Criar um tópico para <?php echo $group_name; ?></h3>
			<hr>
			<br>
			<form onsubmit="return false;">
				<input type="text" name="title" id='group_thread_title' placeholder="Título" value='<?php echo $title; ?>'>
				<textarea id='group_thread_content' placeholder="Conteúdo"><?php echo $content; ?></textarea>
				<a href="<?php echo 'uploads/attachments/'.$filename ?>" download="<?php echo 'uploads/attachments/'.$filename ?>"><?php echo $filename; ?></a>
				<input type="file" name="file" id='group_thread_file'>
				<br>
				<button id='group_editthread_submit'>Editar</button>
			</form>
		</div>

<?php
	}
?>

<?php
	include 'includes/footer.php';
?>