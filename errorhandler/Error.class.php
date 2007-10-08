<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */

class Error {
	
	const ASSERT_TYPE = 3;
	
	public function __construct($type,$message,array $backtrace){
		assert(is_int($type));
		assert(is_string($message));
		
		$this->set_type($type);
		$this->set_message($message);
		$this->set_backtrace($backtrace);
	}
	
	public function is_assertion(){
		return $this->get_type() === self::ASSERT_TYPE;
	}
		
	//--------backtrace
	private $backtrace;
		
	public function get_backtrace(){
		return $this->backtrace;
	}
	public function set_backtrace($backtrace){
		$this->backtrace = $backtrace;
	}
	//--------END backtrace
	
	//--------type
	private $type;
		
	public function get_type(){
		return $this->type;
	}
	public function set_type($type){
		$this->type = $type;
	}
	//--------END type
	
	//--------message
	private $message;
		
	public function get_message(){
		return $this->message;
	}
	public function set_message($message){
		$this->message = $message;
	}
	//--------END message
}
?>