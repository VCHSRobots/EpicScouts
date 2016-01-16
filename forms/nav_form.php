<?php
// --------------------------------------------------------------------
// nav_form.php -- Form for nav area that fits to left of most pages.
//                 Include after header.php if used on a given page.
// 
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "maindef.php";

$bu = $config["BaseUrl"];
echo '<div id="nav_area">';

echo '<div class="btn_nav_div"><a class="btn_nav" href="yourwork.php?Assignments=yes" > Your Work  </a></div>' . "\n";
echo '<div class="btn_nav_div"><a class="btn_nav" href="wo_new.php"   > New Order  </a></div>' . "\n";
echo '<div class="btn_nav_div"><a class="btn_nav" href="wo_lookup.php"> Lookup     </a></div>' . "\n";

if(IsAdmin() || IsEditor() || IsCaptain() || IsIPTLead() ) {
echo '<div class="btn_nav_div"><a class="btn_nav" href="inbox.php?Opened=Yes&UseSelfTeam=Yes"    > In Box     </a></div>' . "\n";
}

echo '<div class="btn_nav_div"><a class="btn_nav" href="findlist_simple.php"     > Find / List</a></div>' . "\n";

echo '<div class="btn_nav_div"><a class="btn_nav" href="team.php"     > Team View  </a></div>' . "\n";

if(IsAdmin() || IsEditor() || IsCaptain() ) {
echo '<div class="btn_nav_div"><a class="btn_nav" href="utils.php"    > Utilities  </a></div>' . "\n";
}

if(IsAdmin()) { 
echo '<div class="btn_nav_div"><a class="btn_nav" href="admin.php"    > Admin      </a></div>' . "\n";
}

echo '<div class="btn_nav_div"><a class="btn_nav" href="help.php"     > Help       </a></div>' . "\n";


echo '</div>' . "\n";

?>
