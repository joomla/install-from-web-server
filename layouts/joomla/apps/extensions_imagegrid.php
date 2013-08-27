<?php
/**
 * @package     Joomla.CMS
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

//@TODO: MOve the single extension grid into a reusable JLayout
defined('JPATH_BASE') or die;
$i = 0;
$count = 1;
$extensions_perrow = $displayData['params']->get('extensions_perrow');
$spanclass = 'span' . (12 / $extensions_perrow);
$data	= array();
?>

<div class="row-fluid">
	<div class="item-view span12">
		<div class='grid-container'>
			<div class="grid-header">
			<div class="breadcrumbs">
				<a href="#">EXTENSIONS</a> / <a href="#">ADMIN NAVIGATION</a> / <span class="active-extension">B2JCONTACT</span>
			</div>
			<div class="sort-by pull-right">
				<select title="asdasd">
				  <option value="84"><?php echo JText::_('COM_APPS_SORT_BY_NAME'); ?></option>
				  <option value="87"><?php echo JText::_('COM_APPS_SORT_BY_SIZE'); ?></option>
				</select>
			</div>
		</div>
		<div class="items grid-view-container">
	<?php 
		foreach ($displayData['extensions'] as $extension) :
			$ratingwidth = round(70 * ($extension->rating / 5));
			if ($i != 0 && $i%$extensions_perrow == 0) {
	?>
<?php 
}
	$data	= array('spanclass' => $spanclass,'extension' => $extension);
	$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid');

	if ($count%4 == 1)
    {  
         echo "<div class='row-fluid'>";
    }
    
    echo $extensions_singlegrid->render($data);
    
    if ($count%4 == 0)
    {
        echo "</div>";
    }
    $count++;
?>

<?php $i++; endforeach; ?>
<?php if ($count%4 != 1) echo "</div>";?>
		</div>
		<hr class="pagination-top" />
		<div class="pagination-container">
			<ul class="grid-pagination">
				<li><a href="#">1</a></li>
				<li><a href="#">2</a></li>
				<li class="current"><a href="#">3</a></li>
				<li>...</li>
				<li><a href="#">5</a></li>
			</ul>
		</div>
	</div>
</div>
<?php 
	$extensions_full = new JLayoutFile('joomla.apps.extensions_full');
	echo $extensions_full->render(array());
?>