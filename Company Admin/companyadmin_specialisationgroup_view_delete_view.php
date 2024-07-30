<?php
session_start();

	include '../Session/session_check_companyadmin.php';

	$_SESSION['message'] = '';

	if (isset($_POST['addTeamMember'])) {
		$_SESSION['groupID'] = $_POST['groupID'];
		
		header('Location: companyadmin_specialisationgroup_view_delete_view_addMember.php');
		exit;
	}

	if (isset($_POST['removeUser']) == 'yes') {
		$groupID = $_SESSION['groupID'];
		$userID = $_POST['userID'];
		$fullname = $_POST['fullname'];
		
		$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
		$result = 	mysqli_query($db, "
									DELETE FROM specialisationgroup
									WHERE UserID = '$userID' AND MainGroupID = $groupID;
									") or die("Select Error");
		
		$_SESSION['message'] = $fullname . " is removed from group";
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
			

            <!-- Add more content as needed -->
			<?php   


				$companyID = $_SESSION['companyID'];;
				$groupID = $_SESSION['groupID'];
				$groupName = $_SESSION['groupName'];
				$specialisationName = $_SESSION['specialisationName'];
				
				echo "<h2>View Group: ". $groupName . " </h2>";
				echo "<p> Specialisation : " . $specialisationName."</p>"; 

				echo $_SESSION['message'];

				$db = mysqli_connect('localhost','root','','tms') or die("Couldnt Connect to database");
				$result = mysqli_query($db,	
					"
					SELECT 
						sp.*,
						eu.FirstName,
						eu.LastName
					FROM 
						specialisationgroup sp
					JOIN 
						existinguser eu ON sp.UserID = eu.UserID
					WHERE 
						sp.MainGroupID = '$groupID';
					") 
				or die("Select Error");
				
				if($result){
					$accountsTable = "<table border = 1 class='center'>";
					$accountsTable .= "	<tr>
											<th>First Name</th>
											<th>Last Name</th>
											<th></th>
											</tr>\n";
					$accountsTable .= "<br/>";
					}
				while ($Row = $result->fetch_assoc()) {
					$accountsTable.= "<tr>\n"
					."<td>" . $Row['FirstName'] . "</td>" 
					."<td>" . $Row['LastName'] . "</td>" ;

	
					$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='fullname' value='" . $Row['FirstName'] .' '. $Row['LastName']  . "'/>
						<input type='hidden' name='userID' value='" . $Row['UserID'] . "'/>
						<input type='hidden' name='removeUser' value='yes'/>
						<input type='button' value='Remove' onclick='confirmDiag(this.form)'>
						</form></td>";

					$accountsTable.= "</tr>";

				}
				
				//if zero rows returned
				if (!@$hasRows) {
					$accountsTable .= "<tr>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "<td>-</td>"
						. "</tr>";
				}
				$accountsTable.= "</table> <br>";
				
				$accountsTable.= "</table> <br>";
				
				$accountsTable .= "<td><form action'' method='POST'>
						<input type='hidden' name='groupID' value='" . $groupID . "'/>
						<input type='submit' name='addTeamMember' value='Add Users to Group'>
						</form></td>";
				echo  $accountsTable;
				
			?>
        </div>
    </div>
			<script>
				function confirmDiag(form){
					console.log('confirmDiag() executing');
					let result = confirm("Remove User from Pool?");
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


