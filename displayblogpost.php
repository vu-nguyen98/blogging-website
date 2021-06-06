<?php
try {
	session_start();
	include_once('blog_exceptions.php');
	$blogID = $_GET["id"];
	
	$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
	if (mysqli_connect_errno()) {
	   throw new MySQLI_Exception(" Unable to connect to MYSQL server ", mysqli_connect_errno());
	}
	
	// Create a join to pull information from the database


	$query= "SELECT ".
			"users.blogger_name, ".
			"post.post_created_date, ".
			"post.post_updated_date, ".
			"post.post_summary, ".
			"post.post_content, ".
			"tags.tag_text ".
			"FROM ".
			"post LEFT JOIN post_has_tags ON post.idBlogItem = post_has_tags.posttags_idpost ".
			"LEFT JOIN tags ON post_has_tags.PostTags_idTag = tags.idTags ".
			"INNER JOIN users ON users.idUser = post.User_idUser1 ".
			"WHERE post.idBlogItem = $blogID;";
	
	$stmt = $db->prepare($query);
	$stmt->execute();
	$stmt->bind_result($bloggername, $createdate, $updatedate, $summary, $content, $tag);
	$stmt->fetch();
	//if there is no tag then just say there is no tag
	if (empty($tag)) $tag = "None";
	echo "</br><h1>Displaying information for blog number ".$blogID . "</h1></br>";
	echo "<b>Blog created by: </b>". $bloggername . "</br>";
	echo "<b>Blog was created on: </b>". date('d-M-Y',strtotime($createdate));
	echo " at ". date('h:m:s a',strtotime($createdate)) . "</br>";
	echo "<b>Blog was updated on: </b>". date('d-M-Y',strtotime($updatedate));
	echo " at ". date('h:m:s a',strtotime($updatedate)) . "</br>";
	echo "<b>Blog summary: </b>". $summary . "</br>";
	echo "<b>Blog content: </b>". $content . "</br>";
	echo "<b>Blog tag: </b>". $tag . "</br></br>";
	
	if ($_SESSION["status"] == "admin") 
	{
		echo "<a href = admin.html>Return to main admin page.</a>";
	} else echo "<a href = user.html>Return to the main page.</a>";
} catch (Exception $e) {
	echo $e;
}
?>