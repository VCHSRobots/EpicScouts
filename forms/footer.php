<?php
// --------------------------------------------------------------------
// footer.php -- footer template for all pages that use header.php
//
// Created 12/29/14 DLB
// --------------------------------------------------------------------
?>

</div> <?php // End of middle area ?>

<div style="clear: both; height: 2px"></div>

<div id="footer_area">
    <div id="footer_msg">
        <span> Valley Christian High School, Cerritos CA</span> 
        <span style="margin-left: 20px">2015/2016 Season</span>
    </div>

    <?php 
      if(IsMasquerading()) 
      {
          echo '<div id="footer_masquerader">[';
          echo GetMasquerader();
          echo ']</div>';
      }
      if(isset($timer))
      {
          echo '<div id="footer_timer">';
          echo $timer->Str();
          echo '</div>';
      }
      if(IsAdmin() && isset($config['DevBypass'])) 
      {
          echo '<div id="footer_test_link"><a href="test.php">Test</a></div>' . "\n"; 
          echo '<div style="clear: both"></div>';
      }

    ?>
    
    
    <div id="footer_home_link">
        <?php // <a href="pages/welcome.php">Home</a> ?>
    </div>
</div>

</div>    <?php // End of screen div ?>

<?php if(!isset($footer_extend)) {
echo '</body>';
echo '</html>';
} ?>
