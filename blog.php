<?php

include 'includes/header.php';



?>

<div class="container">
	
	<h1>Blog</h1>
	<hr>
	<br><br>
	<h2>Newest posts</h2>

	<br>

    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
	          
<?php 
	$query = "SELECT *, DATE(date) > DATE(NOW()) AS unreleased FROM blog_post ORDER BY date DESC LIMIT 3";
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
		    if($row[$i][7]){
?>
            <div class="carousel-item <?php if($i == 0) echo 'active'?>" style="height: 40vh;">
                <img src="uploads/<?php echo $row[$i][4];  ?>" class="d-block w-100">
                <div class="carousel-caption d-none d-md-block">
                    <h5><a href="blog_post.php?bpid=<?=$row[$i][0]?>"><?=$row[$i][1]?></a></h5>
                    <p class="blog_post_preview_content" style="max-height: 80px"><?=$row[$i][2]?></p>
                </div>
            </div>
	        
<?php       }
		}
	}

?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
  	<br><hr>
  <div class="container">
    <div class="row">
      <div class="col col-3">
      	<h3>Categories</h3>
        <select name="Category" id="blogpage_category_select" class="form-select">
			<option value="all">All</option>
<?php
	$query = "SELECT * FROM `blog_category`";	//Get categories
	$result = mysqli_query($conn, $query);

	if(mysqli_num_rows($result) > 0){

		$row = mysqli_fetch_all($result);
		for($i = 0; $i < count($row); $i++){
			?>

			<option value='<?php echo $row[$i][0]; ?>'><?php echo $row[$i][1]; ?></option>


<?php
			// INDEX LIST: 0-ID, 1-category name
		}

	}
?>
	</select>

       
      </div>
      <div class="col col-9" id="blog_posts_container">
        
      </div>
      <span id='blog_pagination' style="margin-bottom: 120px"></span>
    </div>
  </div>


</div>

<?php

include 'includes/footer.php';

?>

