<?php
/**
	@author Micha
*/
class Mocker {
	
	private $mock;
	private $return=array();
	private $calls = array();
	public $log_only = false;
	
	public function __construct($mock){
		$this->mock = $mock;
	}
	
	public function outside_call($func_name,$params){
		assert(strpos($func_name,'mock_')===0);
		$func_name=substr($func_name,5);//cut off mock_
		if(!$this->log_only)return $this->mock->mock_execute($func_name,$params);
	}
	
	public function inside_call($func_name=null,$params=null){
		if(!$func_name){
			$trace = debug_backtrace();
			array_shift($trace);//call to this
			$trace = array_shift($trace);
			if(@$trace['class']==get_class($this->mock)){
				$func_name = $trace['function'];
				$params = $trace['args'];
				
			}
			else throw new Exception('inside_call provide func_name or call from inside a mocked object');
		}
		//user entered '' or null
		if(!$params&&!is_array($params))$params=array();
		
		$this->calls[$func_name][]=$params;
		
		if(@$this->return[$func_name])return array_pop($this->return[$func_name]);
		if(!$this->log_only)return $this->mock->mock_execute($func_name,$params);
		return 'null';
	}

	public function add_return($func_name,$value){
		$this->return[$func_name][]=$value;
	}
	
	public function get_count($func_name){
		return count($this->calls[$func_name]);
	}

	public function get_params($func_name,$call_numer=0){
		return $this->calls[$func_name][$call_numer];
	}
}
?>