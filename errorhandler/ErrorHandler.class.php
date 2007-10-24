<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */

require_once('Error.class.php');
require_once('ErrorReportingStatus.class.php');

class ErrorHandler {
	//--------instance
	protected static $instance;

	/**
	 * @return ErrorHandler
	 */
	public static function get_instance(){
		if(! isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct(){	}

	private function __clone(){}
	
	//--------END instance

	//--------LISTENERS
	private $listeners = array();
	
	/**
	 * @return array
	 */
	public function get_listeners(){
		return $this->listeners;
	}
	
	/**
	 * @param ErrorProcessor
	 */
	public function add_listener(ErrorProcessor $processor){
		$this->listeners[] = $processor;
	}
	
	public function remove_listeners(){
		$this->listeners = array();
	}
	//--------END LISTENERS

	/**
	 * @param $level -- set error level
	 */
	public function activate($level=-1){
		$this->save_state();
		
		$this->activate_assert();
		$this->activate_error_reporting($level);
	}
	
	public function deactivate(){
		$this->restore_state();
	}
	
	//--------STATE
	/**
	 * @var ErrorReportingStatus
	 */
	private $saved_state;
	
	/**
	 * @return bool succsess
	 */
	private function save_state(){
		if(isset($this->saved_state)) return false;
		$this->saved_state = new ErrorReportingStatus();
		return true;
	}
	
	/**
	 * @return bool succsess
	 */
	private function restore_state(){
		if(!isset($this->saved_state)) return false;
		$this->saved_state->restore();
		return true;
	}
	//--------END STATE

	#####################################################################################
	# Assertions
	#####################################################################################
	public function activate_assert(){
		assert_options(ASSERT_ACTIVE, 1);
		assert_options(ASSERT_WARNING, 0);
		assert_options(ASSERT_BAIL, 1);//exit after false assert
		assert_options(ASSERT_CALLBACK, 'assert_callback');
//		assert_options(ASSERT_QUIET_EVAL, 0);
	}

	/**
	 * Assertion failed...
	 *
	 * @param String $script
	 * @param Int $line
	 * @param String $message
	 */
	public function assert_callback($message){
		assert(is_string($message));
		$message = "Condition: $message failed!";
		
		$this->notify_listeners(Error::ASSERT_TYPE,$message);
	}

	#####################################################################################
	# Errorhandling
	#####################################################################################
	/**
	 *@param Int $level -- see error_reporting levels php
	 */
	public function activate_error_reporting($level=-1){
		assert(is_numeric($level));
		if($level != -1){
			//no fatal errors shown when level does not contain E_ERROR
			$level |= E_ERROR;
			error_reporting($level);
		}
		set_error_handler("error_callback");
	}
	
	//--------error_handling
	const HANDLING_ONCE = 'once';				//process once, ignore all afterward
	const HANDLING_MULTIPLE = 'multiple';	//process all that comes
	const HANDLING_BAIL = 'bail';				//exit after processing first error
	
	private $error_handling_type = self::HANDLING_MULTIPLE;
	private $error_handled = false;
	
	public function error_handling($type){
		assert(
			$type==self::HANDLING_BAIL ||
			$type==self::HANDLING_ONCE ||
			$type==self::HANDLING_MULTIPLE
		);
		$this->error_handling_type = $type;
	}
	//--------END error_bail

	public function error_callback($type,$message,$file_name){
		assert(is_numeric($type));
		assert(is_string($message));
		assert(is_string($file_name));
		
		if($this->error_handling_type == self::HANDLING_ONCE){
			if($this->error_handled)return;
			else	$this->error_handled = true;
		}
		
		if($this->error_should_be_ignored($type,$file_name))return;
		$this->notify_listeners($type,$message);
		
		if($this->error_handling_type == self::HANDLING_BAIL)exit;
	}
	
	private function notify_listeners($error_type,$message){
		$backtrace = $this->build_backtrace();
		$error = new Error($error_type,$message,$backtrace);

		foreach($this->get_listeners() as $listener){
			$listener->notify_of_error($error);
		}
	}

	private function build_backtrace(){
		$backtrace = debug_backtrace();
		//filter anything after/including error_callback
		$kill = false;
		foreach($backtrace as $key => $part){
			if( empty($part['class']) && ($part['function']==='error_callback' || $part['function']==='assert_callback')){
				//cleanup global vars...
				if($part['function']==='error_callback'){
					//remove the args & function from the error_handler_call
					unset($backtrace[0]['args']); //NEVER REMOVE!!!! -> recursion!
					unset($backtrace[0]['function']);
				}
				else {//assertion we dont need anything from this line...
					array_shift($backtrace);//NEVER REMOVE!!!! -> recursion!
				}
				break;
			}
			array_shift($backtrace);
		}
		
		if(empty($backtrace[0])){
			array_shift($backtrace);
		}

		return $backtrace;
	}


	#####################################################################################
	# ERROR IGNORING
	#####################################################################################
	
	const IGNORE_WHITELIST = 'whitelist';
	const IGNORE_BLACKLIST = 'blacklist';
	/**
	 * 
	 * @param $ignore_type String IGNORE_WHITELIST or IGNORE_BLACKLIST
	 * @param $list array directory-parts to ignore as source of error
	 * 	array(
	 * 		'simpletest' => E_NOTICE | E_STRICT,
	 * 	);
	 *  
	 */
	public function ignore_errors(array $list,$ignore_type=self::IGNORE_BLACKLIST){
		assert(
			$ignore_type==self::IGNORE_WHITELIST || 
			$ignore_type==self::IGNORE_BLACKLIST
		);
		foreach($list as $key => $value){
			//string instead of error-type as value
			if(!is_numeric($value)){
				assert(is_numeric($key));
				unset($list[$key]);
				$list[$value]=E_ALL|E_STRICT;
			}
		}
		
		$this->error_ignore_type = $ignore_type;
		$this->error_ignore_list = $list;
	}
	
	private $error_ignore_type = self::IGNORE_BLACKLIST; 
	private $error_ignore_list;//never call direct
	
	private function get_error_ignore_list(){
		if(isset($this->error_ignore_list))return $this->error_ignore_list;

		//default (cannot be set since calculations( | ) are not allowed as defaults)
		return array(
			'/usr/share/php/'=>E_NOTICE | E_STRICT,
			'simpletest/'=>E_NOTICE | E_STRICT,
			'/xorc/'=>E_NOTICE | E_STRICT,
		);
	}
	
	/**
	 * @param Int $type
	 * @Todo goes to Error Processor
	 */
	private function error_should_be_ignored($error_type,$file_name){
		if($this->error_type_is_ignored($error_type))return true;
		if($this->is_bailed_assertion($error_type))return false; 
		if($this->file_is_ignored_from_error_handling($file_name,$error_type))return true;
	}
	
	private function error_type_is_ignored($error_type){
		if ($this->bit_is_set($error_type,error_reporting())) return false;
		return true;
	}
	
	/**
	 * $bit in $int
	 * @param $bit int 1 2 4 8
	 * @param $int int 1 2 3 4
	 */
	private function bit_is_set($bit,$int){
		return ($bit | $int) == $int;
	}
	
	private function is_bailed_assertion($type){
		if($type == Error::ASSERT_TYPE && assert_options(ASSERT_BAIL))return true;
		return false;
	}
	
	/**
	 * @param String $file_path
	 */
	private function file_is_ignored_from_error_handling($file_path,$error_type){
		//anything on the blacklist is ignored
		if($this->error_ignore_type == self::IGNORE_BLACKLIST){
			foreach($this->get_error_ignore_list() as $word => $ignored_errors){
				$error_ignored = $this->bit_is_set($error_type,$ignored_errors);
				if($this->word_in_path($word,$file_path) && $error_ignored){
					return true;
				}
			}
		}
		//anything NOT on the whitelist is ignored
		else if($this->error_ignore_type == self::IGNORE_WHITELIST){
			foreach($this->get_error_ignore_list() as $word => $allowed_errors){
				$error_allowed = $this->bit_is_set($error_type,$allowed_errors);
				if($this->word_in_path($word,$file_path) && $error_allowed){
					return false;
				}
			}
			return true;
		}
		else{
			throw new Exception('error_ignore_type is wrong');
		}
		return false;
	}
	
	/**
	 * word is in path ? (in-casesensitive)
	 */
	private function word_in_path($word,$path){
		assert($word && $path);
		return is_int(strpos(strtolower($path),strtolower($word)));
	}
}

/**
 * Called when error occures
 *
 * @param String $script_path
 * @param Int $line
 * @param String $message
 */
function assert_callback($script_path=null, $line=null, $message) {
	$script_path;$line;//we dont need sice we use backtrace! ->no compiler warnings
	
	global $debugger_recursion;
	if(!$debugger_recursion){
		$debugger_recursion = true;
		ErrorHandler::get_instance()->assert_callback($message);
		$debugger_recursion = false;
	}
	else {
		print "ASSERTION in ERROR HANDLER FAILED! $script_path $line $message";
	}

}

function error_callback($type, $message, $file, $line=null, $context=null){
	// don't respond to the error if it
	// was suppressed with a '@' -> error_reporting() = 0
	if(error_reporting()===0)return;
	
	$line;$context;//we dont need sice we use backtrace! ->no compiler warnings
	global $debugger_recursion;
	if(!$debugger_recursion){
		$debugger_recursion = true;
		ErrorHandler::get_instance()->error_callback($type,$message,$file);
		$debugger_recursion = false;
	}
	else {
		print "ERROR in ERROR HANDLER! $type $msg $file $line $contect";
	}
}

?>