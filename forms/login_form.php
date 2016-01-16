<?php
// --------------------------------------------------------------------
// loginform.php -- HTML fragment to show the login form.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

if(isset($ShowError) && $ShowError == true)
{
    echo '<div id="login_fail">Login failed.  Try again. </div>';
    echo '<div style="clear: left"></div>';
}
?>

<div class="login_area">
<form action="login.php" method="post">
<div class="login_label"> Name: </div> 
<div class="login_field"> <input type="text" name="name"> </div>
<div style="clear: both;"></div>
<div class="login_label"> Password: </div> 
<div class="login_field"> <input type="password" name="password"> </div>
<div style="clear: both;"></div>
<div style="margin-left: 140px; margin-top: 30px; margin-bottom: 50px;">
<input class="btn_backgnd" style="width: 208px;" type="submit" value="Log In">
</div>
</div>

</form>

<div id="login_questions"><a href="public.php">Questions?</a></div>