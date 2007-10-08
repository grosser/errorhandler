<?
set_include_path("/usr/share/php");
require_once('../errorhandler/ErrorHandler.class.php');
require_once('../errorhandler/ErrorHandler/RSSProcessor.class.php');

$file = dirname(__FILE__).'/rss.cache';
@unlink($file);
$pro = new RSSProcessor($file);
ErrorHandler::get_instance()->add_listener($pro);
ErrorHandler::get_instance()->activate(E_NOTICE);

$ho = $no;
$ho = $no;
$pro->header();
print $pro->rss();

?>