<?php
/**
 * Joomla! Install From Web Server
 *
 * @copyright  Copyright (C) 2013 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Response\JsonResponse;

/**
 * Dashboard view class
 *
 * @since  1.0
 */
class AppsViewCategory extends HtmlView
{
	/**
	 * @var    array
	 * @since  1.0
	 */
	protected $breadcrumbs = [];

	/**
	 * @var    array
	 * @since  1.0
	 */
	protected $categories = [];

	/**
	 * @var    array
	 * @since  1.0
	 */
	protected $extensions = [];

	/**
	 * @var    string
	 * @since  1.0
	 */
	protected $orderby = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$app->allowCache(true);

		if ($app->input->getCmd('callback', ''))
		{
			$this->document->setMimeEncoding('application/javascript');
		}

		$this->categories  = $this->get('Categories');
		$this->extensions  = $this->get('Extensions');
		$this->breadcrumbs = $this->get('Breadcrumbs');
		$this->orderby     = $this->get('OrderBy');

		$json = new JsonResponse(
			[
				'html'           => iconv("UTF-8", "UTF-8//IGNORE", $this->loadTemplate($tpl)),
				'pluginuptodate' => $this->get('PluginUpToDate'),
			]
		);

		if ($app->input->getCmd('callback', ''))
		{
			echo str_replace(['\n', '\t'], '', $app->input->get('callback') . '(' . $json . ')');
		}
		else
		{
			echo str_replace(['\n', '\t'], '', $json);
		}
	}
}
