<?php
include 'includes/header.php';

if(!isset($_SESSION['ID'])){
	header('Location: index.php');
	die();
	return;
}

?>

<?php if(!isset($_GET['groupID'])){ ?>
<div class='container'>
	<h3>Os meus grupos</h3>
	<div class='group-preview'>
		<ul class='row'>
	<?php
        $currUserID = $_SESSION['ID'];
		$query = "SELECT UG.ID, UG.name, UG.public, UG.image 
                    FROM user_group UG, user_in_group UIG
                    WHERE UG.ID = UIG.user_group_ID
                    AND UIG.user_ID = '$currUserID'
                    AND UG.public = 0";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			$row = mysqli_fetch_all($result);
			for($i = 0; $i <  count($row); $i++){
			    if(!$row[$i][2]){
							?>
							
								<li class='col preview-group'>
									<a href="groups.php?groupID=<?php echo $row[$i][0];?>">
										 <img style="width: 225px; height: auto;" src="uploads/group_images/<?php echo $row[$i][3]; ?>">
										 <div><?php echo $row[$i][1]; ?></div>
									</a>
									
								</li>
							
							<?php
                }
			}
		}


	?>
		</ul>
	</div>
	<br>
	<hr>
	<br>

	<h3>Grupos públicos</h3>
	<?php
		$query = "SELECT * FROM `user_group` WHERE `public` = 1";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			$row = mysqli_fetch_all($result);
			if(count($row) > 0){
				for($i = 0; $i <  count($row); $i++){
					if($row[$i][2]){
					?>
					
						<li class='col preview-group'>
							<a href="groups.php?groupID=<?php echo $row[$i][0];?>"> 
								<img style="width: 225px; height: auto;" src="uploads/group_images/<?php echo $row[$i][3]; ?>">
								<div><?php echo $row[$i][1]; ?></div>
							</a>
						</li>
					
					<?php
							
					}
					
				}
			}else{
				?>
					<div class='alert alert-warning'>Não existem grupos públicos</div>
				<?php
			}
		}

	?>
</div>

<?php }else{ ?>

	<?php 
		if(!isset($_SESSION['ID'])){
			header('Location: index.php');
			die();
			return;
		}

		$groupID = $_GET['groupID'];
        $userID = $_SESSION['ID'];

		$query = "SELECT * FROM `user_group` WHERE ID = '$groupID'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			while($row = mysqli_fetch_assoc($result)){
				$groupName = $row['name'];
				$public = $row['public'];
			}
		}

		if(!$public){
			/*if (!in_array($_SESSION['ID'], $userArr) && $_SESSION['permission'] == 'nao') {
				header("Location: index.php");
				die();
				return;
			}else if($_SESSION['permission'] == 'nao'){
				echo 'AVISO: Você não está neste grupo';
			}*/
            $query = "SELECT GT.ID GT.title, GT.content, GT.date, GT.closed, U.name AS author_name, U.ID AS author_ID
                        FROM user_group_thread GT, user U, user_in_group UIG 
                        WHERE UIG.user_ID = '$userID' 
                        AND UIG.user_group_ID = '$groupID'
                        AND GT.user_group_ID ='$groupID'
                        AND U.ID = GT.user_ID";
            $result = mysqli_query($conn, $query);

            if(mysqli_num_rows($result) == 0){
                echo mysqli_error($conn);
                return;
            }
		}

		

	?>

	<div class='container'>
		<button><a href="group_create_thread.php?groupID=<?php echo $groupID;?>">Criar tópico</a></button>
		<h3>Tópicos do grupo <?php echo $groupName; ?></h3>
		<ul>
			<?php

				$query = "SELECT * FROM `user_groups_thread` WHERE group_id = '$groupID'";
				$result = mysqli_query($conn, $query);

				if(mysqli_num_rows($result) > 0){

					$row = mysqli_fetch_all($result);

					for($i = 0; $i <  count($row); $i++){
						?>
							<li><a href="group_threads.php?groupThreadID=<?php echo $row[$i][0] ?>"><?php echo $row[$i][3] ?></a></li>
						<?php
					}
				}
			?>
		</ul>
	</div>

<?php } ?>


<?php
include 'includes/footer.php';
?>