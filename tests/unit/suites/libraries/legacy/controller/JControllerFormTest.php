<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Controller
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once __DIR__ . '/stubs/controllerform.php';


/**
 * Test class for JControllerForm.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Component
 *
 * @since       3.1.4
 */
class JControllerFormTest extends TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$this->saveFactoryState();

		JFactory::$application = $this->getMockCmsApp();
		JFactory::$config = $this->getMockConfig();

		parent::setUp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();
		parent::tearDown();
	}

	/**
	 * Tests the JControllerForm constructor.
	 *
	 * @since   1.7.0
	 *
	 * @return  void
	 */
	public function testConstructor()
	{
		// Test the auto-naming of the _option, _context, _view_item and _view_list
		$object = new MincesControllerMince(
			array(
				// Neutralise a JPATH_COMPONENT not defined error.
				'base_path' => JPATH_BASE . '/component/com_foobar'
			)
		);

		$this->assertAttributeEquals('com_minces', 'option', $object, 'Checks the _option variable was created properly.');

		$this->assertAttributeEquals('mince', 'context', $object, 'Check the _context variable was created properly.');

		$this->assertAttributeEquals('mince', 'view_item', $object, 'Check the _view_item variable was created properly.');

		$this->assertAttributeEquals('minces', 'view_list', $object, 'Check the _view_list variable was created properly.');

		// Test for correct pluralisation.
		$object = new MiniesControllerMiny(
			array(
				// Neutralise a JPATH_COMPONENT not defined error.
				'base_path' => JPATH_BASE . '/component/com_foobar'
			)
		);

		$this->assertAttributeEquals('minies', 'view_list', $object, 'Check the _view_list variable was created properly');

		$object = new MintsControllerMint(
			array(
				// Neutralise a JPATH_COMPONENT not defined error.
				'base_path' => JPATH_BASE . '/component/com_foobar'
			)
		);

		$this->assertAttributeEquals('mints', 'view_list', $object, 'Check the _view_list variable was created properly');
	}
}
