<?php
class error_handler_gw_test extends UnitTestCase {
	
	/**
	 * @var ErrorHandler
	 */
	public $handler;

	function SetUp(){
		error_reporting(E_ALL);
		$this->handler = ErrorHandler::get_instance();
		$this->handler->remove_listeners();
	}
	
	public function test_report(){
		ErrorHandlerGW::report(ErrorHandler::HANDLING_BAIL);
		ErrorHandlerGW::report(ErrorHandler::HANDLING_ONCE);
		ErrorHandlerGW::report(ErrorHandler::HANDLING_MULTIPLE);
		
		try {
			ErrorHandlerGW::report('wtf');
			$this->fail();
		} catch (Exception $e) {
			$this->pass();
		}
		
	}
	
	function test_initialize(){
		//no params
		ErrorHandlerGW::initialize();
		$listeners = $this->handler->get_listeners();
		
		$this->assertTrue(count($listeners)==1);
		$this->assertTrue(get_class($listeners[0]) == 'DirectProcessor');
		
		//with params
		ErrorHandlerGW::initialize(E_STRICT);
		$this->assertTrue(error_reporting()== E_STRICT^E_ERROR);//no fatal errors without E_ERROR
		
		//nothing added when called twice
		$this->handler->remove_listeners();
		$this->handler->add_listener(new LogProcessor('test.log'));
		
		ErrorHandlerGW::initialize(E_STRICT);
		$listeners = $this->handler->get_listeners();
		$this->assertTrue(count($listeners)==1);
		$this->assertTrue(get_class($listeners[0])=='LogProcessor');
	}

	function test_add(){
		//simple
		ErrorHandlerGW::add(ErrorHandlerGW::LOG_PROCESSOR,"test.log");
		$listeners = $this->handler->get_listeners();
		
		$this->assertTrue(get_class($listeners[0])=='LogProcessor');
		
		//exception
		try{
			ErrorHandlerGW::add(ErrorHandlerGW::LOG_PROCESSOR);
			$this->fail();
		} catch(Exception $e){}
		
		//add another
		ErrorHandlerGW::add(ErrorHandlerGW::RSS_PROCESSOR,"test.rss");
		$listeners = $this->handler->get_listeners();
		
		$this->assertTrue(get_class($listeners[0])=='LogProcessor');
		$this->assertTrue(get_class($listeners[1])=='RSSProcessor');
	}
	
	function test_set(){
		ErrorHandlerGW::add(ErrorHandlerGW::RSS_PROCESSOR,"test.rss");
		ErrorHandlerGW::add(ErrorHandlerGW::RSS_PROCESSOR,"test2.rss");
		ErrorHandlerGW::set(ErrorHandlerGW::DIRECT_PROCESSOR);
		$listeners = $this->handler->get_listeners();
		
		$this->assertTrue(count($listeners)==1);
		$this->assertTrue(get_class($listeners[0])=='DirectProcessor');
	}
	
	function test_ignore(){
		ErrorHandlerGW::initialize(E_ALL);
		
		//default
		ErrorHandlerGW::ignore(array('ErrorCreator'));
		ErrorCreator::E_WARNING();
		
		//white
		ErrorHandlerGW::ignore(array(),ErrorHandler::IGNORE_WHITELIST);
		ErrorCreator::E_WARNING();
		
		//black
		ErrorHandlerGW::ignore(array('ErrorCreator'),ErrorHandler::IGNORE_BLACKLIST);
		ErrorCreator::E_WARNING();
		
		//reset
		ErrorHandlerGW::ignore(
			array(
			'/usr/share/php/'=>E_NOTICE | E_STRICT,
			'simpletest/'=>E_NOTICE | E_STRICT,
			'/xorc/'=>E_NOTICE | E_STRICT,
			));
	}
}
?>



















