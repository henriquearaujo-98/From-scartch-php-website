$( document ).ready(function() {
	/*---------------------------------------*/
	/*-----------FRONNT END------------*/
	/*---------------------------------------*/
	let showSettings = false;
	$('#profile_settings_trigger').on('click', ()=>{

		var div = $('#profile_show_account_settings');
		var button = $('#profile_settings_trigger');

		if(showSettings == false){
			div.slideDown();
			button.css({
				'background': '#db4646',
				'transition': 'background 0.5s'});
			showSettings = true;
		}else{
			div.slideUp();
			button.css({
				'background': '#3898EC',
				'transition': 'background 0.5s'});
			showSettings = false;
		}

	});

	//Handle display of comments and replies
	function showMoreComments(btn_prefix, id, elPrefix, total){

		$("#"+btn_prefix+id).on('click', ()=>{
			var i = id;
			for(i = id; i < (id+3); i++){
				$("#"+elPrefix+i).fadeIn();
			}
			if(i < total){
				$("#"+btn_prefix+id).hide();
				$("#"+btn_prefix+i).show();
			}else{
				$("#"+btn_prefix+id).hide();
				$("#"+btn_prefix+i).hide();
			}
		});
	}


	/*---------------------------------------*/
	/*-----------BACK END------------*/
	/*---------------------------------------*/

	/*---------------------------------------*/
	/*-----------Common functions------------*/
	/*---------------------------------------*/
	$.urlParam = function(name){			//get url parameter
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		return results[1] || 0;
	}

	var filename = window.location.href.split('/').pop(); //get the current file the client is viewing

	switch(filename){										//Call diferent functions to get data available when a certain page is loaded
		case 'dashboard.php': dashboard_showBlogPosts();
								dashboard_showBlogCategories(); 
									dashboard_showForumTopics();
										break;
		case 'blog.php': showBlogPreview(); break;
		case 'index.php': index_showTestimonials(); break;
	}

	if(filename.includes('forum_thread.php')){ 	//Call diferent functions to get data available when a forum thread is loaded
		threadID = $.urlParam('threadID');
		DeleteFromTable_EventListener('forum_thread', threadID, "admin_delete_thread_", 'admin_delete_thread_');
		forum_showThreadReplies();
		terminateThread();

	}else if(filename.includes('profile.php')){
		profile_showUserForumThreads();
	}
	
	function Message(color, text){
		$("#errorMSG span").text(text);
		$("#errorMSG").slideDown();
		$("#errorMSG").css("background-color",color);

		setTimeout(()=>{
			DissmissMessage();
		}, 5000);
	}

	function DissmissMessage(){
		$("#errorMSG").slideUp();
	}

	function emailValidation (email) {
  		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
	}

	

	/*------------- DASHBOARD ----------------*/

	//Deletes from any table, given a key
	function DeleteFromTable_EventListener(table, id, HTMLprefix_button, HTMLprefix_element){
		
		$("#"+HTMLprefix_button+id).on('click',function(){
			console.log("deleting");
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					deleteFromTable : 1,
					table : table,
					id : id
				},

				success: (res)=>{
					console.log(res);
					if(res == -1){
						Message('red', 'You do not have permission to complete this action.');
						return;
					}
					if(res != 0){
						$("#"+HTMLprefix_element+id).slideUp();
						Message('green', 'Record deleted');
					}else{
						Message('red', 'Could not find record');
					}
				},
				error: (jqxhr, status, exception)=> {
            		alert('Exception:', exception);
         		}
			});

		});
	}

	function dashboard_EditUser(prefix, id, name, email, permission){

		$("#"+prefix+id).on('click', ()=>{
				parentEl = $("#dashboard_search_user_results");

				parentEl.empty();

				parentEl.append("<tr id='user_info_id_"+id+"' class='dashboard_disposable_user_search_result'><td>"+
   								id
   								+"</td><td>"+
   								"<input type='text' id='dashboard_edit_user_name_"+id+"' value="+name+" placeholder='"+name+"'>"
   								+"</a></td><td>"+
   								"<input type='text' id='dashboard_edit_user_email_"+id+"' value ="+email+" placeholder='"+email+"'>"
   								+"</td><td>"+
   								"<select id='dashboard_edit_user_permission_"+id+"'>"+
   										"<option value='0'>Admin</option>"+
   										"<option value='1'>Mod</option>"+
										"<option value='2'>User</option>"+
   								"</select>"
   								+"</td><td>"+
								"<input type='text' id='dashboard_edit_user_pw_"+id+"' placeholder='Redefinir Senha'>"+
								"<br>"
								+"</td><td>"  
								+"<button id='dashboard_submit_user_edit_"+id+"' class='btn btn-primary' style='color: cornflowerblue'>Submit</button>"+		//Opção de edição do utilizador
								"<button id='dashboard_cancel_user_edit_"+id+"' class='btn btn-danger' style='color: red'>Cancel</button>"+
								"<tr style='text-align: center;'>"+
								"<td></td> <td></td> <td></td> <td></td>"+
								"<td style='width: 60px;'><small>You can leave this field blank if you don't wish to change the password</small><td>"+
								"</tr>");

				if(permission == 'admin')
					$("#dashboard_edit_user_permission_"+id+" [value=0]").attr("selected", true);
				else if(permission == 'mod')
					$("#dashboard_edit_user_permission_"+id+" [value=1]").attr("selected", true);
				else if(permission == 'user')
					$("#dashboard_edit_user_permission_"+id+" [value=2]").attr("selected", true);


				$("#dashboard_cancel_user_edit_"+id).on('click', ()=>{
					parentEl.empty();
				});

				$("#dashboard_submit_user_edit_"+id).on('click', ()=>{

					var newName = $("#dashboard_edit_user_name_"+id).val();
					var newEmail = $("#dashboard_edit_user_email_"+id).val();
					var newPermi = $("#dashboard_edit_user_permission_"+id).val();
					var newPass = $("#dashboard_edit_user_pw_"+id).val();
					
					if(newName == '')
						Message('red', "Please fill in the user's name");
					else if(newEmail == '')
						Message('red', "Please fill in the user's email");
					else{
						$.ajax({

							url: './includes/functions.php',
							method: 'POST',
							data:{
								dashboard_EditUser : 1,
								id : id,
								name: newName,
								email: newEmail,
								permission: newPermi,
								password: newPass
							},
							success:(res)=>{
								console.log(res);
								if(res == -1){
									Message('red', 'You do not have permission to complete this action.');
									return;
								}
								if(res==0){
									Message('green','User edited successfuly.');
									parentEl.empty();
								}else{
									Message('red', 'Could not edit user. Please try again.');
								}
							}

						});
					}
				});
		});					
	}

	function AcceptUser(id, name, email, permission){
		console.log("Accepted user with id: " + id + " name: "+name +" email: "+email+" permission "+ permission);
		$("#accept_user_"+id).on('click', function(){
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					AcceptUser : 1,
					data: id
				},
				success: (res)=>{
					console.log(res);
					if(res == 1){
						Message('green', 'User added.');
						let el = $('#options_for_'+id);
						el.empty();
						el.append("<small><a href='#dashboard_user_posts' id='dashboard_show_post_user_"+id+"'>Posts </a></small>"+		//Opção de edição do utilizador
   								"<small><a href='#' id='dashboard_edit_user_"+id+"'>Edit </a></small>"+
   								"<small><a href='#' id='remove_user_"+id+"'>Remove</a></small>");
							
						$("#remove_user_"+id).on('click', DeleteFromTable_EventListener('user',id, "remove_user_", "user_info_id_"));	//Construir um listener para depois poder eliminar o utilizador
						
						$("#dashboard_show_post_user_"+id).on('click', dashboard_showUserForumThreads(res ['Id']));
						
						$("#dashboard_edit_user_"+id).on('click', dashboard_EditUser('dashboard_edit_user_', 
																								id, 
																									name, 
																										email, 
																											permission));
					}
					else if(res == -1){
						Message('red', 'You do not have permission to complete this action.');
					}else
						Message('red', 'Could not add user ');
				}
			});
		});
		
	}








	//--------Show all blog posts -----//
	//and make the seperation between those who weren't released yet and those who are already available to the public 
	function dashboard_showBlogPosts(){
		var futureBP_parentEl = $("#dashboard_BPost_show_unreleased");
		var allBP_parentEl = $("#dashboard_BPost_show_released");

		$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					dashboard_showBlogPosts : 1,
				},
				dataType : 'json',
				success: (res)=>{
					console.log(res);
					for(var i = res.length - 1; i >= 0; i--){						//INDEX LIST: 0-ID, 1-Title, 2-Content, 3-author id, 4-author name, 5-date, 6-category

						var currDate = new Date();			//Checking the date
						var postDate = new Date(res[i][3]);

						if(currDate < postDate){	//show blog posts to be released
							futureBP_parentEl.append("<div class='blog-post-preview-card' id='blog_post_preview_card_"+res[i][0]+"'>"+
							"<h3><a href='blog_post.php?bpid="+res[i][0]+"'>"+res[i][1]+"</a></h3>"+
							"<p>Author: "+res[i][6]+"</p>"+
							"<div class='blog_post_preview_content'>"+res[i][2]+"</div>"+
							"<div>"+res[i][8]+"</div>"+
							"<div>"+res[i][3]+"</div>"+
							"<a class='btn btn-outline-primary' href='blog_post.php?bpid="+res[i][0]+"&editbp=true'>Edit</a>"+
							"<button class=\"btn btn-outline-danger\" id='delete_post_"+res[i][0]+"'>Delete post</button>"
							+"</div><hr>");
							$("#delete_post_"+res[i][0]).on('click', DeleteFromTable_EventListener('blog_post',res[i][0], "delete_post_", "blog_post_preview_card_"));
						}else{

							//Show all released blogposts
							allBP_parentEl.append("<div class='blog-post-preview-card' id='blog_post_preview_card_"+res[i][0]+"'>"+
							"<h4><a href='blog_post.php?bpid="+res[i][0]+"'>"+res[i][1]+"</a></h4>"+
							"<p>Author: "+res[i][6]+"</p>"+
							"<div class='blog_post_preview_content'>"+res[i][2]+"</div>"+
							"<div>"+res[i][8]+"</div>"+
							"<div>"+res[i][3]+"</div>"+
							"<a class='btn btn-outline-primary' href='blog_post.php?bpid="+res[i][0]+"&editbp=true'>Edit</a> "+
							"<button  class='delete-btn btn btn-outline-danger' id='delete_post_"+res[i][0]+"'>Delete post</button>"
							+"</div><hr>");
							$("#delete_post_"+res[i][0]).on('click', DeleteFromTable_EventListener('blog_post',res[i][0], "delete_post_", "blog_post_preview_card_"));
						}
						
					}
				}
  		 });
	}

	function dashboard_showBlogCategories(){
		var parentEl = $("#dashboard_show_blog_categories");
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					dashboard_showBlogCategories : 1,
				},
				dataType: 'json',
				success: (res)=>{
					if(res['error'])
						parentEl.append("<small>There are no categories to show. Please create one.</small>");
					else{
						for(var i = res.length - 1; i >= 0; i--){
							parentEl.append("<tr class='dashboard_categories_rows' id='dashboard_show_cat_"+res[i][0]+"'>"+
												"<th><div style='padding: 15px;' id='dashboard_show_category_"+res[i][0]+"'>"+res[i][1]+"</div></th>"+
												"<th><i id='dashboard_delete_category_"+res[i][0]+"' style='color: red; font-size: 24px;' class=\"fas fa-times-circle\"></i><tr>"+
											"</tr>");
						$("#dashboard_delete_category_"+res[i][0]).on('click', DeleteFromTable_EventListener('blog_category',res[i][0], "dashboard_delete_category_", "dashboard_show_cat_"));
						}
					}
				}
			});
	}

	function dashboard_showForumTopics(){
		var parentEl = $("#dashboard_show_forum_topics");
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					dashboard_showForumTopics : 1,
				},
				dataType: 'json',
				success: (res)=>{
					console.log(res);
					if(res['error'])
						parentEl.append("<small>There are no topics to show. Please create one.</small>");
					else{
						for(var i = res.length - 1; i >= 0; i--){
							parentEl.append("<tr class='dashboard_disposable_user_search_result' id='dashboard_disposable_forum_topic_'>"+
												"<td  id='dashboard_show_topic_"+res[i][0]+"'>"+res[i][1]+"</td>"+
												"<td  id='dashboard_show_topic_dscp_"+res[i][0]+"'>"+res[i][2]+"</td>"+
												"<td><i id='dashboard_delete_topic_"+res[i][0]+"' style='color: red; font-size: 24px; cursor: pointer' class=\"fas fa-trash\"></i></td>"
											+"</tr>");
						$("#dashboard_delete_topic_"+res[i][0]).on('click', DeleteFromTable_EventListener('forum_topic',res[i][0], "dashboard_delete_topic_", "dashboard_disposable_forum_topic_"));
						}
					}
				}
			});
	}

	//-------------- Show user forum threads in user search results --------------//
	function dashboard_showUserForumThreads(id){
		$("#dashboard_show_post_user_"+id).on('click',()=>{
			var parentEl = $("#dashboard_user_threads");
			var userID = id;

			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					dashboard_showUserForumThreads : 1,
					userID : userID
				},
				dataType: 'json',
				success: (res)=>{
					if(!res['error']){
						parentEl.empty();
						for(var i = res.length - 1; i >= 0; i--){
							parentEl.append("<div class='dashboard_disposable_user_search_result' id='dahsboard_user_search_show_thread_"+res[i][0]+"'>"+
								"<h4><a href='forum_thread.php?threadID="+res[i][0]+"'>"+res[i][4]+"</a>"+
								"</h4><small>"+res[i][6]+"</small>"+
								"<button id='dashboard_user_search_delete_thread_id_"+res[i][0]+"'>Delete thread</button>"+
								"<hr></hr>"
								+"</div>");
							$('#dashboard_user_search_delete_thread_id_'+res[i][0]).on('click', DeleteFromTable_EventListener('forum_threads', res[i][0], 'dashboard_user_search_delete_thread_id_', 'dahsboard_user_search_show_thread_'));
						}
					}else{
						parentEl.empty();
						parentEl.append("<div>"+res['error']+"</div>");
					}
				}
			});
		});
	}

	/*---------------------------------------*/
	/*------------PROFILE----------------*/
	/*---------------------------------------*/
	function profile_showUserForumThreads(){	//This is only used if a user is view it's own profile
		var parentEl = $("#profile_show_forum_threads");
		var id = $.urlParam('userid');
		$.ajax({
			url: './includes/functions.php',
			method: 'GET',
			data:{
				profile_showUserForumThreads : 1,
				id: id
			},
			dataType: 'json',
			success: (res)=>{
				if(!res['error']){
					parentEl.empty();
					for(var i in res){
						parentEl.append("<div class='dashboard_disposable_user_search_result' id='dahsboard_user_search_show_thread_"+res[i][0]+"'>"+
							"<h4><a href='forum_thread.php?threadID="+res[i][0]+"'>"+res[i][4]+"</a>"+
							"</h4><small>"+res[i][6]+"</small>"+
							"<button style='margin-left: 40px;' class='w-button' id='dashboard_user_search_delete_thread_id_"+res[i][0]+"'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></button>"+
							"<hr></hr>"
							+"</div>");
						$('#dashboard_user_search_delete_thread_id_'+res[i][0]).on('click', DeleteFromTable_EventListener('forum_threads', res[i][0], 'dashboard_user_search_delete_thread_id_', 'dahsboard_user_search_show_thread_'));
					}
				}else{
					parentEl.append("<div>"+res['error']+"</div>");
				}
				console.log(res);
			}
		});
	}

	function profile_showBio(){
		var parentEl = $('#profile_show_bio');
		var id = $.urlParam('userid');
		console.log(id);
		$.ajax({
			url: './includes/functions.php',
			method: 'GET',
			data:{
				profile_showBio: 1,
				id : id
			},
			success:(res)=>{
				if(res != ''){
					parentEl.append(res);
				}else if(res == ''){
					parentEl.append("No information to display");
				}else if(res == 1){
					parentEl.append('Error connecting. Please check your network.');
				}
				console.log(res);
			}
		})

	}

	/*----------------------------------------*/
	/*------------- BLOG ---------------------*/
	/*----------------------------------------*/

	//Show the previews of the blog posts
	function showBlogPreview(){
		
		var cat = $( "#blogpage_category_select" ).val();
  		var parentEl = $("#blog_posts_container");
  		var paginationEl = $("#blog_pagination");
  		var cat = $("#blogpage_category_select").val();
  		parentEl.empty();

  		$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					getBlogPosts : 1,
					cat : cat,
				},
				dataType : 'json',
				success: (res)=>{		//INDEX LIST: 0-ID, 1-title, 2-content, 3-author ID, 4-author name, 5-date, 6-category, 7-image name
					if(!res['error']){
						if(cat === 'all'){
							for(var i = 3; i <= res.length - 1 ; i++){
								var currDate = new Date();
								var postDate = new Date(res[i][5]);

								if(currDate >= postDate){


										parentEl.append("<div id='blog_prev_"+res[i][0]+"'><div style='margin-top: 60px;'>"+
											"<h1 class='heading-12'><a href='blog_post.php?bpid="+res[i][0]+"'>"+res[i][1]+"</a></h1>"+
											"<div class='div-block-2 blog-preview-card-img' id='blog_post_image_"+res[i][0]+"'></div>"+
											"<div class='blog_post_preview_content' style='width = 100%; position: relative; height = 300px;'>"+res[i][2]+"</div>"+
											"<br>"+
											"<span><a href='blog_post.php?bpid="+res[i][0]+"'>Continue reading</a> </span>"+
											"<p>Author:<a href='profile.php?userid="+res[i][5]+"'>"+res[i][6]+"</a></p>"+
											"<small>"+res[i][7]+"</small><br>"+
											"<small>"+res[i][3]+"</small><hr></hr>"+
											"</div></div>");



									$("#blog_post_image_"+res[i][0]).css({'background': "url('uploads/"+res[i][4]+"')",
										'background-size': 'cover',
										'background-position': 'center',
										'height' : '300px'});


								}
							}

						}else{
							for(var i = 0; i <= res.length - 1 ; i++){
								var currDate = new Date();
								var postDate = new Date(res[i][5]);

								if(currDate >= postDate){


										parentEl.append("<div id='blog_prev_"+res[i][0]+"'><div style='margin-top: 60px;'>"+
											"<h1 class='heading-12'><a href='blog_post.php?bpid="+res[i][0]+"'>"+res[i][1]+"</a></h1>"+
											"<div class='div-block-2 blog-preview-card-img' id='blog_post_image_"+res[i][0]+"'></div>"+
											"<div class='blog_post_preview_content' style='width = 100%; position: relative; height = 300px;'>"+res[i][2]+"</div>"+
											"<br>"+
											"<span><a href='blog_post.php?bpid="+res[i][0]+"'>Continue reading</a> </span>"+
											"<p>Author:<a href='profile.php?userid="+res[i][5]+"'>"+res[i][6]+"</a></p>"+
											"<small>"+res[i][7]+"</small><br>"+
											"<small>"+res[i][3]+"</small><hr></hr>"+
											"</div></div>");



									$("#blog_post_image_"+res[i][0]).css({'background': "url('uploads/"+res[i][4]+"')",
										'background-size': 'cover',
										'background-position': 'center',
										'height' : '300px'});


								}
							}
						}
					}else{
						Message('red', res['error']);
					}


					console.log(res);
				},
				error: function(jqxhr, status, exception) {
            		Message('red', 'There are no blog posts available');
         		}
				
  		 });
	}

	/*----------------------------------------*/
	/*------------- FORUM --------------------*/
	/*----------------------------------------*/

	function forum_showThreadReplies(index){
		var repliesLoaded = 0;
		var threadID = $.urlParam('threadID');
		var index = 0;
		console.log(threadID);
		$.ajax({
			url: './includes/functions.php',
			method: 'GET',
			data:{
				forum_showThreadReplies : 1,
				threadID : threadID
			},
			dataType: 'json',
			success: (res)=>{
				var count = Object.keys(res).length;
				console.log(res);
				if(!res['error']){
					var buffer = [];
					for(var i in res){
						console.log(res[i][5]);
						if(!res[i][5]){	//if reply_to_reply_ID is empty
							index++;
							var parentEl = $("#forum-thread-replies");
							if(!res[i][3]) {
								parentEl.append("<div id='reply_num_" + index + "'><div class='forum-reply-thread' id='forum_reply_thread_" + res[i][0] + "'>" +
									"<p class='thread-reply'>" + res[i][1] + "</p>" +
									"<small><a href='profile.php?userid=" + res[i][6] + "'>" + res[i][7] + "</a> at " + res[i][4] + "</small>" +
									"<button id='forum_reply_button_reply_" + res[i][0] + "'><span>&#8680;</span></button>" +
									"<form class='forum-reply-to-reply-form' id='forum_reply_to_reply_form_" + res[i][0] + "'>" +
									"<input type='text' id='forum_reply_content_reply_" + res[i][0] + "'>" +
									"<input type='file' name='file' id='forum_reply_attachment_" + res[i][0] + "'>" +
									"<button id='forum_submit_reply_to_reply_" + res[i][0] + "' class='btn btn-successblog_pagination'>Reply</button>" +
									"</form>"
									+ "</div></div>");
							}else{
								parentEl.append("<div id='reply_num_" + index + "'><div class='forum-reply-thread' id='forum_reply_thread_" + res[i][0] + "'>" +
									"<p class='thread-reply'>" + res[i][1] + "</p>" +
									"<a href='includes/download?file_path=/attachments/"+res[i][3]+"' id='download_from_forum_"+res[i][0]+"'>"+res[i][3]+"</a>"+
									"<small><a href='profile.php?userid=" + res[i][6] + "'>" + res[i][7] + "</a> at " + res[i][4] + "</small>" +
									"<button id='forum_reply_button_reply_" + res[i][0] + "'><span>&#8680;</span></button>" +
									"<form class='forum-reply-to-reply-form' id='forum_reply_to_reply_form_" + res[i][0] + "'>" +
									"<input type='text' id='forum_reply_content_reply_" + res[i][0] + "'>" +
									"<input type='file' name='file' id='forum_reply_attachment_" + res[i][0] + "'>" +
									"<button id='forum_submit_reply_to_reply_" + res[i][0] + "'>Reply</button>" +
									"</form>"
									+ "</div></div>");
							}
							

							if(index == 3){
								parentEl.append("<button id='show_more_btn_"+index+"' class='w-button'>Show more</button>");
							}else if(index % 3 == 0){
								parentEl.append("<button id='show_more_btn_"+index+"' style='display:none;' class='w-button'>Show more</button>");
							}

							showMoreComments("show_more_btn_", index, "reply_num_", count);

							//Submit reply to another reply
	    					ReplyForm_eventListener('forum_reply_button_reply_',
	    												 'forum_reply_to_reply_form_', 
	    												 			res[i][0], 
	    												 				'forum_reply_content_reply_',
	    												 					'forum_submit_reply_to_reply_',
	    												 						'forum_reply_attachment_');

						}else{

							parentEl = $("#forum_reply_thread_"+res[i][5]);
							if(!res[i][3]){
								parentEl.append("<div class='forum-reply-to-reply' id='forum_reply_thread_"+res[i][0]+"'>"+
									"<p class='thread-reply'>"+res[i][1]+"</p>"+
									"<small><a href='profile.php?userid="+res[i][6]+"'>"+res[i][7]+"</a> at "+res[i][4]+"</small>"+
									"<button id='forum_reply_button_reply_"+res[i][0]+"'><span>&#8680;</span></button>"+
									"<form class='forum-reply-to-reply-form' id='forum_reply_to_reply_form_"+res[i][0]+"'>"+
										"<input type='text' id='forum_reply_content_reply_"+res[i][0]+"'>"+
										"<input type='file' name='file' id='forum_reply_attachment_"+res[i][0]+"'>"+
										"<button id='forum_submit_reply_to_reply_"+res[i][0]+"'>Reply</button>"+
									"</form>"
									+"</div>");
    						}else{
    							parentEl.append("<div class='forum-reply-to-reply' id='forum_reply_thread_"+res[i][0]+"'>"+
									"<p class='thread-reply'>"+res[i][1]+"</p>"+
									"<a href='includes/download?file_path=/attachments/"+res[i][3]+"' id='download_from_forum_"+res[i][0]+"'>"+res[i][3]+"</a>"+
									"<small><a href='profile.php?userid="+res[i][6]+"'>"+res[i][7]+"</a> at "+res[i][4]+"</small>"+
									"<button id='forum_reply_button_reply_"+res[i][0]+"'><span>&#8680;</span></button>"+
									"<form class='forum-reply-to-reply-form' id='forum_reply_to_reply_form_"+res[i][0]+"'>"+
										"<input type='text' id='forum_reply_content_reply_"+res[i][0]+"'>"+
										"<input type='file' name='file' id='forum_reply_attachment_"+res[i][0]+"'>"+
										"<button id='forum_submit_reply_to_reply_"+res[i][0]+"'>Reply</button>"+
									"</form>"
									+"</div>");
    						}

    						//Submit reply to another reply
	    					ReplyForm_eventListener('forum_reply_button_reply_',
	    												 'forum_reply_to_reply_form_', 
	    												 			res[i][0], 
	    												 				'forum_reply_content_reply_',
	    												 					'forum_submit_reply_to_reply_',
	    					 							 						'forum_reply_attachment_');	
						}
					}
				}else{
					Message('red', res['error']);
				}


				for(var i = 0; i < index; i++){
					if(i > 2){
						$("#reply_num_"+(i+1)).hide();
					}
					
				}
				
			},
			error: function(jqxhr, status, exception) {
            		Message('red', 'There are no replies to load.');
         	}
		});
	}

	function terminateThread(){
		$("#admin_close_thread_"+threadID).on('click', function(){
			console.log('terminar');
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					terminateThread : 1,
					threadID : threadID
				},
				success: (res)=>{
					if(res == 1){
						Message('green', 'This thread has been closed.');
					}else{
						Message('red', 'We could not close this thread. Please try again later.');
					}
				}
			});
		});
	}
	
	//Submit reply to another reply
	function ReplyForm_eventListener(button_prefix, form_prefix, id, reply_content_prefix, submit_reply_prefix, file_prefix){
		$("#"+form_prefix+id).submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
		 	e.preventDefault();
		});

		$("#"+button_prefix+id).on('click', ()=>{

			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					check_user_session: 1,
				},
				success: (res)=>{
					console.log(res);
					if(res == 0){
						$("#"+form_prefix+id).slideDown();
					}else{
						Message('red', 'You must be logged in to reply.');
						return;
					}
				}
			});
			
		});

		$("#"+submit_reply_prefix+id).on('click', ()=>{

			var content = $("#"+reply_content_prefix+id).val();
			var fileVal = $("#"+file_prefix+id).val();
			threadID = $.urlParam('threadID');

			var formData = new FormData();
			if(fileVal){
				formData.append('file', $('#'+file_prefix+id)[0].files[0]);
				extention = $('#'+file_prefix+id)[0].files[0].name.split('.').pop().toLowerCase();
				size = $('#'+file_prefix+id)[0].files[0].size;

				if(size > 6000000){//6MB
					Message('red', 'The file is more then 6MB');
					return;
				} 
			}
			

			formData.append('forum_reply_thread', 1);
			formData.append('threadID', threadID);
			formData.append('replyTo', id);
			formData.append('content', content);

			if(content == ''){
				Message('red', 'You can not reply without content.');
			}else{
				$.ajax({
					url : './includes/functions.php',
			       	type : 'POST',
			       	data : formData,
			       	processData: false,  // tell jQuery not to process the data
			       	contentType: false,  // tell jQuery not to set contentType
					success: (res)=>{
						console.log(res);
						if(res == 1){
							Message('green', 'Your reply was submitted.');
							setTimeout(()=>{
								 window.location.reload();
								 //res is the id of the new reply. We can use it to scroll to the created comment
							},1000);
						}else{
							Message('red', 'Erro ao submeter mensagem. Por favor tente de novo mais tarde.');
						}
					}

				});
			}
		});
	}

	/*---------------------------*/
	/*-------Index testimonials-------*/
	/*---------------------------*/

	function index_showTestimonials(){
		var parentEl = $("#testemonial_list");
		$.ajax({
			url: './includes/functions.php',
			method: 'GET',
			data:{ index_showTestimonials: 1},
			dataType: 'JSON',
			success:(res)=>{
				if(res['error'] == ''){
					Message('red', res);
				}else{
					for(var i = res.length - 1; i >= (res.length - 4); i--){
							parentEl.append("<li>"+
		      									"<h5 class='heading-2'><a style='text-decoration: none;' href='profile.php?userid="+res[i][1]+"'>"+res[i][2]+"</a></h5>"+
		          								"<blockquote>"+res[i][3]+"</blockquote>"+
        									"</li>");				
					}
				}
			}
		});
	}
	
