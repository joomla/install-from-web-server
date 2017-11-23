<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Install from Web Base Controller
 *
 * @since  1.0
 */
class AppsController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function display($cachable = false, $urlparams = [])
	{
		JLoader::register('AppsHelper', __DIR__ . '/helpers/helper.php');

		$cachable   = true;
		$noforceraw = [];

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'dashboard');
		$this->input->set('view', $vName);

		// Majority views will be raw, lets make it easy & error proof on the client side
		if (!in_array($vName, $noforceraw))
		{
			$this->input->set('format', 'raw');
		}

		return parent::display($cachable, $urlparams);
	}
}
