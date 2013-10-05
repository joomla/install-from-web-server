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
		'latest'	=>	'1.0.0',
		'works'		=>	'0.9.0',
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
	
	public function getMainImageUrl($image) {
		
		$componentParams = JComponentHelper::getParams('com_apps');
		$default_image = $componentParams->get('default_image_path');
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";
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
			// Get remote database
			$db = $this->getRemoteDB();
			
			// Form query
			$query = $db->getQuery(true);
			$query->select(
				array(
					'cat_id AS id',
					'cat_name AS name',
					'alias',
					'cat_desc AS description',
					'cat_parent AS parent',
				)
			);
			$query->from('jos_mt_cats');
			$query->where(
				array(
					'cat_published = 1',
					'cat_approved = 1',
				)
			);
			$query->order('cat_parent, cat_name ASC');
			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			$db->setQuery('SELECT FOUND_ROWS()');
			$this->_total = $db->loadResult();

			// Properties to be populated
			$properties = array('id', 'name', 'alias', 'description', 'parent');
			
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
				if (trim(strtolower($item->name)) == 'root')
				{
					continue;
				}
				
				// Base array is default parent for all categories
				$parent =& $this->_categories;
				
				// Create empty array to populate with parent category's children
				if ($item->parent and !array_key_exists($item->parent, $children))
				{
					$children[$item->parent] = array();
				}
				
				// Change value of parent linking to children array
				if ($item->parent)
				{
					$parent =& $children[$item->parent];
				}
				
				// Populate category with values
				$parent[$item->id] = new stdclass;
				$parent[$item->id]->active = false;
				foreach ($properties as $p)
				{
					$parent[$item->id]->{$p} = $item->{$p};
				}
				
				// Mark selected category
				$parent[$item->id]->selected = false;
				if ($parent[$item->id]->id == $catid)
				{
					$parent[$item->id]->selected = true;
				}

				// Create empty array for current category's own children
				if (!array_key_exists($item->id, $children))
				{
					$children[$item->id] = array();
				}
				$parent[$item->id]->children =& $children[$item->id];
				$refs[$item->id] =& $parent[$item->id];
				if (in_array($item->id, $active))
				{
					$parent[$item->id]->active = true;
					$id = $item->id;
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
	
}
