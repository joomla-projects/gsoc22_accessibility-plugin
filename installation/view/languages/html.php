<?php
/**
 * @package     Joomla.Installation
 * @subpackage  View
 *
 * @copyright   (C) 2013 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The Installation Languages View
 *
 * @since  3.1
 */
class InstallationViewLanguagesHtml extends JViewHtml
{
	/**
	 * Container with all available languages
	 *
	 * @var    array
	 * @since  3.1
	 */
	protected $items;

	/**
	 * Redefine the model so the correct type hinting is available.
	 *
	 * @var     InstallationModelLanguages
	 * @since   3.1
	 */
	protected $model;

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.1
	 */
	public function render()
	{
		$this->items = $this->model->getItems();

		return parent::render();
	}
}
