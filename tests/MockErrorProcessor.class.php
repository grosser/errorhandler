<?php
class MockErrorProcessor extends ErrorProcessor{
	public $count = array(
		'notify_of_error'=>array(),
	);
	
	public function get_count($func_name){
		return count($this->count[$func_name]);
	}
	
	public function get_param($func_name,$call_number=0){
		return $this->count[$func_name][$call_number];
	}
	
	private function add_params($func_name,$params = array()){
		$this->count[$func_name] = array_reverse($this->count[$func_name]);
		array_push($this->count[$func_name],$params);
		$this->count[$func_name] = array_reverse($this->count[$func_name]);
	}
	
	public function notify_of_error(Error $error){
		$this->add_params('notify_of_error',array($error));
	}
}
?>