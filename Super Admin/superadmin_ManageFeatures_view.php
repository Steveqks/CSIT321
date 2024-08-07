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
	
	if (isset($_POST['addNewFeature'])) {
		header('Location: superadmin_ManageFeatures_create.php');
		exit;
	}
	
	if(isset($_POST['delete']) == 'yes')
	{
		$FeatureID = $_POST['FeatureID'];
		$result = mysqli_query($db,	"DELETE FROM features WHERE FeatureID = '$FeatureID' ") or die("Delete Error");
		$_SESSION['message'] = "<p>Feature deleted successfully</p>";
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
					echo $_SESSION['message'];
					
					echo "<a href='superadmin_ManageFeatures_create' target='_self'>Add New Features</a>";
		
					$qres = mysqli_query($db,	"SELECT * FROM features ") or die("Select Error");
						
					if($qres){
						$accountsTable = "<table border = 1 class='center'>";
						$accountsTable .= "	<tr>
												<th style='width:3%;'>S/N</th>
												<th style='width:12%;'>Feature</th>
												<th style='width:55%;'>Description</th>
												<th style='width:15%;'>Icon</th>
												<th style='width:15%;'>Image</th>
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
							
						$accountsTable .= "<td><form action'' method='POST'>
							<input type='hidden' name='FeatureID' value='" . $Row['FeatureID'] . "'/>
							<input type='hidden' name='delete' value='yes'/>
							<input type='button' value='Delete' onclick='confirmDiag(event, this.form);'>
							</form></td>";
						
						
						$accountsTable.= "</tr>";
					}
					$accountsTable.= "</table>";
					echo  $accountsTable;
					
					


			?>
        </div>
    </div>

</body>
				<script>
					function confirmDiag(event, form){
						console.log('confirmDiag() executing');
						let result = confirm("Delete Feature? Feature will not be recoverable afterwards.");
						if (result)
						{
							form.submit();
							console.log('result = pos');	
						}else console.log('result = neg');
						console.log('confirmDiag() executed');
					}
				</script>
</html>


