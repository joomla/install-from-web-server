<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
<div class="com-apps-advanced-search hidden">
	<a href="#"><span class="icon-cog" aria-hidden="true"></span><span><?php echo JText::_('COM_APPS_ADVANCED_SEARCH'); ?></span></a>
	<div>
		<div>
			<div class="inp-row1"><input placeholder="<?php echo JText::_('COM_APPS_EXTENSION_NAME'); ?>" type="text"/></div>
			<div class="inp-row1"><input placeholder="<?php echo JText::_('COM_APPS_DESCRIPTION'); ?>" type="text"/></div>
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
				<input id="j25" type="checkbox"/><label for="j25"><span class="j25">JOOMLA 2.5</span></label>
			</div>
			<div class="inp-row2">
				<input id="j2x" type="checkbox"/><label for="j2x"><span class="j2x">JOOMLA 2.X</span></label>
			</div>
		</div>
		<div>
			<div class="inp-row3">
				<input id="ex_c" type="checkbox"/><label for="ex_c"><span class="c">C</span> <?php echo JText::_('COM_APPS_COMPONENT'); ?></label>
			</div>
			<div class="inp-row3">
				<input id="ex_m" type="checkbox"/><label for="ex_m"><span class="m">M</span> <?php echo JText::_('COM_APPS_MODULE'); ?></label>
			</div>
			<div class="inp-row3">
				<input id="ex_p" type="checkbox"/><label for="ex_p"><span class="p">P</span> <?php echo JText::_('COM_APPS_PLUGIN'); ?></label>
			</div>
			<div class="inp-row3">
				<input id="ex_t" type="checkbox"/><label for="ex_t"><span class="t">T</span> <?php echo JText::_('COM_APPS_TOOL'); ?></label>
			</div>
			<div class="inp-row3">
				<input id="ex_s" type="checkbox"/><label for="ex_s"><span class="s">S</span> <?php echo JText::_('COM_APPS_EXTENSION_SPECIFIC_ADDON'); ?></label>
			</div>
		</div>
		<div class="clearfix">
			<a href="JavaScript:void(0);" class="cancel"><?php echo JText::_('COM_APPS_CANCEL_BTN'); ?></a>
			<a href="" class="search"><?php echo JText::_('COM_APPS_SEARCH_BTN'); ?></a>
		</div>
	</div>
</div>
<div class="view-toggle">
	<span class="grid-view act" title="<?php echo JText::_('COM_APPS_GRID_VIEW'); ?>"></span>
	<span class="list-view pas" title="<?php echo JText::_('COM_APPS_LIST_VIEW'); ?>"></span>
</div>
