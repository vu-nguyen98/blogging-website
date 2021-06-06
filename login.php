<?php

class login  {

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
				
	}
	
	function __destruct(){
	}
	
	
	public function authenticate($username, $password)
	{
	  try
	  {
		// Open the file that contains the usernames and hashed passwords
		// It is in the format
		//         Username';'Hash
		

		// Add code here to make sure the password adhere to the password policy


		
		if (!($fp = @fopen("password_hash_pairs.txt", 'r'))) {
			throw new fileOpenException();
		}

	
		// Verify password hash against the values stored in the file
		// Keep looking for a username match until there are no more lines in the file
		while( feof($fp) == false )
		{
			// Read the next line up to and including the CR (carriage return)
			$next_password_hash_pair = fgets( $fp );
			// See if fgets returned an error. If it did then exit the while loop
			if($next_password_hash_pair == false )
			{
			    break;  // break out of the while loop
			}

			// The format of the line is username;hash<CR>
			// First seperate the username and hash which are separated by a ';'
			$next_pw_hash = explode(";", $next_password_hash_pair);

			// Next, strip of the <CR> from the hash value
			$next_pw_hash[1] = trim ($next_pw_hash[1], "\n");
			
			// If the username matches then enter the if statement
			if( $username == $next_pw_hash[0])
			{
				// Use the PHP function password_verify
				// The actual algorithm used and the salt that was added are all part
				// of the hash created by password_hash
				// password_verify() understaneds the algorithm used and the salt applied 
				$verifyStatus = password_verify( $password, $next_pw_hash[1]);

				if( $verifyStatus == true )
				{
					// Success. The correct password was entered
					echo "<p><strong>Password confirmed<br/>";

					// Stop the PHP script. An exit value of '0' indicates success
					return 0;
				}
				else
				{
					// Bad password
					// Stop the PHP script. An exit value of '1' indicates failure
					echo "<p><strong>Incorrect password<br/>";
					return 1;
				}
			}
		} 

		// Close the file returning file handling resources to the system
		fclose($fp);

		// Inidcate an error because a username match was never found
		echo "<p>Username does not exist</p>";		
		return 1;

	  }
	  catch (fileOpenException $foe)
	  {
		 echo "<p><strong>Password file could not be opened.<br/>";
	  }
	  catch (Exception $e)
	  {
		 echo "<p><strong>Exception occurred.<br/>";
	  }


		
		// verify the password
		return 0; // success
	
	}
	

};
	
?>
