<?php
/**
 * @author Michael Grosser - grosser.michael-AT-gmail.com
 */
require_once('LogProcessor.class.php');
require_once('RSSRenderer.class.php');

class RSSProcessor extends LogProcessor{

	public function __construct($file=null){
		if($file) $this->set_cache_file($file,false);
		$this->set_renderer(new RSSRenderer());
	}

	const ENTRY_SEPERATOR = "NEXT_ENTRY\n";

	protected function render_error(Error $error){
		$this->add_content($this->get_renderer()->render($error));
	}
	
	protected function add_content($content){
		assert(is_string($content));
		$content = $content . self::ENTRY_SEPERATOR;
		@file_put_contents($this->get_cache_file(),$content,FILE_APPEND);
	}

	/**
	 * @return String rss
	 */
	public function rss(){
		$out = $this->rss_head();

		$content = $this->split_cache();
		
		$start = count($content)-1;
		$last_element = max(count($content)-10,0);
		for($i=$start;$i>=$last_element;$i--){
			$out .= $content[$i];
		}
		
		$out .= $this->rss_foot();

		return $out;
	}
	
	private function rss_foot(){
		return '</feed>';
	}
	
	/**
	 * @return array
	 */
	private function split_cache(){
		$content = @file_get_contents($this->get_cache_file());
		$content = split(self::ENTRY_SEPERATOR,$content);
		$last_element = count($content)-1; 
		unset($content[$last_element]);
		return $content;
	}

	/**
	 * Send XML Header
	 */
	public function header(){
		header("Content-type: application/atom+xml; charset=UTF-8");
	}
	
	private function rss_head(){
		$out = '<?xml version="1.0" encoding="utf-8"?>';
		$out .= '<feed xmlns="http://www.w3.org/2005/Atom">';
		$out .= '	<title>Error Log</title>';
		$out .= '	<updated>2007-04-13T15:52:08+02:00</updated>';//TODO: letzter eintrag ??
		$out .= '		<author>';
    	$out .= '			<name>RSS Processor</name>';
  		$out .= '		</author>';

		return $out;
	}

	//--------cache_file - using log_file as cache file
	private $cache_file;

	public function get_cache_file(){
		return $this->get_log_file();
	}

	/**
	 * Set the file to write to
	 *
	 * @param string $cache_file -- path
	 * @param Bool $with_test -- test if file is writeable
	 */
	public function set_cache_file($cache_file,$with_test=self::TEST_FILE){
		$this->set_log_file($cache_file,$with_test);
	}
	//--------END log_file
}
?>