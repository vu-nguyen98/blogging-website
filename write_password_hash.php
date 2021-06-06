
<!DOCTYPE html>
<html>
  <head>
    <title>Password Hashing</title>
  </head>
  <body>
    <h1>Password Hashing</h1>

	<?php
	  
		  try
		  {
			if (!($fp = @fopen("password_hash_pairs.txt", 'w'))) {
				throw new fileOpenException();
			}


			$bob_pw  = password_hash("BobPW",  PASSWORD_DEFAULT );
			$vs = password_verify("BobPW", $bob_pw );

			$mary_pw  = password_hash("MaryPW",  PASSWORD_DEFAULT );
			
			$bob_string = "Bob;".$bob_pw."\n";
			if (!fwrite($fp, $bob_string, strlen($bob_string))) {
			   throw new fileWriteException();
			}
			
			$Mary_string = "Mary;".$mary_pw."\n";
			if (!fwrite($fp, $Mary_string, strlen($Mary_string))) {
			   throw new fileWriteException();
			}
			
			fclose($fp);
			echo "<p>All usernames and passwords successfully written</p>";

		  }
		  catch (fileOpenException $foe)
		  {
			 echo "<p><strong>Password file could not be opened.<br/>";
		  }
		  catch (Exception $e)
		  {
			 echo "<p><strong>Password file could not be written.<br/>";
		  }
			

	?>
  </body>
</html>