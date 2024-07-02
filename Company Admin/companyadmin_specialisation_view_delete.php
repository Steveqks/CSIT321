<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	include_once('companyadmin_specialisation_viewdelete_functions.php');

	if(isset($_POST['delete'])=='yes')
	{
		$delete = new userAccount();
		$delete->deleteSpecialisation($_POST['specialisationID']);
		
		$_SESSION['message'] = "Specialisation \"" . $_POST['specialisationName'] . "\" deleted successful";
		header('Location: companyadmin_specialisation_view_delete.php');
		exit();
	}

	if (isset($_POST['editSpecialisation'])) {
		$_SESSION['specialisationName'] = $_POST['specialisationName'];
		$_SESSION['specialisationID'] = $_POST['specialisationID'];
		$_SESSION['message'] = '';
		header('Location: companyadmin_specialisation_edit.php');
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">

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
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
			<h2>View Specialisation</h2>

				<?php   
					$companyID = $_SESSION['companyID'];;
				

					$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				
					mysqli_query($db, "SET @row_number = 0;") or die("Error setting row number");

					$qres = mysqli_query($db,	"SELECT @row_number := @row_number + 1 AS `S/n`, specialisation.*
												FROM specialisation
												WHERE CompanyID = '$companyID';
												") or die("Select Error");
					
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>S/n</th>
												<th>Specialisation Name</th>
												</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n";
						$accountsTable .= "<td>" . $Row['S/n'] . "</td>";
						$accountsTable .= "<td>" . $Row['SpecialisationName'] . "</td>";
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
							<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
							<input type='submit' name='editSpecialisation' value='Edit'>
							</form></td>";
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='specialisationID' value='" . $Row['SpecialisationID'] . "'/>
							<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
							<input type='hidden' name='delete' value='yes'/>
							<input type='button' value='Delete' onclick='confirmDiag(this.form)'>
							</form></td>";
							

						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;

					if(@$_SESSION['message']) echo $_SESSION['message'];

				?>
	


        </div>
    </div>
			<script>
				function confirmDiag(form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Specialisation?");
					if (result)
					{
						form.submit();
						console.log('result = pos');	
					}else console.log('result = neg');
					console.log('confirmDiag() executed');
				}
			</script>
</body>
</html>


