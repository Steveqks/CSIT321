<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message1'] = '';

	if (isset($_POST['viewGroup'])) {
		$_SESSION['groupName'] = $_POST['groupName'];
		$_SESSION['groupID'] = $_POST['groupID'];
		$_SESSION['specialisationName'] = $_POST['specialisationName'];
		
		header('Location: companyadmin_specialisationgroup_view_delete_view.php');
		exit;
	}

	if (isset($_POST['editGroup'])) {
		$_SESSION['groupID'] = $_POST['groupID'];
		header('Location: companyadmin_specialisationgroup_view_delete_edit.php');
		exit;
	}

	if(isset($_POST['delete']) == 'yes')
	{
		$groupID = $_POST['groupID'];
		$groupName = $_POST['groupName'];
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		
									
		$result2 = 	mysqli_query($db, "
									DELETE FROM specialisationgroupinfo
									WHERE MainGroupID = '$groupID';
									") or die("Select Error");
		
		$_SESSION['message1'] =  "Specialisation Pool \"". $groupName ."\" deleted.";
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh;">
        
        <!-- Left Section (Navigation) -->
		<?php include_once('navigation.php') ?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
			<h2>View Specialisation Group</h2>

  
			<?php     
				echo $_SESSION['message1'];

				
				$companyID = $_SESSION['companyID'];;

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = 	mysqli_query($db,	
								"SELECT 
									spi.GroupName,
									spi.MainGroupID,
									s.SpecialisationName,
									COUNT(sp.MainGroupID) AS Capacity
								FROM 
									(SELECT @row_number := 0) AS init,
									specialisationgroupinfo spi
								JOIN 
									specialisation s ON spi.SpecialisationID = s.SpecialisationID
								LEFT JOIN 
									specialisationgroup sp ON spi.MainGroupID = sp.MainGroupID
								WHERE 
									spi.CompanyID = '$companyID'
								GROUP BY 
									spi.MainGroupID, spi.GroupName, s.SpecialisationName;
								") 
							or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
											<th>Group Name</th>
											<th>Specialisation Name</th>
											<th>Capacity</th>
											</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['GroupName'] . "</td>" 
					."<td>" . $Row['SpecialisationName'] . "</td>"
					."<td>" . $Row['Capacity'] . "</td>";

					
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='groupName' value='" . $Row['GroupName'] . "'/>
						<input type='hidden' name='groupID' value='" . $Row['MainGroupID'] . "'/>
						<input type='hidden' name='specialisationName' value='" . $Row['SpecialisationName'] . "'/>
						<input type='submit' name='viewGroup' value='View'>
						</form></td>";
						
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='groupID' value='" . $Row['MainGroupID'] . "'/>
						<input type='submit' name='editGroup' value='Edit'>
						</form></td>";

					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='MainGroupID' value='" . $Row['MainGroupID'] . "'/>
						<input type='hidden' name='groupName' value='" . $Row['GroupName'] . "'/>
						<input type='hidden' name='groupID' value='" . $Row['MainGroupID'] . "'/>
						<input type='hidden' name='delete' value='yes'/>
						<input type='button' value='Delete' onclick='confirmDiag(this.form)'>
						</form></td>";

					$accountsTable.= "</tr>";
				}
				//if zero rows returned
				if (!@$hasRows) {
					$accountsTable .= "<tr>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "</tr>";
				}
				$accountsTable.= "</table>";
				echo  $accountsTable;
				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(form){
					console.log('confirmDiag() executing');
					let result = confirm("Delete Specialisation Group? ");
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


