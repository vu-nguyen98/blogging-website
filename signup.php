<?php

class signup
{
    /********************************************************
     *
     * Data definitions. Per instantiation
     *
     * Implementatiion details ae hidden from the user
     * This is the definition of encapsulation
     *
     ********************************************************/
    private $referer;

    function __construct()
    {
    }

    function __destruct()
    {
    }

    public function checkusername($username)
    {
        function expandarray(&$value)
        {
            $value = explode(';', $value);
        }
        function trimCR(&$value)
        {
            $value = trim($value, '\r');
        }
        try {
            // Open the file that contains the usernames and hashed passwords
            // It is in the format
            //         Username';'Hash

            if (!($fp = @fopen("password_hash_pairs.txt", 'r'))) {
                throw new fileOpenException();
            }
            if ($fp) {
                $hasharray = explode(
                    "\n",
                    fread($fp, filesize("password_hash_pairs.txt"))
                );
            }

            array_walk($hasharray, 'expandarray');

            // Verify password hash against the values stored in the file
            // Keep looking for a username match until there are no more lines in the file
            foreach ($hasharray as $v1) {
                if (strcasecmp($v1[0], $username) == 0) {
                        return 0;
                }
            }
            fclose($fp);
            return 1;
        } catch (fileOpenException $foe) {
            echo "<p><strong>Password file could not be opened.<br/>";
        } catch (Exception $e) {
            echo "<p><strong>Exception occurred.<br/>";
        }
    }
	
	public function checkpassword($password) {
		//validate password strength
		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number    = preg_match('@[0-9]@', $password);
		
		if (!$uppercase) {
			throw new UserException("Password must contain an uppercase letter.");
		} else if (!$lowercase) {
			throw new UserException("Password must contain an lowercase letter.");
		} else if (!$number) {
			throw new UserException("Password must contain a number.");
		} else if (strlen($password) < 6) {
			throw new UserException("Password must contain at least 6 characters.");
		}
	}
	
	public function writepasswordhash($username, $password) {
			  
	  try
	  {
		if (!($fp = @fopen("password_hash_pairs.txt", 'a+'))) {
			throw new fileOpenException();
		}


		$newpassword  = password_hash($password,  PASSWORD_DEFAULT );
		
		$passwordstring = $username.";".$newpassword."\n";
		if (!fwrite($fp, $passwordstring, strlen($passwordstring))) {
		   throw new fileWriteException();
		}
		
		fclose($fp);

	  }
	  catch (fileOpenException $foe)
	  {
		 echo "<p><strong>Password file could not be opened.<br/>";
	  }
	  catch (Exception $e)
	  {
		 echo "<p><strong>Password file could not be written.<br/>";
	  }
		

	}
	
	public function createnewbloguser($username) {
		$db = @new mysqli('localhost', 'blogDB_user', 'blogDB_userPW', 'blogdb');
		if (mysqli_connect_errno()) {
		   throw new MySQLI_Exception(" Unable to connect to MYSQL server ", mysqli_connect_errno());
		}
		
		$query = "INSERT INTO users (idUser, Blogger_Name)" .
				 "SELECT MAX(idUser+1), '$username' FROM users;";
				 
		$stmt = $db->prepare($query);
		$stmt->execute();

		if( $stmt->error)
			echo "Error ".$stmt->error;
	}
}

?>
