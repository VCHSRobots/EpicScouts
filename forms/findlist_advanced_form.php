<?php
// --------------------------------------------------------------------
// findlist_advanced_form.php -- HTML fragment to show findlist advanced
//      form. This form lets users select specific criteria to filter
//      work orders by.
//
// Created: 12/31/15 SS
// --------------------------------------------------------------------

echo '<div class="content_area">';

echo '<h2 class="page_title">Advanced Filter</h2>' ;

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

echo '<div class="findlist_selection_area">';

    echo '<form action="findlist_advanced.php" method="post">';

    echo '<div class="findlist_title_label">Title: </div>';
    echo '<div class="findlist_text_field"> <input type="text" name="Title" value="' . $title . '">';
    echo '</div>';
        echo '<br>';
        echo '<div class="findlist_view_label"> View: </div>';
        echo '<div class="findlist_selection">';
            echo '<select name="View">';
                if($view !="")              echo '<option value="' . $view . '">' . $view . '</option>';
                if($view =="simple")        echo '<option name="full" value="full">full</option>';
                else if($view == "full")    echo '<option name="simple" value="simple">simple</option>';
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
                    if($priority != $Priority) echo '<option value="' . $Priority . '">' . $Priority . '</option>';
                }
            echo '</select>';
        echo '</div>';

        echo '<div class="findlist_priority_label "> Project: </div>';
        echo '<div class="findlist_selection ">';
            echo '<select name="Project">';
                if($project !="") echo '<option value="'. $project . '">' . $project . '</option>';
                else echo '<option value = ""></option>';
                foreach($WOProjects as $Project){
                    if($project != $Project) echo '<option value="' . $Project . '">' . $Project . '</option>';
                }
            echo '</select>';
        echo '</div>';

        echo '<br>';

        echo '<div class="findlist_requesting_label"> Requesting IPT:</div>';
        echo '<div class="findlist_selection ">';
            echo '<select name="RequestingTeam">';
                if($requestor !="") echo '<option value="' . $requestor . '">' . $requestor . '</option>';
                else echo '<option value = ""></option>';
                foreach($WOIPTeams as $team){
                    if($requestor != $team) echo '<option value="' . $team . '">' . $team . '</option>';
                }
            echo '</select>';
         echo '</div>';


        echo '<div class="findlist_author_label">Author: </div>';
        echo '<div class="findlist_student_selection">';
            echo '<select name="Author">';
                if($author !="") echo '<option value="' . $author . '">' . $authorName . '</option>';
                echo '<option value = ""></option>';
                $sql = "Select UserID, FirstName, LastName FROM Users;";
                $result = SqlQuery($loc, $sql);
                while ($row = $result->fetch_assoc())
                {
                    $name = $row["FirstName"] . " " . $row["LastName"] ;
                    $userID = $row["UserID"];
                    if($author!= $userID) echo '<option value=' . $userID . '> ' . $name  . '</option>';
                }
            echo '</select>';
        echo '</div>';
        echo '<br>';

        echo '<div class="findlist_advreceiving_label"> Receiving IPT:</div>';
        echo '<div class="findlist_selection ">';
            echo '<select name="ReceivingTeam">';
                if($receiver!="")echo '<option value="' . $receiver . '">' . $receiver . '</option>';
                else echo '<option value = ""></option>';
                foreach($WOIPTeams as $team){
                    if($receiver != $team) echo '<option value="' . $team . '">' . $team . '</option>';
                }
            echo '</select>';
        echo '</div>';


        echo '<div class="findlist_assigned_label">Assigned To: </div>';
        echo '<div class="findlist_student_selection">';
            echo '<select name="StudentAssigned">';
                if($studentAssigned !="") echo '<option value="' . $studentAssigned. '">' . $assignName . '</option>';
                echo '<option value = ""></option>';
                $sql = "Select UserID, FirstName, LastName FROM Users;";
                $result = SqlQuery($loc, $sql);
                while ($row = $result->fetch_assoc())
                {
                    $name = $row["FirstName"] . " " . $row["LastName"] ;
                    $userID = $row["UserID"];
                    if($studentAssigned!= $userID) echo '<option value=' . $userID . '> ' . $name  . '</option>';
                }
            echo '</select>';
        echo '</div>';
        echo '<br>';

        echo '<div class="findlist_date_label">Date Created: </div>';
        echo '<div class="findlist_between_label">Between: </div>';
        echo '<div class="findlist_date_field"> <input type="date" name="DateCreatedStart" Caption ="Date Created Start" value = "' . $createdStart . '"></div>';
        echo '<div class="findlist_and_label">And: </div>';
        echo '<div class="findlist_date_field"> <input type="date" name="DateCreatedEnd" Caption ="Date Created End" value = "' . $createdEnd . '"></div>';

        echo '<div class="findlist_date_label">Date Need By: </div>';
        echo '<div class="findlist_between_label">Between: </div>';
        echo '<div class="findlist_date_field"> <input type="date" name="DateNeededStart" Caption ="Date Need By Start" value = "' . $needByStart . '"></div>';
        echo '<div class="findlist_and_label">And: </div>';
        echo '<div class="findlist_date_field"> <input type="date" name="DateNeededEnd" Caption ="Date Need By End" value = "' . $needByEnd . '"></div>';

        echo '<div class="findlist_block">';
        echo '<div class="findlist_label">Approved: </div>';
        if($approved == 1)  echo '<input type="radio" name="Approved" value="1" checked="checked">';
        else echo '<input type="radio" name="Approved" value="1">';
        echo '<div class = findlist_radiolabel>Yes  </div>';
        if($approved == 0) echo '<input type="radio" name="Approved" value="0" checked="checked">';
        else echo '<input type="radio" name="Approved" value="0">';
        echo '<div class = findlist_radiolabel>No  </div>';
        if($approved =="") echo '<input type="radio" name="Approved" value="" checked="checked">';
        else echo '<input type="radio" name="Approved" value="">';
        echo '<div class = findlist_radiolonglabel>Show Both</div>';
        echo '</div>';

        echo '<div class="findlist_block">';
        echo '<div class="findlist_label">Assigned: </div>';
        if($assigned == 1) echo '<input type="radio" name="Assigned" value="1" checked="checked">';
        else echo '<input type="radio" name="Assigned" value="1">';
        echo '<div class = findlist_radiolabel>Yes  </div>';
        if($assigned == 0) echo '<input type="radio" name="Assigned" value="0" checked="checked">';
        else echo '<input type="radio" name="Assigned" value="0">';
        echo '<div class = findlist_radiolabel>No  </div>';
        if($assigned == "") echo '<input type="radio" name="Assigned" value="" checked="checked">';
        else echo '<input type="radio" name="Assigned" value="">';
        echo '<div class = findlist_radiolonglabel>Show Both</div>';
        echo '</div>';

        echo '<div class="findlist_block">';
        echo '<div class="findlist_label">Finished:    </div>';
        if($finished == 1) echo '<input type="radio" name="Finished" value="1" checked="checked">';
        else echo '<input type="radio" name="Finished" value="1">';
        echo '<div class = findlist_radiolabel>Yes  </div>';
        if($finished == 0) echo '<input type="radio" name="Finished" value="0" checked="checked">';
        else echo '<input type="radio" name="Finished" value="0">';
        echo '<div class = findlist_radiolabel>No  </div>';
        if($finished == "") echo '<input type="radio" name="Finished" value="" checked="checked">';
        else echo '<input type="radio" name="Finished" value="">';
        echo '<div class = findlist_radiolonglabel>Show Both</div>';
        echo '</div>';

        echo '<div class="findlist_block">';
        echo '<div class="findlist_label">Closed:    </div>';
        if($closed == 1) echo '<input type="radio" name="Closed" value="1" checked="checked">';
        else echo '<input type="radio" name="Closed" value="1">';
        echo '<div class = findlist_radiolabel>Yes  </div>';
        if($closed == 0) echo '<input type="radio" name="Closed" value="0" checked="checked">';
        else echo '<input type="radio" name="Closed" value="0">';
        echo '<div class = findlist_radiolabel>No  </div>';
        if($closed == "") echo '<input type="radio" name="Closed" value="" checked="checked">';
        else echo '<input type="radio" name="Closed" value="">';
        echo '<div class = findlist_radiolonglabel>Show Both</div>';
        echo '</div>';

        echo '<div class="findlist_block">';
        echo '<div class="findlist_label">Captain Approval:</div>';
        if($approvedByCap == 1) echo '<input type="radio" name="ApprovedByCap" value="1" checked="checked">';
        else echo '<input type="radio" name="ApprovedByCap" value="1">';
        echo '<div class = findlist_radiolabel>Yes  </div>';
        if($approvedByCap == 0) echo '<input type="radio" name="ApprovedByCap" value="0" checked="checked">';
        else echo '<input type="radio" name="ApprovedByCap" value="0">';
        echo '<div class = findlist_radiolabel>No  </div>';
        if($approvedByCap == "") echo '<input type="radio" name="ApprovedByCap" value="" checked="checked">';
        else echo '<input type="radio" name="ApprovedByCap" value="">';
        echo '<div class = findlist_radiolonglabel>Show Both</div>';
        echo '</div>';

        echo '<br>';

        echo '<input class="findlist_load_button" name = "Filter" type="submit" value="Refresh">';

    echo '</form>';
echo '</div>';


?>