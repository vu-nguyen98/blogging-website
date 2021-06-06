<!DOCTYPE html>
<html>
  <head>
     <style type="text/css">
     table, tr, td {
        border: 1px double black;
        border-collapse: collapse;
        padding: 4px;
     }
     </style>
  </head>
  <body>
  
<?php

include_once('blog_exceptions.php');

function create_table_row($data)
{
    echo "<tr>";
    reset($data);
    $value = current($data);
    while ($value)
    {
        echo "<td>$value</td>\n";
        $value = next($data);
    }
    echo ("</tr>\n");
}

class class_DB_Interface  {

    /********************************************************
	 *
	 * Data definitions. Per instantiation
	 *
	 * Implementatiion details ae hidden from the user
	 * This is the definition of encapsulation
	 *
	 ********************************************************/
	private $referer;

	function __construct() {
		$referer = $_SERVER['HTTP_REFERER'];
		echo '<p><a href="'. $referer .'" title="Return to the previous page">&laquo; Go back</a></p>';
				
	}
	function __destruct(){
	}
	
	public function admin_blogPostsSummary()
	{

		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception(" Unable to connect to MYSQL server ", mysqli_connect_errno());
		}
		
	    // Create a join to pull information from the database


		$query = "SELECT blogdb.post.idBlogItem,".
				 "       blogdb.post.User_idUser1,".
				 "       blogdb.users.Blogger_Name,".
				 "       blogdb.post.Post_Summary".
				 "       from blogdb.users".
				 "       inner join blogdb.post".
				 "       on blogdb.users.idUser = blogdb.post.User_idUser1";
		
		$stmt = $db->prepare($query);
		$stmt->execute();
		$stmt->bind_result($blogID, $userID, $bname, $summary);
		echo "Click on the post summary to see its details.</br>";
		echo '<table>';
		create_table_row(["Blogger Name</b>", "Post Summary"]);

		//echo "<br/>Blogs Summary</p>";
		while($stmt->fetch()) {
		  create_table_row([$bname,"<a href=displayblogpost.php?id=".$blogID. ">".$summary."</a>"]);
		}

