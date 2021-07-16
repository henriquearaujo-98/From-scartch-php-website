<?php
	include 'countdown.php';

	if($diff > 0){
		Header("Location: index.php");
		return;
	}

	include 'connection.php';	
	session_start();

	/*-------------------------------------*/
	/*------------- Functions -------------*/
	/*-------------------------------------*/

	function ValidatedForm($formData){								//Process form data to avoid SQL injections
		$formData = addslashes(htmlspecialchars(trim($formData)));
		return $formData;
	}

	function ValidatePermission($hashed){			//Used to initialize session variable 'permission'
		 if(password_verify('admin', $hashed)){
		 	return 'admin';
		 }else if(password_verify('mod', $hashed)){
		 	return 'mod';
		 }else{
		     return 'user';
         }
	}

	function DeleteFile($path){
		return unlink($path);
	}

	/*------------------------------------*/
	/*---Log in / Log out / Register -----*/
	/*------------------------------------*/

	//Register
	if(isset($_POST['signup'])){
		$name = $_POST['fullname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$phone = $_POST['phone'];
		$file_name = '';

		if(isset($_FILES["file"]["name"])){
	            $test = explode(".", $_FILES["file"]["name"]);
	            $extension = end($test);
	            $extension = strtolower($extension);
	            if($extension != 'png' && $extension != 'jpg' && $extension != 'jpeg' && $extension != 'gif'){
	            	echo 'Invalid image file ' . $extension;
	            }else{
	            	$file_name = rand(1, 9999) .'_'. $_FILES["file"]["name"];
		            $location = '../uploads/'.$file_name;
		            move_uploaded_file($_FILES["file"]["tmp_name"], $location);
	        	}
        }
		
		$query = "SELECT email FROM user WHERE email ='$email' ";

		$result = mysqli_query($conn, $query);

		if (mysqli_num_rows($result) > 0){

			echo "Email is already registered.";

		}else if($name != '' && $email != '' && $password != ''){

        	$name = ValidatedForm($name);
        	$email = ValidatedForm($email);
        	$password = ValidatedForm($password);
        	$phone = ValidatedForm($phone);

			$password = password_hash($password, PASSWORD_DEFAULT);
			$permission = password_hash("user", PASSWORD_DEFAULT);

			if($file_name != ''){
				$query = "INSERT INTO user (`email`, `name`, `password`, `role`, `phone`, `profile_image`, `accepted`) VALUES ('$email', '$name', '$password', '$permission', '$phone', '$file_name', 0)";
			}else{
				$query = "INSERT INTO user (`email`, `name`, `password`, `role`, `phone`, `accepted`) VALUES ('$email', '$name', '$password', '$permission', '$phone', 0)";
			}

			if(mysqli_query($conn, $query)){

				$result = mysqli_query($conn, "SELECT ID FROM user WHERE email = '$email'");

				if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_assoc($result)){
						$_SESSION['ID'] = $row['ID'];
					}
				}

				$_SESSION['email']=$email;
				$_SESSION['name']=$name;
				$_SESSION['permission'] = ValidatePermission($permission);
				echo $_SESSION['ID'];
                mysqli_close($conn);
				return;
			}else{
				echo mysqli_errno($conn) . " " . mysqli_error($conn);
				mysqli_close($conn);
				return;
			}
		}else{
		    echo "Error code 1. Please contact the server administrator.";
        }
        mysqli_close($conn);
	}	

	//Log in
	if(isset($_POST['login'])){

		if(!$_POST['email']){
			echo -4;
			return;
		}else{
			$email = ValidatedForm($_POST['email']);

			if(!$_POST['password']){
				echo -4;
				return;
			}else{
				$password = ValidatedForm($_POST['password']);
			}

		}

		$query = "SELECT ID, email, password, name, role, accepted FROM user WHERE email = '$email'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

				while($row = mysqli_fetch_assoc($result)){

					$hashedPassword = $row['password'];
					$email = $row['email'];
					$name = $row['name'];
					$role = $row['role'];	//hashed permission
					$accepted = $row['accepted'];
					$ID = $row['ID'];
				}

				if($accepted == 0){
					echo -1;
					mysqli_close($conn);
					return;
				}

				if(password_verify($password, $hashedPassword)){
					//session_start();
					//setcookie('email', $email, time() + (86400 * 30), "/"); // 86400 = 1 day

					$_SESSION['email']=$email;
					$_SESSION['name']=$name;
					$_SESSION['ID'] = $ID;
					$_SESSION['role'] = ValidatePermission($role);
					
					echo $ID;
				}else{
					echo 0;
				}
		}else{
				echo 0;
		}

	}

	//Log out
	if(isset($_POST['logout'])){
		session_destroy();
		session_start();
    	session_unset();
    	session_destroy();
    	session_write_close();
    	setcookie(session_name(),'',0,'/');
    	session_regenerate_id(true);
    	echo 1;
	}

	/*------------------------------------*/
	/*------------------------------------*/
	/*-------------DASHBOARD--------------*/
	/*------------------------------------*/
	/*------------------------------------*/
	//Common algorithms
	if(isset($_POST['deleteFromTable'])){
		error_reporting(E_ERROR | E_PARSE);

		if(isset($_SESSION['role'])){
		    if($_SESSION['role'] != 'admin'){
		        echo -1;
		        return;
            }
        }else{
		    return;
        }

		$ID = $_POST['id'];
		$table = $_POST['table'];

		if($table == 'blog_post'){
			$query = "SELECT `ID`, `image_name` FROM `blog_post` WHERE ID = '$ID'";
			$result = mysqli_query($conn, $query);

			if(mysqli_num_rows($result) > 0){

				while($row = mysqli_fetch_assoc($result)){

					$imageName = $row['image_name'];
				}
				unlink("../uploads/".$imageName);

				$query = "DELETE FROM ".$table." WHERE ID='$ID'";
				$result = mysqli_query($conn, $query);

				if(mysqli_num_rows($result) > 0){
					echo mysqli_error($conn);
				}else{
					echo 1;
				}

			}else{
				echo "Could not find " . $imageName;
			}
			

		}else{

				$query = "DELETE FROM ".$table." WHERE ID='$ID'";
				$result = mysqli_query($conn, $query);

				if(mysqli_num_rows($result) > 0){
					echo mysqli_error($conn);
				}else{
					echo 1;
				}

		}
		
	}


	/*------------------------------------*/
	/*------------ Accept User -----------*/
	/*------------------------------------*/
	//			   (dashboard)
	if(isset($_POST['AcceptUser'])){

	    if(isset($_SESSION['role'])){
	        if($_SESSION['role'] != 'admin'){
	            echo -1;
	            mysqli_close($conn);
	            return;
            }
            $id = $_POST['data'];

            $query = "UPDATE `user` SET `accepted`=1 WHERE ID = '$id'";

            if(mysqli_query($conn, $query)){
                echo 1;
            }else{
                echo 0;
            }
        }


	}




	/*------------------------------------*/
	/*------------ Search User -----------*/
	/*------------------------------------*/
	//			   (dashboard)
	if(isset($_GET['searchUser'])){
		error_reporting(E_ERROR | E_PARSE);
		if(isset($_GET['data']))
			$data = $_GET['data'];

		$option = $_GET['option'];

		if($option === 'all'){

			$ID=$userName=$userHashedPermission=$userPermission="";

			$query = "SELECT * FROM user";

			$result = mysqli_query($conn, $query);
			if(mysqli_num_rows($result) > 0){
				
				$row = mysqli_fetch_all($result);
				for($i = 0; $i < count($row); $i++){

					$userHashedPermission = $row[$i][4];
					
					$row[$i][3]=ValidatePermission($userHashedPermission);
					
					$row[$i][4] = "deleted key for security messures";
				}


    			$callbackJson = json_encode($row);              
    			echo $callbackJson;
				
			}else{
				$callbackObj->error = "Could not find a user with the name " . $data;
				$callbackJson = json_encode($callbackObj);
				echo $callbackJson;
			}

		}else if($option == 'email'){
			$ID=$userName=$userHashedPermission=$userPermission="";

			$query = "SELECT ID, email, name, role, accepted FROM user WHERE email = '$data'";

			$result = mysqli_query($conn, $query);

			if(mysqli_num_rows($result) > 0){

					while($row = mysqli_fetch_assoc($result)){

						$userName = $row['name'];
						$userHashedPermission = $row['role'];
						$ID = $row['ID'];
						$accepted = $row['accepted'];
					}
					
					$userPermission=ValidatePermission($userHashedPermission);
					

					$callbackObj->name = $userName;
					$callbackObj->permission = $userPermission;
					$callbackObj->Id = $ID;
					$callbackObj->email = $data;
					$callbackObj->accepted = $accepted;
					$callbackObj->error = "";

					$callbackJson = json_encode($callbackObj);
					echo $callbackJson;
					
			}else{
					$callbackObj->error = "Could not find a user with the email " . $data;
					$callbackJson = json_encode($callbackObj);
					echo $callbackJson;
			}

		}else if($option == 'name'){

			$query = "SELECT * FROM user WHERE name LIKE '%".$data."%'";
			$result = mysqli_query($conn, $query);
			if(mysqli_num_rows($result) > 0){
				
				$row = mysqli_fetch_all($result);
				for($i = 0; $i < count($row); $i++){

					$userHashedPermission = $row[$i][4];
					
					$row[$i][3]=ValidatePermission($userHashedPermission);
					
					$row[$i][4] = "deleted key for security messures";
				}


    			$callbackJson = json_encode($row);              
    			echo $callbackJson;
				
			}else{
				$callbackObj->error = "Could not find a user with the name " . $data;
				$callbackJson = json_encode($callbackObj);
				echo $callbackJson;
			}
		}else if($option == 'notAccepted'){
			$query = "SELECT * FROM user WHERE accepted=0";
			$result = mysqli_query($conn, $query);
			if(mysqli_num_rows($result) > 0){
				
				$row = mysqli_fetch_all($result);
				for($i = 0; $i < count($row); $i++){

					$userHashedPermission = $row[$i][4];
					
					$row[$i][3]=ValidatePermission($userHashedPermission);
					
					$row[$i][4] = "deleted key for security messures";
				}


    			$callbackJson = json_encode($row);              
    			echo $callbackJson;
				
			}else{
				$callbackObj->error = "Could not find a user with the name " . $data;
				$callbackJson = json_encode($callbackObj);
				echo $callbackJson;
			}
		}
	}

	/*---------- Edit User -------------*/
	if(isset($_POST['dashboard_EditUser'])){

	    if(isset($_SESSION['role'])){
	        if($_SESSION['role'] != 'admin'){
	            echo -1;
                return;
            }

        }else{
	        return;
        }

		$id = $_POST['id'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		$permission = $_POST['permission'];
		$password = $_POST['password'];

		if($name != '' && $email != '' && $permission != '' && $id != '' && $password != ''){
				$pw = password_hash($password, PASSWORD_DEFAULT);

				if($permission == 0){	//Admin = 0
					$permission = password_hash('admin', PASSWORD_DEFAULT);
					$query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission', `password`='$pw' WHERE ID = '$id'";
				}else if($permission == 1){	//Mod = 1
					$permission = password_hash('mod', PASSWORD_DEFAULT);
					$query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission', `password`='$pw' WHERE ID = '$id'";
				}else if($permission == 2){ //User = 2
                    $permission = password_hash('user', PASSWORD_DEFAULT);
                    $query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission', `password`='$pw' WHERE ID = '$id'";
                }
		}else if ($name != '' && $email != '' && $permission != '' && $id != '' && $password == ''){

			if($permission == 0){	//Admin = 0
				$permission = password_hash('admin', PASSWORD_DEFAULT);
				$query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission' WHERE ID = '$id'";
			}else if($permission == 1){	//Mod = 1
				$permission = password_hash('mod', PASSWORD_DEFAULT);
				$query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission' WHERE ID = '$id'";
			}else if($permission == 2){ //User = 2
                $permission = password_hash('user', PASSWORD_DEFAULT);
                $query = "UPDATE `user` SET `email`='$email',`name`='$name',`role`='$permission' WHERE ID = '$id'";
            }
		}

		if(mysqli_query($conn, $query)){
			echo 0;
		}else{
			echo 1;
		}
		
	}

	/*--------- Delete User ------------*/
	if(isset($_POST['deleteUser'])){

		$data = $_POST['id'];

		$query = "DELETE FROM user WHERE ID = '$data'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			echo 0;
		}else{
			echo 1;
		}
	}

	/*------------------------------------*/
	/*--------------- BLOG ---------------*/
	/*------------------------------------*/
	//			   (dashboard)
	/*------------ Create new blog category ----------------*/
	if(isset($_POST['submit_new_blog_category'])){
		error_reporting(E_ERROR | E_PARSE);
		$cat = $_POST['category'];

		$query = "SELECT category FROM blog_category WHERE name = '$cat'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			echo -1;
		}else{
			$query = "INSERT INTO blog_category (`name`) VALUES ('".$cat."')";

			if(mysqli_query($conn, $query)){
				echo mysqli_insert_id($conn);
			}else{
				echo 0;
			}
		}
	}

	/*------------ Show blog category ----------------*/
	if(isset($_GET['dashboard_showBlogCategories'])){
		error_reporting(E_ERROR | E_PARSE);

		$query = "SELECT * FROM `blog_category`";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			
			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);              
			echo $callbackJson;

		}else{
			$callbackObj->error = "There are no records to display";
			$callbackJson = json_encode($callbackObj);              
			echo $callbackJson;
		}
	}

	/*------------ Show blog posts ----------------*/
	if(isset($_GET['dashboard_showBlogPosts'])){        //Show blog posts in dashboard to be released in the future 
		$query = "SELECT b.ID, b.title, b.content, b.date, b.image_name, b.user_ID, u.name, b.blog_category_ID, c.name
                    FROM blog_post b, user u, blog_category c
                    WHERE b.user_ID = u.ID
                    AND b.blog_category_ID = c.ID";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			
			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);              
			echo $callbackJson;

		}else{
			//transmit error back
		}
		
	}

	/*------------ Publish new blog posts ----------------*/
	if(isset($_POST['publishBlogPost'])){

		if($_FILES["file"]["name"] != ''){
	            $test = explode(".", $_FILES["file"]["name"]);
	            $extension = end($test);
	            $extension = strtolower($extension);
	            if($extension != 'png' && $extension != 'jpg' && $extension != 'jpeg' && $extension != 'gif'){
	            	echo 'Invalid image file ' . $extension;
	            }else{
	            	$name = rand(1, 9999) .'_'. $_FILES["file"]["name"];
		            $location = '../uploads/'.$name;
		            move_uploaded_file($_FILES["file"]["tmp_name"], $location);
	        		


					$title = $_POST['title'];
					$content = $_POST['content'];
					$category = $_POST['cat'];
					$year = $_POST['year'];
					$month = $_POST['month'];
					$day = $_POST['day'];
					$hour = $_POST['hour'];
					$minute = $_POST['minute'];

					$AuthorID = $_SESSION['ID'];
					$AuthorName = $_SESSION['name'];

					$phptime = mktime($hour,$minute,0,$month,$day,$year); //[h:m:s:mês:dia:ano]
					$mysqltime = date ('Y-m-d H:i:s', $phptime);


					$query = "INSERT INTO `blog_post`(`title`, `content`, `user_ID`, `date`, `blog_category_ID`, `image_name`) 
                                            VALUES ('$title','$content','$AuthorID','$mysqltime', '$category', '$name')";
					
					if(mysqli_query($conn, $query)){
						echo 1;
					}else{
						echo mysqli_error($conn);
					}
	            }    
        }
	}
	
	


	/*------------ Edit blog posts ----------------*/

	if(isset($_POST['editBlogPost'])){

	    if(isset($_SESSION['role'])){
	        if($_SESSION['role'] != 'admin'){
	            echo -1;
	            return;
            }
        }else{
	        return;
        }

		if(isset($_FILES["file"]["name"])){
	            $test = explode(".", $_FILES["file"]["name"]);
	            $extension = end($test);
	            if($extension != 'png' && $extension != 'jpg' && $extension != 'jpeg' && $extension != 'gif'){
	            	echo 'Invalid image file ' . $extension;
	            }else{
	            	$name = rand(1, 9999) .'_'. $_FILES["file"]["name"];
		            $location = '../uploads/'.$name;
		            move_uploaded_file($_FILES["file"]["tmp_name"], $location);

					$ID = $_POST['ID'];
					$title = $_POST['title'];
					$content = $_POST['content'];
					$category = $_POST['cat'];
					$year = $_POST['year'];
					$month = $_POST['month'];
					$day = $_POST['day'];
					$hour = $_POST['hour'];
					$minute = $_POST['minute'];

					$phptime = mktime($hour,$minute,0,$month,$day,$year); //[h:m:s:mês:dia:ano]
					$mysqltime = date ('Y-m-d H:i:s', $phptime);

					$query = "UPDATE `blog_post` SET `title`='$title',`content`='$content',`date`='$mysqltime',`blog_category_ID`='$category', `image_name`='$name' WHERE `ID`='$ID'";

					if(mysqli_query($conn, $query)){
						echo 0;
					}else{
						echo 'Could not edit blog post. ' . mysqli_error($conn);
					}
	            }
	            
		}else{
            $ID = $_POST['ID'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            $category = $_POST['cat'];
            $year = $_POST['year'];
            $month = $_POST['month'];
            $day = $_POST['day'];
            $hour = $_POST['hour'];
            $minute = $_POST['minute'];

            $phptime = mktime($hour,$minute,0,$month,$day,$year); //[h:m:s:mês:dia:ano]
            $mysqltime = date ('Y-m-d H:i:s', $phptime);

            $query = "UPDATE `blog_post` SET `title`='$title',`content`='$content',`date`='$mysqltime',`blog_category_ID`='$category' WHERE `ID`='$ID'";

            if(mysqli_query($conn, $query)){
                echo 0;
            }else{
                echo 'Could not edit blog post. ' . mysqli_error($conn);
            }
        }

	}

	/*---------------------------------*/
	/*---------  Forum Page -----------*/
	/*---------------------------------*/
	//			   (dashboard)

	/*---------- Create new forum topic ---------------*/

	if(isset($_POST['dashboard_createForumTopic'])){

	    if(isset($_SESSION['role'])){
	        if($_SESSION['role'] != 'admin'){
	            echo -2;
	            return;
            }
        }else{
	        return;
        }

		$topic = $_POST['topic'];
		$descp = $_POST['descp'];


        $query = "INSERT INTO `forum_topic`( `name`, `description`) VALUES ('".$topic."', '".$descp."')";

        if(mysqli_query($conn, $query)){
            echo mysqli_insert_id($conn);
        }else{
            echo mysqli_error($conn);
        }

	}

	/*------------ Show forum topics ---------------- */
	if(isset($_GET['dashboard_showForumTopics'])){
		$query = "SELECT * FROM forum_topic";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			
			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);              
			echo $callbackJson;

		}else{
			//transmit error back
		}
	}

	/*---------------------------------*/
	/*---------------------------------*/
	/*---------- FORUM THREAD ------------*/
	/*---------------------------------*/
	/*---------------------------------*/

	if(isset($_POST['terminateThread'])){
		$threadID = $_POST['threadID'];

		$query = "UPDATE `forum_thread` SET `closed`=1 WHERE `ID` = '$threadID'";

		if(mysqli_query($conn, $query)){
			echo 1;
		}else{
			echo 0;
		}
	}


	/*---------------------------------*/
	/*---------------------------------*/
	/*---------- Blog Page ------------*/
	/*---------------------------------*/
	/*---------------------------------*/

	if(isset($_GET['getBlogPosts'])){

		$category = $_GET['cat'];

		if($category == 'all'){
			$query = "SELECT B.ID, B.title, B.content, B.date, B.image_name, B.user_ID, U.name as author_name, C.name AS category_name
                        FROM blog_post B, user U, blog_category C 
                        WHERE B.user_ID = U.ID AND C.ID = B.blog_category_ID
                        ORDER BY B.date DESC";
		}else{
			$query = "SELECT B.ID, B.title, B.content, B.date, B.image_name, B.user_ID, U.name as author_name, C.name AS category_name
                        FROM blog_post B, user U, blog_category C 
                        WHERE B.user_ID = U.ID 
                        AND C.ID = B.blog_category_ID
                        AND C.ID = '$category'
                        ORDER BY B.date DESC";
		}

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){
			
			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);              
			echo $callbackJson;

		}else{
			$callbackObj->error = "There is no blog posts available";
			$callbackJson = json_encode($callbackObj);              
			echo $callbackJson;
		}

	}


		/*-------------------------------------*/
		/*--------- Blog Post Page ------------*/
		/*-------------------------------------*/
		//			   (blog page)

		//Coment blog post
		if(isset($_POST['commentBlogPost'])){
			$Content = $_POST['commentContent'];
			$PostID = $_POST['postID'];
			$AuthorID = $_SESSION['ID'];
			$AuthorName = $_SESSION['name'];

			$query = "INSERT INTO `blog_comment`(`content`, `blog_post_ID`, `user_ID`) 
                        VALUES ('$Content','$PostID','$AuthorID')";

			if(mysqli_query($conn, $query)){
				echo $AuthorID;
			}else{
				echo 0;
			}

		}


	/*---------------------------------*/
	/*---------------------------------*/
	/*--------- Forum System ----------*/
	/*---------------------------------*/
	/*---------------------------------*/

	//Submit new forum thread

	if(isset($_POST['forum_SubmitNewThread'])){

		$topic = $_POST['topic'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$authorName = $_SESSION['name'];
		$authorID = $_SESSION['ID'];
		$file_name = '';

		if(isset($_FILES['file']['name'])){
			$test = explode(".", $_FILES["file"]["name"]);
            $extension = end($test);
        	$file_name = rand(1, 9999) .'_'. $_FILES["file"]["name"];
            $location = '../uploads/attachments/'.$file_name;
            move_uploaded_file($_FILES["file"]["tmp_name"], $location);
		}

		//Process the information to avoid sql injections
		if($topic == '')
			echo 1;
		else
			$topic = ValidatedForm($topic);

		if($title == '')
			echo 1;
		else
			$title = ValidatedForm($title);

		if($content == '')
			echo 1;
		else
			$content = ValidatedForm($content);

		if($topic!='' && $title != '' && $content != ''){		//means the data has been processed


				$query = "INSERT INTO `forum_thread`(`user_ID`, `forum_topic_ID`, `title`, `content`, `file`) 
                            VALUES ('".$authorID."','".$topic."','".$title."','".$content."', '".$file_name."')";
			

			if(mysqli_query($conn, $query) > 0){
				$threadID = intval(mysqli_insert_id($conn));
				echo $threadID;
				mysqli_close($conn);
				return;
			}else{
				echo mysqli_error($conn);
                mysqli_close($conn);
				return;
			}

		}
	}

	//Edit forum thread
	if(isset($_POST['forum_EditThread'])){
		$topic = $_POST['topic'];
		$title = $_POST['title'];
		$content = $_POST['content'];
		$threadID = $_POST['threadID'];

		if($topic == ''){
			echo 1;
			return;
		}else
			$topic = ValidatedForm($topic);
		

		if($title == ''){
			echo 1;
			return;
		}else
			$title = ValidatedForm($title);
		

		if($content == ''){
			echo 1;
			return;
		}else
			$content = ValidatedForm($content);

		$query = "UPDATE forum_thread SET forum_topic_ID = '$topic', title='$title', content='$content' WHERE ID ='$threadID'";

		if(mysqli_query($conn, $query)){
			echo 0;
		}else{
			echo mysqli_error($conn);
		}

		
	}

	//Reply to thread
	if(isset($_POST['forum_reply_thread'])){
		$authorID = $_SESSION['ID'];
		$authorName = $_SESSION['name'];
		$content = $_POST['content'];
		$threadID = $_POST['threadID'];

		if(isset($_FILES["file"]["name"])){
	            $test = explode(".", $_FILES["file"]["name"]);
	            $extension = end($test);
            	$file_name = rand(1, 9999) .'_'. $_FILES["file"]["name"];
	            $location = '../uploads/attachments/'.$file_name;
	            move_uploaded_file($_FILES["file"]["tmp_name"], $location);
	        	
        }

		if($authorID == '' || $authorName == '' || $content == '' || $threadID == '' || !is_numeric($threadID)){
			echo 1;
		}else{
			$content = ValidatedForm($content);

			if(!isset($_POST['replyTo'])){
				if(!isset($_FILES['file']['name']))
					$query = "INSERT INTO `forum_reply`( `forum_thread_ID`, `user_ID`, `content`, `file`) 
                                VALUES ('$threadID','$authorID','$content', '')";
				else
					$query = "INSERT INTO `forum_reply`( `forum_thread_ID`, `user_ID`, `content`, `file`)
                                                VALUES ('$threadID','$authorID','$content', '$file_name')";
			}else{
				$replyTo = $_POST['replyTo'];

				if(!isset($_FILES['file']['name']))
					$query = "INSERT INTO `forum_reply`( `forum_thread_ID`, `user_ID`, `content`, `in_reply_to_ID`, `file`)
                                                VALUES ('$threadID','$authorID','$content', '$replyTo', '')";
				else
					$query = "INSERT INTO `forum_reply`( `forum_thread_ID`, `user_ID`, `content`, `in_reply_to_ID`, `file`) 
                                                VALUES ('$threadID','$authorID','$content', '$replyTo', '$file_name')";
			}


			if(mysqli_query($conn, $query)){
				echo 1;
			}else{
				echo mysqli_error($conn);

			}
		}
	}

	//Show replies
	if(isset($_GET['forum_showThreadReplies'])){
		$threadID = $_GET['threadID'];
		$query = "SELECT FR.ID, FR.content, FR.forum_thread_ID, FR.file, FR.date, FR.in_reply_to_ID, FR.user_ID, U.name 
                    FROM forum_reply FR, user U 
                    WHERE FR.forum_thread_id = '$threadID'
                    AND FR.user_ID = U.ID
                    ORDER BY FR.date ASC";	//Get replies

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);
			echo $callbackJson;

		}else{
			$callbackObj->error = "Could not load replies.";
			$callbackJson = json_encode($callbackObj);
			echo $callbackJson;
		}
	}

	//Get if user has a session initiated
	if(isset($_GET['check_user_session'])){

		if(isset($_SESSION['ID']))
			echo 0;
		else
			echo 1;
	}

	/*---------------------------------*/
	/*---------------------------------*/
	/*----------   PROFILE   ----------*/
	/*---------------------------------*/
	/*---------------------------------*/

	if(isset($_GET['profile_showUserForumThreads'])){
		error_reporting(E_ERROR | E_PARSE);

		$userID = $_GET['id'];

		$query =  "SELECT * FROM `forum_threads` WHERE author_id = '$userID' ORDER BY date DESC";

		$result = mysqli_query($conn, $query);
		
		if(mysqli_num_rows($result) > 0){
			
			$row = mysqli_fetch_all($result);
			$callbackJson = json_encode($row);
			$callbackJson->id = $userID;              
			echo $callbackJson;

		}else{
			$callbackObj->error = "You have no threads created.";
			$callbackJson = json_encode($callbackObj);              
			echo $callbackJson;
		}
	}

	if(isset($_POST['profile_editInfo'])){

		$ID = $_SESSION['ID'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$newPass = $_POST['newPass'];
		$newPass2 = $_POST['newPass2'];

		//Get current hashed pass for verification
		$query = "SELECT  `password` FROM `user` WHERE `ID` = '$ID'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			while($row = mysqli_fetch_assoc($result)){

				$hashedPassword = $row['password'];
			}

		}else{
			echo 'Could not query database';
		}

		//Check if email is already registered

		if($email != $_SESSION['email']){
			$query = "SELECT  email FROM `user` WHERE `email` = '$email'";

			$result = mysqli_query($conn, $query);

			if(mysqli_num_rows($result) > 0){
				echo 'Email already registered to another account';
				return;
			}
		}
		


		if(password_verify($password, $hashedPassword)){

			if(!empty($email)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$email = ValidatedForm($email);
				}else{
					echo 'Invalid email.';
					return;
				}
			}
			

			if(!empty($newPass)){
				if($newPass2 == $newPass ){
					$newPass = ValidatedForm($newPass);
					$hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
				}else{
					echo 'Passwords do not match';
					return;
				}
			}
			

			

			if(!empty($name) && !empty($email) && !empty($password) && !empty($newPass)){

				$query = "UPDATE `user` SET `email`='$email' , `name`='$name',`password`='$hashedPass' WHERE `ID` = '$ID'";

			}else if(!empty($name) && !empty($email)){

				$query = "UPDATE `user` SET `email`='$email' , `name`='$name' WHERE `ID` = '$ID'";

			}else if(!empty($email)){

				$query = "UPDATE `user` SET `email`='$email' WHERE `ID` = '$ID'";

			}else if(!empty($name)){

				$query = "UPDATE `user` SET `name`='$name' WHERE `ID` = '$ID'";

			}else if(!empty($newPass)){

				$query = "UPDATE `user` SET `password`='$hashedPass' WHERE `ID` = '$ID'";

			}else{
				echo 'Empty fields';
				return;
			}

			if(mysqli_query($conn, $query)){
				echo 0;
			}else{
				echo 'Could not submit your information.';
			}
		}else{
			echo 'Wrong password';
		}

	}

	if(isset($_POST['profile_submitBio'])){
		$content = $_POST['content'];
		$ID = $_SESSION['ID'];

		if(!empty($content)){
			$content = ValidatedForm($content);

			$query = "UPDATE `user` SET `bio`='$content' WHERE `ID` = '$ID'";

			if(mysqli_query($conn, $query)){
				echo 0;
			}else{
				echo 'Something went wrong, please try again';
			}

		}else{
			echo 'Can not submit empty bio';
		}
	}

	if(isset($_GET['profile_showBio'])){

		$ID = $_GET['id'];

		$query = "SELECT `bio` FROM `user` WHERE `ID` = '$ID'";

		$result = mysqli_query($conn, $query);

		if(mysqli_num_rows($result) > 0){

			while($row = mysqli_fetch_assoc($result)){

				$bio = $row['bio'];
				echo $bio;
			}

		}else{
			echo 1;
		}
	}


?>