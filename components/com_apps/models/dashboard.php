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
	
	public function getExtensions()
	{
		// Get catid, search filter, order column, order direction
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$default_limit		= $componentParams->get('default_limit', 8);
		$input 				= new JInput;
		$catid 				= $input->get('id', null, 'int');
		$release			= preg_replace('/[^\d]/', '', base64_decode($input->get('release', '', 'base64')));
		$limitstart 		= $input->get('limitstart', 0, 'int');
		$limit 				= $input->get('limit', $default_limit, 'int');
		$search 			= str_replace('_', ' ', urldecode($input->get('filter_search', null)));
		$dashboard_limit	= $componentParams->get('extensions_perrow') * 6; // 6 rows of extensions
//		$orderCol 			= $this->state->get('list.ordering', 't2.link_rating');
//		$orderDirn 			= $this->state->get('list.direction', 'DESC');
//		$order 				= $orderCol.' '.$orderDirn;

		$release = intval($release / 5) * 5;

		// Get remote database
		$db = $this->getRemoteDB();
		
		$query = 'SET SESSION group_concat_max_len=150000';
		$db->setQuery($query);
		$db->execute();
		
		// Form query
		$query = $db->getQuery(true);
		$query->select(
			array(
				'SQL_CALC_FOUND_ROWS t2.link_id AS id',
				't2.link_name AS name',
				't2.alias AS alias',
				't2.link_desc AS description',
				't2.link_rating AS rating',
				't2.user_id AS user_id',
				't3.filename AS image',
				't1.cat_id AS cat_id',
				'CONCAT("{", GROUP_CONCAT("\"", t5.cf_id, "\":\"", t4.value, "\""), "}") AS options',
			)
		);

		$where = array(
			'EXISTS (SELECT 1 FROM jos_mt_cfvalues AS t6 WHERE t6.link_id = t2.link_id AND t6.cf_id = 37 AND (\''.$release.'\' REGEXP t6.value OR t6.value = \'\') GROUP BY t6.link_id HAVING COUNT(*) >= 1)'
		);

		// Featured extensions are selected randomly from the whole array
		// When selection method is based on rating or hits, extensions are selected from the top 100
		$query->from('jos_mt_links AS t2');
		$query->join('LEFT', 'jos_mt_cl AS t1 ON t1.link_id = t2.link_id');
		$order = 't2.link_hits DESC';

		$query->join('LEFT', 'jos_mt_images AS t3 ON t3.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_cfvalues AS t4 ON t2.link_id = t4.link_id');
		$query->join('LEFT', 'jos_mt_customfields AS t5 ON t4.cf_id = t5.cf_id');

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
		$limitstart = 0;
		$db->setQuery($query, $limitstart, $dashboard_limit);
		$items = $db->loadObjectList();
		
		$db->setQuery('SELECT FOUND_ROWS()');
		$this->_total = $db->loadResult();

		// Get CDN URL
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";
		
		// Populate array
		$extensions = array();
		foreach ($items as $item) {
			$options = new JRegistry($item->options);
			$item->image = $cdn . $item->image;
			$item->downloadurl = $options->get($componentParams->get('fieldid_download_url'));
			$item->fields = $options;
			$extensions[] = $item;
		}
		
		return $extensions;
		
	}

	public function getCount()
	{
		return $this->_total;
	}
}
