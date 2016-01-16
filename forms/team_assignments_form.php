<?php
// --------------------------------------------------------------------
// team_assignments_form.php -- Displays organization.
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

echo '<p style="margin-top: 5px; margin-bottom: 0px;">Showing all open workorders.</p>';

echo '<div id="header_block" style="margin-top: 15px;">' . "\n";
echo '<div id="header_item_Name">Name</div>';
echo '<div id="header_item_Data">Work Orders</div>';
echo '<div style="clear: both;"></div>' . "\n";
echo '</div>' . "\n";

echo '<div class="list_block">' . "\n";
foreach($table as $t)
{
	echo '<div class="line_block">' . "\n";
	echo '<div class="line_name">' . $t["Name"] . '</div>' . "\n";
	echo '<div class="line_item_block">' . "\n";
	$imax = 0;
	foreach($t["Data"] as $wid)
	{
		if($imax >= 7)
		{
			echo '<div class="line_item">(more...)</div>' . "\n";
			break;
		}
		$wostr = sprintf("W%04d", $wid);
		echo '<div class="line_item"><a href="wo_display.php?wid=' . $wid .'">';
		echo $wostr . '</a></div>' . "\n";
		$imax++;
	}
	echo '</div>' . "\n";
	echo '</div>' . "\n";
	echo '<div style="clear: both;"></div>' . "\n";
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