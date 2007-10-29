<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('LogProcessor.class.php');
require_once('RSSRenderer.class.php');

class RSSProcessor extends LogProcessor{

	const ENTRY_SEPERATOR = "\n";
	const MAX_ENTRIES = 10;
	
	public function __construct($path_to_file){
		assert(is_string($path_to_file));
		$this->set_log_file($path_to_file,self::TEST_FILE);
		$this->set_renderer(new RSSRenderer());
	}

	protected function render_error(Error $error){
		$this->add_content($this->get_renderer()->render($error));
	}
	
	protected function add_content($added_content){
		assert(is_string($added_content));
		
		$content[] = $this->rss_head();
		$content[] = $added_content;
		
		eregi("(<entry>.*</entry>)",@file_get_contents($this->get_log_file()),$items);

		if(@$items[1]){
			//split string into array /w strings
			$items = explode('</entry>',$items[1]);//will leave 1 empty string
			foreach($items as $key => $item)if(trim($item))$items[$key]=$item.'</entry>';
			
			//add to content
			foreach($items as $key => $item){
				if($key>=self::MAX_ENTRIES-1)break;
				$content[]=$item.self::ENTRY_SEPERATOR;
			}
		}
		$content[]=$this->rss_foot();
		
		$content = implode(self::ENTRY_SEPERATOR,$content);
		
		file_put_contents($this->get_log_file(),$content);
	}

	private function rss_foot(){
		return '</feed>';
	}
	
	private function rss_head(){
		$out =  '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$out .= '<feed version="0.3" xmlns="http://www.w3.org/2005/Atom">'."\n";
		$out .= '	<title>Error Log</title>'."\n";
		$out .= '	<updated>2007-04-13T15:52:08+02:00</updated>'."\n";//TODO: letzter eintrag ??
		$out .= '	<author>'."\n";
    	$out .= '		<name>RSS Processor</name>'."\n";
  		$out .= '	</author>'."\n";

		return $out;
	}
}
?>