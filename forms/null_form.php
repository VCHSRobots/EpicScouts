<?php
// --------------------------------------------------------------------
// nul_form.php -- HTML fragment for a non-functioning page.
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

if(isset($pagecontent))
{
	if(!empty($pagecontent))
	{
		echo '<div id="pagecontent">' . $pagecontent . '</div>' . "\n";
	}
}
else 
{
	echo 'Sorry, this feature is not ready yet.';
}
?>