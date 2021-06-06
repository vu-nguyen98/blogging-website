<!DOCTYPE html>
<html>
<head>
  <title>blog database</title>
</head>
<body>
   <h1>Blogging Posts</h1>

<?php
   session_start();
   
   include('login.php');
  


  $loginAPI = new login();

  // Verify Username and Password have been entered
  $name = htmlspecialchars($_POST['Username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $passwd = htmlspecialchars($_POST['Password'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	
  $result = $loginAPI->authenticate($name, $passwd);
  
  if( $result == 0) 
  {
    $_SESSION['bloggerName'] = $name;

    $type = htmlspecialchars($_POST['type'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	  switch( $type )
	  {
	  case 'User':
			$_SESSION['status'] = "user";
		    header("Location: user.html");
        break;
      case 'Admin':
		$_SESSION['status'] = "admin";
        header("Location: admin.html");
        break;
      default:
        header("Location: login.html");	
        break;	       
	  }
 
  }
  else
  {
	  header("Location: login.html");
  }

?>
</body>
</html>
