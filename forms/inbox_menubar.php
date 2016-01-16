<?php
// --------------------------------------------------------------------
// inbox_menubar.php -- HTML fragment to show menu bar for in box.
//
// Created: 12/31/15 DLB
// --------------------------------------------------------------------
?>

 <style>
    .content_area {min-height: 275px; } 
 </style>

<div class="menubar_area">


<div class="btn_menu_div">
<a class="btn_menu" href="inbox.php?Unassigned=Yes">Unassigned</a>
</div>

<div class="btn_menu_div">
<a class="btn_menu" href="inbox.php?Assigned=Yes">Assigned</a>
</div>

<div class="btn_menu_div">
<a class="btn_menu" href="inbox.php?Opened=Yes">All Opened</a>
</div>

<div class="btn_menu_div">
<a class="btn_menu" href="inbox.php?Completed=Yes">Completed</a>
</div>

<div class="btn_menu_div">
<a class="btn_menu" href="inbox.php?Closed=Yes">Closed</a>
</div>

</div>
