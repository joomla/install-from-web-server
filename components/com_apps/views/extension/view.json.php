<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_apps
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML Dashboard View class for the Apps component
 *
 * @package     Joomla.Site
 * @subpackage  com_apps
 * @since       1.5
 */
class AppsViewExtension extends JViewLegacy
{
	protected $state;

	protected $form;

	protected $item;

	protected $return_page;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->categories	= $this->get('Categories');
		$this->extensions	= $this->get('Extension');
		$this->breadcrumbs	= $this->get('Breadcrumbs');
		$this->params 		= new JRegistry();
		
		// Temporary params @DELETE
		$this->params->set('extensions_perrow', 4);
		
		$response = array();
		$response['body'] = $this->loadTemplate($tpl);
		$response['error'] = false;
		$response['message'] = '';
		$json = new JResponseJson(iconv("UTF-8", "UTF-8//IGNORE", $response['body']), $response['message'], $response['error']);

		if ($app->input->get('callback', '', 'cmd')) {
			echo $app->input->get('callback') . '(' . $json . ')';
		} else {
			echo $json;
		}
		
		jexit();
	}

}
