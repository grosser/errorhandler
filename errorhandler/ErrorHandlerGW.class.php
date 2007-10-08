<?php
/**
@author Micha
*/
require_once('ErrorHandler.class.php');
class ErrorHandlerGW {

	const DIRECT_PROCESSOR = 'direct';
	const LOG_PROCESSOR = 'log';
	const RSS_PROCESSOR = 'rss';

	/**
	 * @return ErrorHandler
	 */
	protected static function eh_instance(){
		return ErrorHandler::get_instance();
	}
	
	public static function initialize($error_level=E_ALL){
		$inst = self::eh_instance();
		$listeners = $inst->get_listeners();
		if(empty($listeners)){
			self::add(self::DIRECT_PROCESSOR);
		}
		$inst->activate($error_level);
	}

	public static function set($processor_name,$file_path=null){
		self::eh_instance()->remove_listeners();
		self::add($processor_name,$file_path);
	}

	public static function add($processor_name,$file_path=null){
		switch(strtolower($processor_name)){
			case self::DIRECT_PROCESSOR:
				$classname = "DirectProcessor";
				break;
			case self::LOG_PROCESSOR:
				$classname = 'LogProcessor';
				if(!$file_path)throw new Exception('For LogProcessor filename cannot be empty');
				break;
			case self::RSS_PROCESSOR:
				$classname = 'RSSProcessor';
				break;
			default:
				trigger_error("ErrorProcessor $processor_name is unknown! try (".self::DIRECT_PROCESSOR."),
					(".self::LOG_PROCESSOR.",'logfile'),(".self::RSS_PROCESSOR.",'rssoutput')");
				return;
		}

		self::execute_add($classname,$file_path);
	}

	private static function execute_add($classname,$file_path=null){
		require_once($classname.'.class.php');
		$processor = isset($file_path)?new $classname():new $classname($file_path);
		 self::eh_instance()->add_listener($processor);
	}

	public function ignore(array $list,$ignore_type=ErrorHandler::IGNORE_BLACKLIST){
		 self::eh_instance()->ignore_errors($list,$ignore_type);
	}
}
?>