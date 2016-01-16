<?php
// --------------------------------------------------------------------
// wo_lookup_form.php -- UI for WO Lookup
//
// Created: 01/02/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title"> ' . $pagetitle . '</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div id="wo_lookup_area_1" class="inputform_area">' . "\n";
echo '<form action="wo_lookup.php" method="post">' . "\n";
RenderParams($param_list, "wo_lookup_");
echo '<div style="clear: both;"></div>' . "\n";

echo '<div class="btn_form_submit_div">';
echo '<input class="btn_form_submit" type="submit" value="Submit">' . "\n";
echo '</div>';
echo '</form></div>' . "\n";

echo '<div class="inputfrom_instructions">' . "\n";
echo '<p>Input the numeric part of the work order.</p><p> Do not input the revision letter.</p>';
echo '</div>' . "\n"; 

echo '</div' . "\n";
?>