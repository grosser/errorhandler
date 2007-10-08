<?php
class error_reporting_status_test extends UnitTestCase {
	/**
	 * save a state, change something, restore state, check if old = new
	 */
	function test_simple(){
		$old_error = error_reporting();
		$old_assert = assert_options(ASSERT_ACTIVE);
		$status = new ErrorReportingStatus();
		
		error_reporting(3453);
		assert_options(ASSERT_ACTIVE,!$old_assert);
		
		$status->restore();
		$new_assert = assert_options(ASSERT_ACTIVE);
		$new_error = error_reporting();
		
		$this->assertEqual($old_error,$new_error);
		$this->assertEqual($old_error,$new_error);
		$this->assertEqual($old_assert,$new_assert);
		
	}
}
?>