<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Event
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once __DIR__ . '/JEventInspector.php';

/**
 * Test class for JEventDispatcher.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       1.7.3
 */
class JEventDispatcherTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @var JEventDispatcher
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->object = new JEventDispatcher;
		TestReflection::setValue($this->object, 'instance', $this->object);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		TestReflection::setValue($this->object, 'instance', null);
		unset($this->object);
	}

	/**
	 * Tests the JEventDispatcher::getInstance method.
	 *
	 * @return  void
	 *
	 * @since   1.7.3
	 */
	public function testGetInstance()
	{
		$mock = JEventDispatcher::getInstance();

		$this->assertInstanceOf(
			'JEventDispatcher',
			$mock
		);

		TestReflection::setValue($this->object, 'instance', null);

		$instance = JEventDispatcher::getInstance();

		$this->assertInstanceOf(
			'JEventDispatcher',
			$instance,
			'Tests that getInstance returns a JEventDispatcher object.'
		);

		// Push a new instance into the class.
		TestReflection::setValue($this->object, 'instance', 'foo');

		$this->assertThat(
			JEventDispatcher::getInstance(),
			$this->equalTo('foo'),
			'Tests that a subsequent call to JEventDispatcher::getInstance returns the cached singleton.'
		);

		TestReflection::setValue($this->object, 'instance', $mock);
	}

	/**
	 * Test JEventDispatcher::getState().
	 *
	 * @return void
	 *
	 * @since  1.7.3
	 */
	public function testGetState()
	{
		$this->assertThat(
			$this->object->getState(),
			$this->equalTo(null)
		);

		TestReflection::setValue($this->object, '_state', 'test');

		$this->assertThat(
			$this->object->getState(),
			$this->equalTo('test')
		);
	}

	/**
	 * Test JEventDispatcher::register().
	 *
	 * @since 1.7.3
	 *
	 * @return void
	 */
	public function testRegister()
	{
		// We have an empty Dispatcher object
		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(array())
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(array())
		);

		// We register a function on the event 'onTestEvent'
		$this->object->register('onTestEvent', 'JEventMockFunction');

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					array('event' => 'onTestEvent', 'handler' => 'JEventMockFunction')
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array('ontestevent' => array(0))
			)
		);

		// We register the same function on a different event 'onTestOtherEvent'
		$this->object->register('onTestOtherEvent', 'JEventMockFunction');

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					array('event' => 'onTestEvent', 'handler' => 'JEventMockFunction'),
					array('event' => 'onTestOtherEvent', 'handler' => 'JEventMockFunction')
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0),
					'ontestotherevent' => array(1)
				)
			)
		);

		// Now we attach a class to the dispatcher
		$this->object->register('', 'JEventInspector');

		$observers = TestReflection::getValue($this->object, '_observers');
		$object = $observers[2];

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					array('event' => 'onTestEvent', 'handler' => 'JEventMockFunction'),
					array('event' => 'onTestOtherEvent', 'handler' => 'JEventMockFunction'),
					$object
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0, 2),
					'ontestotherevent' => array(1)
				)
			)
		);
	}

	/**
	 * Test JEventDispatcher::register() with an error.
	 *
	 * @since              3.0.0
	 * @expectedException  InvalidArgumentException
	 *
	 * @return void
	 */
	public function testRegisterException()
	{
		$this->object->register('fakeevent', 'nonExistingClass');
	}

	/**
	 * Test JEventDispatcher::trigger().
	 *
	 * @since    1.7.3
	 *
	 * @return void
	 */
	public function testTrigger()
	{
		$this->object->register('onTestEvent', 'JEventMockFunction');
		$this->object->register('', 'JEventInspector');

		// We check a non-existing event
		$this->assertThat(
			$this->object->trigger('onFakeEvent'),
			$this->equalTo(array())
		);

		// Let's check the existing event "onTestEvent" without parameters
		$this->assertThat(
			$this->object->trigger('onTestEvent'),
			$this->equalTo(
				array(
					'JEventDispatcherMockFunction executed',
					''
				)
			)
		);

		// Let's check the existing event "onTestEvent" with parameters
		$this->assertThat(
			$this->object->trigger('onTestEvent', array('one', 'two')),
			$this->equalTo(
				array(
					'JEventDispatcherMockFunction executed',
					'onetwo'
				)
			)
		);

		// We check a situation where the observer is broken. Joomla should handle this gracefully
		TestReflection::setValue($this->object, '_observers', array());

		$this->assertThat(
			$this->object->trigger('onTestEvent'),
			$this->equalTo(array())
		);
	}

	/**
	 * Test JEventDispatcher::attach().
	 *
	 * @since 1.7.3
	 *
	 * @return void
	 */
	public function testAttach()
	{
		// Let's test an invalid observer
		$observer = array();

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(array())
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(array())
		);

		// Let's test an uncallable observer
		$observer = array('handler' => 'fakefunction', 'event' => 'onTestEvent');

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(array())
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(array())
		);

		// Let's test a callable function observer
		$observer = array('handler' => 'JEventMockFunction', 'event' => 'onTestEvent');
		$observers = array($observer);

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo($observers)
		);

		// Let's test that an observer is not attached twice
		$observer = array('handler' => 'JEventMockFunction', 'event' => 'onTestEvent');
		$observers = array($observer);

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo($observers)
		);

		// Let's test an invalid object
		$observer = new stdClass;

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo($observers)
		);

		// Let's test a valid event object
		$observer = new JEventInspector($this->object);
		$observers[] = $observer;

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0, 1)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo($observers)
		);

		// Let's test that an object observer is not attached twice
		$observer = new JEventInspector($this->object);

		$this->object->attach($observer);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0, 1)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo($observers)
		);
	}

	/**
	 * Test JEventDispatcher::detach().
	 *
	 * @since 1.7.3
	 *
	 * @return void
	 */
	public function testDetach()
	{
		// Adding 3 events to detach later
		$observer1 = array('handler' => 'fakefunction', 'event' => 'onTestEvent');
		$observer2 = array('handler' => 'JEventMockFunction', 'event' => 'onTestEvent');
		$this->object->attach($observer2);
		$observer3 = new JEventInspector($this->object);
		$this->object->attach($observer3);

		// Test removing a non-existing observer
		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0, 1)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					$observer2,
					$observer3
				)
			)
		);

		$return = $this->object->detach($observer1);

		$this->assertFalse($return);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(0, 1)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					$observer2,
					$observer3
				)
			)
		);

		// Test removing a functional observer
		$return = $this->object->detach($observer2);

		$this->assertTrue($return);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array(1 => 1)
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(
				array(
					1 => $observer3
				)
			)
		);

		// Test removing an object observer with more than one event
		$return = $this->object->detach($observer3);

		$this->assertTrue($return);

		$this->assertThat(
			TestReflection::getValue($this->object, '_methods'),
			$this->equalTo(
				array(
					'ontestevent' => array()
				)
			)
		);

		$this->assertThat(
			TestReflection::getValue($this->object, '_observers'),
			$this->equalTo(array())
		);
	}
}
