<?php
// --------------------------------------------------------------------
// utils_menubar.php -- HTML fragment to show menu bar for utilities.
//
// Created: 01/11/16 DLB
// --------------------------------------------------------------------


echo '<style>' . "\n";
echo '   .content_area {min-height: 275px; } ' . "\n";
echo '</style>' . "\n";
echo ' ' . "\n";
echo '<div class="menubar_area">' . "\n";

if(IsAdmin() || IsEditor() || IsCaptain()) 
{
	echo '<div class="btn_menu_div">' . "\n";
	echo '<a class="btn_menu" href="utils_merge.php">Merge WO</a>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="btn_menu_div">' . "\n";
	echo '<a class="btn_menu" href="utils_archive.php">Archive WO</a>' . "\n";
	echo '</div>' . "\n";

	echo '<div class="btn_menu_div">' . "\n";
	echo '<a class="btn_menu" href="utils_list_archived.php">List Archived</a>' . "\n";
	echo '</div>' . "\n";
}

if(IsAdmin()) 
{
	echo '<div class="btn_menu_div">' . "\n";
	echo '<a class="btn_menu" href="utils_bulkwo.php">Bulk WOs</a>' . "\n";
	echo '</div>' . "\n";

	//echo '<div class="btn_menu_div">' . "\n";
	//echo '<a class="btn_menu" href="utils_delete.php">Delete WO</a>' . "\n";
	//echo '</div>' . "\n";
}

//if(IsAdmin() && isset($config['DevBypass']))
//{
//	echo '<div class="btn_menu_div">' . "\n";
//	echo '<a class="btn_menu" href="utils_testdata.php">Add Test Data</a>' . "\n";
//	echo '</div>' . "\n";
//}

echo '</div>' . "\n";

?>