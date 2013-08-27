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
	
	public function getExtensions()
	{
		// Get remote database
		$db = $this->getRemoteDB();
		
		$query = 'SET SESSION group_concat_max_len=15000';
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
				'CONCAT("{", GROUP_CONCAT("\"", t5.caption, "\":\"", t4.value, "\""), "}") AS options',
			)
		);


		$where = array();

		//Randomly select field to order by
		$field = array('rating', 'hits', 'featured');
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		$randval = rand(0, count($field) - 1);
		
		$this->_extensionstitle = JText::_('COM_APPS_EXTENSIONS_TITLE_' . strtoupper($field[$randval]));
		
		// Featured extensions are selected randomly from the whole array
		// When selection method is based on rating or hits, extensions are selected from the top 100
		if ($field[$randval] == 'link_featured')
		{
			$query->from('jos_mt_links AS t2');
			$where[] = 't2.link_featured = 1';
		}
		else
		{
			$query->from('(SELECT * FROM jos_mt_links ORDER BY link_' . $field[$randval] . ' DESC LIMIT 100) AS t2');
		}
		$query->join('LEFT', 'jos_mt_cl AS t1 ON t1.link_id = t2.link_id');
		$order = 'RAND()';

		$query->join('LEFT', 'jos_mt_images AS t3 ON t3.link_id = t2.link_id');
		$query->join('LEFT', 'jos_mt_cfvalues AS t4 ON t2.link_id = t4.link_id');
		$query->join('LEFT', 'jos_mt_customfields AS t5 ON t4.cf_id = t5.cf_id');

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
		$limit = 15;
		$db->setQuery($query, $limitstart, $limit);
		$items = $db->loadObjectList();
		
		$db->setQuery('SELECT FOUND_ROWS()');
		$this->_total = $db->loadResult();

		// Get CDN URL
		$componentParams = JComponentHelper::getParams('com_apps');
		$cdn = preg_replace('#/$#', '', trim($componentParams->get('cdn'))) . "/";
		
		// Populate array
		$extensions = array();
		foreach ($items as $item) {
			$options = new JRegistry($item->options);
			$data = new stdclass;
			$data->id = $item->id;
			$data->cat_id = $item->cat_id;
			$data->name = $item->name;
			$data->description = $item->description;
			$data->rating = $item->rating;
			$data->image = $cdn . $item->image;
			$data->user = $options->get('Developer Name');
			$data->tags = explode('|', trim($options->get('Extension Includes')));
			$data->compatibility = $options->get('Compatibility');
			$data->version = $options->get('Version');
			$data->downloadurl = $options->get('Link for download/registration/purchase: URL');
			$data->type = $options->get('Extension Apps* download type');
			$extensions[] = $data;
		}
		
		return $extensions;
		
	}

	public function getCount()
	{
		return $this->_total;
	}
}
