<?php
session_start();
	include '../Session/session_check_superadmin.php';

	include 'db_connection.php';


	$_SESSION['message'] ='';

	if (isset($_POST['editFeature'])) {
		$_SESSION['FeatureID'] = $_POST['FeatureID'];
		header('Location: superadmin_ManageFeatures_edit.php');
		exit;
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
    <div style="display: flex; border: 1px solid black; min-height: 80vh">
        
        <!-- Left Section (Navigation) -->
			<?php include_once('navigation.php');?>
        
        <!-- Right Section (Activity) -->
        <div style="width: 80%; padding: 10px;">
						
			<h2>View Features Page</h2>

  
			<?php     
					echo $_SESSION['message1'];
		
					$qres = mysqli_query($db,	"SELECT * FROM features ") or die("Select Error");
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th>No.</th>
												<th>Feature</th>
												<th>Description</th>
												<th>Icon</th>
												<th>Image</th>
											</tr>\n";
						$accountsTable .= "<br/>";
						}
					while ($Row = $qres->fetch_assoc()) {
						$accountsTable.= "<tr>\n"
						."<td>" . $Row['FeatureID'] . "</td>" 
						."<td>" . $Row['Name'] . "</td>" 
						."<td>" . $Row['Description'] . "</td>" 
						."<td>" . $Row['Icon'] . "</td>" 
						."<td>" . $Row['Image'] . "</td>" ;
						
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='FeatureID' value='" . $Row['FeatureID'] . "'/>
							<input type='submit' name='editFeature' value='Edit'>
							</form></td>";
						
						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					


			?>
        </div>
    </div>

</body>
</html>


