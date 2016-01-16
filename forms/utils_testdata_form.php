<?php
// --------------------------------------------------------------------
// utils_testdata_form.php -- HTML fragment to show the logs.
//
// Created: 01/01/16 DLB
// --------------------------------------------------------------------
?>

<div class="page_title">Generate Random Data For Testing</div>

<?php
if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}
?>

<div class="showlog_selection_area">
<form action="utils_testdata.php" method="post">

<input class="showlog_load_button" type="submit" name="Accounts" value="Make Random Accounts">

<input class="showlog_load_button" type="submit" name="WO" value="Make Random Work Orders">

<input class="showlog_load_button" type="submit" name="Append" value="Make Random Appended Data">

</form>

</div>
<div style="clear: both"> </div>

<div class="inputform_instructions">
<p>Use only once.  Random source is not unique.</p>

<div class="showlog_output_area">

</div>