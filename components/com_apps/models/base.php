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
 * This models supports retrieving lists of contact categories.
 *
 * @package     Joomla.Site
 * @subpackage  com_apps
 * @since       1.6
 */
class AppsModelBase extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_apps.base';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_apps';

	private $_parent = null;

	private $_items = null;
	
	private $_baseURL = 'index.php?format=json&option=com_apps';
	
	private $_remotedb = null;
	
	private $_categories = null;
	
	private $_children = array();
	
	private $_breadcrumbs = array();
	
	private $_pv = array(
		'latest'	=>	'1.1.0',
		'works'		=>	'1.0.5',
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
	
	public function getMainImageUrl($item) {
		
		$componentParams = JComponentHelper::getParams('com_apps');
		$default_image = $componentParams->get('default_image_path');
		$cdn = trim($componentParams->get('cdn'), '/') . "/";
		$image = $item->logo_value->path;
		
		if ($image) {
			$url = $cdn . $image;
		} else {
			$url = $default_image;
		}
		
		return $url;
	}
	public static function getEntryUrl($entryId)
	{
		return $this->_baseURL . '&view=extension&id=' . $entryId;
	}
	
	public function getRemoteDB() {
		if (!is_object($this->_remotedb)) {
			jimport('joomla.application.component.helper');
			$componentParams = JComponentHelper::getParams('com_apps');

			$fields = array('driver', 'host', 'user', 'password', 'database', 'port');
			$options = array();
		
			foreach ($fields as $field) {
				$options[$field] = $componentParams->get($field);
			}
		
			$this->_remotedb = JDatabaseDriver::getInstance( $options );
		}
		return $this->_remotedb;
	}
	
	public function getCategories($catid)
	{
		if (!is_object($this->_categories))
		{
			$cache = JFactory::getCache();
			$cache->setCaching( 1 );
			$http = new JHttp;
			$categories_json = $cache->call(array($http, 'get'), 'http://extensions.joomla.org/index.php?option=com_jed&view=category&layout=list&format=json&order=level&limit=-1');

			$items = json_decode($categories_json->body);
			$this->_total = count($items);

			// Properties to be populated
			$properties = array('id', 'title', 'alias', 'parent');
			
			// Array to collect children categories
			$children = array();
			
			// References to category objects
			$refs = array();
			
			// Array to collect active categories
			$active = array($catid);
			
			// Array to be returned
			$this->_categories = array();
			foreach ($items as $item)
			{
				// Skip root category
				if (trim(strtolower($item->title->value)) == 'root')
				{
					continue;
				}
				
				// Base array is default parent for all categories
				$parent =& $this->_categories;
				
				// Create empty array to populate with parent category's children
				if ($item->parent_id->value > 0 && !array_key_exists($item->parent_id->value, $children))
				{
					$children[$item->parent_id->value] = array();
				}
				
				// Change value of parent linking to children array
				if ($item->parent_id->value)
				{
					$parent =& $children[$item->parent_id->value];
				}
				
				// Populate category with values
				$parent[$item->id->value] = new stdclass;
				$parent[$item->id->value]->active = false;
				
				$parent[$item->id->value]->id = $item->id->value;
				$parent[$item->id->value]->name = $item->title->value;
				$parent[$item->id->value]->alias = $item->alias->value;
				$parent[$item->id->value]->parent = $item->parent_id->value;
				
				// Mark selected category
				$parent[$item->id->value]->selected = false;
				if ($parent[$item->id->value]->id == $catid)
				{
					$parent[$item->id->value]->selected = true;
				}

				// Create empty array for current category's own children
				if (!array_key_exists($item->id->value, $children))
				{
					$children[$item->id->value] = array();
				}
				$parent[$item->id->value]->children =& $children[$item->id->value];
				$refs[$item->id->value] =& $parent[$item->id->value];
				if (in_array($item->id->value, $active))
				{
					$parent[$item->id->value]->active = true;
					$id = $item->id->value;
					do
					{
						if (!array_key_exists($id, $refs)) {
							break;
						}
						$par = $refs[$id]->parent;
						$active[] = $par;
						if (array_key_exists($par, $refs)) {
							$refs[$par]->active = true;
							$id = $par;
						}
						else {
							break;
						}
					} while ($id);
				}
			}
			
			$this->_children = $children;
			
			// Build breadcrumbs array
			$selected = $catid;
			if (!is_null($selected))
			{
				$this->_breadcrumbs = array($refs[$selected]);
				while ($refs[$selected]->parent) {
					$selected = $refs[$selected]->parent;
					array_unshift($this->_breadcrumbs, $refs[$selected]);
				}
			}
		}

		$input = new JInput;
		$view = $input->get('view', null);
		$popular = new stdClass();
		$popular->active = $view == 'dashboard' ? true : false;
		$popular->id = 0;
		$popular->name = JText::_('COM_APPS_HOME');
		$popular->alias = 'home';
		$popular->description = JText::_('COM_APPS_EXTENSIONS_DASHBOARD');
		$popular->parent = 0;
		$popular->selected = $view == 'dashboard' ? true : false;
		$popular->children = array();
		array_unshift($this->_categories, $popular);
		
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
		$input = new JInput;
		$remote = preg_replace('/[^\d\.]/', '', base64_decode($input->get('pv', '', 'base64')));
		$local = $this->_pv;
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
	
	public function getOrder($col, $dir) {
		switch ($col) {
			case 't2.link_rating':
				$ret = 't2.link_rating '.$dir.', t2.link_votes '.$dir.', t2.link_id '.(strtoupper($dir) == 'DESC' ? 'ASC' : 'DESC');
				break;
			default:
				$ret = $col.' '.$dir;
		}
		return $ret;
	}
}
