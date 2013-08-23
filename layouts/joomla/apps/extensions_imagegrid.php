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
$extensions_perrow = $displayData['params']->get('extensions_perrow');
$spanclass = 'span' . (12 / $extensions_perrow);
$data	= array();
?>

<div class="row-fluid">
	<?php 
		foreach ($displayData['extensions'] as $extension) :
			$ratingwidth = round(70 * ($extension->rating / 5));
			if ($i != 0 && $i%$extensions_perrow == 0) {
	?>
</div>
<hr />
<?php 
}
echo '<div class="item-view">';

	$data	= array('spanclass' => $spanclass,'extension' => $extension);
	$extensions_singlegrid = new JLayoutFile('joomla.apps.extensions_singlegrid');

	echo "<div class='grid-container'>";

	echo '<div class="grid-header">
		<div class="breadcrumbs">
			<a href="#">EXTENSIONS</a> / <a href="#">ADMIN NAVIGATION</a> / <span class="active-extension">B2JCONTACT</span>
		</div>
		<div class="sort-by">
			<select class="sort-by-select" style="display: none;">
			  <option value="84">Name</option>
			  <option value="87">Size</option>
			</select>
		</div>
	</div>';

		echo "<div class='row-fluid'>";
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
		echo "</div>";
		echo "<div class='row-fluid'>";
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
			echo $extensions_singlegrid->render($data);
		echo "</div>";
	echo "</div>";
?>
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

<?php $i++; endforeach; ?>
<?php 
	$extensions_full = new JLayoutFile('joomla.apps.extensions_full');
	echo $extensions_full->render();
?>