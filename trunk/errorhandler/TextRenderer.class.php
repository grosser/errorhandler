<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('ErrorRenderer.class.php');

//TODO: Fat / colored output ??
class TextRenderer extends ErrorRenderer{
	/**
	 * hint how to disable debugger (with @)
	 */
	protected function disabling_hint(){
		return '';
		//return "! use the @ in front of function or brackets to suppress unavoidable errors.\n";
	}
	
	/**
	 * Render file and linenumer of error to string
	 * @param String $file_name
	 * @param Int $line_number
	 * @param Bool $first_line
	 */
	protected function render_file_and_line($file_name,$line_number,$first_line){
		return ($first_line ? "At: " : "Called from: ") . "$file_name : $line_number\n"; 
	}
	
	/**
	 * Render the message(error or assert) to string
	 * @param String $message
	 */
	protected function render_message($message){
		return "$message\n\n";
	}
	
	/**
	 * render the topic to string
	 * @param String $topic
	 */
	protected function render_topic($topic){
		return "-->>Error type $topic<<--\n";
	}
	
	/**
	 * render a function mit arguments to string
	 * @param Array $args
	 * @param String $function_name
	 */
	protected function render_function_call($function_name,array $args){
		$out = "---- $function_name(";
		//dont show connect & login functions
		if (stristr($function_name,"connect") or stristr($function_name,"login"))  {
			$out .= ">--- HIDDEN DUE TO SECURITY CONCERN ---";
		}
		else if(!empty($args)) {
			$out .= $this->render_args($args);
		}
		else {
			$out .= "- no arguments -";
		}
		$out .= ")\n";
		return $out;
	}
	
	protected function render_unknown_type($var){
		return gettype($var) . ": $var\n";
	}
	
	protected function render_resource($var){
		return "resource of type" . get_resource_type($var) . "\n";
	}
	
	protected function render_bool($bool){
		return 'bool: <font color="#FF6633">' . $bool?'true':'false' . '</font>' ;
	}
	
	protected function render_object($object){
		return $this->print_to_string($object) . "\n";
	}
	
	protected function render_array($array){
		return  " array of size " . sizeof($array) . ": ".$this->print_to_string($array)."\n";
	}
	
	//TODO: somehow merge with HtmlDebugger ?
	private function print_to_string($object){
		assert(is_object($object)||is_array($object));
		$real_content = print_r($object,true);
		
		$content = substr($real_content,0,self::MAX_STRING_LENGTH);
		$content = str_replace("\n","",$content);
		
		if (strlen($real_content) >= self::MAX_STRING_LENGTH) $content .= '.........';
		return $content;
	}
	
	/**
	 * Render globals if requested
	 * @todo merge with HtmlDebugger
	 */
	protected function render_globals(){
		$out = '';	
		
		if($this->get_dump_globals()){
			$this->set_dump_globals(false);//run this code only once per run..

			$out .= "-----------------------GLOBALS:\n";
			$out .= print_r($GLOBALS,true) ."\n";
			$out .= "-----------------------END OF GLOBALS\n";
			$out .= "-----------------------CONSTANTS:\n";
			$out .= print_r(get_defined_constants(),true);
			$out .= "-----------------------END OF CONSTANTS\n";
		}
		return $out;
	}
}
?>