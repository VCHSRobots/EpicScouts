<?php
// --------------------------------------------------------------------
// findlist_simple_form.php -- HTML fragment to select what WOs to display
//
// Created: 12/31/15 SS
// --------------------------------------------------------------------


echo '<div class="content_area">';

	echo '<h2 class="page_title">Simple Filter</h2>';

		echo '<div class="findlist_selection_area" >';
		echo '<form action="findlist_simple.php" method="post">';

			echo '<br>';
			echo '<div class="findlist_view_label"> View: </div>';
			echo '<div class="findlist_selection">';
				echo '<select name="View">';
					if($view !="") echo '<option value="' . $view . '">' . $view  . '</option>';
					if($view =="simple") echo '<option name="full" value="full">full</option>';
					else if($view == "advanced")	echo '<option name="simple" value="simple">simple</option>';
					else 
					{
						echo '<option name="simple" value="simple">simple</option>';
						echo '<option name="full" value="full">full</option>';
					}					

				echo '</select>';
			echo ' </div>';

			echo '<div class="findlist_priority_label "> Priority: </div>';
			echo '<div class="findlist_selection ">';
				echo '<select name="Priority">';
					if($priority !="") echo '<option value="' . $priority . '">' . $priority . '</option>';
				        echo '<option value = ""></option>';
						foreach($WOPriorities as $Priority){
							if($priority != $Priority)
							echo '<option value="' . $Priority . '">' . $Priority . '</option>';
						}

				echo '</select>';
			 echo '</div>';

			echo '<div class="findlist_receiving_label"> Receiving IPT:</div>';
			echo '<div class="findlist_selection ">';
				echo '<select name="ReceivingTeam">';
				echo '<option value="' . $receiver . '">' . $receiver . '</option>';
				foreach($WOIPTeams as $team){
					if($receiver != $team)
						echo '<option value="' . $team . '">' . $team . '</option>';
				}
				echo '</select>';
			 echo '</div>';
			echo '<br>';
			echo '<br>';
			echo '<input class="findlist_load_button" name="Refresh" type="submit" value="Refresh">';
		echo '</form>';
	echo '</div>';
	/*content_area div closed later*/
	echo '<div style="clear: both"> </div>';
	echo '<div class="showlog_output_area">	</div>';

