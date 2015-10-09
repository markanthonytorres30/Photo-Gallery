<?php


/**
 * FileException
 *
 * Raised when a file is missing.
 */

class FileException 
extends Exception
{	
	/// Path to missing file
	public $file;
}

?>