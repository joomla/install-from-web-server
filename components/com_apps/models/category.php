<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
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
class AppsModelCategory extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_apps.category';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_apps';

	private $_parent = null;

	private $_items = null;
	
	private $_remotedb = null;
	
	public $_extensionstitle = null;
	
	private $_categories = null;
	
	private $_breadcrumbs = array();
	
	private $_total = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
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

		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
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
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if (!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry;
			if ($active)
			{
				$params->loadString($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories = JCategories::getInstance('Contact', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if (is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			} else {
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

	private function getRemoteDB() {
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
	
	private function getBaseModel()
	{
		$base_model = JModelLegacy::getInstance('Base', 'AppsModel');
		return $base_model;
	}
	
	private function getCatID()
	{
		$input = new JInput;
		return $input->get('id', null, 'int');
	}
	
	public function getCategories()
	{
		$base_model = $this->getBaseModel();
		return $base_model->getCategories($this->getCatID());
	}

	public function getBreadcrumbs()
	{
		$base_model = $this->getBaseModel();
		return $base_model->getBreadcrumbs($this->getCatID());
	}
	
	private function loadExtensionIDs($db, $catid)
	{
		// Get catid, search filter, order column, order direction
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$default_limit		= $componentParams->get('default_limit');
		$input 				= new JInput;
		$release			= preg_replace('/[^\d]/', '', base64_decode($input->get('release', '', 'base64')));
		$limitstart 		= $input->get('limitstart', 0, 'int');
		$limit 				= $input->get('limit', $default_limit, 'int');
		$order 				= $input->get('ordering', 't2.link_rating');
		$search 			= str_replace('_', ' ', urldecode($input->get('filter_search', null)));
		$orderCol 			= $this->state->get('list.ordering', $order);
		$orderDirn 			= $orderCol == 't2.link_name' ? 'ASC' : 'DESC';
		$order 				= $orderCol.' '.$orderDirn;

		$release = intval($release / 5) * 5;

		$query = 'SET SESSION group_concat_max_len=150000';
		$db->setQuery($query);
		$db->execute();
		
		// Get category name
		if ($catid) {
			$query = $db->getQuery(true);
			$query->select('cat_name');
			$query->from('jos_mt_cats');
			$query->where('cat_id = ' . (int)$catid);
			$db->setQuery($query);
			$catname = $db->loadResult();
			if ($catname) {
				$this->_extensionstitle = $catname;
			}
		}
		
		// Form query
		$query = $db->getQuery(true);
		$query->select(array('t2.link_id AS id'));
		$query->from('jos_mt_cl AS t1');
		$query->join('LEFT', 'jos_mt_links AS t2 ON t1.link_id = t2.link_id');
		$query->join('RIGHT', 'jos_mt_cfvalues AS t3 ON t3.link_id = t2.link_id AND t3.cf_id = 37 AND ("'.$release.'" REGEXP t3.value OR t3.value = "")');

		if (!$order) {
			$order = 't2.link_rating DESC';
		}

		$where = array();
		if ($catid && !$search) {
			$where[] = 't1.cat_id IN (' . implode(',', $catid) . ')';
		}

		if ($search) {
			$where[] = '(t2.link_name LIKE(' . $db->quote('%'.$search.'%') . ') OR t2.link_desc LIKE(' . $db->quote('%'.$search.'%') . '))';
		}
		
		$where = array_merge($where, array(
			't2.link_published = 1',
			't2.link_approved = 1',
			'(t2.publish_up <= NOW() OR t2.publish_up = "0000-00-00 00:00:00")',
			'(t2.publish_down >= NOW() OR t2.publish_down = "0000-00-00 00:00:00")',
		));

		$query->where($where);
		$query->order($order);
		$query->group('t2.link_id');
		$db->setQuery($query, $limitstart, $limit);
		return $db->loadColumn();
	}
	
	public function getExtensions()
	{
		// Get catid, search filter, order column, order direction
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$input 				= new JInput;
		$catid 				= $input->get('id', null, 'int');
		$search 			= str_replace('_', ' ', urldecode($input->get('filter_search', null)));
		$order 				= $input->get('ordering', 't2.link_hits');
		$orderCol 			= $this->state->get('list.ordering', $order);
		$orderDirn 			= $orderCol == 't2.link_name' ? 'ASC' : 'DESC';
		$order 				= $orderCol.' '.$orderDirn;
		
		// Get remote database
		$db = $this->getRemoteDB();
		$ids = $this->loadExtensionIDs($db, array($catid));
		
		if (!count($ids)) {
			$base_model = $this->getBaseModel();
			$children = $base_model->getChildren($catid);
			$catid = $this->getAllChildren($children, $catid);
			if (count($catid)) {
				$ids = $this->loadExtensionIDs($db, $catid);
			}
		}

		$items = array();	
		if (count($ids)) {
			$query = $db->getQuery(true);
			$fields = array(
				't2.link_id AS id',
				't2.link_name AS name',
				't2.alias AS alias',
				't2.link_desc AS description',
				't2.link_rating AS rating',
				't2.user_id AS user_id',
				't3.filename AS image',
				't1.cat_id AS cat_id',
				'CONCAT("{", GROUP_CONCAT(DISTINCT "\"", t5.cf_id, "\":\"", t4.value, "\""), "}") AS options',
			);
			if ($search) {
				$fields[] = 'IF(t2.link_name LIKE(' . $db->quote('%'.$search.'%') . '), 1, 0) as foundintitle';
			}
			$query->select($fields);

			$query->from('jos_mt_cl AS t1');
			$query->join('LEFT', 'jos_mt_links AS t2 ON t1.link_id = t2.link_id');
			$query->join('LEFT', 'jos_mt_images AS t3 ON t3.link_id = t2.link_id');
			$query->join('LEFT', 'jos_mt_cfvalues AS t4 ON t2.link_id = t4.link_id');
			$query->join('LEFT', 'jos_mt_customfields AS t5 ON t4.cf_id = t5.cf_id');

			$query->where(array(
				't2.link_id IN (' . implode(',', $ids) . ')',
			));
			$query->order($order);
			$query->group('t2.link_id');
			$db->setQuery($query);
			$items = $db->loadObjectList();
		}

		// Get CDN URL
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";
		
		// Populate array
		$extensions = array(0=>array(), 1=>array());
		foreach ($items as $item) {
			$options = new JRegistry($item->options);
			$item->image = $this->getBaseModel()->getMainImageUrl($item->image);
			$item->downloadurl = $options->get($componentParams->get('fieldid_download_url'));
			$item->fields = $options;
			if ($search) {
				$extensions[1 - $item->foundintitle][] = $item;
			}
			else {
				$extensions[0][] = $item;
			}
		}
		
		return array_merge($extensions[0], $extensions[1]);
		
	}
	
	private function getAllChildren($children, $catid)
	{
		$allchildren = array();
		if (is_array($children[$catid]) and count($children[$catid])) {
			$allchildren = array_merge($allchildren, array_keys($children[$catid]));
			foreach ($children[$catid] as $key => $value) {
				$allchildren = array_merge($allchildren, $this->getAllChildren($children, $key));
			}
		}
		return $allchildren;
	}
	
	public function getCount()
	{
		return $this->_total;
	}
	
	public function getPagination() {
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$default_limit		= $componentParams->get('default_limit', 8);
		$input 				= new JInput;

		$pagination = new stdClass;
		$pagination->limit 		= $input->get('limit', $default_limit, 'int');
		$pagination->limitstart = $input->get('limitstart', 0, 'int');
		$pagination->total		= $this->getCount();
		$pagination->next		= $pagination->limitstart + $pagination->limit;
		
		return $pagination;

	}
}
