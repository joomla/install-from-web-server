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
 * This models supports retrieving lists of JED categories.
 *
 * @since  1.0.0
 */
class AppsModelBase extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var	string
	 */
	public $_context = 'com_apps.base';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var	string
	 */
	protected $_extension = 'com_apps';

	private $_baseURL = 'index.php?format=json&option=com_apps';

	private $_categories = array();

	private $_children = array();

	private $_breadcrumbs = array();

	private $_pv = array(
		'latest' => '1.1.0',
		'works'	 => '1.0.5',
	);

	public static function getMainUrl()
	{
		return $this->_baseURL . '&view=dashboard';
	}

	public static function getCategoryUrl($categoryId)
	{
		return $this->_baseURL . '&view=category&id=' . $categoryId;
	}

	public static function getEntryListUrl( $categoryId, $limit = 30, $start = 0)
	{

	}

	public function getMainImageUrl($item)
	{
		$componentParams = JComponentHelper::getParams('com_apps');
		$default_image   = $componentParams->get('default_image_path');
		$cdn             = trim($componentParams->get('cdn'), '/') . "/";

		if (isset($item->logo->value[0]->path) && $item->logo->value[0]->path)
		{
			$image = $item->logo->value[0]->path;
		}
		else
		{
			$image = $item->images->value[0]->path;
		}

		return $image;
	}

	public static function getEntryUrl($entryId)
	{
		return $this->_baseURL . '&view=extension&id=' . $entryId;
	}

	public function getCategories($catid)
	{
		if (empty($this->_categories))
		{
			$cache = JFactory::getCache();
			$http  = new JHttp;

			$cache->setCaching(1);

			$categories_json = $cache->call(array($http, 'get'), 'http://extensions.joomla.org/index.php?option=com_jed&view=category&layout=list&format=json&order=order&limit=-1');
			$items           = json_decode($categories_json->body);
			$this->_total    = count($items);

			// Properties to be populated
			$properties = array('id', 'title', 'alias', 'parent');

			// Array to collect children categories
			$children = array();

			// Array to collect active categories
			$active = array($catid);

			$breadcrumbRefs = array();

			// Array to be returned
			$this->_categories = array();

			$this->_children = array();

			foreach ($items as $item)
			{
				// Skip root category
				if (trim(strtolower($item->title->value)) === 'root')
				{
					continue;
				}

				$id       = (int) $item->id->value;
				$parentId = (int) $item->parent_id->value;

				if ((int) $item->level->value > 1)
				{
					// Ignore subitems without a parent.
					if (is_null($item->parent_id->value))
					{
						continue;
					}

					// It is a child, so let's store as a child of it's parent
					if (!array_key_exists($parentId, $this->_categories))
					{
						$this->_categories[$parentId] = new stdClass;
					}

					$parent =& $this->_categories[$parentId];

					if (!isset($parent->children))
					{
						$parent->children = array();
					}

					if (!isset($parent->children[$id]))
					{
						$parent->children[$id] = new stdClass;
					}
					$category =& $parent->children[$id];

					// Populate category with values
					$category->id          = $id;
					$category->active      = ($catid == $category->id);
					$category->selected    = $category->active;
					$category->name        = $item->title->value;
					$category->alias       = $item->alias->value;
					$category->parent      = (int) $parentId;
					$category->description = '';
					$category->children    = array();

					$this->_children[] = $category;

					if ($category->active)
					{
						$this->_categories[$parentId]->active = true;

						if (!array_key_exists($parentId, $breadcrumbRefs))
						{
							$breadcrumbRefs[$parentId] = &$this->_categories[$parentId];
						}

						$breadcrumbRefs[$id] = &$category;
					}
				}
				else
				{
					// It is parent, so let's add it to the parent array
					if (!array_key_exists($id, $this->_categories))
					{
						$this->_categories[$id] = new stdClass;
						$this->_categories[$id]->children = array();
					}

					$category     =& $this->_categories[$id];
					$category->id = $id;

					if (!isset($category->active))
					{
						$category->active = ($catid == $category->id);
					}

					$category->selected    = $category->active;
					$category->name        = $item->title->value;
					$category->alias       = $item->alias->value;
					$category->parent      = (int) $parentId;
					$category->description = '';

					if ($category->active)
					{
						$breadcrumbRefs[$id] = &$category;
					}
				}
			}

			if (!empty($catid))
			{
				$this->_breadcrumbs = $breadcrumbRefs;
			}
		}

		// Add the Home item
		$input = new JInput;
		$view  = $input->get('view', null);

		$home              = new stdClass();
		$home->active      = $view == 'dashboard' ? true : false;
		$home->id          = 0;
		$home->name        = JText::_('COM_APPS_HOME');
		$home->alias       = 'home';
		$home->description = JText::_('COM_APPS_EXTENSIONS_DASHBOARD');
		$home->parent      = 0;
		$home->selected    = ($view == 'dashboard' ? true : false);
		$home->children    = array();

		array_unshift($this->_categories, $home);

		return $this->_categories;
	}

	public function getBreadcrumbs($catid)
	{
		if (!count($this->_breadcrumbs))
		{
			$this->getCategories($catid);
		}

		return $this->_breadcrumbs;
	}

	public function getChildren($catid)
	{
		if (!count($this->_children))
		{
			$this->getCategories($catid);
		}

		return $this->_children;
	}

	public function getPluginUpToDate()
	{
		$input  = new JInput;
		$remote = preg_replace('/[^\d\.]/', '', base64_decode($input->get('pv', '', 'base64')));
		$local  = $this->_pv;

		if (version_compare($remote, $local['latest']) >= 0)
		{
			return 1;
		}
		elseif (version_compare($remote, $local['works']) >= 0)
		{
			return 0;
		}

		return -1;
	}
}
