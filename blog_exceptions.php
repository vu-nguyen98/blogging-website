<?php


class MySQLI_Exception extends Exception
{
  function __toString()
  {
       return $this->getCode().
			  ": ". 
			  $this->getMessage().
			  "<br />".
			  " in ".
              $this->getFile(). 
			  " on line ". 
			  $this->getLine(). 
			  "<br />";
   }
}

class UserException extends Exception
{
  function __toString()
  {
       return "An error has occured: " .
			  $this->getMessage().
			  "<br />";
   }
}
	

?>
