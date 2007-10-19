<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
abstract class ErrorRenderer {
	const MAX_STRING_LENGTH = 1500;
	
	/**
	 * hint how to disable debugger (with @)
	 */
	abstract protected function disabling_hint();

	/**
	 * Render file and linenumer of error to string
	 * @param String $file_name
	 * @param Int $line_number
	 * @param Bool $first_line
	 */
	abstract protected function render_file_and_line($file_name,$line_number,$first_line);

	/**
	 * Render the message(error or assert) to string
	 * @param String $message
	 */
	abstract protected function render_message($message);

	/**
	 * render the topic to string
	 * @param String $topic
	 */
	abstract protected function render_topic($topic);

	/**
	 * render a function call mit arguments to string
	 * @param Array $args
	 * @param String $function_name
	 */
	abstract protected function render_function_call($function_name,array $args);

	/**
	 * Render globals if requested
	 */
	abstract protected function render_globals();

	abstract protected function render_unknown_type($var);

	abstract protected function render_resource($var);

	abstract protected function render_bool($bool);

	abstract protected function render_object($object);

	abstract protected function render_array($array);

	//--------dump globals
	private $dump_globals = false;
	public function set_dump_globals($var){
		assert(is_bool($var));
		$this->dump_globals = $var;
	}
	protected function get_dump_globals(){
		return $this->dump_globals;
	}
	//--------

	final public function render(Error $error){
		$type=$this->errortype_to_word($error->get_type());
		switch($type) {
			case 'ERROR':
			case 'WARNING':
			case 'ASSERT':
				return $this->render_error($error,$type);
			case 'INFO':
				$out = $this->render_error($error,$type);
				$out .= $this->disabling_hint();
				return $out;
			case 'UNKNOWN':
				$out =  $this->render_error($error,"TYPE ".$error->get_type());
				$out .= "---------UNKNOWN ERROR CODE {$error->get_type()}----------";
				return $out;
			default: throw new Exception("Errorcode is wrong got $type, expected INFO/WARNING/ERROR/UNKNOWN");
				
		}
	}
	
	public function errortype_to_word($type){
		assert(is_int($type));
		switch($type){
			case Error::ASSERT_TYPE:
				return 'ASSERT';
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_USER_ERROR:
			case E_COMPILE_ERROR:
			case E_RECOVERABLE_ERROR:
				return 'ERROR';
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
				return 'WARNING';
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
				return 'INFO';
			default:
				return 'UNKNOWN';
		}
		
	}

	/**
	 * Render a backtrace to string
	 */
	protected function render_error(Error $error,$topic='Error'){
		$out = $this->render_topic($topic);
		$out .= $this->render_message($error->get_message());
		$out .= $this->render_backtrace($error->get_backtrace());
		$out .= $this->render_globals();

		return $out;
	}

	/**
	 * render the actual php_backtrace to string
	 */
	protected function render_backtrace(array $trace){
		$out = '';
		$first_line = true;
		foreach($trace as $trace_part) {
			$line = @$trace_part['line']?$trace_part['line']:'Unknown';
			$file = @$trace_part['file']?$this->short_file_name($trace_part['file']):'Unknown';
			
			$out .= $this->render_file_and_line($file,$line,$first_line);
			
			if(!$first_line){
				$args = isset($trace_part['args']) ? $trace_part['args'] : array();
				$out .= $this->render_function_call($trace_part['function'],$args);
			}
			$first_line=false;
		}
		return $out;
	}

	/**
	 * render the args of a function
	 */
	final protected function render_args(array $args){
		$out_arr = array();
		$out = '';

		foreach($args as $var) {
			if	(is_null($var)) {
				$out_arr[] = 'null' ;
			}
			elseif(is_array($var)) {
				$out_arr[] = $this->render_array($var);
			}
			elseif(is_object($var)) {
				$out_arr[] = $this->render_object($var);
			}
			elseif(is_bool($var)) {
				$out_arr[] = $this->render_bool($var);
			}
			elseif(is_resource($var)) {
				$out_arr[] = $this->render_resource($var);
			}
			else{
				$real_var = (string) $var;
				$var = substr($real_var,0,self::MAX_STRING_LENGTH);
				if (strlen($real_var) > self::MAX_STRING_LENGTH) $var .= '... ... ...';

				$out_arr[] = $this->render_unknown_type($var);
			}
			$out = implode($out_arr,', ');
		}

		return $out;
	}

	private function short_file_name($file_path=null){
		if(!isset($file_path)) return 'call_user_func()';
		assert(is_string($file_path));
		$try = array(
			'PWD',
			'DOCUMENT_ROOT',
		);
		foreach($try as $name){
			$will_work = !empty($_SERVER[$name]) &&  (strpos($file_path,$_SERVER[$name])===0);//isset is not enougth!
			if($will_work){
				return substr($file_path,strlen($_SERVER[$name]));
			}
		}
		//nothing works...
		return $file_path;
	}
}

?>
