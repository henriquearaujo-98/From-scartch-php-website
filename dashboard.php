<?php
include 'includes/header.php';

if(!isset($_SESSION['mock_email'])){				//Vemos primeiro se o utilizador está loggado
	Header("Location: index.php");
}

$email = $_SESSION['mock_email'];

$query = "SELECT role FROM user WHERE email = '$email'";		//Conferimos na base de dados se o utilizador tem permissão
																	//Vamos diretamente á BD porque as session variables são alvo de ataque
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){

	while($row = mysqli_fetch_assoc($result)){
		$permission = $row['role'];	//hashed permission
	}

	if(password_verify('user', $permission)){
		Header("Location: index.php");
	}
}else{
	echo 'no results for '. $_SESSION['mock_email'];
	Header("Location: index.php");
}
?>



<div class="container" style="margin: 120px auto">


<div class="container-8 w-container">
    <h1 class="heading-13">Dashboard</h1>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Manage Users</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Manage Blog</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Manage Forum</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
        	<br><br>
			<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'get' id="dashboard_search_user_form">
				<br>
				<span>Search users by: </span>
				<select id="dashboard_search_user_option" class="form-select" style="width: auto">
					<option value="all">All</option>
					<option value="email">User's email</option>
					<option value="name">User's name</option>
					<option value="notAccepted">Access pending</option>
				</select><br><br>
				<input type="text" name="email" id="dashboard_search_user_data" style="display: none;">
				<input type="submit" name="search" class="btn btn-primary" value="Search users" id="dashboard_search_userBTN">
			</form><br><br>
			<div id="dashboard_manage_user" style="display:none;">
				<table style="width: 100%; text-align: center;" id ="dashboard_search_user_results" class="table table-striped">
			  		<tr>
					    <th>ID</th>
					    <th>Name</th>
					    <th>Email</th>
					    <th>Role</th>
					    <th>Options</th>
			  		</tr>
				</table>
			</div>
			<div id="dashboard_user_threads">

			</div>
        </div>



        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
				<h4 style="margin-top: 60px">Manage blog categories</h4>
				<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="dashboard_create_BlogCategory_form">
					<label>Add category</label>
					<input type="text" name="addcategory" id="dashboard_blog_add_category">
					<button id="dashboard_submit_blog_category" class="btn btn-success">Create category</button>
				</form>
				<table style="margin-bottom: 60px;">
					<tbody id="dashboard_show_blog_categories">

					</tbody>
				</table>
				<hr>
				<h2 style="text-align: center">Unreleased Posts</h2>
				<div id="dashboard_BPost_show_unreleased">

				</div>

				<h4>Released posts</h4>
				<div id="dashboard_BPost_show_released">

				</div>

				<br><br>
				<br>

				<h2 style="text-align: center">Create a blog post</h2>
				<form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = 'post' id="dashboard_create_BPost_form" enctype="multipart/form-data" onsubmit='return null'>
					<label>Category</label>
					<select name="Category" id="dashboard_new_BPost_category" class="form-select">

			<?php

				$query = "SELECT * FROM `blog_category`";	//Get categories
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
					</select>
					<br>
					<input type="text" name="title" placeholder="Title" id="dashboard_create_new_BPost_title"><br>
					<textarea id="dashboard_create_new_BPost_content" rows="15"></textarea><br>
					<label>Select an image</label><br>
					 <input type="file" name="file" id="file">
					 <br><br>
					<label>Post date</label><br>
					<input type="datetime-local" name="date" id="dashboard_create_new_BPost_datetime"><br>

					<input type="submit" name="SubmitBlogPost" value="Submit Blog Post" class="btn btn-success" id="dashboard_submit_new_BPost">
				</form>
        </div>


        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
        	<br><br>
			<h4>Create new topic</h4>
			<form onsubmit="return false">
				<label>Add topic</label><br>
				<input type="text" name="forumtopic" id="dashboard_create_new_forum_topic"><br>
				<label>Add a description</label><br>
				<input type="text" name="forumtopicdescription" id="dashboard_create_new_forum_topic_description"><br><br>
				<button id="dashboard_submit_new_forum_topic" class="btn btn-success">Submit new topic</button>
			</form>
			<br>
			<table id="dashboard_show_forum_topics" style="width: 100%; margin: auto; text-align: center;" class="table">
				<tr>
				    <th>Topic name</th>
				    <th>Description</th>
				    <th>Options</th>
		  		</tr>
			</table>
        </div>
      </div>
    </div>
  </div>

</div> <!-- END OF CONTAINER -->




<?php
 include 'includes/footer.php'; 
?>