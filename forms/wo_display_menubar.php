<?php
// --------------------------------------------------------------------
// wo_display_menubar.php -- HTML fragment to show menu
//
// Created: 01/02/16 DLB
// --------------------------------------------------------------------

$arg = "";
if(!empty($wid)) $arg = '?wid=' . $wid;

echo '<style>' . "\n";
echo '   .content_area {min-height: 275px; } '. "\n";
echo '</style>'. "\n";

echo '<div class="menubar_area">'. "\n";

echo '<div class="btn_menu_div">'. "\n";
echo '<a class="btn_menu" href="wo_display.php' . $arg . '">View</a>'. "\n";
echo '</div>'. "\n";

echo '<div class="btn_menu_div">'. "\n";
echo '<a class="btn_menu" href="wo_add_data.php' . $arg . '">Add Data</a>'. "\n";
echo '</div>'. "\n";

echo '<div class="btn_menu_div">'. "\n";
echo '<a class="btn_menu" href="wo_change_status.php' . $arg . '">Change Status</a>'. "\n";
echo '</div>'. "\n";

echo '<div class="btn_menu_div">'. "\n";
echo '<a class="btn_menu" href="wo_assign_workers.php' . $arg .'">Assign Workers</a>'. "\n";
echo '</div>'. "\n";

echo '<div class="btn_menu_div">'. "\n";
echo '<a class="btn_menu" href="wo_edit.php' . $arg . '">Edit</a>'. "\n";
echo '</div>'. "\n";

echo '</div>' . "\n";

?>