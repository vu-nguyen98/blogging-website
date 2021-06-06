<!DOCTYPE html>
<html>
<head>
  <title>Blog Database</title>
</head>
<body>

<?php
  include('blog_db_Interface.php');
  try
  {
	  session_start();
	  $_SESSION["status"] = "admin";
      $myDB = new class_DB_Interface();

      if( isset($_POST['AllBlogs']))
      {
        $myDB->admin_blogPostsSummary();  
      }      
      else if( isset($_POST['Delete_Blog']))
      {
        $blogID= htmlspecialchars($_POST['idBlogItem'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $userID= htmlspecialchars($_POST['User_idUser1'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $myDB->admin_deleteBlogPost($blogID, $blogID);   

      }
      else
      {
        // error handling
      }
  }
  catch( MySQLI_Exception $msql_e)
  {
    echo $msql_e;
  }
  catch( Exception $ge)
  {
    echo $ge;
  }
?>
</body>
</html>
