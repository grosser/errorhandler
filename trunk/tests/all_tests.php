<?php
class all_tests {
	public static function tested_dir(){
		return "/home/data/micha/_LIB/errorhandler";
	}
	
	public static function run_html(){
		$test = self::run();
		$test->run(new HtmlReporter());
	}
	
	private static function run(){
		require_once('/home/data/micha/_LIB/simpletest/unit_tester.php');
		require_once('/home/data/micha/_LIB/simpletest/reporter.php');
		//require_once('/home/data/micha/_LIB/simpletest/mock_objects.php');

		
		require_once(self::tested_dir().'/errorhandler/ErrorHandler.class.php');
		require_once(self::tested_dir().'/errorhandler/ErrorHandlerGW.class.php');
		require_once(self::tested_dir().'/errorhandler/ErrorProcessor.class.php');
		require_once(self::tested_dir().'/errorhandler/ErrorReportingStatus.class.php');
		require_once(self::tested_dir().'/errorhandler/TextRenderer.class.php');
		require_once(self::tested_dir().'/errorhandler/HtmlRenderer.class.php');
		require_once(self::tested_dir().'/errorhandler/RSSRenderer.class.php');
		require_once(self::tested_dir().'/errorhandler/LogProcessor.class.php');
		require_once(self::tested_dir().'/errorhandler/RSSProcessor.class.php');
		require_once(self::tested_dir().'/errorhandler/ErrorReportingStatus.class.php');
		require_once("ErrorCreator.class.php");
		require_once("MockErrorProcessor.class.php");
		require_once('TestHtmlRenderer.class.php');
		
		
		
		$test = new TestSuite('ErrorHandler');
		$test->addTestFile(self::tested_dir().'/tests/error_reporting_status_test.php');
		$test->addTestFile(self::tested_dir().'/tests/error_handler_test.php');
		$test->addTestFile(self::tested_dir().'/tests/error_handler_gw_test.php');
		//$test->addTestFile('renderer_test.php');
		$test->addTestFile(self::tested_dir().'/tests/text_renderer_test.php');
		$test->addTestFile(self::tested_dir().'/tests/html_renderer_test.php');
		$test->addTestFile(self::tested_dir().'/tests/rss_renderer_test.php');
		$test->addTestFile(self::tested_dir().'/tests/log_processor_test.php');
		$test->addTestFile(self::tested_dir().'/tests/rss_processor_test.php');
		return $test;
	}
	
	public static function run_text(){
		$test = self::run();
		$test->run(new TextReporter());
	}
}
?>