<?php
App::uses('TwitterPermission', 'Model');

/**
 * TwitterPermission Test Case
 *
 */
class TwitterPermissionTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.twitter_permission'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TwitterPermission = ClassRegistry::init('TwitterPermission');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TwitterPermission);

		parent::tearDown();
	}

}
