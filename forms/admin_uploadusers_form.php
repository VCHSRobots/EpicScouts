<?php
// --------------------------------------------------------------------
// admin_uploadusers_form.php -- HTML fragment to show a form for
//                              uploading csv files of users for
//                              making user accounts.
//
// Created: 12/17/14 DLB
// Updated: 12/30/14 DLB -- Hacked from Epic Scouts
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<div class="page_title">Upload User Accounts</div>' . "\n";

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
    echo '<div id="admin_upload_block" class="inputform_area">' . "\n";
    echo '<form action="admin_uploadusers.php" method="post"  enctype="multipart/form-data">' . "\n";
    RenderParams($param_list, "admin_buldUpload");
    echo '<div class="btn_form_submit_div">';
    echo '<input class="btn_form_submit" type="submit" value="Upload" name="Upload">' . "\n";
    echo '</div>';
    echo '</form></div>' . "\n";

    echo '<div id="admin_download_block" class="inputform_area">' . "\n";
    echo '<form action="admin_uploadusers.php" method="post"  enctype="multipart/form-data">' . "\n";
    echo '<p style="font-size: 16pt;">Download all accounts into a CSV file.</p>';
    echo '<div class="btn_form_submit_div">';
    echo '<input class="btn_form_submit" type="submit" value="Download" name="Download">' . "\n";
    echo '</div>';
    echo '</form></div>' . "\n";


    if(!empty($instructions)) 
    { 
        echo '<div class="instructions">';
        echo $instructions;
        echo '</div>';
    }
}

echo '</div>' . "\n";

?>
