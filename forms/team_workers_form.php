<?php
// --------------------------------------------------------------------
// team_workers_form.php -- Displays organization.
//
// Created: 01/05/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">' . "\n";
echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div class="header_block">' . "\n";
echo '<div class="header_item">Name</div>';
echo '<div class="header_item">Work Orders</div>';
echo '</div>' . "\n";

echo '<div class="list_block">' . "\n";
foreach($table as $t)
{
	echo '<div class="line_block">' . "\n";
	echo '<div class="line_name">' . $t["Name"] . '</div>' . "\n";
	echo '<div class="line_item_block">' . "\n";
	foreach($t["Data"] as $wid)
	{
		echo '<div class="line_item"><a href="wo_display.php?wid=' . $wid .'">';
		echo $wid . '</a></div>' . "\n";
	}
	echo '</div>' . "\n";
	echo '</div>' . "\n";
}
echo '</div>' . "\n";

if(!empty($instructions))
{
	echo '<div class="inputfrom_instructions">' . "\n";
	echo $instructions;
	echo '</div>' . "\n"; 
}
echo '</div' . "\n";
?>