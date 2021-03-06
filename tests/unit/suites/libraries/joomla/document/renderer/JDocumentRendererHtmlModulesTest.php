<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Document
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JDocumentRendererHtmlModules.
 *
 * @since  3.6
 */
class JDocumentRendererHtmlModulesTest extends TestCaseDatabase
{
	/**
	 * @var  JEventDispatcher
	 */
	protected $dispatcher;

	/**
	 * Flag indicating execution of onAfterRenderModules event in JDocumentRendererHtmlModules::render
	 * method.
	 *
	 * @var  boolean
	 */
	protected $callbackExecuted;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		$mockApp = $this->getMockCmsApp();
		$mockApp->expects($this->any())
			->method('getName')
			->willReturn('site');

		$mockApp->expects($this->any())
			->method('isClient')
			->with('site')
			->willReturn(true);

		JFactory::$application = $mockApp;
		JFactory::$session     = $this->getMockSession();
		$this->dispatcher      = new JEventDispatcher;
		TestReflection::setValue($this->dispatcher, 'instance', $this->dispatcher);
		$this->dispatcher->register('onAfterRenderModules', array($this, 'eventCallback'));

		$mockRouter = $this->getMockBuilder('Joomla\\CMS\\Router\\Router')->getMock();
		$mockRouter->expects($this->any())
			->method('build')
			->willReturn(new \JUri);

		TestReflection::setValue('JRoute', '_router', array('site' => $mockRouter));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function tearDown()
	{
		TestReflection::setValue('JRoute', '_router', array());

		$this->restoreFactoryState();
		TestReflection::setValue($this->dispatcher, 'instance', null);
		parent::tearDown();
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 *
	 * @since   3.6
	 */
	protected function getDataSet()
	{
		$dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');

		$dataSet->addTable('jos_extensions', JPATH_TEST_DATABASE . '/jos_extensions.csv');
		$dataSet->addTable('jos_modules', JPATH_TEST_DATABASE . '/jos_modules.csv');
		$dataSet->addTable('jos_modules_menu', JPATH_TEST_DATABASE . '/jos_modules_menu.csv');

		return $dataSet;
	}

	/**
	 * Test JDocumentRendererHtmlModules::render
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function testRender()
	{
		$document               = new JDocumentHtml;
		$renderer               = $document->loadRenderer('modules');
		$params                 = array('name' => 'position-0', 'style' => 'xhtml');
		$this->callbackExecuted = false;
		$output                 = $renderer->render('position-0', $params);
		$htmlClean              = trim(preg_replace('~>\s+<~', '><', $output));
		$this->assertTrue($this->callbackExecuted, 'onAfterRenderModules event is not executed');
		$html = '<div class="moduletable"><h3>Search</h3><div class="search mod_search63">'
			. '<form action="" method="post" class="form-inline" role="search">'
			. '<label for="mod-search-searchword63" class="element-invisible">Search ...</label>'
			. '<input name="searchword" id="mod-search-searchword63" maxlength="200"  '
			. 'class="inputbox search-query input-medium" type="search" size="20" placeholder="Search ..." />'
			. '<input type="hidden" name="task" value="search" /><input type="hidden" name="option" value="com_search" />'
			. '<input type="hidden" name="Itemid" value="" /></form></div></div>';
		$this->assertEquals($html, $htmlClean, 'render output does not match expected content');
	}

	/**
	 * Callback for event 'onAfterRenderModules'
	 *
	 * @param   string  &$buffer  contains rendered output from JDocumentRendererHtmlModules::render
	 *
	 * @param   array   &$params  template position and style parameters
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	public function eventCallback(&$buffer, &$params)
	{
		$this->assertContains('mod_search63', $buffer, 'buffer empty when processing onAfterRenderModules event');
		$this->assertArrayHasKey('name', $params, "params['name'] empty when processing onAfterRenderModules event");
		$this->callbackExecuted = true;
	}
}

