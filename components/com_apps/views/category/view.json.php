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
class AppsViewCategory extends JViewLegacy
{
	protected $state;

	protected $form;

	protected $item;

	protected $return_page;

	public function display($tpl = null)
	{
		JResponse::allowCache(true);
		$app = JFactory::getApplication();
		
		if ($app->input->get('callback', '', 'cmd')) {
			$document =& JFactory::getDocument();
			$document->setMimeEncoding('application/javascript');
		}

		$this->categories	= $this->get('Categories');
		$this->extensions	= $this->get('Extensions');
		$this->breadcrumbs	= $this->get('Breadcrumbs');
		$this->total		= $this->get('Count');
		$this->pagination	= $this->get('Pagination');
		$this->params 		= new JRegistry();
		
		$response = array();
		$response['body'] = $this->loadTemplate($tpl);
		$response['error'] = false;
		$response['message'] = '';
		$json = new JResponseJson(iconv("UTF-8", "UTF-8//IGNORE", $response['body']), $response['message'], $response['error']);
		
		if ($app->input->get('callback', '', 'cmd')) {
			echo str_replace(array('\n', '\t'), '', $app->input->get('callback') . '(' . $json . ')');
		} else {
			echo str_replace(array('\n', '\t'), '', $json);
		}
	}

}
