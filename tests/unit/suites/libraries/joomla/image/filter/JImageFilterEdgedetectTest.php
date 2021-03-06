<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Image
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Test class for JImage.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Image
 * @since       2.5.0
 */
class JImageFilterEdgedetectTest extends TestCase
{
	/**
	 * Setup for testing.
	 *
	 * @return  void
	 *
	 * @since   2.5.0
	 */
	protected function setUp()
	{
		// Verify that GD support for PHP is available.
		if (!extension_loaded('gd'))
		{
			$this->markTestSkipped('No GD support so skipping JImage tests.');
		}

		parent::setUp();
	}

	/**
	 * Tests the JImageFilterContrast::execute method.
	 *
	 * This tests to make sure we can brighten the image.
	 *
	 * @return  void
	 *
	 * @since   2.5.0
	 */
	public function testExecute()
	{
		// Create an image handle of the correct size.
		$imageHandle = imagecreatetruecolor(100, 100);

		// Define red.
		$dark = imagecolorallocate($imageHandle, 90, 90, 90);
		$light = imagecolorallocate($imageHandle, 120, 120, 120);

		imagefilledrectangle($imageHandle, 0, 0, 50, 99, $dark);
		imagefilledrectangle($imageHandle, 51, 0, 99, 99, $light);

		$filter = new JImageFilterEdgedetect($imageHandle);

		$filter->execute(array());

		$this->assertEquals(
			187,
			imagecolorat($imageHandle, 51, 25) >> 16 & 0xFF
		);
	}
}
