<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Log
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JLogLoggerEcho.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Log
 * @since       1.7.0
 */
class JLogLoggerEchoTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test the JLogLoggerEcho::addEntry method.
	 *
	 * @return void
	 */
	public function testAddEntry01()
	{
		// Create bogus config.
		$config = array();

		// Get an instance of the logger.
		$logger = new JLogLoggerEcho($config);

		$this->expectOutputString("DEBUG: TESTING [deprecated]\n");
		$logger->addEntry(new JLogEntry('TESTING', JLog::DEBUG, 'DePrEcAtEd'));
	}

	/**
	 * Test the JLogLoggerEcho::addEntry method.
	 *
	 * @return void
	 */
	public function testAddEntry02()
	{
		// Create bogus config.
		$config = array();

		// Get an instance of the logger.
		$logger = new JLogLoggerEcho($config);

		$this->expectOutputString("CRITICAL: TESTING2 [bam]\n");
		$logger->addEntry(new JLogEntry('TESTING2', JLog::CRITICAL, 'BAM'));
	}

	/**
	 * Test the JLogLoggerEcho::addEntry method.
	 *
	 * @return void
	 */
	public function testAddEntry03()
	{
		// Create bogus config.
		$config = array();

		// Get an instance of the logger.
		$logger = new JLogLoggerEcho($config);

		$this->expectOutputString("ERROR: Testing3\n");
		$logger->addEntry(new JLogEntry('Testing3', JLog::ERROR));
	}

	/**
	 * Test the JLogLoggerEcho::addEntry method.
	 *
	 * @return void
	 */
	public function testAddEntry04()
	{
		// Create bogus config.
		$config = array();

		// Get an instance of the logger.
		$logger = new JLogLoggerEcho($config);

		$this->expectOutputString("INFO: Testing 4\n");
		$logger->addEntry(new JLogEntry('Testing 4'));
	}
}
