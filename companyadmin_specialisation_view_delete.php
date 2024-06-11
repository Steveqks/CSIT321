<?php
session_start();

if (isset($_POST['editSpecialisation'])) {
	$_SESSION['specialisationName'] = $_POST['specialisationName'];
	$_SESSION['specialisationID'] = $_POST['specialisationID'];
	$_SESSION['message'] = '';
	header('Location: companyadmin_edit_specialisation.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="a.css">

    <title>TrackMySchedule</title>
</head>
<body>

    <!-- Top Section -->
	<div style="border: 1px solid black; height: 20vh; overflow: hidden; text-align: left;">
        <img src="tms.png" alt="TrackMySchedule Logo" style="height: 100%; width: auto;">
    </div>

    <!-- Middle Section -->
    <div style="display: flex; border: 1px solid black; height: 80vh;">
        
        <!-- Left Section (Navigation) -->
			<div class="vertical-menu" style="border-right: 1px solid black; padding: 0px;">
			  <a href="#">Home</a>
			  <a href="#">Link 1</a>
			  <a href="#">Link 2</a>
			  <a href="#">Link 3</a>
			  <a href="#">Link 4</a>
			</div>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
            <!-- Add more content as needed -->
			
				<?php   include_once('companyadmin_viewdelete_specialisation_functions.php');

						$view = new viewSpecialisationController();
						$qres = $view->viewSpecialisation();
						
						if($qres){
							$accountsTable = "<table border = 1 class='center'>";
							$accountsTable .= "	<tr>
													<th>SpecialisationID</th>
													<th>Specialisation Name</th>
													</tr>\n";
							$accountsTable .= "<br/>";
							}
						while ($Row = $qres->fetch_assoc()) {
							$accountsTable.= "<tr>\n";
							$accountsTable .= "<td>" . $Row['SpecialisationID'] . "</td>";
							$accountsTable .= "<td>" . $Row['SpecialisationName'] . "</td>";
							
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
								<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
								<input type='submit' name='editSpecialisation' value='Edit'>
								</form></td>";
							$accountsTable .= "<td><form action'' method='POST'>
								<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
								<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
								<input type='submit' name='deleteSpecialisation' value='Delete'>
								</form></td>";
								

							$accountsTable.= "</tr>";
						}
						$accountsTable.= "</table>";
						echo  $accountsTable;
						

						if(isset($_POST['deleteSpecialisation']))
						{
							$delete = new userAccount();
							
							$delete->deleteSpecialisation($_POST['specialisationID']);
							header('Location: companyadmin_viewdelete_specialisation.php');
						}
				?>
        </div>
    </div>

</body>
</html>


