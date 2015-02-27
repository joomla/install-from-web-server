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
 * This models supports retrieving lists of contact categories.
 *
 * @since  1.0.0
 */
class AppsModelDashboard extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var string
	 */
	public $_context = 'com_apps.dashboard';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var	string
	 */
	protected $_extension = 'com_apps';

	private $_parent = null;

	private $_items = null;

	private $_remotedb = null;

	public $_extensionstitle = null;

	private $_categories = null;

	private $_breadcrumbs = array();

	private $_total = null;

	private $_orderby = 'score';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @note Calling getState in this method will result in recursion.
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = $app->input->getInt('id');
		$this->setState('filter.parentId', $parentId);

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('filter.published', 1);
		$this->setState('filter.access', true);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id	A prefix for the store id.
	 *
	 * @return  string  A store id
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.extension');
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		if (!count($this->_items))
		{
			$menu   = JFactory::getApplication()->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;

			if ($active)
			{
				$params->loadString($active->params);
			}

			$options               = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories            = JCategories::getInstance('Contact', $options);
			$this->_parent         = $categories->get($this->getState('filter.parentId', 'root'));

			if (is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			}
			else
			{
				$this->_items = false;
			}
		}

		return $this->_items;
	}

	public function getParent()
	{
		if (!is_object($this->_parent))
		{
			$this->getItems();
		}

		return $this->_parent;
	}

	private function getBaseModel()
	{
		return JModelLegacy::getInstance('Base', 'AppsModel');
	}

	private function getCatID()
	{
		return null;
	}

	public function getCategories()
	{
		return $this->getBaseModel()->getCategories($this->getCatID());
	}

	public function getBreadcrumbs()
	{
		return $this->getBaseModel()->getBreadcrumbs($this->getCatID());
	}

	public function getPluginUpToDate()
	{
		return $this->getBaseModel()->getPluginUpToDate();
	}

	public function getOrderBy()
	{
		return $this->_orderby;
	}

	public function getExtensions()
	{
		// Get catid, search filter, order column, order direction
		$http            = new JHttp;
		$componentParams = JComponentHelper::getParams('com_apps');
		$api_url         = new JUri;
		$default_limit   = $componentParams->get('default_limit', 8);
		$input           = new JInput;
		$catid           = $input->get('id', null, 'int');
		$order           = $input->get('ordering', $this->getOrderBy());
		$orderCol        = $this->state->get('list.ordering', $order);
		$orderDirn       = $orderCol == 'core_title' ? 'ASC' : 'DESC';
		$release         = preg_replace('/[^\d]/', '', base64_decode($input->get('release', '', 'base64')));
		$limitstart      = $input->get('limitstart', 0, 'int');
		$limit           = $input->get('limit', $default_limit, 'int');
		$dashboard_limit = $componentParams->get('extensions_perrow') * 6; // 6 rows of extensions
		$search          = str_replace('_', ' ', urldecode(trim($input->get('filter_search', null))));
		$release         = intval($release / 5) * 5;
		$cache           = JFactory::getCache();

		$cache->setCaching(1);

		$api_url->setScheme('http');
		$api_url->setHost('extensions.joomla.org/index.php');
		$api_url->setvar('option', 'com_jed');
		$api_url->setvar('controller', 'filter');
		$api_url->setvar('view', 'extension');
		$api_url->setvar('format', 'json');
		$api_url->setvar('limit', $limit);
		$api_url->setvar('limitstart', $limitstart);
		$api_url->setvar('filter[approved]', '1');
		$api_url->setvar('filter[published]', '1');
		$api_url->setvar('extend', '0');
		$api_url->setvar('order', $orderCol);
		$api_url->setvar('dir', $orderDirn);

		if ($search)
		{
			$api_url->setvar('searchall', $search);
		}

		$extensions_json = $cache->call(array($http, 'get'), $api_url);

		$items = json_decode($extensions_json->body);
		$items = $items->data;

		// Get CDN URL
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";

		// Populate array
		$extensions = array(
			'0' => array(),
			'1' =>array(),
		);

		foreach ($items as $item)
		{
			$item->image = $this->getBaseModel()->getMainImageUrl($item);

			if ($search)
			{
				$extensions[1 - $item->foundintitle][] = $item;
			}
			else
			{
				$extensions[0][] = $item;
			}
		}

		return array_merge($extensions[0], $extensions[1]);
	}

	public function getCount()
	{
		return $this->_total;
	}
}
