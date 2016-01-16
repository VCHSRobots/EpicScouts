<?php
// --------------------------------------------------------------------
// team_org_form.php -- Displays organization.
//
// Created: 01/04/16 DLB
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

echo '<div class="org_block">' . "\n";
$i = 0;
foreach($teams as $t)
{
	echo '<div class="team_block">' . "\n";
	echo '<div class="team_name">' . $t["Name"] . '</div>' . "\n";
	echo '<div class="team_member_block">' . "\n";
	foreach($t["Members"] as $p)
	{
		echo '<div class="team_member">' . $p . '</div>' . "\n";
	}
	echo '</div>' . "\n";
	echo '</div>' . "\n";
	$i++;
	if($i == 3)
	{
		$i = 0;
		echo '<div style="clear: both;"></div>' . "\n";
	}
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