		$stmt->free_result();
		$db->close();

	}
	
	public function admin_deleteBlogPost($blogID, $userID)
	{

		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception(" Unable to connect to MYSQL server ", mysqli_connect_errno());
		}
		
	    // This function will fail of a BLOG has a TAG
		//   In the situtation the post_has_tags entry for the blog and its tag(s) must be deleted
		//   before the blog/post can be deleted
		
		//check if the post has a tag
		$query = "SELECT PostTags_idPost from post_has_tags WHERE PostTags_idPost = '$blogID';";
		$stmt = $db->prepare($query);
		$stmt->execute();
		$stmt->bind_result($tagcheck);
		$stmt->fetch();
		$stmt->free_result();
		
		//if there is then purge all tags
		if (!is_null($tagcheck)) {
			$query = "DELETE FROM post_has_tags WHERE PostTags_idpost = '$blogID';";
			$stmt = $db->prepare($query);
			$stmt->execute();
			echo "All post_with_tags for the blogID " . $blogID . "has been deleted.</br>";
		}
		
		//run this after all tags are guaranteed to be removed already
		$query = "DELETE from blogdb.post ".
				 "WHERE  blogdb.post.idBlogItem =  $blogID ".
				 "AND    blogdb.post.User_idUser1 = $userID;";

		
		$stmt = $db->prepare($query);
		$stmt->execute();
		echo "Blog post number " . $blogID . "has been deleted successfully.</br>";
		$db->close();
	}

	
	public function blogPostsSummary()
	{

		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception(" Unable to connect to MYSQL server ", mysqli_connect_errno());
		}
		
		// Create a join to pull information from the database


		$query = "SELECT blogdb.post.idBlogItem,".
				 "       blogdb.post.User_idUser1,".
				 "       blogdb.users.Blogger_Name,".
				 "       blogdb.post.Post_Summary".
				 "       from blogdb.users".
				 "       inner join blogdb.post".
				 "       on blogdb.users.idUser = blogdb.post.User_idUser1";
		
		$stmt = $db->prepare($query);
		$stmt->execute();
		$stmt->bind_result($blogID, $userID, $bname, $summary);

		//echo "<br/>Blogs Summary</p>";
		echo "Click on the post summary to see its details.</br>";
		echo '<table>';
		create_table_row(["Blogger Name", "Post Summary"]);

		//echo "<br/>Blogs Summary</p>";
		while($stmt->fetch()) {
		  create_table_row([$bname,"<a href=displayblogpost.php?id=".$blogID. ">".$summary."</a>"]);
		}

		$stmt->free_result();
		$db->close();

	}


	
	public function allBloggers()
	{

		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception();
		}
		
		// Create a simple query to pull all blogger names from the DB

		$query = "SELECT blogdb.users.Blogger_Name".
                "        from blogdb.users";
		
		$stmt = $db->prepare($query);
		$stmt->execute();
		$stmt->bind_result($bname);
		
		//echo "<br/>Blogs Summary</p>";
		echo "<h2>Here is a list of all bloggers on the block: </h2>";
		while($stmt->fetch()) {
		  echo $bname."</p>";
		}

		$stmt->free_result();
		$db->close();

		return;
	
	}

	public function blogPostsByBlogger($bname)
	{
		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception();
		}

		$query = "SELECT blogdb.post.idBlogItem,".
				 "		 blogdb.users.Blogger_Name,".
				 "       blogdb.post.Post_Summary".
                 "       from blogdb.users".
                 "       inner join blogdb.post".
                 "       on blogdb.users.idUser = blogdb.post.User_idUser1".
				 "       and blogdb.users.Blogger_Name = ?";

		
		$stmt = $db->prepare($query);
		$stmt->bind_param('s', $bname);

		
		$stmt->execute();

		$stmt->bind_result($blogID, $bname, $summary);

		// Pull the result into the PHP runtime. copies result data allowing memory to be freed in the
		// MYSQL runtime environment
		$stmt->store_result();  // Note: $STMT::num_rows is zero without first calling this API
		
			if ($stmt->num_rows === 0) {
			echo "There are no posts from the user " . $bname . "!";
		}
		else {
		echo "<br />Blogs created by blogger ".$bname."</p>";
		echo "Click on the post summary to see its details.</br>";
		echo '<table>';
		create_table_row(["Blogger Name", "Post Summary"]);
		while($stmt->fetch()) {
		  create_table_row([$bname,"<a href=displayblogpost.php?id=".$blogID. ">".$summary."</a>"]);
		}
		$stmt->free_result();
		$db->close();	
		}		

	}
	
		public function blogPostsByTag($tag)
    {
        $db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
        if (mysqli_connect_errno())
        {
            throw new MySQLI_Exception();
        }

        $query = "SELECT post.idBlogItem, users.blogger_name, post.Post_Summary " .
				 "FROM post " .
				 "INNER JOIN users ON post.User_idUser1 = users.idUser " .
				 "INNER JOIN post_has_tags ON post.idBlogItem = post_has_tags.PostTags_idPost " .
				 "INNER JOIN tags ON post_has_tags.PostTags_idTag = tags.idTags " .
				 "WHERE tags.Tag_Text = '$tag';";

        $stmt = $db->prepare($query);
        $stmt->execute();

        $stmt->bind_result($blogID, $bname, $summary);

        // Pull the result into the PHP runtime. copies result data allowing memory to be freed in the
        // MYSQL runtime environment
        $stmt->store_result(); // Note: $STMT::num_rows is zero without first calling this API
		if ($stmt->num_rows === 0) {
			echo "There are no posts with the tag " . $tag . "!";
		}
		else {
        echo "<br />Blogs with the tag " . $tag . "</p>";
		echo "Click on the post summary to see its details.</br>";
		echo '<table>';
		create_table_row(["Blogger Name", "Post Summary"]);
		while($stmt->fetch()) {
		  create_table_row([$bname,"<a href=displayblogpost.php?id=".$blogID. ">".$summary."</a>"]);
		}

        $stmt->free_result();
        $db->close();
		}
    }

  public function insertBlog($bloggerName, $blogSummary, $blogContent, $blogTag)
    {
        $db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
        if (mysqli_connect_errno())
        {
            throw new MySQLI_Exception();
        }

        // Get the user ID
        $query = "SELECT idUser FROM users where Blogger_Name = ?;";
        $stmt = $db->prepare($query);
        $stmt->bind_param('s', $bloggerName);
        $stmt->execute();
        $stmt->bind_result($idUser);
        $stmt->store_result(); // Note: $STMT::num_rows is zero without first calling this API
        $stmt->fetch();

        $stmt->close();

        // Now add a row to the post table
        //$query1 = "insert into post ".
        //		" (User_idUser1, Post_Summary, Post_Content) ".
        //		" VALUES ( ?, ?, ?);";
        

        $query1 = "insert into post " . " 
				  SELECT Max(idBlogItem)+1, $idUser, NOW(), NOW(), '$blogSummary', '$blogContent' FROM post;";

        $stmt = $db->prepare($query1);
        $stmt->execute();

        if ($stmt->error) echo "Error " . $stmt->error;
        else echo "Added to blog content</br>";

        $stmt->free_result();
        $stmt->close();

        // Need to fetch the newly added blog ID
        $query = "SELECT MAX(idBlogItem) FROM post";
        $stmt = $db->prepare($query);
        $stmt->execute();
        if ($stmt->error) echo "Error " . $stmt->error;
        $stmt->bind_result($latestidBlogItem);
        $stmt->store_result();
        $stmt->fetch();

        //check if tag exists
		//kinda dual purpose as well since it also fetches the id for tags
		//that are already there.
        $checktagquery = "SELECT idTags FROM tags where Tag_Text = '$blogTag';";
        $stmt = $db->prepare($checktagquery);
        $stmt->execute();
        if ($stmt->error) echo "Error " . $stmt->error;
        $stmt->bind_result($tagresult);
        $stmt->store_result();
        $stmt->fetch();

        //if not then create new tag entry
        if (is_null($tagresult))
        {
            $inserttagquery = "INSERT INTO tags " . "SELECT MAX(idTags)+1, '$blogTag' FROM tags;";
            $stmt = $db->prepare($inserttagquery);
            $stmt->execute();
            if ($stmt->error) echo "Error " . $stmt->error;
            else echo "New tag detected! Added into tags database.</br>";

            //Fetch the tag id again now that we have inserted it.
            $checktagquery = "SELECT idTags FROM tags where Tag_Text = '$blogTag';";
            $stmt = $db->prepare($checktagquery);
            $stmt->execute();
            if ($stmt->error) echo "Error " . $stmt->error;
            $stmt->bind_result($tagresult);
            $stmt->store_result();
            $stmt->fetch();
        }

        // Create the assoiative entity entry
        $query = "insert into post_has_tags " . " VALUES (  $latestidBlogItem, $tagresult);";
        $stmt = $db->prepare($query);
        $stmt->Execute();
        if ($stmt->error) echo "Error " . $stmt->error;

        $stmt->close();
        $db->close();

    }

	public function searchByDate($dt_stamp, $beforeafter)
	{
       $db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
        if (mysqli_connect_errno()) {
            throw new MySQLI_Exception("An error occured while attempting the database connection", mysqli_connect_errno());
        }
        // Create a query to search for blogs that were created before the date passed in by $dt_stamp
        if ($beforeafter == "before") {
		$query =   "SELECT blogdb.post.idBlogItem, blogdb.users.Blogger_Name," . "       blogdb.post.Post_Summary" . "       from blogdb.users" . "       inner join blogdb.post" . "       on blogdb.users.idUser = blogdb.post.User_idUser1" . "       where blogdb.post.Post_created_date < ?";
		} else if ($beforeafter == "after") {
		$query =   "SELECT blogdb.post.idBlogItem, blogdb.users.Blogger_Name," . "       blogdb.post.Post_Summary" . "       from blogdb.users" . "       inner join blogdb.post" . "       on blogdb.users.idUser = blogdb.post.User_idUser1" . "       where blogdb.post.Post_created_date > ?";
		}
        $stmt = $db->prepare($query);
        if (!$db->prepare($query)) {
            throw new MYSQLI_Exception("An error has occured while processing the query", mysqli_errno($db));
        }
		$stmt->bind_param('s', $dt_stamp);
        if (!$stmt->execute()) {
            throw new MYSQLI_Exception("An error has occured while processing the query", mysqli_errno($db));
        }


        if (!$stmt->bind_result($blogID, $bname, $summary)) {
            throw new MYSQLI_Exception("An error has occured while processing the query", mysqli_errno($db));
        }
			$stmt->store_result();
		if ($stmt->num_rows === 0) {
			echo "There are no posts that was created " . $beforeafter . " " . $dt_stamp . "!";
		}
			else {

        //output stuff
        echo "<br />List of blogs posted ". $beforeafter . " " . $dt_stamp . "</p>";
        echo '<table>';
        create_table_row(["Blogger Name", "Blog Summary"]);
		while ($stmt->fetch()) {
		  create_table_row([$bname,"<a href=displayblogpost.php?id=".$blogID. ">".$summary."</a>"]);
		}
		}
	}


};
	
?>
