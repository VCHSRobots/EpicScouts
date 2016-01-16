<?php
// --------------------------------------------------------------------
// menubar.php -- HTML fragment to show a menu bar at top of page.
//
// Created: 12/31/15 DLB
// --------------------------------------------------------------------

// Before including this form, define an array named $menubar, with one
// sub array for each menu item.  The sub array should have two elements:
// the "caption" and the "href"

echo '<style>' . "\n";
echo '.content_area {min-height: 275px; }' . "\n";
echo '</style>' . "\n";

echo '<div class="menubar_area">' . "\n";

foreach($menubar as $menuitem) {
	echo '<div class="btn_menu_div">' . "\n";
	echo '<a class="btn_menu" href="' . $menuitem["href"] . '">' . $menuitem["caption"] . '</a>' . "\n";
	echo '</div>' . "\n";
}

echo '</div>' . "\n";

?>
