<?php
/**
	@author Micha
*/
class ErrorCreator {
	
	public static function E_NOTICE(){
		$x = $what_the_fuck;
	}
	
	public static function E_WARNING(){
		$x=  file_get_contents('does_not_exists');
	}
}
?>