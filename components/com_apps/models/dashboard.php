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
class AppsModelDashboard extends JModelList
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_apps.dashboard';

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
	
	private $_orderby = 't2.link_hits';
	
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
		$base_model = $this->getBaseModel();
		return $base_model->getRemoteDB();
	}
	
	private function getBaseModel()
	{
		$base_model = JModelLegacy::getInstance('Base', 'AppsModel');
		return $base_model;
	}
	
	private function getCatID()
	{
		return null;
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
	
	public function getPluginUpToDate()
	{
		$base_model = $this->getBaseModel();
		return $base_model->getPluginUpToDate();
	}
	
	public function getOrderBy()
	{
		return $this->_orderby;
	}
	
	public function getExtensions()
	{
		// Get catid, search filter, order column, order direction
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$default_limit		= $componentParams->get('default_limit', 8);
		$input 				= new JInput;
		$catid 				= $input->get('id', null, 'int');
		$order 				= $input->get('ordering', $this->getOrderBy());
		$orderCol 			= $this->state->get('list.ordering', $order);
		$orderDirn 			= $orderCol == 't2.link_name' ? 'ASC' : 'DESC';
		$order 				= $this->getBaseModel()->getOrder($orderCol, $orderDirn);
		$release			= preg_replace('/[^\d]/', '', base64_decode($input->get('release', '', 'base64')));
		$limitstart 		= $input->get('limitstart', 0, 'int');
		$limit 				= $input->get('limit', $default_limit, 'int');
		$dashboard_limit	= $componentParams->get('extensions_perrow') * 6; // 6 rows of extensions
		$search 			= str_replace('_', ' ', urldecode($input->get('filter_search', null)));

		$release = intval($release / 5) * 5;

		// Get remote database
		$db = $this->getRemoteDB();
		
		$query = 'SET SESSION group_concat_max_len=150000';
		$db->setQuery($query);
		$db->execute();
		
		$query = $db->getQuery(true);
		$query->select(array('t2.link_id AS id'));
		$query->from('jos_mt_links AS t2');
		$query->join('RIGHT', 'jos_mt_cfvalues AS t3 ON t3.link_id = t2.link_id AND t3.cf_id = 37 AND ("'.$release.'" REGEXP t3.value OR t3.value = "")');

		$where = array();
		if ($search) {
			$where[] = '(t2.link_name LIKE(' . $db->quote('%'.$db->escape($search, true).'%') . ') OR t2.link_desc LIKE(' . $db->quote('%'.$db->escape($search, true).'%') . '))';
		}
		
		if (preg_match('/^t2\.link_rating/', $order)) {
			$where[] = 't2.link_votes >= 15';
		}

		$where = array_merge($where, array(
			't2.link_published = 1',
			't2.link_approved = 1',
			'(t2.publish_up <= NOW() OR t2.publish_up = "0000-00-00 00:00:00")',
			'(t2.publish_down >= NOW() OR t2.publish_down = "0000-00-00 00:00:00")',
		));
		
		$query->where($where);
		$query->order($order);
		$db->setQuery($query, $limitstart, $dashboard_limit);
		$ids = $db->loadColumn();
		
		if ($search && !count($ids)) {
			return array();
		}

		$query = $db->getQuery(true);
		$fields = array(
			't2.link_id AS id',
			't2.link_id',
			't2.link_name AS name',
			't2.alias AS alias',
			't2.link_desc AS description',
			't2.link_rating AS rating',
			't2.user_id AS user_id',
			't2.link_votes',
			't3.filename AS image',
			't1.cat_id AS cat_id',
			'CONCAT("{", GROUP_CONCAT(DISTINCT "\"", t5.cf_id, "\":\"", t4.value, "\""), "}") AS options',
			'COUNT(DISTINCT t7.rev_id) AS reviews',
		);
		if ($search) {
			$fields[] = 'IF(t2.link_name LIKE(' . $db->quote('%'.$db->escape($search, true).'%') . '), 1, 0) as foundintitle';
		}
		$query->select($fields);

		$query->from('jos_mt_links AS t2');
		$query->join('LEFT', 'jos_mt_cl AS t1 ON t1.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_images AS t3 ON t3.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_cfvalues AS t4 ON t2.link_id = t4.link_id');
		$query->join('LEFT', 'jos_mt_customfields AS t5 ON t4.cf_id = t5.cf_id');
		$query->join('LEFT', 'jos_mt_reviews AS t7 ON t7.link_id = t2.link_id');
		
		if (count($ids)) {
			$query->where(array(
				't2.link_id IN (' . implode(',', $ids) . ')',
			));
		}
		$query->group('t2.link_id');
		$query->order($order);
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
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

	public function getCount()
	{
		return $this->_total;
	}
}
