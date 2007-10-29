<?php
error_reporting(E_ALL);

require_once('../../errorhandler/ErrorHandlerGW.class.php');


ErrorHandlerGW::initialize(E_ALL);
ErrorHandlerGW::set('rss',"test.xml");
test_test(array('testen'=>"t>e>s>t"));

function test_test($c){
	$x = $u;
}
?>