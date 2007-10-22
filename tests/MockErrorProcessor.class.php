<?php
class MockErrorProcessor extends ErrorProcessor{
	public function notify_of_error(Error $error){
		if($this->mock_inside_call)return parent::notify_of_error($error);
		$this->mocker->inside_call('notify_of_error',array($error));
	}
	
	public function get_count($func_name){
		return $this->mocker->get_count($func_name);
	}
	
	public function get_param($func_name,$call_number=0){
		return $this->mocker->get_params($func_name,$call_number);
	}
	
	
	//--------Mocker basics
	/**
	 * @var Mocker
	 */
	public $mocker;
	private $mock_inside_call=false;
	
	public function __construct($a=null,$b=null,$c=null){
		$this->mocker = new Mocker($this);
		$this->mocker->log_only=true;
	}
	
	public function __call($name,$params){
		if(strpos($name,'mock_')!==0)return call_user_func_array(array(parent,$func),$params);
		return $this->mocker->outside_call($name,$params);
	}
	
	protected function create_mail(){
		
		return $this->mocker->inside_call();
	}
	
	public function mock_execute($func,array $params){
		$this->mock_inside_call=true;
			$out=call_user_func_array(array($this,$func),$params);
		$this->mock_inside_call=false;
		return $out;
	}
}
?>