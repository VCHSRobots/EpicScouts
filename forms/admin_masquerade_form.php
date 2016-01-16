<?php
// --------------------------------------------------------------------
// admin_masquerade_form.php -- HTML fragment to show a masquerade form.
//
// Created: 12/29/14 DLB
// Update:  01/06/15 DLB  Make it much easier to masquerade.
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<div class="page_title">Masquerade as a Different User</div>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

if(!empty($doform))
{
	echo '<div id="admin_masquerade_block" class="inputform_area">' . "\n";
	echo '<form action="admin_masquerade.php" method="post">' . "\n";
	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Masquerade">' . "\n";
	echo '</div>';
	RenderParams($param_list, "admin_masquerade");
	echo '</form></div>' . "\n";
}

echo '</div>' . "\n";

?>

