<!DOCTYPE html>
<html>
<head>
  <title>Books Database</title>
</head>
<body>

<?php
  session_start();
  //We will use sessions once we create the login page
  // placeholder for now
  // in the meantime the blogger name will be entered via a text box 
  // as can be seen oon the user.html page
  //$bloggerName = $_SESSION['blogger'];
    
  include_once('blog_db_Interface.php');
  include_once('blog_exceptions.php');
  
try
{

  $myDB = new class_DB_Interface();


  if( isset($_POST['AllBlogs']))
  {
    // TODO - add code to verify student first and last names are entered
    $myDB->blogPostsSummary();   
  }
  else if( isset($_POST['AllBloggers']))
  {
    $myDB->allBloggers();   

  }
  else if( isset($_POST['DisplayBloggerBlogs']))
  {
    $bloggerName = htmlspecialchars($_POST['BloggerName1'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	if (empty($bloggerName)) {
		throw new UserException("This field cannot be left empty!");
	}
    $myDB->blogPostsByBlogger($bloggerName);  
  }
    else if ( isset($_POST['DisplayTaggedBlogs']))
  {
	$tagSearch = htmlspecialchars($_POST['TagSearch'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	if (empty($tagSearch)) {
		throw new UserException("This field cannot be left empty!");
	}
	$myDB->blogPostsByTag($tagSearch);
  }
  else if( isset($_POST['Submit_Blog']))
  {
	  
    $bloggerName =  $_SESSION['bloggerName'];
	
    $blogSummary= htmlspecialchars($_POST['Blog_Summary'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $blogContent= htmlspecialchars($_POST['Blog_Content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	$blogTag = htmlspecialchars($_POST['Blog_Tag'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	
	if (empty($blogSummary) || empty($blogContent)) {
		throw new UserException("Blog content and/or blog summary cannot be left empty!");
	}

    $myDB->insertBlog($bloggerName, $blogSummary, $blogContent, $blogTag);  
  }
   else if( isset($_POST['compare-time']))
  {
    // your code here
    $datetime = htmlspecialchars($_POST['compare-time'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	if (!strtotime($datetime)) {
		throw new UserException ("Something happened while processing date information.");
	} else $datetime = strtotime($datetime);
	if (!date($datetime)) {
		throw new UserException ("Something happened while processing date information.");
	} else $datetime = date('Y-m-d H:i:s', $datetime);
	
	$beforeafter = htmlspecialchars($_POST['beforeafter'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $myDB->searchByDate($datetime, $beforeafter);   
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
catch ( UserException $u_e) {
	echo $u_e;
}

?>
</body>
</html>
