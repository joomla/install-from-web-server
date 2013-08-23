<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');

JHtml::_('formbehavior.chosen', 'select'); 
$category_sidebar 		= new JLayoutFile('joomla.apps.category_sidebar');
$extensions_imagegrid 	= new JLayoutFile('joomla.apps.extensions_imagegrid');
$extensions_singlegrid 	= new JLayoutFile('joomla.apps.extensions_singlegrid');
$extension_data			= array('extensions' => $this->extensions, 'params' => $this->params);
?>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/bgo.css"/>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/zach.css"/>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/onyx.css"/>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/jquery.jscrollpane.css"/>
<link rel="stylesheet" href="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/css/custom_select.css"/>
<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.jscrollpane.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_apps/views/dashboard/js/jquery.japps.js"></script>
<div class="com-apps-container">
<div class="row-fluid">
	<div class="span3">
		<?php echo $category_sidebar->render($this->categories); ?>
	</div> 
	<div class="span9">
		<div class="row-fluid">
			<div class="span6">
				<div class="com-apps-search">
					<input type="text" placeholder="Search..."/>
					<i class="icon-search"></i>
				</div>
			</div>
			<div class="span6">
				<div class="com-apps-advanced-search">
					<a href="#"><i class="icon-cog"></i><span>Advanced Search</span></a>
					<div>
						<div>
							<div class="inp-row1"><input type="text"/></div>
							<div class="inp-row1"><input type="text"/></div>
							<div class="inp-row1">
								<select name="" id="">
									<option value="01">value 1</option>
									<option value="02">value 2</option>
									<option value="03">value 3</option>
								</select>
							</div>
							<div class="inp-row1">
								<select class="chzn-single chzn-single-with-drop">
									<option value="01">value 1</option>
									<option value="02">value 2</option>
									<option value="03">value 3</option>
								</select>
							</div>
							
						</div>
						<div>
							<div class="inp-row2">
								<input type="checkbox"/><label for="">JOOMLA</label>
							</div>
							<div class="inp-row2">
								<input type="checkbox"/><label for="">JOOMLA</label>
							</div>
						</div>
						<div>
							<div class="inp-row3">
								<input type="checkbox"/><label for=""><span class="m">M</span> component</label>
							</div>
							<div class="inp-row3">
								<input type="checkbox"/><label for=""><span class="s">S</span> module</label>
							</div>
							<div class="inp-row3">
								<input type="checkbox"/><label for=""><span class="c">C</span> component</label>
							</div>
							<div class="inp-row3">
								<input type="checkbox"/><label for=""><span class="t">T</span> module</label>
							</div>
							<div class="inp-row3">
								<input type="checkbox"/><label for=""><span class="p">P</span> module</label>
							</div>
						</div>
						<div class="clearfix">
							<a href="" class="cancel">CANCEL</a>
							<a href="" class="search">SEARCH</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $extensions_imagegrid->render($extension_data); ?>
	</div>
</div>
</div>
