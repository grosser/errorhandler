<?
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
class FileNotFoundExc extends Exception {
	// Redefine the exception so message isn't optional
	public function __construct($file, $code = 0) {
		$message = "File: $file was not found!"; 
				
		parent::__construct($message, $code);
	}
}
?>