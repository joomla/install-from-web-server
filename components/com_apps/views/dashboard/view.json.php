<?php
/**
 * @package     InstallFromWebServer
 * @subpackage  Site
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML Dashboard View class for the Apps component
 *
 * @since  1.0.0
 */
class AppsViewDashboard extends JViewLegacy
{
	protected $state;

	protected $form;

	protected $item;

	protected $return_page;

	/**
	 * Method to display a view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.	 *
	 *
	 * @return  void
	 *
	 * @since   1.0.5
	 */
	public function display($tpl = null)
	{
		JResponse::allowCache(true);
		$app = JFactory::getApplication();

		if ($app->input->get('callback', '', 'cmd'))
		{
			$document = JFactory::getDocument();
			$document->setMimeEncoding('application/javascript');
		}

		$this->categories  = $this->get('Categories');
		$this->extensions  = $this->get('Extensions');
		$this->breadcrumbs = $this->get('Breadcrumbs');
		$this->orderby     = $this->get('OrderBy');
		$this->params      = new JRegistry();

		// Temporary params @DELETE
		$this->params->set('extensions_perrow', 4);

		$response = array();

		$response['body'] = array(
			'html'           => iconv("UTF-8", "UTF-8//IGNORE", $this->loadTemplate($tpl)),
			'pluginuptodate' => $this->get('PluginUpToDate'),
		);

		$response['error']   = false;
		$response['message'] = '';

		$json = new JResponseJson($response['body'], $response['message'], $response['error']);
		
		if ($app->input->get('callback', '', 'cmd'))
		{
			echo str_replace(array('\n', '\t'), '', $app->input->get('callback') . '(' . $json . ')');
		}
		else
		{
			echo str_replace(array('\n', '\t'), '', $json);
		}
	}

}
