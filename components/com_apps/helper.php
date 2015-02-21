<?php
class AppsHelper {

	static function getAJAXUrl($fragment) {
		$componentParams 	= JComponentHelper::getParams('com_apps');
		$route_prefix		= $componentParams->get('route_prefix', 'index.php?option=com_apps&format=json');

		if (!$route_prefix) {
			return $fragment;
		}

		$uri = JURI::getInstance($route_prefix);
		$query = $uri->getQuery();
		$query .= '&' . $fragment;
		$uri->setQuery($query);
		$url = $uri->toString();

		return $url;
	}

	static function getJEDUrl($item) {
		$url = 'http://extensions.joomla.org/';

		if (!isset($item->id->value)) { return $url; }

		$url .= 'index.php?option=com_jed&view=extension&layout=default&id=' . $item->id->value;
		return $url;
	}

	static function getJEDCatUrl($catid = 0) {
		$url = 'http://extensions.joomla.org/';

		if (!$catid) { return $url; }

		//$url .= 'index.php?option=com_jed&view=category&layout=list&id=' . $catid;
		$url .= 'index.php?option=com_jed&controller=filter&view=extension&layout=list&Itemid=145&filter[core_catid]=' . $catid;
		return $url;
	}
}
