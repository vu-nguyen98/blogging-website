<!DOCTYPE html>
<html>
<head>
  <title>Sign Up Page</title>
</head>
<body>
   <h2>Signing You Up...</h2>

<?php
try {
   session_start();
   
   include('signup.php');
   include_once('blog_exceptions.php');
   
   $signupAPI = new signup();

  // Verify Username and Password have been entered
  $name = htmlspecialchars($_POST['Username'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  $passwd = htmlspecialchars($_POST['Password'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  //if it is empty then throw some exceptions
  if (empty($name)) {
	  throw new UserException("The name field is empty.");
  }
  if (empty($passwd)) {
	  throw new UserException("The password field is empty.");
  }
  
  //check if username already exists, ripped from my old password api
  $result = $signupAPI->checkusername($name);
  
  if ($result == 0) {
	  throw new UserException("The chosen username already exists.");
  }
  
  //check if the password is valid
  $signupAPI->checkpassword($passwd);
  
  //write password to hash txt file
  $signupAPI->writepasswordhash($name,$passwd);
  
  //create new blog user with the chosen name
  $signupAPI->createnewbloguser($name);
  
  echo "Congratulations, " . $name . "! You have successfully been registered onto our blog services.</br></br>";
  
  
} catch (Exception $e) {
	echo $e;
}
  
?>
  <a href="login.html">Click here to return to the login page.</a></br>
  <a href="signup.html">Click here to return to the sign up page.</a>
</body>
</html>