/*-----------------------------------------------------------------------------------------------------------*/
	/*---------------------------*/
	/*-------Register form-------*/
	/*---------------------------*/

	$("#regform").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#regBTN").on('click', function(){

		var name = $("#regName").val();
		var email = $("#regEmail").val();
		var password = $("#regPassword").val();
		var password2 = $('#regPassword2').val();
		var phone = $('#regPhone').val();
		let imgVal = $("#file_img").val();

		var formData = new FormData();

		if(imgVal){
			formData.append('file', $('#file_img')[0].files[0]);
			extention = $('#file_img')[0].files[0].name.split('.').pop().toLowerCase();
			size = $('#file_img')[0].files[0].size;

			if(size > 6000000){//6MB
				Message('red', 'The file is more then 6MB');
				return;
			} 
			else if(!$('#file_img')[0].files[0]){
				Message('red','Can not publish without an image.');
				return;
			}else if(extention != 'jpg' && extention != 'jpeg' && extention != 'gif' && extention != 'png'){
				Message('red', 'Invalid image file');
				return;
			}
		}
		

		formData.append('signup', 1);
		formData.append('fullname', name);
		formData.append('password', password);
		formData.append('email', email);
		formData.append('phone', phone);

		
		if(name == ""){
			Message('red', 'Please enter your full name.');
		}else if(email == ""){
			Message('red', 'Please enter your email');
		}else if(password == ""){
			Message('red', 'Please enter your desired password');
		}else if(password != password2){
			Message('red', 'The passwords do not match');
		}else if(emailValidation(email) == 0){
			Message('red', 'Please enter a valid email address');
		}else{

			$.ajax({
		       url : './includes/functions.php',
		       type : 'POST',
		       data : formData,
		       processData: false,  // tell jQuery not to process the data
		       contentType: false,  // tell jQuery not to set contentType
		       success: (res)=>{
		       	console.log(res);
		       	$("body").append(res);
		       		if(res != 1){
						Message('green', 'User created successfuly. Redirecting...');
						setTimeout(()=>{
	  						window.location.href = "profile.php?userid=" + res;
						}, 1000);
						
					}else{
						Message('red', 'Something went wrong. Please try again later.');
					}
		       }
			});
		}
	});


	/*---------------------------*/
	/*--------Log in form--------*/
	/*---------------------------*/

	$("#loginform").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#loginBTN").on('click', function(){

		var email = $("#loginEmail").val();
		var password = $("#loginPassword").val();

		if(email == ""){
			Message('red', 'Please enter your email');
		}else if(password == ""){
			Message('red', 'Please enter your password');
		}else if(!emailValidation(email)){
			Message('red', 'Please enter a valid email address');
		}else{
			DissmissMessage();
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					login : 1,
					email : email,
					password : password
				},
				success: function(res){				//O callback do servidor será true se não houver problemas e false e não conseguir completar a operação
					if(res == -1){
						Message('yellow', 'This account is awaiting validation.');
						return;
					}else if(res == 0){
						Message('red', 'Wrong password or email.');
						return;
					}else if(res == -4) {
						Message("red", "Unexpected error. Please contact the server administrator.");
					}else if(res >= 1){
						window.location.href = "profile.php?userid="+res;
						return;
					}
					Message("red", "Unexpected error. Please contact the server administrator.");
				},
				error: function(jqxhr, status, exception) {
            		alert('Exception:', exception);
         		}
			});
		}
	});



	/*---------------------------*/
	/*----------Log out----------*/
	/*---------------------------*/

	$("#logoutBTN").on('click', function(){
		$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					logout : 1,
				},
				success: function(res){
					if(res){
						window.location.replace("index.php");
					}else{
						
						Message('red', 'Could not logout.');
						console.log(res);
					}
				},
				error: function(jqxhr, status, exception) {
            		alert('Exception:', exception);
         		}
		});
	});

	/*---------------------------*/
	/*---------------------------*/
	/*---------DASHBOARD---------*/
	/*---------------------------*/
	/*---------------------------*/

	/*----------------------------*/
	/*-------- User manager ------*/
	/*----------------------------*/
				//dasboard

	//--------Search user form----------//
	
	$("#dashboard_search_user_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

    $("#dashboard_search_user_option").on('change', function(){
    	let option = $("#dashboard_search_user_option").val();
    	let input = $("#dashboard_search_user_data");
    	console.log(option);
    	if(option == 'email' || option == 'name')
    		input.slideDown();
    	else
    		input.slideUp();
    });

	$("#dashboard_search_userBTN").on('click', function(){

		$(".dashboard_disposable_user_search_result").remove();
		let data = $("#dashboard_search_user_data").val();
		let option = $("#dashboard_search_user_option").val();
		let parentEl = $("#dashboard_search_user_results");

		if(option === 'all'){
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					searchUser : 1,
					option : option,
					data : data
				},
				dataType : 'json',
				success: function(res){
					console.log(res);
					if(!res['error']){
						$("#dashboard_manage_user").slideDown();
						var result = [];
						for(var i = res.length - 1; i >= 0; i--){
							result.push([i, res [i]]);

							if(res[i][3] === 'admin')
								var permission = 'Admin';
							else if(res[i][3] === 'mod')
								var permission = 'Mod';
							else
								var permission = 'User';
							console.log(res[i][8]);
							if(res[i][8] == 1){		//User accepted

								parentEl.append("<tr id='user_info_id_"+res[i][0]+"' class='dashboard_disposable_user_search_result'><td>"+
									res[i][0]
									+"</td><td><a href='profile.php?userid="+res[i][0]+"'>"+
									res[i][2]
									+"</a></td><td>"+
									res[i][1]
									+"</td><td>"+
									permission
									+"</td><td id='options_for_"+res[i][0]+"'>"
									+"<small><a href='#dashboard_user_posts' id='dashboard_show_post_user_"+res[i][0]+"' class='dashboard-icon'><i class=\"fas fa-copy\" style='color: lightblue; font-size: 24px;'></i> </a></small><small><a href='#' id='dashboard_edit_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: cornflowerblue; font-size: 24px;' class=\"fas fa-pencil-alt\"></i> </a></small><small><a href='#' id='remove_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>");

								$("#remove_user_"+res[i][0]).on('click', DeleteFromTable_EventListener('users',res[i][0], "remove_user_", "user_info_id_"));
								$("#dashboard_show_post_user_"+res[i][0]).on('click', dashboard_showUserForumThreads(res [i][0]));
								$("#dashboard_edit_user_"+res[i][0]).on('click', dashboard_EditUser('dashboard_edit_user_',
									res[i][0],
									res[i][2],
									res[i][1],
									res[i][3]));

							}else if(res[i][8] == 0){

								parentEl.append("<tr id='user_info_id_"+res[i][0]+"' class='dashboard_disposable_user_search_result'><td>"+
									res[i][0]
									+"</td><td><a href='profile.php?userid="+res[i][0]+"'>"+
									res[i][2]
									+"</a></td><td>"+
									res[i][1]
									+"</td><td>"+
									permission
									+"</td><td id='options_for_"+res[i][0]+"'>"
									+"<small><a href='#' id='remove_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>"
									+"<small><a href='#' id='accept_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: #2fe0ff' class=\"fas fa-check\"></i></a></small></tr>");

								$("#accept_user_"+res[i][0]).on('click', AcceptUser(res[i][0], res[i][2], res[i][1], res[i][3]));
								$("#remove_user_"+res[i][0]).on('click', DeleteFromTable_EventListener('users',res[i][0], "remove_user_", "user_info_id_"));
								$("#user_info_id_"+res[i][0]).css('backgroundColor', '#ff756b');
							}
						}
						Message('green', 'Found ' + result.length +' results.');


					}else{
						Message('red', res['error']);
					}
				},
				error: function(jqxhr, status, exception) {
					alert('Exception:', exception);
				}
			});
		}
		else if(option === 'email'){
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					searchUser : 1,
					option : option,
					data : data
				},
				dataType: 'json',
				success: function(res){
					console.log(res);
					if(res['error'] == ""){

						if(res['permission'] === 'admin')
							var permission = 'Admin';
						else if(res['permission'] === 'mod')
							var permission = 'Mod';
						else
							var permission = 'User';
						$("#dashboard_manage_user").slideDown();

						if(res['accepted'] == 1){
							parentEl.append("<tr id='user_info_id_"+res['Id']+"' class='dashboard_disposable_user_search_result'><td>"+
   								res['Id']
   								+"</td><td><a href='profile.php?userid="+res['Id']+"'>"+
   								res['name']
   								+"</a></td><td>"+
   								res['email']
   								+"</td><td>"+
   								permission
   								+"</td><td id='options_for_"+res['Id']+"'>"+
   								"<small><a href='#dashboard_user_posts' id='dashboard_show_post_user_"+res['Id']+"' class='dashboard-icon'><i class=\"fas fa-copy\" style='color: lightblue; font-size: 24px;'></i> </a></small>"+		//Opção de edição do utilizador
   								"<small><a href='#' id='dashboard_edit_user_"+res['Id']+"' class='dashboard-icon'><i style='color: cornflowerblue; font-size: 24px;' class=\"fas fa-pencil-alt\"></i> </a></small>"+
   								"<small><a href='#' id='remove_user_"+res['Id']+"' class='dashboard-icon'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>");
							
							$("#remove_user_"+res['Id']).on('click', DeleteFromTable_EventListener('users',res['Id'], "remove_user_", "user_info_id_"));	//Construir um listener para depois poder eliminar o utilizador
							
							$("#dashboard_show_post_user_"+res['Id']).on('click', dashboard_showUserForumThreads(res ['Id']));
							
							$("#dashboard_edit_user_"+res['Id']).on('click', dashboard_EditUser('dashboard_edit_user_', 
																									res['Id'], 
																										res['name'], 
																											res['email'], 
																												permission));
						}else{
							parentEl.append("<tr id='user_info_id_"+res['Id']+"' class='dashboard_disposable_user_search_result'><td>"+
   								res['Id']
   								+"</td><td><a href='profile.php?userid="+res['Id']+"'>"+
   								res['name']
   								+"</a></td><td>"+
   								res['email']
   								+"</td><td>"+
   								permission
   								+"</td><td id='options_for_"+res['Id']+"'>"
   								+"<small><a href='#' id='remove_user_"+res['Id']+"'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>"
   								+"<small><a href='#' id='accept_user_"+res['Id']+"'><i style='color: #2fe0ff' class=\"fas fa-check\"></i></a></small></tr>");

	   							$("#accept_user_"+res['Id']).on('click', AcceptUser(res['Id'], res['name'], res['email'], permission));
	   							$("#remove_user_"+res['Id']).on('click', DeleteFromTable_EventListener('users',res['Id'], "remove_user_", "user_info_id_"));
								$("#user_info_id_"+res['Id']).css('backgroundColor', '#ff756b');
						}
						
					}else{
						$("#dashboard_manage_user").slideUp();
						Message('red', res['error']);
					}
					
				},
				error: function(jqxhr, status, exception) {
            		alert('Exception:', exception);
         		}
			});
		}else if(option === 'name'){
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					searchUser : 1,
					option : option,
					data : data
				},
				dataType : 'json',
				success: function(res){
					console.log(res);
					if(!res['error']){
						$("#dashboard_manage_user").slideDown();
						var result = [];
						for(var i = res.length - 1; i >= 0; i--){
   							result.push([i, res [i]]);

   							if(res[i][3] === 'admin')
   								var permission = 'Admin';
   							else if(res[i][3] === 'mod')
   								var permission = 'Mod';
   							else
   								var permission = 'User';
							console.log(res[i][8]);
   							if(res[i][8] == 1){		//User accepted

   								parentEl.append("<tr id='user_info_id_"+res[i][0]+"' class='dashboard_disposable_user_search_result'><td>"+
   								res[i][0]
   								+"</td><td><a href='profile.php?userid="+res[i][0]+"'>"+
   								res[i][2]
   								+"</a></td><td>"+
   								res[i][1]
   								+"</td><td>"+
   								permission
   								+"</td><td id='options_for_"+res[i][0]+"'>"
   								+"<small><a href='#dashboard_user_posts' id='dashboard_show_post_user_"+res[i][0]+"'><i class=\"fas fa-copy\" style='color: lightblue; font-size: 24px;'></i> </a></small><small><a href='#' id='dashboard_edit_user_"+res[i][0]+"'><i style='color: cornflowerblue; font-size: 24px;' class=\"fas fa-pencil-alt\"></i> </a></small><small><a href='#' id='remove_user_"+res[i][0]+"'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>");
   							
	   							$("#remove_user_"+res[i][0]).on('click', DeleteFromTable_EventListener('users',res[i][0], "remove_user_", "user_info_id_"));
								$("#dashboard_show_post_user_"+res[i][0]).on('click', dashboard_showUserForumThreads(res [i][0]));
								$("#dashboard_edit_user_"+res[i][0]).on('click', dashboard_EditUser('dashboard_edit_user_', 
																									res[i][0], 
																										res[i][2], 
																											res[i][1], 
																												res[i][3]));

   							}else if(res[i][8] == 0){

   								parentEl.append("<tr id='user_info_id_"+res[i][0]+"' class='dashboard_disposable_user_search_result'><td>"+
   								res[i][0]
   								+"</td><td><a href='profile.php?userid="+res[i][0]+"'>"+
   								res[i][2]
   								+"</a></td><td>"+
   								res[i][1]
   								+"</td><td>"+
   								permission
   								+"</td><td id='options_for_"+res[i][0]+"'>"
   								+"<small><a href='#' id='remove_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>"
   								+"<small><a href='#' id='accept_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: #2fe0ff' class=\"fas fa-check\"></i></a></small></tr>");

	   							$("#accept_user_"+res[i][0]).on('click', AcceptUser(res[i][0], res[i][2], res[i][1], res[i][3]));
	   							$("#remove_user_"+res[i][0]).on('click', DeleteFromTable_EventListener('users',res[i][0], "remove_user_", "user_info_id_"));
								$("#user_info_id_"+res[i][0]).css('backgroundColor', '#ff756b');
   							}	
						}
						Message('green', 'Found ' + result.length +' results.');


					}else{
						Message('red', res['error']);
					}
				},
				error: function(jqxhr, status, exception) {
            		alert('Exception:', exception);
         		}
			});
		}else if (option == 'notAccepted'){
			$.ajax({
				url: './includes/functions.php',
				method: 'GET',
				data:{
					searchUser : 1,
					option : option,
				},
				dataType : 'json',
				success: function(res){

					if(!res['error']){
						console.log(res);
						$("#dashboard_manage_user").slideDown();
						var result = [];
						for(var i = res.length - 1; i >= 0; i--){
   							result.push([i, res [i]]);

   							if(res[i][3] === 'admin')
   								var permission = 'Admin';
   							else if(res[i][3] === 'mod')
   								var permission = 'Mod';
   							else
   								var permission = 'User';

   							parentEl.append("<tr id='user_info_id_"+res[i][0]+"' class='dashboard_disposable_user_search_result'><td>"+
   								res[i][0]
   								+"</td><td><a href='profile.php?userid="+res[i][0]+"'>"+
   								res[i][2]
   								+"</a></td><td>"+
   								res[i][1]
   								+"</td><td>"+
   								permission
   								+"</td><td id='options_for_"+res[i][0]+"'>"
   								+"<small><a href='#' id='remove_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: red; font-size: 24px;' class=\"fas fa-trash\"></i></a></small>"
   								+"<small><a href='#' id='accept_user_"+res[i][0]+"' class='dashboard-icon'><i style='color: #2fe0ff' class=\"fas fa-check\"></i></a></small></tr>");

   							$("#accept_user_"+res[i][0]).on('click', AcceptUser(res[i][0], res[i][2], res[i][1], res[i][3]));
   							$("#remove_user_"+res[i][0]).on('click', DeleteFromTable_EventListener('users',res[i][0], "remove_user_", "user_info_id_"));
							$("#dashboard_show_post_user_"+res[i][0]).on('click', dashboard_showUserForumThreads(res [i][0]));
						}
						Message('green', 'Found ' + result.length +' results.');


					}else{
						Message('red', res['error']);
					}
					console.log(res);
				},
				error: function(jqxhr, status, exception) {
            		alert('Exception:', exception);
         		}
			});
		}
		
	});

	/*----------------------------*/
	/*-------- User manager ------*/
	/*----------------------------*/
				//dasboard



	


	/*-----------------------------------------*/
	/*------------------ Blog -----------------*/
	/*-----------------------------------------*/
					//dasboard

	//-------- Create new blog category -------//

	$("#dashboard_create_BlogCategory_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#dashboard_submit_blog_category").on('click', ()=>{
		
		var category = $("#dashboard_blog_add_category").val();
		var parentEl = $("#dashboard_show_blog_categories");

		if(category == ''){
			Message('red','Please fill in your category name.')
		}else{
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					submit_new_blog_category : 1,
					category: category
				},
				success:(res)=>{
					if(res != 1){
						Message('green', 'Category submitted.');
						/*setTimeout(()=>{
	              				window.location.href = "dashboard.php";
	        				}, 1000);*/

	        			parentEl.append("<tr class='dashboard_categories_rows' id='dashboard_show_cat_"+res+"'>"+
												"<th><div style='padding: 15px;' id='dashboard_show_category_"+res+"'>"+category+"</div></th>"+
												"<th><i id='dashboard_delete_category_"+res+"' style='color: red; font-size: 24px;' class=\"fas fa-times-circle\"></i><tr>"+
											"</tr>");
						$("#dashboard_delete_category_"+res[i][0]).on('click', DeleteFromTable_EventListener('blog_category',res, "dashboard_delete_category_", "dashboard_show_cat_"));
						

					}else if(res == 0){
						Message('red', 'Could not submit category.');
					}else if(res == -1){
						Message('red', 'There can not be two equally named blog categories');
					}else{
						Message('red', res);
					}
				}
			});
		}		
	});
	

	//--------Create new blog post-------------//
	$("#dashboard_create_BPost_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#dashboard_submit_new_BPost").on('click', function(){
		
		var title = $("#dashboard_create_new_BPost_title").val();
		var content = $("#dashboard_create_new_BPost_content").val();
		var cat = $("#dashboard_new_BPost_category").val();
		var datetime =  new Date($("#dashboard_create_new_BPost_datetime").val());

		var year = datetime.getYear() + 1900;
		var month = datetime.getMonth() + 1;
		var day = datetime.getDate();
		var hour = datetime.getHours();
		var minute = datetime.getMinutes();
		
	
		//Form processing (we use this method so we can upload our image)
	    var formData = new FormData();
		formData.append('file', $('#file')[0].files[0]);
		extention = $('#file')[0].files[0].name.split('.').pop().toLowerCase();
		size = $('#file')[0].files[0].size;

		formData.append('publishBlogPost', 1);
		formData.append('title', title);
		formData.append('content', content);
		formData.append('cat', cat);
		formData.append('year', year);
		formData.append('month', month);
		formData.append('day', day);
		formData.append('hour', hour);
		formData.append('minute', minute);

		if(size > 6000000){//6MB
			Message('yellow', 'The file is more then 6MB');
		} 
		
		if(!$('#file')[0].files[0]){
			Message('red','Can not publish without an image.');
		}else if(extention != 'jpg' && extention != 'jpeg' && extention != 'gif' && extention != 'png')
			Message('red', 'Invalid image file');
		else if(title == "")
			Message('red','Can not publish without a title.');
		else if (content == "")
			Message('red','Can not publish without content.');
		else if (datetime == "Invalid Date")
			Message('red', 'Please confirm the time established to publish this post.');
		else{

			$.ajax({
		       url : './includes/functions.php',
		       type : 'POST',
		       data : formData,
		       processData: false,  // tell jQuery not to process the data
		       contentType: false,  // tell jQuery not to set contentType
		       success: (res)=>{
		       		if(res == 1){
						Message('green', 'Blog post was successfuly added');
						setTimeout(()=>{
	  						window.location.href = "dashboard.php";
						}, 1000);
						
					}else{
						Message('red', res);
					}
		       }
			});
		}
	});

	//---------------Edit blog post------------//
	$("#edit_blog_post_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#edit_blog_post_submit").on('click', function(){

		var ID = $.urlParam('bpid');
		var title = $("#edit_blog_post_title").val();
		var content = $("#edit_blog_post_content").val();
		var cat = $("#edit_blog_post_category").val();
		var datetime =  new Date($("#edit_blog_post_datetime").val());

		var year = datetime.getYear() + 1900;
		var month = datetime.getMonth() + 1;
		var day = datetime.getDate();
		var hour = datetime.getHours();
		var minute = datetime.getMinutes();

		var formData = new FormData();

		if($('#editfile')[0].files[0]){
			formData.append('file', $('#editfile')[0].files[0]);
			extention = $('#editfile')[0].files[0].name.split('.').pop().toLowerCase();
			size = $('#editfile')[0].files[0].size;
		}


		formData.append('editBlogPost', 1);
		formData.append('ID', ID);
		formData.append('title', title);
		formData.append('content', content);
		formData.append('cat', cat);
		formData.append('year', year);
		formData.append('month', month);
		formData.append('day', day);
		formData.append('hour', hour);
		formData.append('minute', minute);

		if(title == "")
			Message('red','Can not publish without a title.');
		else if (content == "")
			Message('red','Can not publish without content.');
		else if (datetime == "Invalid Date")
			Message('red', 'Please confirm the time established to publish this post.');
		else{	
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:formData,
				processData: false,  // tell jQuery not to process the data
		       	contentType: false,  // tell jQuery not to set contentType
				success: (res)=>{

					if(res == -1){
						Message('red', 'You do not have permissions to complete this action.');
						return;
					}

					if(res == 0){
						Message('green', 'Post edited.');
						setTimeout(function(){
              				window.location.href = "blog_post.php?bpid=" + ID;
        				}, 1000);  
					}else{
						Message('red', res);
						console.log(res);
					}
				},
				error: (jqxhr, status, exception)=> {
            		alert('Exception:', exception);
         		}
			});
		}
		
	});


	/*-------------------------------------*/
	/*--------------Forum------------------*/
	/*-------------------------------------*/
				//dasboard

	//-------Create new forum topic--------*/
	$("#dashboard_submit_new_forum_topic").on('click', ()=>{
		var topic = $("#dashboard_create_new_forum_topic").val();
		var descp = $("#dashboard_create_new_forum_topic_description").val();
		parentEl = $("#dashboard_show_forum_topics");

		if(topic == '')
			Message('red', 'Please choose a name for your forum topic.');
		else{
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					dashboard_createForumTopic : 1,
					topic : topic,
					descp : descp
				},
				success:(res)=>{
					console.log(res);
					if(res == -2){
						Message('red', 'You do not have permission to complete this action.');
						return;
					}

					if(res != 1 && res != -1){
						Message('green', 'Forum topic added');
						parentEl.append("<tr class='dashboard_disposable_user_search_result' id='dashboard_disposable_forum_topic_"+res+"'>"+
												"<td  id='dashboard_show_topic_"+res+"'>"+topic+"</td>"+
												"<td  id='dashboard_show_topic_dscp_"+res+"'>"+descp+"</td>"+
												"<td><i id='dashboard_delete_topic_"+res+"' style='cursor: pointer' class=\"fas fa-trash\"></i></td>"
											+"</tr>");
						$("#dashboard_delete_topic_"+res).on('click', DeleteFromTable_EventListener('forum_topics',res, "dashboard_delete_topic_", "dashboard_disposable_forum_topic_"));
						console.log(res);

					}else if (res == 1){
						Message('red', 'Could not add topic');
					}else if(res == -1){
						Message('red', 'There can not be two equally named forum topics');
					}
				},
				error: (jqxhr, status, exception)=> {
            		alert('Exception:', exception);
         		}
			});
		}
		
	});

	/*-------------------------------------*/
	/*-------------------------------------*/
	/*----------- BLOG PAGE ---------------*/
	/*-------------------------------------*/
	/*-------------------------------------*/

	$( "#blogpage_category_select" ).change(function() {			//If a user changes the category selector, we call the function
		showBlogPreview();
	});


	/*-------------------------------------*/
	/*-------------------------------------*/
	/*--------- BLOG POST PAGE ------------*/
	/*-------------------------------------*/
	/*-------------------------------------*/
	$("#blog_post_comment_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
     e.preventDefault();
	});

	$("#blog_post_comment_submit").on('click', function(){
		var commentContent = $("#blog_post_comment_text").val();
		var postID = $.urlParam('bpid');
		var name = $("#blog_post_comment_authorname").val();

		var date = new Date();
		var parentEl = $("#blog_post_comment_container");

		if(!commentContent){
			Message("red", "Can not submit an empty comment.");
			return;
		}

		$.ajax({
			url: './includes/functions.php',
			method: 'POST',
			data:{
				commentBlogPost : 1,
				commentContent : commentContent,
				postID : postID
			},
			success: (res)=>{
				if(res != 0){
					Message('green', 'Comment submited');

					parentEl.append("<div style='border: 2px solid grey;' id='new_blog_comment'>"+
										"<h5><a href='profile.php?userid="+res+"'>"+name+"</a></h5><hr>"+
										"<p>"+commentContent+"</p>"+
										"<small>"+date+"</small>"+
									"<div>");
					$("#new_blog_comment").slideDown();

				}else{
					Message('red', 'Could not submit comment');
				}
			},
			error: (jqxhr, status, exception) =>{
        		alert('Exception:', exception);
     		}
		});

	});

	/*-------------------------------------*/
	/*-------------------------------------*/
	/*----------- FORUM PAGE --------------*/
	/*-------------------------------------*/
	/*-------------------------------------*/

	//-------------------Submit new thread--------------------//
	$("#forum_submit_new_thread").on('click',()=>{
		var topic = $("#forum_create_thread_topic_select").val();
		var title = $("#forum_create_thread_title").val();
		var content = $("#forum_create_thread_content").val();
		var attach = $("#forum_file").val();


		var formData = new FormData();
		if($('#forum_file')[0].files[0]){
			formData.append('file', $('#forum_file')[0].files[0]);
			extention = $('#forum_file')[0].files[0].name.split('.').pop().toLowerCase();
			size = $('#forum_file')[0].files[0].size;
			if(size > 6000000){//6MB
				Message('red', 'The file is more then 6MB');
				return;
			} 
		}

		formData.append('forum_SubmitNewThread', 1);
		formData.append('topic', topic);
		formData.append('title', title);
		formData.append('content', content);

		if(topic == '')
			Message('red','Please insert a topic');
		else if(title == '')
			Message('red', 'Please insert a title');
		else if(content == ''){
			Message('red', 'Please insert some content');
		}else{
			$.ajax({
				url : './includes/functions.php',
		       	type : 'POST',
		      	data : formData,
		       	processData: false,  // tell jQuery not to process the data
		       	contentType: false,  // tell jQuery not to set contentType
				success: (res)=>{
					$("body").append(res);
					if(res > 1){
						Message('green', 'Thread submitted');
						setTimeout(()=>{
							window.location.href = "forum_thread.php?threadID=" + res;
						}, 1000);
					}else{
						Message('red', 'Could not submit thread');
					}
				},
				error: (jqxhr, status, exception) =>{
        			alert('Exception:', exception);
     			}

			});
		}
	});

	//-------------------Edit thread--------------------//
	$("#forum_submit_edit_thread").on('click', ()=>{
		var topic = $("#forum_edit_thread_topic_select").val();
		var title = $("#forum_edit_thread_title").val();
		var content = $("#forum_edit_thread_content").val();
		var threadID = $.urlParam('editThread')

		if(topic == '')
			Message('red','Please insert a topic');
		else if(title == '')
			Message('red', 'Please insert a title');
		else if(content == ''){
			Message('red', 'Please insert some content');
		}else{
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					forum_EditThread : 1,
					topic : topic,
					title : title,
					content : content,
					threadID : threadID
				},
				success: (res)=>{
					console.log(res);
					console.log(topic);
					if(res == 0){
						Message('green', 'Thread submetida');
						setTimeout(()=>{
							//window.location.href = "forum_thread.php?threadID=" + threadID;
						}, 1000);
					}else if(res == 1){
						Message('red', 'Algo correu mal. Por favor tente de novo mais tarde.');
					}else{
						Message('red', res);
					}
				},
				error: (jqxhr, status, exception) =>{
        			alert('Exception:', exception);
     			}

			});
		}
	});


	//------------------------Reply to thread------------------------//
	$("#forum_reply_form").submit((e)=>{	//prevenir que o formulário dê submit para dar lugar ao ajax
     e.preventDefault();
	});

	$("#forum_reply_submit").on('click',()=>{
		var content = $('#forum_reply_text').val();
		var threadID = $.urlParam('threadID');
		var attach = $("#forum_reply_attachment").val();

		var formData = new FormData();

		if(attach){
			formData.append('file', $('#forum_reply_attachment')[0].files[0]);
			extention = $('#forum_reply_attachment')[0].files[0].name.split('.').pop().toLowerCase();
			size = $('#forum_reply_attachment')[0].files[0].size;
			if(size > 6000000){//6MB
				Message('red', 'The file is more then 6MB');
				return;
			} 
		}

		formData.append('forum_reply_thread', 1);
		formData.append('threadID', threadID);
		formData.append('content', content);

		if(content == ''){
			Message('red', "Please type in your reply before submitting.");
		}else{
			$.ajax({
				url : './includes/functions.php',
		       	type : 'POST',
		      	data : formData,
		       	processData: false,  // tell jQuery not to process the data
		       	contentType: false,  // tell jQuery not to set contentType
				success: (res)=>{
					console.log(res)
					if(res == 1){
						window.location.reload();
						//res is the id of the reply. We can use it to scroll down to the reply
					}else{
						Message('red', 'Could not submit');
					}
				},
				error: (jqxhr, status, exception) =>{
        			alert('Exception:', exception);
     			}
			});
		}
	});


	/*-------------------------------------*/
	/*-------------------------------------*/
	/*----------- INDEX PAGE --------------*/
	/*-------------------------------------*/
	/*-------------------------------------*/
	$("#index_testimonial_form").submit(function(e){	//prevenir que o formulário dê submit para dar lugar ao ajax
         e.preventDefault();
    });

	$("#index_submit_testimonial").on('click', ()=>{
		var content = $("#index_testimonial_content").val();

		if(content == ''){
			Message('red','Please create your content for your testemonial');
		}else{
			$.ajax({
				url: './includes/functions.php',
				method: 'post',
				data:{
					index_submitTestimonial: 1,
					content: content
				},
				success:(res)=>{
					if(res == 0)
						Message('green', 'Your testimonial was submitted');
					else
						Message('red', 'Could not submit testimonial');
					console.log(res);

				},
				error: (jqxhr, status, exception) =>{
        			alert('Exception:', exception);
     			}
			});
		}
	});


	/*-------------------------------------*/
	/*-------------------------------------*/
	/*----------- PROFILE --------------*/
	/*-------------------------------------*/
	/*-------------------------------------*/

	$('#profile_edit_info').on('click', ()=>{

		var email = $("#profile_edit_email").val();
		var name = $('#profile_edit_name').val();
		var password = $('#profile_password').val();
		var newPass = $("#profile_edit_password").val();
		var newPass2 = $("#profile_edit_password2").val();

		if(email == '' && name == '' && password == '' && newPass == '' && newPass2 == ''){
			Message('red', 'Can not submit empty fields');
		}else if(newPass != newPass2){
			Message('red', "Passwords do not match");
		}else if (!emailValidation(email) && email != ''){
			Message('red', 'Please enter a valid email address');
		}else{
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					profile_editInfo: 1,
					email: email,
					name: name,
					password: password,
					newPass: newPass,
					newPass2: newPass2
				},
				success:(res)=>{
					if(res == 0){
						Message('green', 'Your information has been changed.');
					}else if (res == 1){
						Message('red', 'Could not submit your information.');
					}else{
						Message('red',res);
					}
				}
			});
		}


	});

	$("#profile_submit_bio").on('click', ()=>{
		var parentEl = $("#profile_show_bio");
		var content = $("#profile_bio_content").val();
		if(content == ''){
			Message('red', 'Can not submit empty bio');
		}else{
			$.ajax({
				url: './includes/functions.php',
				method: 'POST',
				data:{
					profile_submitBio: 1,
					content: content
				},
				success:(res)=>{
					if(res == 0){
						$("#profile_show_account_settings").slideUp();
						parentEl.empty();
						parentEl.append(content);
					}else{
						Message('red', res);
					}
				}
			});
		}
		
	});





});