<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

/**
 * Test class for JApplicationSite.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Application
 * @since       3.2
 */
class JApplicationSiteTest extends TestCaseDatabase
{
	/**
	 * Value for test host.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_HTTP_HOST = 'mydomain.com';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_USER_AGENT = 'Mozilla/5.0';

	/**
	 * Value for test user agent.
	 *
	 * @var    string
	 * @since  3.2
	 */
	const TEST_REQUEST_URI = '/index.php';

	/**
	 * An instance of the class to test.
	 *
	 * @var    JApplicationSite
	 * @since  3.2
	 */
	protected $class;

	/**
	 * Backup of the SERVER superglobal
	 *
	 * @var    array
	 * @since  3.4
	 */
	protected $backupServer;

	/**
	 * Data for fetchConfigurationData method.
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getRedirectData()
	{
		return array(
			// Note: url, base, request, (expected result)
			array('/foo', 'http://mydomain.com/', 'http://mydomain.com/index.php?v=3.2', 'http://mydomain.com/foo'),
			array('foo', 'http://mydomain.com/', 'http://mydomain.com/index.php?v=3.2', 'http://mydomain.com/foo'),
		);
	}

	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		JFactory::$document = $this->getMockDocument();
		JFactory::$language = $this->getMockLanguage();
		JFactory::$session  = $this->getMockSession();

		$this->backupServer = $_SERVER;

		$_SERVER['HTTP_HOST'] = self::TEST_HTTP_HOST;
		$_SERVER['HTTP_USER_AGENT'] = self::TEST_USER_AGENT;
		$_SERVER['REQUEST_URI'] = self::TEST_REQUEST_URI;
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		// Set the config for the app
		$config = new Registry;
		$config->set('session', false);

		// Get a new JApplicationSite instance.
		$this->class = new JApplicationSite($this->getMockInput(), $config);
		TestReflection::setValue('JApplicationCms', 'instances', array('site' => $this->class));
	}

	/**
	 * Overrides the parent tearDown method.
	 *
	 * @return  void
	 *
	 * @see     \PHPUnit\Framework\TestCase::tearDown()
	 * @since   3.2
	 */
	protected function tearDown()
	{
		// Reset the dispatcher and application instances.
		TestReflection::setValue('JEventDispatcher', 'instance', null);
		TestReflection::setValue('JApplicationCms', 'instances', array());

		$_SERVER = $this->backupServer;
		unset($this->backupServer, $config, $this->class);
		$this->restoreFactoryState();

		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.2
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');
		$dataSet->addTable('jos_menu', JPATH_TEST_DATABASE . '/jos_menu.csv');
		$dataSet->addTable('jos_menu_types', JPATH_TEST_DATABASE . '/jos_menu_types.csv');
		$dataSet->addTable('jos_template_styles', JPATH_TEST_DATABASE . '/jos_template_styles.csv');
		$dataSet->addTable('jos_usergroups', JPATH_TEST_DATABASE . '/jos_usergroups.csv');
		$dataSet->addTable('jos_users', JPATH_TEST_DATABASE . '/jos_users.csv');
		$dataSet->addTable('jos_viewlevels', JPATH_TEST_DATABASE . '/jos_viewlevels.csv');

		return $dataSet;
	}

	/**
	 * Tests the JApplicationCms::getClientId method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getClientId
	 */
	public function testGetClientId()
	{
		$this->assertSame(0, $this->class->getClientId());
	}

	/**
	 * Tests the JApplicationCms::getName method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getName
	 */
	public function testGetName()
	{
		$this->assertSame('site', $this->class->getName());
	}

	/**
	 * Tests the JApplicationCms::getMenu method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getMenu
	 */
	public function testGetMenu()
	{
		$this->assertInstanceOf('JMenuSite', $this->class->getMenu());
	}

	/**
	 * Tests the JApplicationSite::getParams method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getParams
	 */
	public function testGetParams()
	{
		JFactory::$application = $this->class;
		$this->class->loadLanguage();
		$params = $this->class->getParams('com_content');

		$this->assertEquals(
			$params->get('show_item_navigation'),
			'1',
			'com_content show_item_navigation defaults to 1'
		);
	}

	/**
	 * Tests the JApplicationCms::getPathway method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getPathway
	 */
	public function testGetPathway()
	{
		$this->assertInstanceOf('JPathwaySite', $this->class->getPathway());
	}

	/**
	 * Tests the JApplicationSite::getRouter method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getRouter
	 */
	public function testGetRouter()
	{
		$this->assertInstanceOf('JRouterSite', JApplicationSite::getRouter());
	}

	/**
	 * Tests the JApplicationSite::getTemplate method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getTemplate
	 */
	public function testGetTemplate()
	{
		$template = $this->class->getTemplate(true);

		$this->assertInstanceOf('\\Joomla\\Registry\\Registry', $template->params);

		$this->assertEquals('protostar', $template->template);
	}

	/**
	 * Tests the JApplicationCms::isAdmin method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::isAdmin
	 */
	public function testIsAdmin()
	{
		$this->assertFalse($this->class->isAdmin());
	}

	/**
	 * Tests the JApplicationCms::isSite method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::isSite
	 */
	public function testIsSite()
	{
		$this->assertTrue($this->class->isSite());
	}

	/**
	 * Tests the JApplicationCms::isClient method.
	 *
	 * @return  void
	 *
	 * @since   3.7.0
	 * @covers  JApplicationSite::isClient
	 */
	public function testIsClient()
	{
		$this->assertFalse($this->class->isClient('administrator'));
		$this->assertTrue($this->class->isClient('site'));
	}

	/**
	 * Tests the JApplicationCms::render method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::render
	 */
	public function testRender()
	{
		JFactory::$application = $this->class;

		$document = $this->getMockDocument();

		$this->assignMockReturns($document, array('render' => 'JWeb Body'));

		// Manually inject the document.
		TestReflection::setValue($this->class, 'document', $document);

		TestReflection::invoke($this->class, 'render');

		$this->assertEquals(array('JWeb Body'), TestReflection::getValue($this->class, 'response')->body);
	}

	/**
	 * Tests the JApplicationSite::setDetectBrowser and getDetectBrowser methods.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getDetectBrowser
	 * @covers  JApplicationSite::setDetectBrowser
	 */
	public function testSetGetDetectBrowser()
	{
		$this->assertFalse($this->class->setDetectBrowser(true));

		$this->assertTrue($this->class->getDetectBrowser());
	}

	/**
	 * Tests the JApplicationSite::setLanguageFilter and getLanguageFilter methods.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::getLanguageFilter
	 * @covers  JApplicationSite::setLanguageFilter
	 */
	public function testSetGetLanguageFilter()
	{
		$this->assertFalse($this->class->setLanguageFilter(true));

		$this->assertTrue($this->class->getLanguageFilter());
	}

	/**
	 * Tests the JApplicationSite::setTemplate method.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 * @covers  JApplicationSite::setTemplate
	 */
	public function testSetTemplate()
	{
		$this->class->setTemplate('beez3');

		$template = $this->class->getTemplate(true);

		$this->assertInstanceOf('\\Joomla\\Registry\\Registry', $template->params);

		$this->assertEquals('beez3', $template->template);
	}
}
