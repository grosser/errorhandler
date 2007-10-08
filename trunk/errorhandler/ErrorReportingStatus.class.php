<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
class ErrorReportingStatus {
	public function __construct(){
		$this->record_current_status();
	}
	
	/**
	 * Restore the State in that the Error/Assert system was when 
	 * this status was recorded
	 */
	public function restore(){
		$this->restore_assert();
		$this->restore_error();
	}
	
	private function restore_assert(){
		$state = $this->get_state();
		
		foreach($state['assert'] as $option => $value){
			assert_options($option,$value);
		}
	}
	
	private function restore_error(){
		$state = $this->get_state();
		foreach($state['error'] as $option => $value){
			$option($value);
		}
		
		//a non-custom errorhandler was called last
		//this might not always work...(activating twice in a row etc...)
		if(empty($state['error']['set_error_handler']))restore_error_handler();
	}
	
	private function record_current_status(){
		$assert_options = $this->retrive_assert_options();
		$error_options =  $this->retrive_error_options();
		
		$options = array(
			'assert'=>$assert_options,
			'error'=>$error_options,
		);
		$this->set_state($options);
	}
	
	private function  retrive_error_options(){
		$error_options = array(
			'error_reporting',
		);
		
		$error_options=array_flip($error_options);
		foreach($error_options as $name => $null){
			$error_options[$name] = $name();
		}

//		has to be handled seperately
		$old = set_error_handler('ErrorReportingStatus_fake_error_handler');
		restore_error_handler();
		if($old)$error_options['set_error_handler']=$old;
		
		return $error_options;
	}

	private function retrive_assert_options(){
		$assert_options = array(
			ASSERT_ACTIVE=>	null,
			ASSERT_WARNING=>	null,
			ASSERT_BAIL=>		null,
			ASSERT_QUIET_EVAL=>null,
			ASSERT_ACTIVE=>	null,
			ASSERT_CALLBACK=>	null,
		);
		
		foreach($assert_options as $name => $null){
			$assert_options[$name] = assert_options($name);
		}
		return $assert_options;
	}
	
	//--------state
	private $state;
		
	private function get_state(){
		if(!isset($this->state)) throw new Exception('State never recorded, impossible !?');
		return $this->state;
	}
	private function set_state(array $state){
		$this->state = $state;
	}
	//--------END state

}

function ErrorReportingStatus_fake_error_handler(){
	throw new Exception('Status recording failed...');
}



/*
 maybe someone can use this....
 this are options that can be set with ini_set...
	$unhandled_options = array(
		'error_log'				=>null,
		'display_errors'		=>null,
		'display_startup_errors'=>null,
		'log_errors'			=>null,
		'log_errors_max_len'	=>null,
		'ignore_repeated_errors'=>null,
		'ignore_repeated_source'=>null,
		'report_memleaks'		=>null,
		'track_errors'			=>null,
		'html_errors'			=>null,
		'docref_root'			=>null,
		'docref_ext'			=>null,
		'error_prepend_string'=>null,
		'error_append_string'=>null,
		'warn_plus_overloading'=>null,
	);
 */
?>