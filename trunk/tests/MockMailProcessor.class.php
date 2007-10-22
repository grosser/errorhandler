<?php
class MockMailProcessor extends MailProcessor{
	
	/**
	 * @var Mocker
	 */
	public $mocker;
	private $mock_inside_call=false;
	
	public function __construct($a=null,$b=null,$c=null){
		parent::__construct($a,$b,$c);
		$this->mocker = new Mocker($this);
	}
	
	public function __call($name,$params){
		if(strpos($name,'mock_')!==0)return call_user_func_array(array(parent,$func),$params);
		return $this->mocker->outside_call($name,$params);
	}
	
	protected function create_mail(){
		if($this->mock_inside_call)return parent::create_mail();
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