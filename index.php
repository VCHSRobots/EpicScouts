<?php
// --------------------------------------------------------------------
// index.php -- Default entry page into Epic Admin website.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "maindef.php";
session_start();
log_page();

if(IsLoggedIn()) { JumpToPage("pages/welcome.php");  }
else             { JumpToPage("pages/login.php");    }

?>