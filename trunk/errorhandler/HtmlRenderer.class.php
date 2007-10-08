<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('ErrorRenderer.class.php');

class HTMLRenderer extends ErrorRenderer{
	
	protected function render_error(Error $error,$topic='Error'){
		return "<div style='position:absolute;background:white;top:100px;left:100px;z-index:99999'>".parent::render_error($error,$topic)."</div>";
	}
	
	
	protected function disabling_hint(){
		return;
		//return "<b>!!</b> use @ in front of function or brackets to suppress unavoidable errors.<br>";
	}
	
	/**
	 * Render file and linenumer of error to string
	 */
	protected function render_file_and_line($file_name,$line_number,$first_line){
		return "<b><span style='font-size:20px;color:#888'>" . (($first_line) ? "At " : "Called from ") .
				"<span color='black'>$file_name : $line_number</span></span></b><br/>";
	}
	
	/**
	 * Render the message(error or assert) to string
	 */
	protected function render_message($message){
		return "<b><span style='font-size:15px;background:yellow;'>&nbsp;".
			str_replace("\n","<br>",$message)." </span></b><br>";	
	}
	
	/**
	 * render the topic to string
	 */
	protected function render_topic($topic){
		return "<b><u>". htmlspecialchars($topic) . ": </b></u><br>";
	}
	
	/**
	 * render a function mit arguments to string
	 * @param Array $args
	 * @param String $function_name
	 */
	protected function render_function_call($function_name,array $args){
		$out = "<span style='color:blue;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>$function_name( </b>";
		//dont show connect & login functions
		if (stristr($function_name,"connect") or stristr($function_name,"login"))  {
			$out .= "<span color=red>--- HIDDEN DUE TO SECURITY CONCERN ---</span>";
		}
		else if(!empty($args)) {
			$out .= $this->render_args($args);
		}
		else {
			$out .= "<!-- no arguments -->";
		}
		$out .= "<b>)</b></font><br>";
		return $out;
	}
	
	/**
	 * Render globals if requested
	 * @todo merge with ConsoleDebugger
	 */
	protected function render_globals(){
		$out = '';	
		
		if($this->get_dump_globals()){
			$this->set_dump_globals(false);//run this code only once per run..

			$out .= "<span color=red><b>GLOBALS:</b></font><font color='#440000'><pre>";
			$out .= htmlspecialchars(print_r($GLOBALS,true));
			$out .= "</pre><br><b>END OF GLOBALS</b></span><hr>";
			$out .= "<font span='#CC3300'><b>CONSTANTS:</b></font><br><pre>";
			$out .= htmlspecialchars(print_r(get_defined_constants(),true));
			$out .= "</pre><br><b>END OF CONSTANTS</b></span><br>";
		}
		return $out;
	}
	
	protected function render_unknown_type($var){
		return htmlspecialchars(gettype($var)) . ": <span color='#FF6633'>\"$var\"</span>";
	}
	
	protected function render_resource($var){
		return 'resource of type <span color="#FF6633">"' . htmlspecialchars(get_resource_type($var)) . '"</span>';
	}
	
	protected function render_bool($bool){
		return 'bool: <span color="#FF6633">' . $bool?'true':'false' . '</span>' ;
	}
	
	protected function render_object($object){
		return "<span color=magenta>".$this->print_to_string($object)."</span>";
	}
	
	protected function render_array($array){
		$out  = "array of size " . sizeof($array);
		$out .= ": <span color=green>".$this->print_to_string($array)."</span>";
		
		return $out;
	}
	
	//TODO: somehow merge with ConsoleDebugger ?
	private function print_to_string($object){
		assert(is_object($object)||is_array($object));
		$real_content = print_r($object,true);
		
		$content = substr($real_content,0,self::MAX_STRING_LENGTH);
		$content = str_replace("\n"," ",$content);
		
		if (strlen($real_content) >= self::MAX_STRING_LENGTH) $content .= '<b>.........</b>';
		return $content;
	}
	
	protected function render_backtrace(array $trace){
		$trace = $this->everything_to_html($trace);
		return parent::render_backtrace($trace);
	}
	
	protected function everything_to_html(array $vars){
		foreach($vars as $key => $var){
			if(is_array($var)){
				$var = $this->everything_to_html($var);
			}
			elseif(is_object($var)){
				$var = get_class($var);
			}
			else{
				$var = @htmlspecialchars($var);
			}
			$vars[$key]=$var;
		}
		return $vars;
	}
}
?>