<?php
class error_handler_test extends UnitTestCase {
	/**
	 * @var ErrorHandler
	 */
	private $handler;
	
	function SetUp(){
		error_reporting(E_ALL);
		$this->handler = ErrorHandler::get_instance();
		$this->handler->remove_listeners();
		
		$this->processor1 = new MockErrorProcessor();
		$this->processor2 = new MockErrorProcessor();
	}
	
	function TearDown(){
		//reset to defaults
		$this->handler->ignore_errors(array(
			'/usr/share/php/'=>E_NOTICE | E_STRICT,
			'simpletest/'=>E_NOTICE | E_STRICT,
			'/xorc/'=>E_NOTICE | E_STRICT,
		));
	}
	
	function test_activate(){
		//level was remembered
		$level = 4444;
		error_reporting($level);
		$this->handler->activate();
		
		error_reporting(34535);
		
		$this->handler->deactivate();
		$this->assertEqual(error_reporting(),$level);
		
		//level unchanged by second activate
		error_reporting($level);
		$this->handler->activate();
		
		error_reporting(2354);
		$this->handler->activate();
		
		$this->handler->deactivate();
		$this->assertEqual(error_reporting(),$level);
	}
	
	function test_deactivate(){
		$mock = $this->add_mock_processor();
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		$this->handler->deactivate();
		
		ErrorCreator::E_NOTICE();
		$this->assertError();
		
		$this->assertTrue($mock->get_count('notify_of_error')==1);
	}
	
	function test_callback(){
		//--------no error -- @
		error_reporting(0);
		
		$this->handler->add_listener($this->processor1);
		$this->handler->add_listener($this->processor2);
		
		$this->handler->error_callback(123,'dssdsd','sdsdfd');
		
		$this->assertEqual($this->processor1->get_count('notify_of_error'),0);
		$this->assertEqual($this->processor2->get_count('notify_of_error'),0);
		
		//--------unrecognized error error
		error_reporting(E_ALL);
		$this->handler->error_callback(E_STRICT,'dssdsd','sdsdfd');
		
		$this->assertEqual($this->processor1->get_count('notify_of_error'),0);
		$this->assertEqual($this->processor2->get_count('notify_of_error'),0);
		
		//--------some error
		$this->handler->error_callback(E_WARNING,'Nachricht','Datei');
		
		$this->assertEqual($this->processor1->get_count('notify_of_error'),1);
		$this->assertEqual($this->processor2->get_count('notify_of_error'),1);
		
		//--------unknown error
		$this->handler->error_callback(123,'Nachricht','Datei');
		
		$this->assertEqual($this->processor1->get_count('notify_of_error'),2);
		$this->assertEqual($this->processor2->get_count('notify_of_error'),2);
	}
	
	function test_error_obj(){
		//--------some error
		$this->handler->add_listener($this->processor1);
		
		$type = 1;
		$message = 'Nachricht';
		error_callback($type,$message,'Datei');
		
		$params = $this->processor1->get_param('notify_of_error');
		$error = $params[0];
		
		$trace = $error->get_backtrace();
		$this->assertFalse(empty($trace));
		$this->assertEqual($error->get_message(),$message);
		$this->assertEqual($error->get_type(),$type);
		
		//--------assertion
		$message = 'Mein Assert';
		$this->handler->assert_callback($message);
		$params = $this->processor1->get_param('notify_of_error');
		$error = $params[0];
		
		$real_out = 'Condition: Mein Assert failed!';

		$this->assertEqual($error->get_message(),$real_out);
		$this->assertTrue($error->is_assertion());
	}
	
	function test_error_ignoring_black(){
		$mock = $this->add_mock_processor();
		
		//before
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		//ignore creator - notice
		$this->handler->ignore_errors(array(
			'ErrorCreator'=>E_NOTICE
		));
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		ErrorCreator::E_WARNING();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
		
		//ignore creator - notice + warning - incasesensitive
		$this->handler->ignore_errors(array(
			'errorcreator'=>E_NOTICE|E_WARNING
		),ErrorHandler::IGNORE_BLACKLIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
		
		ErrorCreator::E_WARNING();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
		
		//ignore creator - all
		$this->handler->ignore_errors(array('errorcreator'),ErrorHandler::IGNORE_BLACKLIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
		
	}

	function test_error_ignoring_white(){
		$mock = $this->add_mock_processor();
		
		//nothing allowed
		$this->handler->ignore_errors(array(),ErrorHandler::IGNORE_WHITELIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==0);
		
		//ignore creator - notice
		$this->handler->ignore_errors(array(
			'ErrorCreator'=>E_NOTICE
		),ErrorHandler::IGNORE_WHITELIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		ErrorCreator::E_WARNING();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		//ignore creator - notice + warning - incasesensitive
		$this->handler->ignore_errors(array(
			'errorcreator'=>E_NOTICE|E_WARNING
		),ErrorHandler::IGNORE_WHITELIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
		
		ErrorCreator::E_WARNING();
		$this->assertTrue($mock->get_count('notify_of_error')==3);
		
		//ignore creator - all - incasesensitive
		$this->handler->ignore_errors(array(
			'errorcreator'
		),ErrorHandler::IGNORE_WHITELIST);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==4);
	}

	function test_error_handling_type(){
		$this->handler->error_handling(ErrorHandler::HANDLING_ONCE);
		$mock = $this->add_mock_processor();
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==1);
		
		$this->handler->error_handling(ErrorHandler::HANDLING_MULTIPLE);
		
		ErrorCreator::E_NOTICE();
		$this->assertTrue($mock->get_count('notify_of_error')==2);
	}
	
	private function add_mock_processor(){
		$this->handler->activate_error_reporting();
		$mock = new MockErrorProcessor();
		$this->handler->add_listener($mock);
		return $mock;
	}
}
?>



















